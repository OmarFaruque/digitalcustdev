<?php
/**
 * @author Deepen.
 * @created_on 7/16/19
 */
global $wpdb;
$metatable = $wpdb->prefix . 'postmeta';
//Check if any transient by name is available
$users = cstm_video_conferencing_zoom_api_get_user_transients();
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


// echo 'Users <br/><pre>';
// print_r($users);
// echo '</pre>';
// echo '<hr/>';
// echo 'Webinars <br/><pre>';
// print_r($webinars);
// echo '</pre>';
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
                <th class="zvc-text-left" class="zvc-text-left"><?php _e( 'Start Time', 'video-conferencing-with-zoom-api' ); ?></th>
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
                    
                    $dbusers = get_users(array(
                        'meta_key'      => 'user_zoom_hostid',
                        'meta_value'    => $webinar->host_id
                    ));



                    $lesson_id = $wpdb->prepare( "SELECT post_id FROM $metatable where meta_key ='_webinar_ID' and meta_value like %s", $webinar->id );
                    $lesson_id = $wpdb->get_row( $lesson_id );
                    $course = learn_press_get_item_courses( $lesson_id->post_id );
                    
                    $course_id = 0;
                    $alternative_hoster = '';
                    $alterHostStatus = '';
                    $course_author_id = 0;
                    if($course){
                        $course_author_id = $course[0]->post_author;
                        $course_author = get_userdata($course[0]->post_author);
                        $userhostid = get_user_meta( $course[0]->post_author, 'user_zoom_hostid', true );
                        $key = array_search($userhostid, array_column($users, 'id'));
                        $alterHostStatus = $users[$key]->status;
                        
                        // echo 'author info<br/><pre>';
                        // print_r($course_author);
                        // echo '</pre>';
                        $course_id = $course[0]->ID;
                        $alternative_hoster = $course_author->data->user_email;
                    }


                    /*
                    * Filtering for non-master user
                    */
                    if(isset($_GET['host_id']) && $_GET['host_id'] != $users[0]->id){
                        if($hoster_user[0]->ID != $course_author_id){
                            continue;
                        } 
                    }

                    

					?>
                    <tr>
                        <td><?php echo $webinar->id; ?></td>
                        <td><?php
							$timezone = ! empty( $webinar->timezone ) ? $webinar->timezone : "America/Los_Angeles";
							$tz       = new DateTimeZone( $timezone );
							$date     = new DateTime( $webinar->start_time );
							$date->setTimezone( $tz );
                            echo $date->format( 'd.m.Y, g:i a');
                            echo '<br/>' . $date->format( '( e )' );
							?></td>
                        <td>
                            <a href="admin.php?page=zoom-video-conferencing-webinars&edit=<?php echo $webinar->id; ?>&host_id=<?php echo $webinar->host_id; ?>"><?php  echo $webinar->topic; ?></a>
                            
                        </td>
                        <td>
                            <a target="_blank" href="<?php echo get_edit_post_link( $course_id ); ?>"><?php echo get_the_title( $course_id ); ?></a>
                        </td>
                        
                        <td>
                            <?php echo $webinar->host_id; ?>
                            <?php
                                if($dbusers){
                                    echo '<br/>('.$dbusers[0]->data->user_email.')';
                                }
                            ?>
                            <div class="row-actions s-webinar">
                                <span class="view">
                                    <a href="<?php  echo ! empty( $webinar->start_url ) ? $webinar->start_url : $webinar->join_url; ?>" rel="permalink" target="_blank"><?php  _e( 'Start Meeting', 'video-conferencing-with-zoom-api' ); ?></a>
                                </span>
                            </div>
                        </td>
                        <td class="alternative_hoster">
                            <?php echo ($alternative_hoster) ? $alternative_hoster . ' ('. ucfirst($alterHostStatus) . ')' : '-'; ?>
                            <?php if($alternative_hoster): ?>
                                <div class="row-actions s-webinar">
                                    <span class="view">
                                        <a href="<?php  echo ! empty( $webinar->start_url ) ? $webinar->start_url : $webinar->join_url; ?>" rel="permalink" target="_blank"><?php  _e( 'Start Meeting', 'video-conferencing-with-zoom-api' ); ?></a>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>-</td>
                        <td><?php echo date( 'd.m.Y, g:i a', strtotime( $webinar->created_at ) ); ?></td>
                    </tr>
					<?php
				}
			} ?>
            </tbody>
        </table>
    </div>
</div>
