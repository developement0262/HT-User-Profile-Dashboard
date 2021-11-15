<?php

// If this file is called directly, abort. //
if ( ! defined( 'ABSPATH' ) ) {die;} // end if

// Define Our Constants
define( 'HT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/*****************************
* Enqueue styles and scripts
*****************************/
add_action('wp_enqueue_scripts', 'ht_up_scripts');
function ht_up_scripts(){

	wp_enqueue_style( 'ht_user_profile_css', plugins_url( 'assets/css/ht_user_profile.css', __FILE__ ) );
}

/****************************
* Plugin activation function
*****************************/
function ht_user_profile_plugin_activation(){
	include( HT_PLUGIN_PATH . 'inc/activation.php' );
}

/*****************************
* User Profile Shortcode
*****************************/
add_shortcode('ht_user_profile', 'ht_user_profile_func');
function ht_user_profile_func(){
	ob_start();
	include( HT_PLUGIN_PATH . 'inc/ht_user_profile.php');
	$return_string = ob_get_clean();
	return $return_string;
}

/*****************************
* User Profile Ajax
*****************************/
function ht_up_open_to_work(){
	
	global $wpdb;
	$user_id = get_current_user_id();
	$status = $_REQUEST['status'];
	$field_status = xprofile_get_field_data( 18, $user_id );
	if ( $status == 1 ) {
		xprofile_set_field_data(18, $user_id, 'Actively Looking');
	}else{
		xprofile_set_field_data(18, $user_id, 'Not Looking');
	}

	$author_detail = get_user_by('id', $user_id);
	$author_email = $author_detail->user_email;
	$output = '';
	$job_redirect = site_url().'/jobs';
	$get_total_rem = get_job_recommendation($author_email);
	
	$ht_up_open_to_work = $wpdb->prefix . 'ht_up_open_to_work';
	$checkIfExists = $wpdb->get_var("SELECT * FROM $ht_up_open_to_work WHERE user_id = $user_id ");
	
	if ($checkIfExists == NULL) {

		$wpdb->insert($ht_up_open_to_work, array(
			'user_id'		=> $user_id,
			'status' 		=> $status,
			'total' 		=> $get_total_rem->total,
		), array(
			'%d', '%d', '%d'
		));

		$output .= '<div class="con-lable"><p>Job Recommendations</p></div><div class="con-cou"><p><a href="'.$job_redirect.'">'.$get_total_rem->total.'</a></p></div>';

	}else{
		$wpdb->query( $wpdb->prepare( "UPDATE $ht_up_open_to_work SET `status` = $status WHERE `user_id` = $user_id " ) );
		$output .= '<div class="con-lable"><p>Job Recommendations</p></div><div class="con-cou"><p><a href="'.$job_redirect.'">'.$get_total_rem->total.'</a></p></div>';
	}
	echo json_encode(array(
		'status'	=> $status,
		'job_rem'	=> $get_total_rem->total,
		'output'	=> $output
	));

	wp_die();
}
add_action('wp_ajax_ht_up_open_to_work', 'ht_up_open_to_work');

function get_job_recommendation($email){
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://services-test.hackertrail.com/api/list_jobs?email='.$email,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	));

	$response = curl_exec($curl);

	curl_close($curl);
	return json_decode($response);
}


?>