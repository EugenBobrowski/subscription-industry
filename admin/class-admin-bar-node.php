<?php

class Admin_Bar_Node
{
    private $version;
    protected static $instance;

    private function __construct($version)
    {
        $this->version = $version;
        add_action('admin_bar_menu', array($this, 'admin_bar_menu'), 99);
        add_action('admin_print_styles', array($this, 'style'));
    }

    public function admin_bar_menu($wp_admin_bar)
    {
        $wp_admin_bar->add_node( array(
            'id'    => 'si-node',
            'title' => 'Si',
            'href'  => '#',
            'meta'  => array( 'class' => 'si-subscription-admin-bar-node' )
        ) );

        $wp_admin_bar->add_node(array(
            'id' => 'si-node-newsletters',
            'href' => admin_url('edit.php?post_type=newsletters'),
            "title" => 'Newsletters',
            "parent" => "si-node",
        ));
        $wp_admin_bar->add_node(array(
            'id' => 'si-node-groups',
            'href' => admin_url('edit-tags.php?taxonomy=newsletter_groups&post_type=newsletters'),
            "title" => 'Groups',
            "parent" => "si-node",
        ));
        $wp_admin_bar->add_node(array(
            'id' => 'si-node-subscribers',
            'href' => admin_url('users.php?page=subscribers'),
            "title" => 'Subscribers',
            "parent" => "si-node",
        ));


    }
    public function style()
    {
        ?>
        <style>
            #wp-admin-bar-si-node > .ab-item:before {
                content: "\f466" !important;
                top: 4px;
            }
        </style><?php
    }

    public static function get_instance($version)
    {
        if (null === self::$instance) {
            self::$instance = new self($version);
        }
        return self::$instance;
    }
}