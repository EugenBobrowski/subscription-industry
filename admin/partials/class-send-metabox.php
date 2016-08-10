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
        wp_enqueue_style('subscribtion-industry', plugin_dir_url(__FILE__) . '../css/subscribtion-industry-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($prefix)
    {
        global $post;
        if ('newsletter' != $post->post_type) return;
        include_once plugin_dir_path(__FILE__) . '../atf-fields/htmlhelper.php';

        AtfHtmlHelper::assets($prefix . '__newsletter-send-metabox');
        wp_enqueue_script('subscribtion-industry', plugin_dir_url(__FILE__) . '../js/subscribtion-industry-admin.js', array('jquery', 'wp-color-picker', 'jquery-ui-sortable'), $this->version, false);
    }

    public function newsletter_metabox()
    {
        add_meta_box('sender_metabox', __('Sending', 'subsribtion-industry'), array($this, 'newsletter_metabox_callback'), 'newsletters', 'side', 'high');
        remove_meta_box( 'tagsdiv-newsletter_groups', 'newsletters', 'side' );
    }

    public function newsletter_metabox_callback($post)
    {


        include_once plugin_dir_path(__FILE__) . '../class-subscribers-model.php';

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


        $groups = wp_get_post_terms($post->ID, 'newsletter_groups',  array("fields" => "ids"));
        $single_receivers = get_post_meta($post->ID, 'single_receivers', true);
        
        wp_nonce_field(plugin_basename(__FILE__), 'newsletter_send_nonce');
        wp_schedule_single_event(time() + 10, 'send_newsletter', array('some_element', 'seconf'));
        var_dump(wp_get_schedule( 'send_newsletters', array('some_element', 'seconf')));
        file_put_contents(ABSPATH . '/cron.txt', 'kjslkj');
        ?>

        <table class="form-table atf-fields">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="favicon">Send to groups:</label>
                </th>
                <td>
                    <?php AtfHtmlHelper::multiselect(array(
                        'id' => 'groups',
                        'name' => 'groups',
                        'value' => $groups,
                        'class' => 'check-buttons',
                        'vertical' => false,
                        'options' => AtfHtmlHelper::get_taxonomy_options('newsletter_groups'),
                    )); ?>

                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="favicon">Send to subscribers too:</label>
                </th>
                <td>
                    <?php AtfHtmlHelper::multiselect(array(
                        'id' => 'receivers',
                        'name' => 'receivers',
                        'value' => $single_receivers,
                        'class' => 'check-buttons',
                        'vertical' => false,
                        'options' => $subscribers_opts,
                    )); ?>

                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="favicon">Start:</label>
                </th>
                <td>
                    <?php AtfHtmlHelper::datepicker(array(
                        'id' => 'receivers',
                        'name' => 'receivers',
                        'value' => '',
                    )); ?>

                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="favicon">Period:</label>
                </th>
                <td>
                    <?php AtfHtmlHelper::text(array(
                        'id' => 'receivers',
                        'name' => 'receivers',
                        'value' => '',
                    )); ?>

                </td>
            </tr>
            </tbody>
        </table>


        <div class="si-sender-metabox-actions atf-fields">
            <?php add_thickbox(); ?>
            <div id="my-content-id" style="display:none;">
                <p>
                    This is my hidden content! It will appear in ThickBox when the link is clicked.
                </p>
            </div>


            <div class="statistic-info">
                Send now
            </div>
            <div class="send-action">
                <a href="#TB_inline?width=600&height=550&inlineId=my-content-id" class="thickbox button button-primary" >View schedules</a>
            </div>
            <div class="clear"></div>
        </div>

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

        $groups = array();

        if (isset($_POST['groups'])) $groups = array_map('absint', $_POST['groups']);

        wp_set_object_terms($post_id, $groups, 'newsletter_groups');



        $model = Subscribers_Model::get_instance();

        $receivers = $model->get_groups_subscribers($groups);


        $receivers = array_map('array_pop', $receivers);



        if (isset($_POST['receivers']) && is_array($_POST['receivers'])) {
            $single_receivers = array_map('absint', $_POST['receivers']);

            update_post_meta($post_id, 'single_receivers', $single_receivers);


            $receivers = array_merge($_POST['receivers'], $receivers);
        }

        foreach ($receivers as $key => $receiver) {
            $receiver = absint($receiver);
            if (!empty($receiver)) $receivers[$key] = $receiver;
        }

        if ($_POST['send_now']) {

            include_once plugin_dir_path(__FILE__) . '../../public/class-si-sender.php';

            $sender = Si_Sender::get_instance();

            $sender->subscribers = array_unique($receivers);

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