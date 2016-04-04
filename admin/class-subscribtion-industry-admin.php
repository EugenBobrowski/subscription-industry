<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
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
    public function __construct($subscribtion_industry, $version)
    {

        $this->subscribtion_industry = $subscribtion_industry;
        $this->version = $version;

        add_action('init', array($this, 'newsletters'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('add_meta_boxes', array($this, 'newsletter_metabox'));
        add_filter('si_templates', array($this, 'default_templates'));
        add_action('save_post', array($this, 'newsletter_save'));
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Plugin_Name_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Plugin_Name_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->subscribtion_industry, plugin_dir_url(__FILE__) . 'css/subscribtion-industry-admin.css', array(), $this->version, 'all');
        wp_enqueue_style('atf-options-si', plugin_dir_url(__FILE__) . 'css/options.css', array(), '1.1', 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Plugin_Name_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Plugin_Name_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->subscribtion_industry, plugin_dir_url(__FILE__) . 'js/subscribtion-industry-admin.js', array('jquery'), $this->version, false);

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

    public function newsletter_metabox()
    {
        add_meta_box('newsletter_metabox', __('Newsletter Settings', 'subsribtion-industry'), array($this, 'newsletter_metabox_callback'), 'newsletters');
    }
    public function default_templates ($templates) {
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
    public function newsletter_metabox_callback($post)
    {
        include_once 'htmlhelper.php';

        $templates = apply_filters('si_templates', array());
        $templates_opts = array();
        foreach ($templates as $id => $tmpl) {
            $templates_opts[$id] = '<img src="' . $tmpl['preview'] . '" width="150" height="300"/><br />' . $tmpl['name'];
        }

        wp_nonce_field( plugin_basename(__FILE__), 'myplugin_noncename' );

        $current_template = get_post_meta($post->ID, 'newsletter_template', true);

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
        if ( ! wp_verify_nonce( $_POST['myplugin_noncename'], plugin_basename(__FILE__) ) )
            return $post_id;
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
            return $post_id;
        if ( 'page' == $_POST['post_type'] && ! current_user_can( 'edit_page', $post_id ) ) {
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
}
