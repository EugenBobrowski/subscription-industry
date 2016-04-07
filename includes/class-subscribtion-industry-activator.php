<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Subscribtion_Industry
 * @subpackage Subscribtion_Industry/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Subscribtion_Industry
 * @subpackage Subscribtion_Industry/includes
 * @author     Your Name <email@example.com>
 */
class Subscribtion_Industry_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'si_subscribers';
        $sql = "
	CREATE TABLE $table_name (
	  id int(11) NOT NULL AUTO_INCREMENT,
      name varchar(255) DEFAULT NULL,
      user_id int(11),
      activation_key varchar(255),
      UNIQUE KEY id (id)
	);
	";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }


}
