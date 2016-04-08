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
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $subscribtion_industry The ID of this plugin.
     */
    private $subscribtion_industry;

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
        add_action('init', array($this, 'newsletters'));
        add_filter('si_templates', array($this, 'default_templates'));


        add_action('load-post.php', array($this, 'load_metabox'));
        add_action('load-post-new.php', array($this, 'load_metabox'));

        add_action('admin_menu', array($this, 'subscribers_page'));
    }

    public function load_metabox()
    {
        include_once 'class-atf-options-metabox.php';
        Newsletters_Metabox::get_instance($this->version);
    }

    public function newsletters()
    {
        $labels = array(
            'name' => _x('Newsletters', 'post type general name', 'your-plugin-textdomain'),
            'singular_name' => _x('Newsletter', 'post type singular name', 'your-plugin-textdomain'),
            'menu_name' => _x('Newsletters', 'admin menu', 'your-plugin-textdomain'),
            'name_admin_bar' => _x('Newsletter', 'add new on admin bar', 'your-plugin-textdomain'),
            'add_new' => _x('Add New', 'Newsletter', 'your-plugin-textdomain'),
            'add_new_item' => __('Add New Newsletter', 'your-plugin-textdomain'),
            'new_item' => __('New Newsletter', 'your-plugin-textdomain'),
            'edit_item' => __('Edit Newsletter', 'your-plugin-textdomain'),
            'view_item' => __('View Newsletter', 'your-plugin-textdomain'),
            'all_items' => __('All Newsletters', 'your-plugin-textdomain'),
            'search_items' => __('Search Newsletters', 'your-plugin-textdomain'),
            'parent_item_colon' => __('Parent Newsletters:', 'your-plugin-textdomain'),
            'not_found' => __('No Newsletters found.', 'your-plugin-textdomain'),
            'not_found_in_trash' => __('No Newsletters found in Trash.', 'your-plugin-textdomain'),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'newsletter'),
            'capability_type' => 'page',
            'has_archive' => true,
            'hierarchical' => true,
            'menu_position' => null,
            'supports' => array('title', 'page-attributes'),
        );
        register_post_type('newsletters', $args);
    }


    public function default_templates($templates)
    {
        return array_merge($templates, array(
            'default' => array(
                'name' => 'Default text',
                'describtion' => 'The default text template',
                'preview' => plugin_dir_url(__FILE__) . 'img/email_template_txt.png',
                'fields' => array(
                    'content' => array(
                        'type' => 'textarea',
                        'title' => 'Content',
                    ),
                ),
            ),
            'default_html' => array(
                'name' => 'Default HTML',
                'describtion' => 'The default text template',
                'preview' => plugin_dir_url(__FILE__) . 'img/email_template_html.png',
                'fields' => array(
                    'content' => array(
                        'type' => 'editor',
                        'title' => 'Content',
                    ),
                ),
            ),
            'default_example' => array(
                'name' => 'Default HTML',
                'describtion' => 'The default text template',
                'preview' => plugin_dir_url(__FILE__) . 'img/email_campaigns_en.png',
                'fields' => array(
                    'content' => array(
                        'type' => 'textarea',
                        'title' => 'Content',
                    ),
                    'editor' => array(
                        'type' => 'editor',
                        'title' => 'Content',
                    ),
                    'text' => array(
                        'type' => 'text',
                        'title' => 'Content',
                    ),
                ),
            ),


        ));
    }

    public function subscribers_page()
    {
        add_submenu_page(
            'users.php',
            'Subscribes',
            'Subscribes',
            'manage_options',
            'subscribers',
            array($this, 'subscribers_views'));
    }

    public function subscribers_views()
    {

        if (isset($_GET['action'])) $action = $_GET['action'];
        else $action = '';

        switch ($action) {
            case 'delete':
                $this->load_confirn_deletetion_view();
                break;
            default:
                $this->load_default_view();
        }


    }


    public function load_confirn_deletetion_view()
    {
        if (isset($_GET['subscriber'])) $subscribers[] = $_GET['subscriber'];
        elseif (isset($_GET['subscribers'])) $subscribers = $_GET['subscribers'];
        else $subscribers = array();

        global $wpdb;

        $select = 'SELECT * FROM ' . $wpdb->prefix . 'si_subscribers WHERE id IN (' . implode(',', $subscribers) . ');';
        $subscribers = $wpdb->get_results($select);

        ?>
        <div class="wrap">


        <h2><?php _e('Delete subscriber', 'si'); ?></h2>
        <form method="post">
            <p>
                You have specified this subscriber for deletion:
            </p>
            <ul>
                <?php foreach ($subscribers as $subscriber) {
                    ?>
                    <li>
                        <input type="hidden" name="subscribers[]" value="<?php echo $subscriber->id; ?>"><?php echo 'ID #' . $subscriber->id . ': ' . $subscriber->email . ' [' . $subscriber->name . ']'; ?></li>
                    <?php
                } ?>

            </ul>

            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Confirm Deletion"></p>
        </form>

        <?php
    }

    public function load_default_view()
    {

        if (isset($_GET['orderby']) && in_array($_GET['orderby'], array('name', 'email', 'status', 'last_send'))) $orderby = $_GET['orderby'];
        else $orderby = 'id';

        if ($orderby == 'status') $orderby_sql = 'activation_key';
        else $orderby_sql = $orderby;

        if (isset($_GET['order'])) $order = $_GET['order'];
        else $order = 'asc';

        global $wpdb;
        $select = 'SELECT * FROM ' . $wpdb->prefix . 'si_subscribers ORDER BY ' . $orderby_sql . ' ' . $order . ';';
        $subscribers = $wpdb->get_results($select);

        include 'subscribers_views/default.php';
    }

}
