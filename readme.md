#Subscribtion Industry by Soft-Industry#

##Subscribers##

Subscribtion industry use the users table to menage subscribers. 


##Customizing##

###Add custom template###

###Customizing form###

The Subscribtion Industry plugin provide your subscribe form be ever so custom as you would like. 

Just insert the html code in textarea of widget using the SI Form shortcodes:
 
 * `[email]` E-mail input. Required. 
	+ `type` type text
	+ `class` 
 * `[name]` Name of subscriber. Optional
 * `[submit]` or `[button]`. Optional. But really recomended.
 
 You can add your own shortcode. Use `si_form_shortcodes` filter to add your callback function. 
 
###Customizing messaging###
 
1. Create the js function in your theme. 
2. Use the `si_form_localize` filter to hook the alert function to return messages.

 
 
