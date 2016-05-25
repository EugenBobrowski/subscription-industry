# Subscribtion Industry by Soft-Industry

Add subscription and newsletter system to your website. With unlimited newsletters, subscribers and custom shortcodes.

---

Subscribtion industry is a real customizable newsletter system for your site. 

**Features**

* Unlimited Subscribers
* Unlimited Responsive Newsletters
* Simple editor. With an code-free experience. And grate
* Customizable subscription widget/form. Custom HTML structure
* Web versions of Newsletters
* Separate Newsletters and subscribers by groups 
* Include default text and HTML template
* Customizable and addable Newsletter Templates with easy to use hooks and newsletters fields
* Compatible with Postman, WP Mail SMTP, Easy WP SMTP, Easy SMTP Mail, WP Mail Bank

There are many shortcode types to use in the plugin;

**Letter shortcodes (public and personal)**

* `[subscriber]` 
* `[unsubscribe]` 

**Si Widget Shortcodes**



**Hooks**

* 


## Installation

This section describes how to install the plugin and get it working.

e.g.

1. Upload `subscribtion-industry.zip to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

## Frequently Asked Questions


### How to add my custom template?

Adding your custom template you can add via `si_templates` hook. 

**Example**

```php
add_filter('si_templates', 'add_my_templates');
function add_my_templates ($templates) {
    $templates = array(
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
     <td width="600"  style="background-color:#ffffff;" bgcolor="#ffffff" cellpadding="0">
     {content}
 </td>
     <td></td>
 </tr>
 <tr>
 <td></td><td width="600"></td><td></td>
 </tr>
 </table>',

                 );
    return $templates;
}

```


### How to create simple subscription form
 
 The Subscribtion Industry plugin provide your subscribe form be ever so custom as you would like. 
 
 Just insert the html code in textarea of widget using the SI Form shortcodes:
 
  * `[form]` Not required. Use it if you need to save your DOM for styles.
  * `[email]` E-mail input. Required. 
 	+ `type` type text
 	+ `class` 
  * `[name]` Name of subscriber. Optional
  * `[group]`
     + `type` Type of field. Available: `hidden`,
  * `[submit]` or `[button]`. Optional. But really recomended.
  
  You can add your own shortcode. Use `si_form_shortcodes` filter to add your callback function.
 
   ```php
     function my_si_form_shortcode_callback($args, $contnent = ''){
     if (empty($args[color])) $class = 'default'
     return '<span class=' . $class . '>' . $content . '</span>';
   }
 
   function add_my_si_form_shortcode ($shrtcodes) {
     $shorcodes[my_form_element] = 'my_si_form_shortcode_callback';
     return $shortcodes;
   }
   add_filter('si_form_shortcodes', 'my_si_form_shortcode');
   ```

### How to customize subscription form messages?
    
Lorem ipsum

### How to change subscribtion form messages displaying function?

Lorem ipsum

## Screenshots

1. Newsletter composing with default html template.
2. Newsletter composing with template, defined in theme.

## Changelog

### 1.0
*Release Date - 25th May, 2016*

* Initial