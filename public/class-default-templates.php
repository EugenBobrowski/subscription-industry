<?php

class Si_Default_Templates
{

    protected static $instance;
    
    private function __construct()
    {

        add_filter('si_templates', array($this, 'default_templates'));
        add_filter('si_template_default_field_content_stringify_web', 'wpautop', 10);
        add_filter('si_template_default_html_field_content', 'wpautop', 10);

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
<td style="padding:15px;"></td><td style="padding:15px;" width="600">
    <img src="{logo}" alt="" height="70" style="" />
</td><td style="padding:15px;"></td>
</tr>
<tr>
    <td style="padding:15px;"></td>
    <td width="600"  style="background-color:#ffffff;padding:15px;" bgcolor="#ffffff" cellpadding="0">{content}</td>
    <td style="padding:15px;"></td>
</tr>
<tr>
<td style="padding:15px;"></td><td width="600"></td><td style="padding:15px;"></td>
</tr>
</table>',

                ),
            )
        );
    }

}