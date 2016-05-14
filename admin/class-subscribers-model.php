<?php


if (!class_exists('Subscribers_Model')) {
    class Subscribers_Model {
        protected static $instance;
        private $wpdb;

        private function __construct()
        {
//            global $wbdb;
//            $this->wbdb = $wbdb;
        }
        public static function get_instance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function get_subscribers($args = array(), $output = OBJECT) {

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
            return  $wpdb->get_results($select, $output);
            
        }
        public function unsubscribe ($email, $hash) {
            global $wpdb;
            $table = $wpdb->prefix . 'si_subscribers';
            $select = 'SELECT * FROM ' . $table . ' WHERE email=\'' . $email . '\';';
            $r = $wpdb->get_results($select );
            if (empty($r))  return null;

            $r = array_shift($r);

            if ($hash != hash('md5', $r->activation_key)) return null;

            if (1 == $r->status) {
                $unsubscribe = $wpdb->update($table, array('status' => 0), array('id' => $r->id));
            } else {
                $unsubscribe = 0;
            }

            return  array(
                $r, $unsubscribe
            );
            
        }
        public function update_last_send ($ids) {
            global $wpdb;
            $table = $wpdb->prefix . 'si_subscribers';
            $ids = implode(', ', $ids);
            $update = "UPDATE `{$table}` SET `last_send`=NOW() WHERE `id` in ({$ids})";
            $wpdb->get_results($update);
        }
        public function confirm ($email, $hash) {
            global $wpdb;
            $table = $wpdb->prefix . 'si_subscribers';
            $select = 'SELECT * FROM ' . $table . ' WHERE email=\'' . $email . '\' AND activation_key=\'' . $hash . '\';';
            $r = $wpdb->get_results($select );
            if (empty($r)) {
                return null;
            }
            $r = array_shift($r);
            if (0 == $r->status) {
                $unsubscribe = $wpdb->update($table, array('status' => 1), array('id' => $r->id));
            } else {
                $unsubscribe = 0;
            }
            return  array(
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
    }
}
