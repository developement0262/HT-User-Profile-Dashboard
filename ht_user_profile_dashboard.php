<?php
/**
* Plugin Name: HT User Profile Dashboard
* Plugin URI: https://www.hackertrail.com/
* Description: User Profile Widget
* Version: 1.0.1
* Author: Hackertrial
* Author URI: https://www.hackertrail.com/
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// If this file is called directly, abort. //
if ( ! defined( 'ABSPATH' ) ) {die;} // end if

// Initialize Everything
if ( file_exists( plugin_dir_path( __FILE__ ) . 'core-init.php' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'core-init.php' );
}

/*
* Plugin Activation and Deactivation hooks
*/
register_activation_hook( __FILE__ , 'ht_user_profile_plugin_activation' );

?>