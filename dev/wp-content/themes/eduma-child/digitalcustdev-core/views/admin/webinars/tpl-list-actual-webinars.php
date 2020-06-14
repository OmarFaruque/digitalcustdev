<?php
/**
 * @author Deepen.
 * @created_on 7/16/19
 */
global $wpdb;
$metatable = $wpdb->prefix . 'postmeta';
//Check if any transient by name is available
$users = cstm_video_conferencing_zoom_api_get_user_transients();
// $users = cstm_video_conferencing_zoom_api_get_user_transients();
if(isset($users->users)){
    $users = $users->users;
}

if ( ! isset( $_GET['host_id'] ) ) {
	$host_id = $users[0]->id;
} else {
	$host_id = $_GET['host_id'];
}


$encoded_meetings = dcd_zoom_conference()->listWebinar( $host_id );
$decoded_meetings = json_decode( $encoded_meetings );



$webinars         = false;
if ( ! empty( $decoded_meetings ) && !isset($decoded_meetings->code) ) {
	$webinars = $decoded_meetings->webinars;
}else{
    $host_id = $users[0]->id;
    $encoded_meetings = dcd_zoom_conference()->listWebinar( $host_id );
    $decoded_meetings = json_decode( $encoded_meetings );
    if ( ! empty( $decoded_meetings ) ) {
        $webinars = $decoded_meetings->webinars;
    }

    $hoster_user = get_users(array(
        'meta_key'      => 'user_zoom_hostid',
        'meta_value'    => $_GET['host_id']
    ));
}


?>
<div class="wrap">
    <h2><?php _e( "Webinars", "video-conferencing-with-zoom-api" ); ?></h2>
	<?php $get_host_id = isset( $_GET['host_id'] ) ? $_GET['host_id'] : $users[0]->id; ?>
    <div class="select_zvc_user_listings_wrapp">
        <div class="alignright">
            <select onchange="location = this.value;" class="zvc-hacking-select">
                <option value="?page=zoom-video-conferencing-webinars"><?php _e( 'Select a User', 'video-conferencing-with-zoom-api' ); ?></option>
				<?php foreach ( $users as $user ) { ?>
                    <option value="?page=zoom-video-conferencing-webinars&host_id=<?php echo $user->id; ?>" <?php echo $get_host_id == $user->id ? 'selected' : false; ?>><?php echo $user->first_name . ' ( ' . $user->email . ' )'; ?></option>
				<?php } ?>
            </select>
        </div>
        <div class="clear"></div>
    </div>

    <div class="zvc_listing_table">
        <table id="zvc_meetings_list_table_customOrder" class="display schub-table-display" width="100%">
            <thead>
            <tr>
                <th class="zvc-text-left"><?php _e( 'Webinar ID', 'video-conferencing-with-zoom-api' ); ?></th>
                <th class="zvc-text-left" class="zvc-text-left"><?php _e( 'Action Time', 'video-conferencing-with-zoom-api' ); ?></th>
                <th class="zvc-text-left"><?php _e( 'Topic (Webinar Lesson)', 'video-conferencing-with-zoom-api' ); ?></th>
                <th class="zvc-text-left"><?php _e( 'Webinar Course', 'video-conferencing-with-zoom-api' ); ?></th>
                
                <th class="zvc-text-left"><?php _e( 'Host', 'video-conferencing-with-zoom-api' ); ?></th>

                <th class="zvc-text-left"><?php _e( 'Alternative Host', 'video-conferencing-with-zoom-api' ); ?></th>
                <th class="zvc-text-left"><?php _e( 'Cloud Recording', 'video-conferencing-with-zoom-api' ); ?></th>
                <th class="zvc-text-left"><?php _e( 'Created On', 'video-conferencing-with-zoom-api' ); ?></th>
                
            </tr>
            </thead>
            <tbody>
			<?php
			if ( ! empty( $webinars ) ) {
				foreach ( $webinars as $webinar ) {

                    // echo '<pre>';
                    // print_r($webinar);
                    // echo '</pre>';

                    // Hoster Mail
                    $userKey = array_search($webinar->host_id, array_column($users, 'id'));    
                    $hosterMail = $users[$userKey]->email;

                    $lesson_id = $wpdb->prepare( "SELECT post_id FROM $metatable where meta_key ='_webinar_ID' and meta_value like %s", $webinar->id );
                    $lesson_id = $wpdb->get_row( $lesson_id );
                    $start_url = get_post_meta($lesson_id->post_id, 'ah_start_link', true);
                    echo 'start url: ' . $start_url . '<br/>';
                    

                    
                    
                    
                    $master_token = get_option('zoom_master_host_token');
                    $master_url = '';
                    if($master_token){
                        $master_url = get_post_meta($lesson_id->post_id, 'mh_start_link', true);
                    }
                    
                    $course = learn_press_get_item_courses( $lesson_id->post_id );

                    // change zoom status
                    $start_time = get_post_meta( $lesson_id->post_id, '_lp_webinar_when', true );
                    $change_sdate = str_replace( '/', '-', $start_time );
                    
                
                    $time   = date( 'Y-m-d H:i', strtotime( $change_sdate ) );
                    
                    $timezone = get_post_meta($lesson_id->post_id, '_lp_timezone', true);
                    if($timezone){
                        $newcreatedtime = new DateTime("now", new DateTimeZone( $timezone ) );
                        $now = $newcreatedtime->format('Y-m-d H:i:s');
                        // echo 'now : ' .  $now . '<br/>';
                                            
                        $linkvisiable = date( 'Y-m-d H:i', strtotime('-10 minutes', strtotime( $time )) );
                    
                        // End Time 
                        $duration   = get_post_meta( $lesson_id->post_id, '_lp_duration', true );
                       
                        $endtime    = strtotime('+'.$duration, strtotime($time));
                        $endtime    = strtotime('+5 minutes', $endtime);
                      
                    

                        if( $now > $linkvisiable && $now < date('Y-m-d H:i', $endtime) ){
                            update_post_meta($lesson_id->post_id, 'zoom_status', 'active');
                        }else{
                            update_post_meta($lesson_id->post_id, 'zoom_status', 'inactive');
                        }
                    }

                    $zoom_active = get_post_meta($lesson_id->post_id, 'zoom_status', true);
                    
                    $course_id = 0;
                    $alternative_hoster = '';
                    $alterHostStatus = '';
                    $course_author_id = 0;

            

                    if($course){
                        $course_id = $course[0]->ID;
                        $course_author_id = $course[0]->post_author;
                        $course_author = get_userdata($course[0]->post_author);

                        $userhostid = '';
                        
                        if(get_post_meta( $lesson_id->post_id, '_lp_alternative_host', true )){
                            // echo 'lesson post id: ' . $lesson_id->post_id . '<br/>';
                            $userhostid = get_post_meta( $lesson_id->post_id, '_lp_alternative_host', true );
                        }else{
                            $userhostid = get_user_meta( $course[0]->post_author, 'user_zoom_hostid', true );
                        }
                        // echo 'hosterid: ' . $userhostid . '<br/>';

                        $key = array_search($userhostid, array_column($users, 'id'));
                        // echo 'key: ' . $key . '<br/>';
                        // echo '<pre>';
                        // print_r($users[$key]);
                        // echo '</pre>';
                        $alterHostStatus = $users[$key]->status;
                        $hoster_type = $users[$key]->type;
                        $hoster_type = ($hoster_type == 2) ? __(', Licensed', 'webinar') : '';
                        
                        $alternative_hoster = $users[$key]->email;

                        // echo 'alternative hoster: ' . $alternative_hoster . '<br/>';
                    }

                    
                    

                    /*
                    * Filtering for non-master user
                    */
                    if(isset($_GET['host_id']) && $_GET['host_id'] != $users[0]->id){
                        if($hoster_user[0]->ID != $course_author_id){
                            continue;
                        } 
                    }

                    if(!$webinar->start_time){
                        continue;
                    }

                   
                  

                    $timezone = ! empty( $webinar->timezone ) ? $webinar->timezone : "America/Los_Angeles";
					$tz       = new DateTimeZone( $timezone );
					$date     = new DateTime( $webinar->start_time );
                    $date->setTimezone( $tz );
                    
                    if(date('Y-m-d') > $date->format( 'Y-m-d') ){
                        continue;
                    }

                    // echo 'start time: ' . $webinar->start_time . '<br/>';
                    // echo 'duration: ' . $webinar->duration . '<br/>';

                    $endtime = strtotime("+".$webinar->duration." minutes", strtotime($date->format( 'd.m.Y, G:i' )));
                    $endtime = date('G:i', $endtime);
                    
                    // Calculate GMT time 
                    $timezoneoffset = get_timezone_offset($webinar->timezone);
                    $gmtStartTime = date('d.m.Y, G:i', strtotime($date->format( 'd.m.Y, G:i')) + $timezoneoffset);
                    $gmtStartEnd = strtotime("+".$webinar->duration." minutes", strtotime($gmtStartTime));
                    $gmtStartEnd = date('G:i', $gmtStartEnd);

                    
					?>
                    <tr data-time="<?php echo $webinar->start_time; ?>" class="<?php echo 'zoom_'. $zoom_active; ?>">
                        <td><?php echo $webinar->id; ?></td>
                        <td 
                         data-sort="<?php 
                         //echo $date->format( 'd-m-Y');
                         echo $zoom_active;
                        ?>"><?php
                            echo '<small>' . $gmtStartTime . '-' . $gmtStartEnd . '</small>';
                            echo '<br/><small>(GMT)</small>';
                            echo '<hr/>';
                            echo '<small>' . $date->format( 'd.m.Y, G:i') . '-' . $endtime . '</small>';
                            echo '<br/><small>' . $date->format( '(e)' ) . '</small>';
							?></td>
                        <td >
                            <a href="admin.php?page=zoom-video-conferencing-webinars&edit=<?php echo $webinar->id; ?>&host_id=<?php echo $webinar->host_id; ?>"><?php  echo $webinar->topic; ?></a>
                            
                        </td>
                        <td>
                            <a target="_blank" href="<?php echo get_edit_post_link( $course_id ); ?>"><?php echo get_the_title( $course_id ); ?></a>
                        </td>
                        
                        <td>
                            <?php echo $webinar->host_id; ?>
                            <?php
                                if($hosterMail){
                                    echo '<br/>('.$hosterMail.')';
                                }
                            ?>
                            <?php if(!empty($master_url)): ?>
                            <div class="row-actions s-webinar">
                                <span class="view">
                                    <a href="<?php  echo ! empty( $master_url ) ? $master_url : $webinar->join_url; ?>" rel="permalink" target="_blank"><?php  _e( 'Start Meeting', 'video-conferencing-with-zoom-api' ); ?></a>
                                </span>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="alternative_hoster">
                            <?php echo ($alternative_hoster) ? $alternative_hoster . ' ('. ucfirst($alterHostStatus) .$hoster_type.')' : '-'; ?>
                            <?php if($alternative_hoster && $start_url): ?>
                                <div class="row-actions s-webinar">
                                    <span class="view">
                                        <a href="<?php echo $start_url; ?>" rel="permalink" target="_blank"><?php  _e( 'Start Meeting', 'video-conferencing-with-zoom-api' ); ?></a>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>-</td>
                        <td><?php echo date( 'd.m.Y, G:i', strtotime( $webinar->created_at ) ); ?></td>
                        
                    </tr>
					<?php
				}
			} ?>
            </tbody>
        </table>
    </div>
</div>
