<?php

class ATFOptionsMetabox
{

    protected static $instance;

    private function __construct()
    {

    }

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}