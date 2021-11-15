<?php

if ( ! defined( 'ABSPATH' ) ) {die;} // end if
global $wpdb;

/****************************
* Creating table for open to work
*****************************/

$ht_up_open_to_work = $wpdb->prefix . 'ht_up_open_to_work';
$query = "CREATE TABLE $ht_up_open_to_work(
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `status` INT(11) NOT NULL ,
  `total` INT(11) NOT NULL ,
  PRIMARY KEY (`id`)
)";

require_once(ABSPATH ."wp-admin/includes/upgrade.php");
dbDelta( $query );

?>