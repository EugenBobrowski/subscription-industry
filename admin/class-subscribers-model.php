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

        public static function get_instance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }
    }
}
