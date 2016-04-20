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


        add_action('the_content', array($this, 'subscribtion_content'));
    }

    public static function get_instance($subscribtion_industry = null, $version = null)
    {
        if (null === self::$instance) {
            self::$instance = new self($subscribtion_industry, $version);
        }
        return self::$instance;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Subscribtion_Industry_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Subscribtion_Industry_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->subscribtion_industry, plugin_dir_url(__FILE__) . 'css/subscribtion-industry-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Subscribtion_Industry_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Subscribtion_Industry_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->subscribtion_industry, plugin_dir_url(__FILE__) . 'js/subscribtion-industry-public.js', array('jquery'), $this->version, false);

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
