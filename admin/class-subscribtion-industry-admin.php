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
        add_action('init', array($this, 'newsletters'));
        add_filter('si_templates', array($this, 'default_templates'));


        add_action('load-post.php', array($this, 'load_metabox'));
        add_action('load-post.php', array($this, 'load_metabox_send'));
        add_action('load-post-new.php', array($this, 'load_metabox'));
        $this->load_subscribers_page();
        $this->load_options_page();
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
                    'type' => 'plain',
                    'preview' => plugin_dir_url(__FILE__) . 'img/email_template_txt.png',
                    'fields' => array(
                        'content' => array(
                            'type' => 'textarea',
                            'title' => 'Content',
                        ),
                    ),
                    'body' => '{content}'
                ),
                'default_html' => array(
                    'name' => 'Default HTML',
                    'describtion' => 'The default text template',
                    'type' => 'html',
                    'preview' => plugin_dir_url(__FILE__) . 'img/email_template_html.png',
                    'fields' => array(
                        'content' => array(
                            'type' => 'editor',
                            'title' => 'Content',
                        ),
                        'logo' => array(
                            'type' => 'media',
                            'title' => 'Logo',
                        ),
                        'bg' => array(
                            'type' => 'color',
                            'title' => 'Background',
                        )

                    ),
                    'body' => '<table border="0" cellspacing="0" cellpadding="15" style="background-color:{bg};font-family:Helvetica,Arial,sans-serif" width="100%" bgcolor="{bg}">
<tr>
<td></td><td width="600">
    <img src="{logo}" alt="">
</td><td></td>
</tr>
<tr>
    <td></td>
    <td width="600"  style="background-color:#ffffff;" bgcolor="#ffffff" cellpadding="0">
    {content}
</td>
    <td></td>
</tr>
<tr>
<td></td><td width="600"></td><td></td>
</tr>
</table>',

                ),
                'system' => array(
                    'name' => 'System Template',
                    'describtion' => 'System template',
                    'preview' => plugin_dir_url(__FILE__) . 'img/email_campaigns_en.png',
                    'fields' => array(
                        'type' => array(
                            'type' => 'select',
                            'title' => 'Letter Type',
                            'options' => array(
                                'text' => 'Text',
                                'html' => 'HTML',
                            ),
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
            )
        );
    }

}
