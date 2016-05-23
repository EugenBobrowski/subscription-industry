<?php
/**
 * Widget API: SI_Subscribe_Widget class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Core class used to implement a Text widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class Si_Subscribe_Widget extends WP_Widget {


    public $used_tags = array();
    public $pattern;
    public $si_widget_shortcodes;
    /**
     * Sets up a new Text widget instance.
     *
     * @since 2.8.0
     * @access public
     */
    public function __construct() {
        $widget_ops = array('classname' => 'widget-si-form', 'description' => __('Customizible Form'));
        $control_ops = array('width' => 400, 'height' => 350);
        parent::__construct('si_subscribe_form', __('Subscribe Form'), $widget_ops, $control_ops);

        add_filter ('si_form_code', array($this, 'form_shortcodes'));

        add_action('wp_ajax_si_subscribe', array($this, 'ajax_callback'));
        add_action('wp_ajax_nopriv_si_subscribe', array($this, 'ajax_callback'));
        add_action('wp_print_footer_scripts', array($this, 'javascript'), 99);
        add_action('wp_print_scripts', array($this, 'localize'));

        $this->si_widget_shortcodes = apply_filters('si_form_shortcodes',  array(
            'form' => array($this, 'shortcode_form'),
            'email' => array($this, 'shortcode_email'),
            'name' => array($this, 'shortcode_name'),
            'group' => array($this, 'shortcode_group'),
            'submit'  => array($this, 'shortcode_submit'),
            'button' => array($this, 'shortcode_button'),
        ) );

        $this->pattern = get_shortcode_regex( array_keys( $this->si_widget_shortcodes ) );
    }
    /**
     * Outputs the content for the current Text widget instance.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     * @param array $instance Settings for the current Text widget instance.
     */
    public function widget( $args, $instance ) {

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        echo $args['before_widget'];

        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        echo (empty($instance['html'])) ? $this->html($instance) : $instance['html'];

        echo $args['after_widget'];

    }
    public function html ($instance) {
        $code = ! empty( $instance['code'] ) ? $instance['code'] : '';

        /**
         * Filter the content of the Text widget.
         *
         * @since 2.3.0
         * @since 4.4.0 Added the `$this` parameter.
         *
         * @param string         $widget_code The widget content.
         * @param array          $instance    Array of settings for the current widget.
         * @param WP_Widget_Text $this        Current Text widget instance.
         */
//        $code = apply_filters( 'widget_text', $code, $instance, $this );
        $code = apply_filters( 'si_form_code', $code, $instance, $this );

        $code = !empty( $instance['filter'] ) ? wpautop( $code ) : $code;

        if (in_array('form', $this->used_tags)) $code = '<div class="si-widget-content">' . $code . '</div>';
        else $code = '<form class="si-widget-content si-widget-form">' . $code . '</form>';

        return $code;
    }


    public function form_shortcodes ($code) {

        $output = preg_replace_callback( "/$this->pattern/", array($this, 'do_shortcode_tag'), $code );

        if ($output != $code) $output = $this->form_shortcodes($output);

        return $output;
    }
    /**
     * Clone of do_shortcode_tag()
     * Regular Expression callable for form_shortcode() for calling shortcode hook.
     * @see get_shortcode_regex for details of the match array contents.
     *
     * @since 2.5.0
     * @access private
     *
     * @global array $shortcode_tags
     *
     * @param array $m Regular expression match array
     * @return string|false False on failure.
     */
    public function do_shortcode_tag ($m) {
        $shortcode_tags = $this->si_widget_shortcodes;
        // allow [[foo]] syntax for escaping a tag
        if ( $m[1] == '[' && $m[6] == ']' ) {
            return substr($m[0], 1, -1);
        }

        $tag = $m[2];
        $attr = shortcode_parse_atts( $m[3] );

        if ( ! is_callable( $shortcode_tags[ $tag ] ) ) {
            /* translators: %s: shortcode tag */
            $message = sprintf( __( 'Attempting to parse a shortcode without a valid callback: %s' ), $tag );
            _doing_it_wrong( __FUNCTION__, $message, '4.3.0' );
            return $m[0];
        }

        if ( isset( $m[5] ) ) {
            // enclosing tag - extra parameter
            return $m[1] . call_user_func( $shortcode_tags[$tag], $attr, $m[5], $tag ) . $m[6];
        } else {
            // self-closing tag
            return $m[1] . call_user_func( $shortcode_tags[$tag], $attr, null,  $tag ) . $m[6];
        }

    }
    public function shortcode_form ($attr, $content) {
        $this->used_tags[] = 'form';

        $attributes = '';
        $attr = wp_parse_args($attr, array(
            'class' => '',
        ));
        $attr['class'] .= ' si-widget-form';
        foreach ($attr as $attribute=>$value) {
            $attributes .= $attribute . '="' . $value . '" ';
        }

        return '<form ' . $attributes . '>'. $content . '</form>';
    }
    public function shortcode_email ($attr) {
        $attributes = '';
        $attr = wp_parse_args($attr, array(

        ));
        foreach ($attr as $attribute=>$value) {
            $attributes .= $attribute . '="' . $value . '" ';
        }

        return '<input name="email" type="text"' . $attributes . '>';
    }
    public function shortcode_name ($attr) {
        $attributes = '';
        $attr = wp_parse_args($attr, array(

        ));
        foreach ($attr as $attribute=>$value) {
            $attributes .= $attribute . '="' . $value . '" ';
        }

        return '<input name="name" type="text"' . $attributes . '>';
    }
    public function shortcode_group ($attr) {
        $attributes = '';
        $attr = wp_parse_args($attr, array(
            'type' => 'hidden'

        ));
        $c_attrs = $attr;
        $groups = array();
        unset($c_attrs['type']);
        foreach ($c_attrs as $attribute=>$value) {
            if (is_string($attribute)) $attributes .= $attribute . '="' . $value . '" ';
            else if ($term = get_term_by('slug', $value, 'newsletter_groups')) $groups[] = $term->term_id;
        }

        $html = '';

        switch ($attr['type']) {
            case 'select':
                break;
            case 'multiselect':
                break;
            case 'checkbox':
                break;
            default:
                foreach ($groups as $group) {
                    $html .= '<input name="groups[]" type="hidden"' . $attributes . ' value="' . $group . '">';
                }
                break;
        }

        return $html;
    }
    public function shortcode_submit ($attr) {
        $attributes = '';
        $attr = wp_parse_args($attr, array(

        ));
        foreach ($attr as $attribute=>$value) {
            $attributes .= $attribute . '="' . $value . '" ';
        }

        return '<input type="submit" ' . $attributes . '>';
    }

    public function shortcode_button ($attr, $content) {
        $attributes = '';
        foreach ($attr as $attribute=>$value) {
            $attributes .= $attribute . '="' . $value . '" ';
        }

        return '<button ' . $attributes . '>'. $content . '</button>';
    }


    public function localize () {
        wp_localize_script('jquery', 'siFormAjax',
            apply_filters( 'si_form_localize', array(
                'url' => admin_url('admin-ajax.php'),
                'siSubscribeNonce' => wp_create_nonce('si-subscribe-nonce'),
                'messages' => array(
                    'emptyMail' => __('Email is empty'),
                    'success' => __('Subscribed successfully. You have successfully subscribed to the newsletter. You will receive a confirmation email in few minutes. Please follow the link in it to confirm your subscription. If the email takes more than 15 minutes to appear in your mailbox, please check your spam folder.'),
                    'exists' => "Email already exist.",
                    'unexpectedError' => "Oops.. Unexpected error occurred.",
                    'invalidEmail' => 'Invalid email address.'
                ),
            ))
        );
    }

    public function javascript() {
        ?>
        <script id="si_widget" type="text/javascript">
            (function ($) {
                var siSubscribeForms;
                $(document).ready(function ($) {
                    siSubscribeForms = $('.si-widget-form');
                    console.log(siFormAjax.messages);

                    var do_ajax = function (data) {
                        if (typeof data == 'undefined') {
                            data = {}
                        }
                        var pattern_email = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;

                        if (!$.trim(data.email).length || !pattern_email.test(data.email)) {
                            siAlertFunction('Email is not valid');
                            return;
                        } else {

                        }


                        $.post(siFormAjax.url, data, function (response) {
                            console.log(response);
                            response = JSON.parse(response);
                            if (response.message_id != undefined && siFormAjax.messages[response.message_id] != undefined) {
                                console.log('message to alert:', siFormAjax.messages[response.message_id]);
                                siSubscribeForms.trigger('si.subscribe.alert', response);
                            } else {
                                console.log((siFormAjax.messages[response.message_id] != undefined), 'Something wrong:', siFormAjax.messages, response);
                            }
                        });
                    };
                    siSubscribeForms.on('si.subscribe.alert', function (e, responseObj) {
                        if (e.isDefaultPrevented()) return;
                        alert(siFormAjax.messages[responseObj.message_id]);
                    });

                    siSubscribeForms.submit(function(e){
                        e.preventDefault();
                        do_ajax($(this).serializeForm({
                            action: 'si_subscribe',
                            nonce: siFormAjax.siSubscribeNonce
                        }));
                    })
                });



                $.fn.serializeForm = function (data) {
                    if (data == undefined) data = {};
                    var arr = $(this).serializeArray();
                    arr.forEach(function (el, i, arr) {
                        el.name = el.name.replace('[]', '');
                        if (el.value == '') return;
                        if (!(el.name in data)) {
                            data[el.name] = el.value;
                        } else {
                            data[el.name] += ',' + el.value;
                        }
                    });
                    return data;
                }

            })(jQuery);
        </script>
        <?php
    }

    /**
     * Echo the json object.
     *
     */
    public function ajax_callback () {
        $nonce = $_POST['nonce'];


        if (!wp_verify_nonce($nonce, 'si-subscribe-nonce'))
            die ('Stop!');

        if (!isset($_POST['email']))
            wp_die( json_encode( array(
                'message_id' => 'emptyMail', 'add_message'=>'Empty E-mail')));

        $email = sanitize_email($_POST['email']);

        if (empty($email)) wp_die( json_encode( array(
            'message_id' => 'error', 'message'=>'Wrong E-mail')));

        $name = (empty($_POST['name'])) ? '' : sanitize_text_field($_POST['name']);

        include_once plugin_dir_path(__FILE__) . '../admin/class-subscribers-model.php';

        $subscribers_model = Subscribers_Model::get_instance();

        $insert = $subscribers_model->insert_subscriber($email, $name);

        if (!empty(intval($insert))) {

            if (isset($_POST['groups'])) {
                $groups = array();

                foreach (array_map('absint', explode( ',', trim( $_POST['groups'], " \n\t\r\0\x0B," ) )) as $group_id) {
                    if (term_exists($group_id, 'newsletter_groups')) $groups[] = $group_id;
                }

                $subscribers_model->set_subscriber_group($insert, $groups, true);
            }

            include_once plugin_dir_path(__FILE__) . '../public/class-si-sender.php';
            $sender = Si_Sender::get_instance();
            $sender->send_confirmation_letter($insert);
            $message_id = 'success';
        } else {
            $message_id = $insert;
        }

        echo json_encode(
            array(
                'message_id' => $message_id,
                )
        );
        exit();

    }

    /**
     * Handles updating settings for the current Text widget instance.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $new_instance New settings for this instance as input by the user via
     *                            WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Settings to save or bool false to cancel saving.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        if ( current_user_can('unfiltered_html') )
            $instance['code'] =  $new_instance['code'];
        else
            $instance['code'] = wp_kses_post( stripslashes( $new_instance['code'] ) );
        $instance['filter'] = ! empty( $new_instance['filter'] );

        return $instance;
    }

    /**
     * Outputs the Text widget settings form.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $instance Current settings.
     */
    public function form( $instance ) {
        $default_code = '';
        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'code' => $default_code ) );
        $filter = isset( $instance['filter'] ) ? $instance['filter'] : 0;
        $title = sanitize_text_field( $instance['title'] );
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

        <p><label for="<?php echo $this->get_field_id( 'code' ); ?>"><?php _e( 'Code:' ); ?></label>
            <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('code'); ?>" name="<?php echo $this->get_field_name('code'); ?>"><?php echo esc_textarea( $instance['code'] ); ?></textarea></p>
        <p>
            Avaliable shortcodes:
            <?php echo '<code>[', implode(']</code>, <code>[', array_keys($this->si_widget_shortcodes)) . ']</code>'; ?>
        </p>
        <p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox"<?php checked( $filter ); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>
        <?php
    }
}