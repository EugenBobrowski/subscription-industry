<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Subscribtion_Industry
 * @subpackage Subscribtion_Industry/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Subscribtion_Industry
 * @subpackage Subscribtion_Industry/admin
 * @author     Your Name <email@example.com>
 */
class Subscribtion_Industry_Admin
{

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $subscribtion_industry The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($version)
    {
        $this->version = $version;
        add_action('load-post.php', array($this, 'load_metabox'));
        add_action('load-post.php', array($this, 'load_metabox_send'));
        add_action('load-post-new.php', array($this, 'load_metabox'));

        $this->load_subscribers_page();
        $this->load_options_page();
        $this->load_admin_bar_node();
    }

    /**
     * @return string
     */
    public function load_subscribers_page()
    {
        include_once plugin_dir_path(__FILE__) . 'partials/class-subscribers-page.php';
        Subscribers_Page::get_instance($this->version);
    }

    public function load_options_page()
    {
        include_once plugin_dir_path(__FILE__) . 'partials/class-si-options.php';
        Si_Options::get_instance($this->version);
    }

    public function load_metabox()
    {
        include_once plugin_dir_path(__FILE__) . 'partials/class-template-metabox.php';
        Newsletters_Metabox::get_instance($this->version);
    }

    public function load_metabox_send()
    {
        include_once plugin_dir_path(__FILE__) . 'partials/class-send-metabox.php';
        Sender_Metabox::get_instance($this->version);
    }

    public function load_admin_bar_node()
    {
        include_once plugin_dir_path(__FILE__) . 'class-admin-bar-node.php';
        Admin_Bar_Node::get_instance($this->version);
    }


}
