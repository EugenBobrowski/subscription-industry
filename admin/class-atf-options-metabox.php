<?php

class Newsletters_Metabox
{
    private $version;
    protected static $instance;

    private function __construct($version)
    {
        $this->version = $version;

        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('add_meta_boxes', array($this, 'newsletter_metabox'));
        add_action('save_post', array($this, 'newsletter_save'));

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
        wp_localize_script('subscribtion-industry', 'redux_upload', array('url' => get_template_directory_uri().'/atf/options/admin/assets/blank.png'));
    }

    public function newsletter_metabox()
    {
        add_meta_box('newsletter_metabox', __('Newsletter Settings', 'subsribtion-industry'), array($this, 'newsletter_metabox_callback'), 'newsletters');
    }
    public function newsletter_metabox_callback($post)
    {
        include_once 'htmlhelper.php';

        $templates = apply_filters('si_templates', array());

        $templates_opts = array();
        foreach ($templates as $id => $tmpl) {
            $templates_opts[$id] = '<img src="' . $tmpl['preview'] . '" width="150" height="300"/><br />' . $tmpl['name'];
        }

        wp_nonce_field( plugin_basename(__FILE__), 'newsletter_nonce' );

        $current_template = get_post_meta($post->ID, 'newsletter_template', true);

        $current_template = (empty($current_template) || !array_key_exists($current_template, $templates)) ? 'default' : $current_template;

        $data = get_post_meta($post->ID, 'newsletter_data', true);



        ?>

        <table class="form-table atf-fields">
            <tbody>
            <?php
            foreach ($templates[$current_template]['fields'] as $key=>$field) {
                $field['id'] = $key;
                $field['name'] = $key;
                $field['value'] = (isset ($data[$key])) ? $data[$key] : '';

                ?>
                <tr>
                    <th scope="row">
                        <label for="<?php echo $field; ?>"><?php echo $field['title']?></label>
                    </th>
                    <td>
                        <?php AtfHtmlHelper::$field['type']($field); ?>
                    </td>
                </tr>
                <?php
            }
            ?>

            <tr>
                <th scope="row">
                    <label for="favicon">Avaliable templates</label>
                </th>
                <td>
                    <?php AtfHtmlHelper::radioButtons(array(
                        'id' => 'template',
                        'name' => 'template',
                        'value' => $current_template,
                        'class' => 'radio-image',
                        'options' => $templates_opts
                    )); ?>
                </td>
            </tr>
            </tbody>
        </table>

        <?php


    }
    public function newsletter_save($post_id) {


        if ( ! isset( $_POST['newsletter_nonce'] ) )
            return $post_id;



        if ( ! wp_verify_nonce( $_POST['newsletter_nonce'], plugin_basename(__FILE__) ) )
            return $post_id;

        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
            return $post_id;
        if ( 'newsletters' == $_POST['post_type'] && ! current_user_can( 'edit_page', $post_id ) ) {
            return $post_id;
        } elseif( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }
        /**
         * _newsletter
         *
         *
         *
         */
        $template = $_POST['template'];

        $templates = apply_filters('si_templates', array());

        $data2save = array();

        foreach ($templates[$template]['fields'] as $key=>$field) {
            $data2save[$key] = $_POST[$key];
        }

        update_post_meta($post_id, 'newsletter_data', $data2save);
        update_post_meta($post_id, 'newsletter_template', $template);

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