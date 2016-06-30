<?php


if (!class_exists('Subscribers_Model')) {
    class Subscribers_Model
    {
        protected static $instance;
        private $wpdb;

        private $table_subscribers;
        private $table_groups;

        private function __construct()
        {
            global $wpdb;

            $this->table_subscribers = $wpdb->prefix . 'si_subscribers';
            $this->table_groups = $wpdb->prefix . 'si_groups_relations';
            
        }

        public static function get_instance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function get_subscribers($args = array(), $output = OBJECT)
        {

            global $wpdb;

            $args = wp_parse_args($args, array(
                'orderby' => 'id',
                'order' => 'asc',
            ));

            $where = '';

            if (isset($args['where'])) {
                $where = 'WHERE ' . $args['where']['field'] . ' ' . $args['where']['compare'] . ' ' . $args['where']['value'];
            }

            $select = 'SELECT * FROM ' . $wpdb->prefix . 'si_subscribers ' . $where . ' ORDER BY ' . $args['orderby'] . ' ' . $args['order'] . ';';
            return $wpdb->get_results($select, $output);

        }

        public function get_groups_subscribers ($groups_ids, $confirmed = 1, $full = false, $output = OBJECT) {

            if ($full) {
                $select = '';
            }
            else {
                $select = 'subscriber_id';
                $output = ARRAY_N;
            }

            if ($confirmed === null) $where_confirmed = "";
            else $where_confirmed = $this->table_subscribers . '.status=' . intval($confirmed == true);

            $groups_ids = implode(', ', $groups_ids);

            $sql = "SELECT {$select} FROM {$this->table_groups} INNER JOIN {$this->table_subscribers} 
              ON {$this->table_groups}.subscriber_id = {$this->table_subscribers}.id 
              WHERE {$this->table_groups}.term_taxonomy_id IN ({$groups_ids}) AND {$where_confirmed} ;";

            global $wpdb;

            return $wpdb->get_results($sql, $output);
        }

        public function unsubscribe($email, $hash)
        {
            global $wpdb;
            $table = $wpdb->prefix . 'si_subscribers';
            $select = 'SELECT * FROM ' . $table . ' WHERE email=\'' . $email . '\';';
            $r = $wpdb->get_results($select);
            if (empty($r)) return null;

            $r = array_shift($r);

            if ($hash != hash('md5', $r->activation_key)) return null;

            if (1 == $r->status) {
                $unsubscribe = $wpdb->update($table, array('status' => 0), array('id' => $r->id));
            } else {
                $unsubscribe = 0;
            }

            return array(
                $r, $unsubscribe
            );

        }

        public function update_last_send($ids)
        {
            global $wpdb;
            $table = $wpdb->prefix . 'si_subscribers';
            $ids = implode(', ', $ids);
            $update = "UPDATE `{$table}` SET `last_send`=NOW() WHERE `id` in ({$ids})";
            $wpdb->get_results($update);
        }

        public function confirm($email, $hash)
        {
            global $wpdb;
            $table = $wpdb->prefix . 'si_subscribers';
            $select = 'SELECT * FROM ' . $table . ' WHERE email=\'' . $email . '\';';
            $r = $wpdb->get_results($select);
            if (empty($r)) {
                return null;
            }
            $r = array_shift($r);

            if ($hash != hash('md5', $r->activation_key)) return null;

            if (0 == $r->status) {
                $unsubscribe = $wpdb->update($table, array('status' => 1), array('id' => $r->id));
            } else {
                $unsubscribe = 0;
            }
            return array(
                $r, $unsubscribe
            );

        }

        public function insert_subscriber($email, $name = '', $confirm = true)
        {
            global $wpdb;

            $select = 'SELECT activation_key FROM ' . $wpdb->prefix . 'si_subscribers WHERE email=\'' . $email . '\';';


            $exists = $wpdb->get_results($select);
            if (!empty($exists)) {
                $confirm = '';
                if ($exists[0]->activation_key) $confirm = 'unconfirmed';
                return 'exists ' . $confirm;
            }


            $pass = ($confirm) ? wp_generate_password(24, true) : '';


            $insert = $wpdb->insert($wpdb->prefix . 'si_subscribers', array(
                'email' => $email,
                'name' => $name,
                'activation_key' => $pass));

            if (true == $insert) {
                return $wpdb->insert_id;
            } else {
                return $insert;
            }
        }

        public function update_subscriber($data, $where)
        {
            global $wpdb;

            return $wpdb->update($wpdb->prefix . 'si_subscribers', $data, $where);

        }

        public function set_subscriber_group($subscriber_id = 0, $groups = '', $append = false)
        {
            $subscriber_id = (int) $subscriber_id;

            if ( !$subscriber_id )
                return false;

            if ( empty($groups) )
                return false;

            if ( ! is_array( $groups ) ) {
                $comma = _x( ',', 'tag delimiter' );
                if ( ',' !== $comma )
                    $groups = str_replace( $comma, ',', $groups );
                $groups = explode( ',', trim( $groups, " \n\t\r\0\x0B," ) );
            }

            global $wpdb;
            
            $table_groups_relations = $wpdb->prefix . 'si_groups_relations';

            $sql = '';

            if (!$append) {
                $not_in = implode(', ', $groups);
                $sql .= "DELETE FROM `{$table_groups_relations}` WHERE `subscriber_id` = {$subscriber_id} AND `term_taxonomy_id` NOT IN ({$not_in});";
                $wpdb->query($sql);
            }
            

            foreach ($groups as $group) {
                if (!term_exists($group, 'newsletter_groups')) {var_dump($group, 'xxxxxxxxxxxx'); continue;}
                $sql = "INSERT INTO {$table_groups_relations} (subscriber_id, term_taxonomy_id) VALUES ({$subscriber_id}, {$group});";
                var_dump($sql);
                $wpdb->query($sql);
            }

            return true;
        }
        public function get_subscriber_group ($subscriber_id, $return_ids = true) {
            global $wpdb;

            if ($return_ids) {
                $sql = "SELECT `term_taxonomy_id` FROM `{$this->table_groups}` WHERE `subscriber_id` = {$subscriber_id};";
                return array_map('array_pop', $wpdb->get_results($sql, ARRAY_A));
            } else {
                $sql = "SELECT {$wpdb->terms}.term_id, {$wpdb->terms}.name, {$wpdb->terms}.slug  "
                    . "FROM `{$this->table_groups}` INNER JOIN `{$wpdb->terms}` "
                    . "ON {$this->table_groups}.term_taxonomy_id={$wpdb->terms}.term_id "
                    . "WHERE `subscriber_id` = {$subscriber_id};";
                return $wpdb->get_results($sql);

            }


        }
    }
}
