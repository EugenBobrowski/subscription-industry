<?php

/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 4/15/16
 * Time: 12:50 PM
 */
class Si_Templater
{
    protected static $instance;

    public $templates;
    public $letter_shortcodes;
    public $letter_shortcodes_personal;
    public $subscriber;
    public $options;
    public $charset = 'UTF-8';
    public $letter_type = 'plain';

    private function __construct()
    {
        $this->options = wp_parse_args(get_option('si_options'), array(
            'confirm_page' => 0,
            'email' => '',
            'confirm_request_content' => 'Dear [subscriber]' . PHP_EOL .
                PHP_EOL .
                'Your e-mail address was subscribed to our site.' . PHP_EOL .
                'To confirm please go this link [confirm]confirm[/confirm]',
            'confirm_letter_type' => 'plain',
        ));
        
        $this->templates = apply_filters('si_templates', array());

        $this->letter_shortcodes_personal = apply_filters('si_letter_shortcodes_personal', array(
            'subscriber' => array($this, 'shortcode_subscriber'),
            'confirm' => array($this, 'shortcode_confirm'),
            'unsubscribe' => array($this, 'shortcode_unsubscribe'),
        ));
        $this->letter_shortcodes = apply_filters('si_letter_shortcodes', array());

    }

    public function get_newsletter_web($post_id, $template = null)
    {

        $data = get_post_meta($post_id, 'newsletter_data', true);

        if ($template == null) $template = $this->get_template($post_id);

        $newsletter = $template['body'];

        foreach ($template['fields'] as $field_name => $settings) {

            $data[$field_name] = apply_filters("si_template_{$template['id']}_field_{$field_name}_stringify",
                is_string($data[$field_name]) ? $data[$field_name] : '',
                $data[$field_name]);
            $data[$field_name] = apply_filters("si_template_{$template['id']}_field_{$field_name}_stringify_web",
                is_string($data[$field_name]) ? $data[$field_name] : '',
                $data[$field_name]);
            
            if (is_string($data[$field_name])) {
                $newsletter = str_replace("{{$field_name}}", $data[$field_name], $newsletter);
            }

        }

        $newsletter = apply_filters("si_template_all", $newsletter, $template, $data);
        $newsletter = apply_filters("si_template_web_all", $newsletter, $template, $data);
        $newsletter = apply_filters("si_template_{$template['id']}", $newsletter, $template, $data);
        $newsletter = apply_filters("si_template_web_{$template['id']}", $newsletter, $template, $data);


        return $newsletter;
    }

    public function get_newsletter($post_id, $template = null)
    {

        $data = get_post_meta($post_id, 'newsletter_data', true);

        if ($template == null) $template = $this->get_template($post_id);

        $this->letter_type = $template['type'];

        $newsletter = $template['body'];

        foreach ($template['fields'] as $field_name => $settings) {

            $data[$field_name] = apply_filters("si_template_{$template['id']}_field_{$field_name}_stringify",
                is_string($data[$field_name]) ? $data[$field_name] : '',
                $data[$field_name]);

            if (is_string($data[$field_name])) {
                $newsletter = str_replace("{{$field_name}}", $data[$field_name], $newsletter);
            }

        }

        $newsletter = apply_filters("si_template_all", $newsletter, $template, $data);
        $newsletter = apply_filters("si_template_{$template['id']}", $newsletter, $template, $data);

        return $newsletter;
    }

    public function get_template($post_id)
    {

        $template_name = get_post_meta($post_id, 'newsletter_template', true);
        return array_merge($this->templates[$template_name], array('id' => $template_name));
    }


    public function letter_shortcodes($code)
    {
        $pattern = get_shortcode_regex(array_keys($this->letter_shortcodes));
        $code = preg_replace_callback("/$pattern/", array($this, 'do_shortcode_tag_all'), $code);
        return $code;
    }

    public function do_shortcode_tag_all($m)
    {
        $shortcode_tags = $this->letter_shortcodes;
        return $this->do_shortcode_tag($m, $shortcode_tags);
    }

    public function letter_shortcodes_personal($code)
    {
        $pattern = get_shortcode_regex(array_keys($this->letter_shortcodes_personal));
        $code = preg_replace_callback("/$pattern/", array($this, 'do_shortcode_tag_personal'), $code);
        return $code;
    }

    public function do_shortcode_tag_personal($m)
    {
        $shortcode_tags = $this->letter_shortcodes_personal;
        return $this->do_shortcode_tag($m, $shortcode_tags);
    }

    public function do_shortcode_tag($m, $shortcode_tags)
    {
        // allow [[foo]] syntax for escaping a tag
        if ($m[1] == '[' && $m[6] == ']') {
            return substr($m[0], 1, -1);
        }

        $tag = $m[2];
        $attr = shortcode_parse_atts($m[3]);

        if (!is_callable($shortcode_tags[$tag])) {
            /* translators: %s: shortcode tag */
            $message = sprintf(__('Attempting to parse a shortcode without a valid callback: %s'), $tag);
            _doing_it_wrong(__FUNCTION__, $message, '4.3.0');

            return $m[0];
        }

        if (isset($m[5])) {
            // enclosing tag - extra parameter
            return $m[1] . call_user_func($shortcode_tags[$tag], $attr, $m[5], $tag) . $m[6];
        } else {
            // self-closing tag
            return $m[1] . call_user_func($shortcode_tags[$tag], $attr, null, $tag) . $m[6];
        }

    }

    public function shortcode_subscriber($attr)
    {
        if (empty($this->subscriber['name'])) return 'Subscriber';
        return $this->subscriber['name'];
    }

    public function shortcode_confirm($attr = array(), $content)
    {
        $confirm_link = add_query_arg(array(
            'hash' => hash('md5', $this->subscriber['activation_key']),
            'action' => 'confirm',
            'email' => $this->subscriber['email'],
        ), get_permalink($this->options['confirm_page']));

        if ('html' == $this->letter_type && null == $content) return '<a href="' . $confirm_link . '" title="confirm">confirm</a>';
        elseif ('html' == $this->letter_type) return '<a href="' . $confirm_link . '" title="confirm">' . $content . '</a>';
        else return $confirm_link;
    }

    public function shortcode_unsubscribe($attr = array(), $content)
    {
        $subscribe_link = add_query_arg(array(
            'hash' => hash('md5', $this->subscriber['activation_key']),
            'action' => 'unsubscribe',
            'email' => $this->subscriber['email'],
        ), get_permalink($this->options['confirm_page']));

        if ('html' == $this->letter_type && null == $content) return '<a href="' . $subscribe_link . '" title="confirm">' . __('Unsubscribe', 'subscribtion-industry') . '</a>';
        elseif ('html' == $this->letter_type) return '<a href="' . $subscribe_link . '" title="confirm">' . $content . '</a>';
        else return $subscribe_link;
    }

    public static function get_simple_html($title, $body)
    {
        $message = "<html>
<head>
  <title>{$title}</title>
</head>
<body>
  {$body}
</body>
</html>";
        return $message;
    }

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}