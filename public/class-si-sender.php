<?php

/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 4/15/16
 * Time: 12:50 PM
 */
class Si_Sender
{
    protected static $instance;

    public $subscribers;
    public $subscriber;
    public $headers = array();
    public $code;
    public $subject;
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
        if (empty($this->options['confirm_page'])) {
            get_pages();
        }
        if (empty($this->options['email'])) {
            $sitename = strtolower($_SERVER['SERVER_NAME']);
            if (substr($sitename, 0, 4) == 'www.') {
                $sitename = substr($sitename, 4);
            }
            $this->options['email'] = 'no-reply@' . $sitename;
        }

        include_once plugin_dir_path(__FILE__) . 'class-templater.php';

    }
    public function get_headers () {
        $this->headers['MIME-Version'] = "MIME-Version: 1.0";
        $this->headers['Content-type'] = "Content-type: text/{$this->letter_type}; charset={$this->charset}";
        $this->headers['To'] = "";
        $this->headers['From'] = "From: {$this->options['email']}";
        $this->headers['Reply-To'] = "Reply-To: {$this->options['email']}";
        $this->headers['X-Mailer'] = "X-Mailer: PHP/" . phpversion();
    }
    public function send_confirmation_letter($subscriber_id)
    {
        
        $this->subject = 'Confirm you letter';
        $this->letter_type = $this->options['confirm_letter_type'];
        
        $this->get_headers();

        if ('html' == $this->letter_type) {
            
            $this->code = Si_Templater::get_simple_html($this->subject, apply_filters('si_confirmation_letter_html', wpautop($this->options['confirm_request_content'])));
        } else {
            $this->code = $this->options['confirm_request_content'];
        }

        include_once plugin_dir_path(__FILE__) . '../admin/class-subscribers-model.php';

        $subscribers_model = Subscribers_Model::get_instance();

        $this->subscribers = $subscribers_model->get_subscribers(array(
            'where' => array(
                'field' => 'id',
                'value' => $subscriber_id,
                'compare' => '='
            ),
        ), ARRAY_A);

        $this->send();

    }
    public function send_newsletter ($post_id) {

        $templater = Si_Templater::get_instance();
        
        $template_name = get_post_meta($post_id, 'newsletter_template', true);

        $template = $templater->templates[$template_name];

        $this->subject = get_the_title($post_id);

        $this->letter_type = $template['type'];

        $this->get_headers();
        
        $this->code = $templater->get_newsletter($post_id);

        if ('html' == $this->letter_type) {
            $this->code = Si_Templater::get_simple_html($this->subject, $this->code);
        }


        $this->get_subscribers();

        $this->send();

    }

    /**
     * @return array
     */
    public function get_subscribers()
    {
        $subscribers2get = array();
        foreach ($this->subscribers as $key=>$subscriber ) {
            if (!is_array($subscriber) && !empty(intval($subscriber))) { 
                $subscribers2get[] = $subscriber;
                unset($this->subscribers[$key]);
            } elseif (!empty($subscriber['id']) && (empty($subscriber['email'] || empty($subscriber['pass'])))) {
                $subscribers2get[] = $subscriber['id'];
                unset($this->subscribers[$key]);
            }
        }

        include_once plugin_dir_path(__FILE__) . '../admin/class-subscribers-model.php';

        $subscribers_model = Subscribers_Model::get_instance();

        $subscribers = $subscribers_model->get_subscribers(array(
            'where' => array(
                'field' => 'id',
                'value' => '(' . implode(', ', $subscribers2get) . ')',
                'compare' => 'IN'
            ),
        ), ARRAY_A);

        $this->subscribers = array_merge($subscribers, $this->subscribers);

    }

    public function send()
    {
        $templater = Si_Templater::get_instance();
        $templater->options = $this->options;
        
        $this->code = $templater->letter_shortcodes($this->code);
        $receivers = array();
        foreach ($this->subscribers as $subscriber) {
            $templater->subscriber = $subscriber;
            if (empty($subscriber['name'])) {
                $this->headers['To'] = $subscriber['email'];
            } else {
                $this->headers['To'] = 'To: ' . $subscriber['name'] . ' <' . $subscriber['email'] . '>';
            }
            
            $name = (empty($name)) ? 'Subscriber' : $name;
            $message = $templater->letter_shortcodes_personal($this->code);

            wp_mail($subscriber['email'], $this->subject, $message, implode("\r\n", $this->headers));
            
            $receivers[] = $subscriber['id'];
            
        }
        include_once plugin_dir_path(__FILE__) . '../admin/class-subscribers-model.php';

        $subscribers_model = Subscribers_Model::get_instance();

        $subscribers_model->update_last_send($receivers);
        
    }

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}