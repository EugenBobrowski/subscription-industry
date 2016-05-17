<?php

class Si_Default_Templates
{

    protected static $instance;
    
    private function __construct()
    {

        add_filter('si_templates', array($this, 'default_templates'));
        add_filter('si_template_web_default_field_content', array($this, 'template_web_default_field_content'), 10, 2);
        add_filter('si_template_default_html_field_content', array($this, 'template_default_html_field_content'), 10, 2);

    }

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function default_templates($templates)
    {
        
        
        return wp_parse_args($templates, array(
                'default' => array(
                    'name' => 'Default text',
                    'describtion' => 'The default text template',
                    'type' => 'plain',
                    'preview' => plugin_dir_url(__FILE__) . 'img/email_template_txt.png',
                    'fields' => array(
                        'content' => array(
                            'type' => 'textarea',
                            'title' => 'Content',
                        ),
                    ),
                    'body' => '{content}'
                ),
                'default_html' => array(
                    'name' => 'Default HTML',
                    'describtion' => 'The default text template',
                    'type' => 'html',
                    'preview' => plugin_dir_url(__FILE__) . 'img/email_template_html.png',
                    'fields' => array(
                        'content' => array(
                            'type' => 'editor',
                            'title' => 'Content',
                        ),
                        'logo' => array(
                            'type' => 'media',
                            'title' => 'Logo',
                        ),
                        'bg' => array(
                            'type' => 'color',
                            'title' => 'Background',
                        )

                    ),
                    'body' => '<table border="0" cellspacing="0" cellpadding="15" style="background-color:{bg};font-family:Helvetica,Arial,sans-serif" width="100%" bgcolor="{bg}">
<tr>
<td></td><td width="600">
    <img src="{logo}" alt="">
</td><td></td>
</tr>
<tr>
    <td></td>
    <td width="600"  style="background-color:#ffffff;" bgcolor="#ffffff" cellpadding="0">{content}</td>
    <td></td>
</tr>
<tr>
<td></td><td width="600"></td><td></td>
</tr>
</table>',

                ),
            )
        );
    }
    public function template_web_default_field_content ($newsletter, $data) {
        if (is_string($data['content'])) {
            $newsletter = str_replace("{content}", wpautop($data['content']), $newsletter);
        }
        return $newsletter;
    }
    public function template_default_html_field_content($newsletter, $data) {
        if (is_string($data['content'])) {
            $newsletter = str_replace("{content}", wpautop($data['content']), $newsletter);
        }
        return $newsletter;
    }

}