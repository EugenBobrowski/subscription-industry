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
class Si_Options
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
    protected static $instance;

    private function __construct($version)
    {
        $this->version = $version;
        add_action('admin_menu', array($this, 'options_page'));

    }

    public static function get_instance($version)
    {
        if (null === self::$instance) {
            self::$instance = new self($version);
        }
        return self::$instance;

    }

    public function options_page()
    {
        $hook_suffix = add_options_page(
            'Subscribtion Industry Options',
            'Si Options',
            'manage_options',
            'si-options',
            array($this, 'options_page_callback')
        );
        global $plugin_page;

        if (strpos($hook_suffix, $plugin_page)) {
            add_action('admin_enqueue_scripts', array('AtfHtmlHelper', 'assets'));
            $this->save_options();
            include_once plugin_dir_path(__FILE__) . '../atf-fields/htmlhelper.php';
        }
    }

    public function save_options()
    {

        if (isset($_POST['si_options'])) {
            $to_save = array();
            foreach ($_POST['si_options'] as $option => $value) {
                switch ($option) {
                    case 'confirm_page':
                        $to_save['confirm_page'] = intval($value);
                        break;
                    case 'email':
                        $value = sanitize_email($value);
                        if (empty($value)) {
                            $sitename = strtolower($_SERVER['SERVER_NAME']);
                            if (substr($sitename, 0, 4) == 'www.') {
                                $sitename = substr($sitename, 4);
                            }
                            $to_save['email'] = 'no-reply@' . $sitename;
                        } else {
                            $to_save['email'] = $value;
                        }

                        break;
                    case 'confirm_request_content':

                        $to_save['confirm_request_content'] = wp_kses_post($value);
                        break;
                    case 'confirm_letter_type':
                        $to_save['confirm_letter_type'] = sanitize_text_field($value);
                        break;
                }
            }
            update_option('si_options', $to_save);
        }
    }

    public function options_page_callback()
    {
        $options = wp_parse_args(get_option('si_options'), array(
            'confirm_page' => 0,
            'email' => '',
            'confirm_request_content' => 'Dear [subscriber]' . PHP_EOL .
                PHP_EOL .
                'Your e-mail address was subscribed to our site.' . PHP_EOL .
                'To confirm please go this link [confirm]confirm[/confirm]',
            'confirm_letter_type' => 'plain',
        ));

        ?>
        <div class="wrap atf-fields">

            <h2><?php echo esc_html(get_admin_page_title()); ?>
                <span class="others-parts" style="float: right; margin-right: -15px;">
            <a href="<?php echo admin_url('edit.php?post_type=newsletters'); ?>"
               class="page-title-action">Newsletters</a>
            <a href="<?php echo admin_url('users.php?page=subscribers'); ?>"
               class="page-title-action">Subscribers</a>
        </span>
            </h2>

            <form method="post">

                <table class="form-table">
                    <tr class="form-required">
                        <th scope="row"><label for="name"><?php _e('Subscribtion Page'); ?></label></th>
                        <td><?php wp_dropdown_pages(array(
                                'name' => 'si_options[confirm_page]',
                                'show_option_none' => __('&mdash; Select &mdash;'),
                                'option_none_value' => '0',
                                'selected' => $options['confirm_page'])); ?>
                            <p class="description"><?php _e('This is service page to show notifications about email confirmation or unsubnscribtion', 'si'); ?></p>
                        </td>
                    </tr>
                    <tr class="form-required">
                        <th scope="row"><label for="email">Email <span class="description">(required)</span></label>
                        </th>
                        <td>
                            <?php AtfHtmlHelper::text(array(
                                'id' => 'email',
                                'name' => 'si_options[email]',
                                'value' => $options['email'])); ?>
                            <p class="description">Email to send messages</p>
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label>Confirmation text</label></th>
                        <td><?php AtfHtmlHelper::textarea(array(
                                'id' => 'confirm',
                                'name' => 'si_options[confirm_request_content]',
                                'value' => $options['confirm_request_content'])); ?></td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row"><label>Confirmation text</label></th>
                        <td><?php AtfHtmlHelper::select(array(
                                'id' => 'confirm',
                                'name' => 'si_options[confirm_letter_type]',
                                'value' => $options['confirm_letter_type'],
                                'options' => array('plain' => 'Text', 'html' => 'HTML'))); ?></td>
                    </tr>
                </table>

                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                         value="Submit"></p>

            </form>
        </div>
        <?php
    }

}
