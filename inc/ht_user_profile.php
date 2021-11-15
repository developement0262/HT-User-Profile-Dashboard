<?php 

if ( ! defined( 'ABSPATH' ) ) {die;} // end if
global $wpdb;
$user_id = get_current_user_id();
$username = get_user_by('id', $user_id);
$admin_url = admin_url("admin-ajax.php");
$job_redirect = site_url().'/jobs';


$user_name = bp_core_get_core_userdata( $user_id );
$args = array(
	'item_id' => $user_id,
);
$user_cover_image = bp_attachments_get_attachment( 'url', $args );

$args2 = array( 'item_id' => $user_id, 'html' => false, );
$user_avatar = bp_get_displayed_user_avatar($args2);

$connection = friends_get_total_friend_count( $user_id );
$connection_req = bp_friend_get_total_requests_count( $user_id );

$company = groups_get_user_groups( $user_id );
$company_req = bp_notifications_get_grouped_notifications_for_user( $user_id );

$ht_up_open_to_work = $wpdb->prefix . 'ht_up_open_to_work';
$otw = $wpdb->get_row(" SELECT * FROM $ht_up_open_to_work WHERE user_id = $user_id ");

$field_company_name = xprofile_get_field_data( 10, $user_id );
$field_role_title = xprofile_get_field_data( 11, $user_id );

if ( $field_company_name == '' || $field_role_title == '' ) {
	$user_desc = $field_role_title.''.$field_company_name;
}else{
	$user_desc = $field_role_title.' at '.$field_company_name;
}

$earned_achievements = gamipress_get_user_achievements( array(
    'user_id'           => $user_id,
    'achievement_type'  => gamipress_get_achievement_types_slugs(),
) );

$count_achievements = count($earned_achievements);
$info_icon = site_url() . '/wp-content/plugins/ht_user_profile_dashboard/assets/img/info.png';
$coin_icon = site_url() . '/wp-content/plugins/ht_user_profile_dashboard/assets/img/coin.png';
$default_badge = site_url() . '/wp-content/plugins/ht_user_profile_dashboard/assets/img/default_badge.png';

if ( !is_user_logged_in() ) {
	?>
	<style type="text/css">
		aside.ht_pad_0{
			display: none;
		}
	</style>
	<?php
}
?>
<section id="ht_user_profile">
<?php
	if ( is_user_logged_in() ) {
		?>
		  	<div class="card-box">
			    <div class="main-card">
			      <div class="iner-card">
			        <div class="main-img">
			          <img src="<?php echo $user_cover_image; ?>">
			          <div class="man-img">
			            <img src="<?php echo $user_avatar; ?>">
			          </div>
			        </div>
			        <div class="contant">
			          <div class="card-heading">
			            <h4><?php echo $user_name->display_name; ?></h4>
			            <p><?php echo $user_desc; ?></p>
			          </div>
			          <div class="hacker-box">
			            <div class="coin-img">
			              <img src="<?php echo $coin_icon; ?>">
			            </div>
			            <div class="coin-point">
			            	<a href="<?php echo site_url(). '/rewards/'; ?>">
			              		<?php echo do_shortcode( '[gamipress_points type="hb" user_id="'.$user_id.'" layout="none" thumbnail="no" label="yes"]' ); ?>
			              	</a>
			            </div>
			          </div>
			          <div class="work">
			            <div class="open-work">
			              <div class="open">
			                <p>Open to Work <img src="<?php echo $info_icon; ?>"></p>
			                 <div class="toltip">
			                <p>Toggling Open to Work would allow us to give you jobs recommendations based on your profile.</p>
			              </div>
			              </div>
			             
			              <div class="open-button">
			                <div class="toggle-button-cover">
			                  <div class="button-cover">
			                    <div class="button r" id="button-1">
			                   	<?php if ( !empty($otw->status) && $otw->status == 1 ) { ?>
			                      	<input type="checkbox" class="checkbox" checked="checked">
			                   	<?php }else{ ?>
			                   		<input type="checkbox" class="checkbox">
			                   	<?php } ?>
			                      <div class="knobs"></div>
			                      <div class="layer"></div>
			                    </div>
			                  </div>
			                </div>
			              </div> 
			            </div>
			            <div class="connection">
			              <div class="con-lable">
			                <p>Connections</p>
			              </div>
			              <div class="con-cou">
			                <p><a href="<?php echo site_url().'/members/'.$username->user_login.'/friends/'; ?>"><?php echo $connection; ?></a></p>
			              </div>
			            </div>
			            <?php if ( $connection_req != 0 ) { ?>
				            <div class="list">
				              <ul>
				                <li>
				                  <p>
				                  	<a href="<?php echo site_url().'/members/'.$username->user_login.'/friends/requests/'; ?>"><?php echo $connection_req; ?><span>requests</span>
				                  	</a>
				                  </p>
				                </li>
				              </ul>
				            </div>
			            <?php } ?>
			            <div class="connection">
			              <div class="con-lable">
			                <p>Companies</p>
			              </div>
			              <div class="con-cou">
			                <p><a href="<?php echo site_url().'/members/'.$username->user_login.'/groups/'; ?>"><?php echo $company['total']; ?></a></p>
			              </div>
			            </div>
			            <?php 
	                  	foreach ($company_req as $notification_item) {
							if ( $notification_item->component_name == 'groups' && $notification_item->is_new != 0 ) {?>
					            <div class="list">
					              <ul>
					                <li>
					                  	<p>
						                  	<?php echo $notification_item->total_count; ?>
						                  	<span>new</span>
					                  	</p>
					                </li>
					              </ul>
					            </div>
					        <?php }
					    }?>
				            <div class="connection job_recommend">
					    	<?php if ( !empty($otw->status) && $otw->status == 1 ) { ?>
				              <div class="con-lable">
				                <p>Job Recommendations</p>
				              </div>
				              <div class="con-cou">
				                <p><a href="<?php echo $job_redirect; ?>"><?php echo $otw->total; ?></a></p>
				              </div>
				        	<?php } ?>
				            </div>
			            <div class="achivment">
			              <p>Achievements</p>
			            </div>
			            <?php if ($count_achievements == 0) { ?>
				            <div class="learn">
				              <p> You currently do not have any achievements. <a href="#"> Learn More </a> </p>
				            </div>
			            <?php }else{ ?>
			            <div class="achi-img">
			              <ul>
			              	<?php 
			              	$cnt = 1;
			              	foreach($earned_achievements as $data){
			              		
								$image = wp_get_attachment_image_src( get_post_thumbnail_id( $data->post_id ));
								if ( $cnt > 6 ) {
									if (has_post_thumbnail( $data->post_id ) ){
										$get_total = ($count_achievements+1) - $cnt;
										echo '<li class="break_achievement"><p><a href="#">+'.$get_total.'</a></p></li>';
										break;
				              		}
								}else{
				              		if (has_post_thumbnail( $data->post_id ) ){
										echo '<li> <div class="achive-img">
											<img src="'.$image[0].'" />
										</div> 
										<div class="content-achive"> <p>'.$data->title.'</p> </div>
										</li>';
				              		}else{
				              			echo '<li> <div class="achive-img">
											<img src="'.$default_badge.'" />
										</div> 
										<div class="content-achive"> <p>'.$data->title.'</p> </div>
										</li>';
				              		}
								}
			              		$cnt++;
							}
			              	?>
			                
			                <!-- <li><p></p></li>
			                <li><p></p></li>
			                <li><p></p></li>
			                <li><p></p></li> -->
			                <!-- <li><p>+4</p></li> -->
			              </ul>
			            </div><!-- .achi-img -->
			        	<?php } ?>
			          </div>
			        </div>
			      </div><!-- .iner-card -->
			    </div> <!-- .main-card -->
		  </div><!-- .card-box -->
		<?php
	}

	?>
</section>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('section#ht_user_profile').parent().addClass('ht_pad_0');
	jQuery(document).on('click', 'div#button-1 .checkbox', function(){
	    if (jQuery(this).is(":checked"))
	    {
	    	jQuery(this).attr('checked','checked');
	    	var status = 1;
	    	jQuery('.connection.job_recommend').show();
	    }else{
	      	jQuery(this).removeAttr('checked');
	      	var status = 0;
	      	jQuery('.connection.job_recommend').hide();
	    }

	    jQuery.ajax({
		   	type : "get",
			dataType : "json",
			url : '<?php echo $admin_url ?>',
			data : {
				action: "ht_up_open_to_work",
				status: status,
			},
			success : function( response ) {
				jQuery('.connection.job_recommend').html(response.output);

			}
		});
	});
});
</script>
