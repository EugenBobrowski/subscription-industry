<?php

/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 4/15/16
 * Time: 12:50 PM
 */
class si_sender
{
    protected static $instance;

    public $letter_shortcodes;
    public $letter_shortcodes_personal;
    public $subscribers;
    public $subscriber;
    public $headers = array();
    public $code;
    public $subject;
    public $options;
    public $charset = 'UTF-8';

    private function __construct()
    {
        $this->letter_shortcodes_personal = apply_filters('si_letter_shortcodes_personal', array(
            'subscriber' => array($this, 'shortcode_subscriber'),
            'confirm' => array($this, 'shortcode_confirm')
        ));
        $this->letter_shortcodes = apply_filters('si_letter_shortcodes', array());

        $this->options = wp_parse_args(get_option('si_options'), array(
            'confirm_page' => 0,
            'email' => '',
            'confirm_request_content' => 'Dear [subscriber]' . PHP_EOL .
                PHP_EOL .
                'Your e-mail address was subscribed to our site.' . PHP_EOL .
                'To confirm please go this link [confirm]confirm[/confirm]',
            'confirm_letter_type' => 'plain',
        ));

        if (empty($this->options['email'])) {
            $sitename = strtolower($_SERVER['SERVER_NAME']);
            if (substr($sitename, 0, 4) == 'www.') {
                $sitename = substr($sitename, 4);
            }
            $this->options['email'] = 'no-reply@' . $sitename;
        }

        $this->headers['MIME-Version'] = "MIME-Version: 1.0";
        $this->headers['Content-type'] = "Content-type: text/{$this->options['confirm_letter_type']}; charset={$this->charset}";
        $this->headers['To'] = "";
        $this->headers['From'] = "From: {$this->options['email']}";
        $this->headers['Reply-To'] = "Reply-To: {$this->options['email']}";
        $this->headers['X-Mailer'] = "X-Mailer: PHP/" . phpversion();


    }

    public function send()
    {

        $this->code = $this->letter_shortcodes($this->code);


        foreach ($this->subscribers as $subscriber) {
            $this->subscriber = $subscriber;
            if (empty($subscriber['name'])) {
                $this->headers['To'] = $subscriber['email'];
            } else {
                $this->headers['To'] = 'To: ' . $subscriber['name'] . ' <' . $subscriber['email'] . '>';
            }

            $name = (empty($name)) ? 'Subscriber' : $name;
            $message = $this->letter_shortcodes_personal($this->code);

            wp_mail($subscriber['email'], $this->subject, $message, implode("\r\n", $this->headers));
        }
    }

    public function letter_shortcodes($code)
    {

        $pattern = get_shortcode_regex(array_keys($this->letter_shortcodes));

        $code = preg_replace_callback("/$pattern/", array($this, 'do_shortcode_tag_all'), $code);

        return $code;
    }

    public function letter_shortcodes_personal($code)
    {

        $pattern = get_shortcode_regex(array_keys($this->letter_shortcodes_personal));

        $code = preg_replace_callback("/$pattern/", array($this, 'do_shortcode_tag_personal'), $code);

        return $code;
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
    public function do_shortcode_tag_all($m)
    {
        $shortcode_tags = $this->letter_shortcodes;
        return $this->do_shortcode_tag($m, $shortcode_tags);
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

    public function shortcode_confirm($attr, $content)
    {
        $confirm_link = htmlspecialchars(add_query_arg(array(
            'pass' => $this->subscriber['pass'],
        ), get_permalink($this->options['confirm_page'])));

        if ('html' == $this->options['confirm_letter_type'] && null == $content) return '<a http="' . $confirm_link . '" title="confirm">confirm</a>';
        elseif ('html' == $this->options['confirm_letter_type']) return '<a http="' . $confirm_link . '" title="confirm">' . $content . '</a>';
        else return $confirm_link;

    }

    public function get_simple_html($title, $body)
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