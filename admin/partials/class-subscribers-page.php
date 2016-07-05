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
class Subscribers_Page
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
	 * @param      string $version The version of this plugin.
	 */


	protected static $instance;

	private function __construct($version)
	{
		$this->version = $version;
		include_once plugin_dir_path(__FILE__) . '../class-subscribers-model.php';

		add_action('admin_menu', array($this, 'subscribers_page'));
		add_action('wp_ajax_check_import', array($this, 'check_import'));
		add_action('wp_ajax_do_import', array($this, 'do_import'));
	}

	public static function get_instance($version)
	{
		if (null === self::$instance) {
			self::$instance = new self($version);
		}
		return self::$instance;
	}


	public function subscribers_page()
	{


		add_submenu_page(
			'users.php',
			'Subscribes',
			'Subscribes',
			'manage_options',
			'subscribers',
			array($this, $this->subscribers_views_controller()));

	}

	public function getSubscribtionIndustry()
	{
		return $this->subscribtion_industry;
	}

	public function subscribers_views_controller()
	{


		if (isset($_GET['action'])) $action = $_GET['action'];
		else $action = '';

		switch ($action) {
			case 'delete':
				if (isset($_POST['confirm_delete']) && 'dodelete' == $_POST['confirm_delete']) {
					$this->do_delete();
					wp_redirect(get_admin_url(null, 'users.php?page=subscribers'));
					exit;
				} elseif (!isset($_GET['subscribers']) && !isset($_GET['subscriber'])) {
					wp_redirect(get_admin_url(null, 'users.php?page=subscribers'));
					exit;
				} else {
					return 'load_confirm_deletetion_view';
				}
				break;
			case 'edit':
				if (isset($_POST['action']) && 'doedit' == $_POST['action']) {
					$this->do_edit();
					wp_redirect(get_admin_url(null, 'users.php?page=subscribers'));
					exit;
				} else {
					include_once plugin_dir_path(__FILE__) . '../atf-fields/htmlhelper.php';
					add_action('admin_enqueue_scripts', array($this, 'assets'));
					return 'load_edit_view';
				}
				break;
			case 'import':

				if (isset($_POST['action']) && 'doimport' == $_POST['action']) {

				} else {

					include_once plugin_dir_path(__FILE__) . '../atf-fields/htmlhelper.php';
					add_action('admin_enqueue_scripts', array($this, 'assets'));
					add_action('admin_enqueue_scripts', array($this, 'import_assets'));
					return 'load_import_view';
				}
				break;
			default:
				add_action('admin_enqueue_scripts', array($this, 'assets'));
				return 'load_default_view';
		}
	}

	public function assets($prefix)
	{
		include_once plugin_dir_path(__FILE__) . '../atf-fields/htmlhelper.php';
		AtfHtmlHelper::assets($prefix, null);
		wp_enqueue_style('subscribtion-industry', plugin_dir_url(__FILE__) . '../css/subscribtion-industry-admin.css', array(), $this->version, 'all');
	}

	public function import_assets()
	{
		wp_enqueue_script('si-import', plugin_dir_url(__FILE__) . '../js/import.js', array('jquery'), '1.0', true);
		wp_enqueue_style('subscribtion-industry', plugin_dir_url(__FILE__) . '../css/subscribtion-industry-admin.css', array(), $this->version, 'all');

		wp_localize_script( 'si-import', 'si_admin_ajax',
			array( 'ajax_url' => admin_url( 'admin-ajax.php?action=import' ), 'we_value' => 1234 ) );
	}



	public function check_import()
	{
		
		$data = $_POST['data'];
		
		$emails = array();
		
		foreach ($data as $key=>$entry) {
			if (empty($entry['email']) || !sanitize_text_field($entry['email']) || in_array($entry['email'], $emails)) {
				unset($data[$key]);
				continue;
			}

			$entry['gravatar'] = get_avatar($entry['email'], 32);

			//group exists

			if (!empty($entry['groups'])) {
				$data[$key]['groups'] = explode(',', $entry['groups']);
			}

			$emails[] = $entry['email'];

			$data[$entry['email']] = $entry;
			unset($data[$key]);


		}

		/**
		 * Check emails for existing
		 */
		$model = Subscribers_Model::get_instance();

		$subscribers = $model->get_subscribers(array(
			'where' => array(
				'field' => 'email',
				'compare' => 'IN',
				'value' => '(\'' . implode('\', \'', $emails) . '\')',
			)
		), ARRAY_A);

		foreach ($subscribers as $exist) {
			$data[$exist['email']]['exists'] = true;
			$data[$exist['email']]['status'] = intval($exist['status']);
			$data[$exist['email']]['name'] = $exist['name'];
		}

		//email exists
		echo json_encode($data);
		exit;
	}
	public function do_import()
	{
		if (!isset($_POST['data'])) {
			echo 'no data';
			exit;
		}
		$data = $_POST['data'];
		$make_unconfirmed = absint($_POST['unconfirmed2all']);
		$send_confirmation = absint($_POST['send2unconfirmed']);
		
		$response = array(
			'no_mail' => 0,
			'wrong_mails' => array(),
			'existing' => array(),
			'imported' => array()
		);


		include_once plugin_dir_path(__FILE__) . '../class-subscribers-model.php';
		$subscribers_model = Subscribers_Model::get_instance();
		include_once plugin_dir_path(__FILE__) . '../../public/class-si-sender.php';
		$sender = Si_Sender::get_instance();



		foreach ($data as $subscriber) {
			if (!isset($subscriber['email'])) {
				continue;
			}

			$email = sanitize_email($subscriber['email']);

			if (empty($email)) {
				$response['wrong'][] = $subscriber['email'];
				continue;
			}

			$name = (empty($subscriber['name'])) ? '' : sanitize_text_field($subscriber['name']);

			$status = (isset($subscriber['status']) && $subscriber['status'] != 'false' && $subscriber['status'] && !$make_unconfirmed);

			$insert = $subscribers_model->insert_subscriber($email, $name, $status);


			if (!empty(intval($insert))) {



				if (isset($subscriber['groups'])) {
					$groups = array();
//
					foreach (explode( ',', trim( $subscriber['groups'], " \n\t\r\0\x0B," ) ) as $group_name) {
						$term = term_exists($group_name, 'newsletter_groups');
						if (empty($term['term_id'])) $term = wp_insert_term($group_name, 'newsletter_groups');
						$groups[] = $term['term_id'];
					}
					$groups = array_map('intval', $groups);
				}

				if ($send_confirmation && !$status) $sender->send_confirmation_letter($insert);

				$subscriber['id'] = $insert;
				$subscriber['status'] = $status;
				$response['imported'][] = $subscriber['email'];

			} else {
				$response['exists'][] = $subscriber['email'];
				continue;
			}

		}

		echo json_encode($response);
		exit;



	}

	public function do_delete()
	{
		//ToDO: use confirmation by nonce
		//ToDo: Use Subscribers_Model class

		if (isset($_POST['subscribers']) && is_array($_POST)) {
			$subscribers = array_map('intval', $_POST['subscribers']);

			global $wpdb;

			$delete = 'DELETE FROM ' . $wpdb->prefix . 'si_subscribers WHERE id IN (' . implode(',', $subscribers) . ');';
			$wpdb->get_results($delete);
		}


		return true;
	}

	public function do_edit()
	{

		//ToDo: use confirmation by nonce


		$subscriber_id = intval($_POST['id']);
		$name = sanitize_text_field($_POST['name']);
		$email = sanitize_email($_POST['email']);
		$groups = array_map('intval', $_POST['groups']);


		$model = Subscribers_Model::get_instance();

		if (empty($subscriber_id)) {
			$subscriber_id = $model->insert_subscriber($email, $name, !empty($_POST['confirm']));

			$model->set_subscriber_group($subscriber_id, $groups);

		} else {
			$data = array(
				'name' => $name,
				'email' => $email,
			);
			$where = array(
				'id' => $subscriber_id,
			);
			if (!$_POST['confirm']) {
				$data['status'] = 0;
			} elseif (!empty($_POST['confirm'])) {
				$data['status'] = 1;
			}

			$model->update_subscriber($data, $where);

			$model->set_subscriber_group($subscriber_id, $groups);
		}


		return true;
	}

	public function load_confirm_deletetion_view()
	{


		if (isset($_GET['subscriber'])) $subscribers[] = $_GET['subscriber'];
		elseif (isset($_GET['subscribers'])) $subscribers = $_GET['subscribers'];
		else $subscribers = array();


		//ToDo: Use Subscribers_Model class
		global $wpdb;
		$select = 'SELECT * FROM ' . $wpdb->prefix . 'si_subscribers WHERE id IN (' . implode(',', $subscribers) . ');';
		$subscribers = $wpdb->get_results($select);

		include 'subscribers_views/confirm_deletion.php';

		return true;
	}

	public function load_edit_view()
	{

		$data = array(
			'id' => '',
			'name' => '',
			'email' => '',
			'groups' => '',
			'status' => '',
		);

		if (isset($_GET['subscriber'])) {
			$subscriber = absint($_GET['subscriber']);

			global $wpdb;

			$model = Subscribers_Model::get_instance();

			$subscriber = $model->get_subscribers(array(
				'where' => array(
					'field' => 'id',
					'compare' => 'IN',
					'value' => '(' . $subscriber . ')',
				)
			), ARRAY_A);

			$data = wp_parse_args($subscriber[0], $data);

			$data['groups'] = $model->get_subscriber_group($data['id']);


		} else {
			$subscriber = null;

		}
		include plugin_dir_path(__FILE__) . 'subscribers_views/edit.php';
		
		return true;
	}

	public function load_import_view()
	{

		include plugin_dir_path(__FILE__) . 'subscribers_views/import.php';
		return true;
	}

	public function load_default_view()
	{

		if (isset($_GET['orderby']) && in_array($_GET['orderby'], array('name', 'email', 'status', 'last_send'))) $orderby = $_GET['orderby'];
		else $orderby = 'id';

		if (isset($_GET['order'])) $order = $_GET['order'];
		else $order = 'asc';

		$model = Subscribers_Model::get_instance();

		$subscribers = $model->get_subscribers(array('orderby' => $orderby, 'order' => $order));


		include plugin_dir_path(__FILE__) . 'subscribers_views/default.php';

		return true;
	}

}
