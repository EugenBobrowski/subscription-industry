#Subscribtion Industry by Soft-Industry

##Subscribers

Subscribtion industry use the users table to menage subscribers. 

##Customizing

###Add custom template

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

 
 
