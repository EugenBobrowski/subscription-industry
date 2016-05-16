#Subscribtion Industry by Soft-Industry

* Сделать отправку имейлов в разделе подписчиков и непосредственно из настроек Ньюзлеттера.
* Реализовать группы отмеченные.


##Subscribers

Subscribtion industry use the users table to menage subscribers. 

##Customizing

###Add custom template

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


###Customizing form

The Subscribtion Industry plugin provide your subscribe form be ever so custom as you would like. 

Just insert the html code in textarea of widget using the SI Form shortcodes:
 
 * `[email]` E-mail input. Required. 
	+ `type` type text
	+ `class` 
 * `[name]` Name of subscriber. Optional
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
 
###Customizing messaging
 
1. Create the js function in your theme. 
2. Use the `si_form_localize` filter to hook the alert function to return messages.

 
 
