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
class Subscribtion_Industry_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $subscribtion_industry    The ID of this plugin.
	 */
	private $subscribtion_industry;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $subscribtion_industry       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $subscribtion_industry, $version ) {

		$this->subscribtion_industry = $subscribtion_industry;
		$this->version = $version;

		add_action('widgets_init', array($this, 'define_widget'));
		add_action('wp', array($this, 'confirm_key'));
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->subscribtion_industry, plugin_dir_url( __FILE__ ) . 'css/subscribtion-industry-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->subscribtion_industry, plugin_dir_url( __FILE__ ) . 'js/subscribtion-industry-public.js', array( 'jquery' ), $this->version, false );

	}

	public function define_widget () {
		include_once 'widget-subscribe.php';
		register_widget('SI_Subscribe_Widget');
	}

	public function confirm_key () {
		global $wp_query;

		var_dump($wp_query);

		if (isset($_GET['confirm_subscribtion']) && is_page()) {
			set_query_var('page_id', 2);
//			query_posts(array('page_id' => 2));
		}

	}
	public function replace_title_on_confirm ($title) {
		return 'Subscribtion industry';
	}
	public function replace_template_on_confirm ($template) {
		return get_page_template();
	}

}
