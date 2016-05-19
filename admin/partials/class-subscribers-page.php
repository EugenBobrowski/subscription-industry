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
                    return 'load_import_view';
                }
                break;
            default:
                return 'load_default_view';
        }
    }
    public function assets($prefix) {
        AtfHtmlHelper::assets($prefix, null);
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
            $subscriber_id = $model->insert_subscriber($email, $name, empty($_POST['confirm']));

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
            $subscriber = intval($_GET['subscriber']);

            global $wpdb;

            $select = 'SELECT * FROM ' . $wpdb->prefix . 'si_subscribers WHERE id IN (' . $subscriber . ');';
            $subscriber = $wpdb->get_results($select, ARRAY_A);

            $data = wp_parse_args($subscriber[0], $data);

        } else {
            $subscriber = null;
        }

        ?>
        <div class="wrap atf-fields">


        <h2><?php
            if ($subscriber !== null) echo __('Edit subscriber', 'si') . ' #' . $data['id'];
            else _e('New subscriber', 'si');
            ?></h2>

        <form method="post">
            <input type="hidden" name="action" value="doedit"/>
            <input type="hidden" name="id" value="<?php echo $data['id']; ?>"/>
            <table class="form-table">
                <tr class="form-required">
                    <th scope="row"><label for="name"><?php _e('Name'); ?></label></th>
                    <td><?php AtfHtmlHelper::text(array('id' => 'name', 'name' => 'name', 'value' => $data['name'])); ?></td>
                </tr>
                <tr class="form-required">
                    <th scope="row"><label for="email">Email <span class="description">(required)</span></label></th>
                    <td><?php AtfHtmlHelper::text(array('id' => 'email', 'name' => 'email', 'value' => $data['email'])); ?></td>
                </tr>
                <tr class="form-required">
                    <th scope="row"><label for="groups">Groups</label></th>
                    <td>
                    <?php
                    $model = Subscribers_Model::get_instance();
                    AtfHtmlHelper::multiselect(array('id' => 'groups', 'name' => 'groups',
                    'value' => $model->get_subscriber_group($data['id']),
                    'options' => AtfHtmlHelper::get_taxonomy_options(array('taxonomy' => 'newsletter_groups'))
                    )); ?>
                    </td>
                </tr>
                <tr class="form-field form-required">
                    <th scope="row"><label>Confirm</label></th>
                    <td><?php AtfHtmlHelper::tumbler(array('id' => 'confirm', 'name' => 'confirm', 'value' => $data['status'])); ?></td>
                </tr>
            </table>

            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                     value="Submit"></p>
        </form>

        <?php
        return true;
    }
    public function load_import_view()
    {

        ?>
        <div class="wrap">


        <h2><?php _e('Import Subscribers', 'si');
            ?></h2>

        <form method="post">
            <input type="hidden" name="action" value="doedit"/>
            <table class="form-table atf-fields">
                <tr class="form-required">
                    <th scope="row"><label for="name"><?php _e('Import text'); ?></label></th>
                    <td><input type="file" name="subscribers_base" multiple accept="text/*"></td>
                </tr>
                <tr class="form-required">
                    <th scope="row"><label for="name"><?php _e('Import text'); ?></label></th>
                    <td><?php AtfHtmlHelper::textarea(array(
                    'id' => 'import_text',
                    'name' => 'import_text',
                    'value' => '',
                    'desc' => __('', 'si'),
                    )); ?></td>
                </tr>
                <tr class="form-field form-required">
                    <th scope="row"><label>Confirm</label></th>
                    <td><?php AtfHtmlHelper::tumbler(array('id' => 'confirm', 'name' => 'confirm', 'value' => 1)); ?></td>
                </tr>
            </table>


            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                     value="Submit"></p>
        </form>
</div>

        <?php
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
