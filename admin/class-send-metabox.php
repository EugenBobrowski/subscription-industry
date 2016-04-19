<?php

class Sender_Metabox
{
    private $version;
    protected static $instance;

    private function __construct($version)
    {
        $this->version = $version;

        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('add_meta_boxes', array($this, 'newsletter_metabox'));
        add_action('save_post', array($this, 'newsletter_send'));

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        wp_enqueue_style('subscribtion-industry', plugin_dir_url(__FILE__) . 'css/subscribtion-industry-admin.css', array(), $this->version, 'all');
        wp_enqueue_style('atf-options-si', plugin_dir_url(__FILE__) . 'css/options.css', array(), '1.1', 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script('atf-options-js', plugin_dir_url(__FILE__) . 'js/atf-options.js', array('jquery', 'wp-color-picker', 'jquery-ui-sortable'), $this->version, false);
        wp_enqueue_script('subscribtion-industry', plugin_dir_url(__FILE__) . 'js/subscribtion-industry-admin.js', array('jquery', 'wp-color-picker', 'jquery-ui-sortable'), $this->version, false);
        wp_localize_script('subscribtion-industry', 'redux_upload', array('url' => get_template_directory_uri() . '/atf/options/admin/assets/blank.png'));
    }

    public function newsletter_metabox()
    {
        add_meta_box('sender_metabox', __('Send', 'subsribtion-industry'), array($this, 'newsletter_metabox_callback'), 'newsletters');
    }

    public function newsletter_metabox_callback($post)
    {
        include_once 'htmlhelper.php';

        include_once 'class-subscribers-model.php';

        $subscribers_model = Subscribers_Model::get_instance();

        $subscribers = $subscribers_model->get_subscribers(array(
            'where' => array(
                'field' => 'status',
                'value' => 1,
                'compare' => '='
            ),
        ));

        $subscribers_opts = array();
        foreach ($subscribers as $subscriber) {
            $subscribers_opts[$subscriber->id] = $subscriber->name . ' [' . $subscriber->email . ']';
        }

        wp_nonce_field(plugin_basename(__FILE__), 'newsletter_send_nonce');

        ?>

        <table class="form-table atf-fields">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="favicon">Avaliable subscribers</label>
                </th>
                <td>
                    <?php AtfHtmlHelper::checkbox(array(
                        'id' => 'receivers',
                        'name' => 'receivers',
                        'value' => '',
                        'class' => 'radio-image',
                        'options' => $subscribers_opts
                    )); ?>
                </td>
            </tr>
            </tbody>
        </table>

        <?php


    }

    public function newsletter_send($post_id)
    {


        if (!isset($_POST['newsletter_send_nonce']))
            return $post_id;

        if (!wp_verify_nonce($_POST['newsletter_send_nonce'], plugin_basename(__FILE__)))
            return $post_id;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;

        if (isset($_POST['receivers']) && is_array($_POST['receivers'])) {
            $receivers = array();
            foreach ($_POST['receivers'] as $key => $receiver) {
                $receiver = intval($receiver);
                if (!empty($receiver)) $receivers[] = $receiver;
            }

            include_once plugin_dir_path(__FILE__) . '../public/si_sender.php';

            $sender = si_sender::get_instance();

            $sender->subscribers = $receivers;

            $sender->send_newsletter($post_id);
            
        }


        return true;
    }

    public static function get_instance($version)
    {
        if (null === self::$instance) {
            self::$instance = new self($version);
        }
        return self::$instance;
    }
}