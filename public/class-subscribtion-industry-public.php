<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Subscribtion_Industry
 * @subpackage Subscribtion_Industry/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Subscribtion_Industry
 * @subpackage Subscribtion_Industry/public
 * @author     Your Name <email@example.com>
 */
class Subscribtion_Industry_Public
{

    protected static $instance;
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
     * @param      string $subscribtion_industry The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    private function __construct($subscribtion_industry, $version)
    {

        $this->subscribtion_industry = $subscribtion_industry;
        $this->version = $version;

        add_action('init', array($this, 'newsletters'));
        add_action('the_content', array($this, 'subscribtion_content'));
        add_action('the_content', array($this, 'newsletter_preview'));

        include_once 'class-default-templates.php';
        Si_Default_Templates::get_instance();

    }

    public static function get_instance($subscribtion_industry = null, $version = null)
    {
        if (null === self::$instance) {
            self::$instance = new self($subscribtion_industry, $version);
        }
        return self::$instance;
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
            'view_item' => __('Web version', 'your-plugin-textdomain'),
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

        register_taxonomy(
            'newsletter_groups',
            'newsletters',
            array(
                'label' => __( 'Groups' ),
                'rewrite' => array( 'slug' => 'groups' ),
                'show_ui' => true,
                'show_admin_column'     => true,
                'hierarchical' => false,
            )
        );
    }

    public function newsletter_preview ($content) {
        global $post;

        if ('newsletters' != $post->post_type) return $content;

        include_once plugin_dir_path(__FILE__) . 'class-templater.php';

        $templater = Si_Templater::get_instance();
        
        return $templater->get_newsletter_web($post->ID);

    }

    public function define_widget()
    {
        include_once 'widget-subscribe.php';
        register_widget('SI_Subscribe_Widget');
    }

    public function update_subscriber($data, $where)
    {
        global $wpdb;

        return $wpdb->update($wpdb->prefix . 'si_subscribers', $data, $where);

    }


    public function subscribtion_content($content)
    {

        $options = get_option('si_options');

        if ($options['confirm_page'] == get_the_ID() && isset($_GET['action'])  && isset($_GET['hash'])  && isset($_GET['email']) ) {
            include_once plugin_dir_path(__FILE__) . '../admin/class-subscribers-model.php';
            switch ($_GET['action']) {
                case 'unsubscribe':
                    $subscribtion_model = Subscribers_Model::get_instance();
                    $email = sanitize_email($_GET['email']);
                    $hash = sanitize_text_field($_GET['hash']);
                    $unsubscribe = $subscribtion_model->unsubscribe($email, $hash);
                    if ($unsubscribe == null) {
                        return 'No subscriber';
                    }
                    $subscriber = array_shift($unsubscribe);
                    $unsubscribe = array_shift($unsubscribe);
                    if (empty($subscriber->name)) {
                        $subscriber->name = 'Subscriber';
                    }
                    return '<p>Dear ' . $subscriber->name. '. </p><p> You successfully unsubscribed</p>';

                    break;
                case 'confirm':
                    $subscribtion_model = Subscribers_Model::get_instance();
                    $email = sanitize_email($_GET['email']);
                    $hash = sanitize_text_field($_GET['hash']);
                    $unsubscribe = $subscribtion_model->confirm($email, $hash);
                    if ($unsubscribe == null) {
                        return 'No subscriber';
                    }
                    $subscriber = array_shift($unsubscribe);
                    $unsubscribe = array_shift($unsubscribe);
                    if (empty($subscriber->name)) {
                        $subscriber->name = 'Subscriber';
                    }
                    return '<p>Dear ' . $subscriber->name. '. </p><p> You successfully subscribed</p>';
                    break;
            }
        } else {
            return $content;
        }
        
        return false;

    }


    public function replace_title_on_confirm($title)
    {
        return 'Subscribtion industry';
    }

    public function replace_template_on_confirm($template)
    {
        return get_page_template();
    }
}
