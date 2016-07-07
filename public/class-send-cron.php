<?php

/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 7/6/16
 * Time: 1:35 PM
 */
class Sender_Cron
{
    private $version;
    protected static $instance;
    public $colms;

    private function __construct($version)
    {
        $this->version = $version;
        add_action('send_newsletter', array($this, 'cron_send'));
        if ( ! wp_next_scheduled( 'my_task_hook' ) ) {
            wp_schedule_event( time()+5, 'hourly', 'send_newsletter' );
        }
    }


    public function cron_send($args) {

        file_put_contents(ABSPATH . '/cron.txt', implode('/', $args) . time());
    }


    public static function get_instance($version)
    {
        if (null === self::$instance) {
            self::$instance = new self($version);
        }
        return self::$instance;
    }
}