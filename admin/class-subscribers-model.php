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
