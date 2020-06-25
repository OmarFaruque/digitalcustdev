<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//Check if any transient by name is available
// $users                     = video_conferencing_zoom_api_get_user_transients();
$users 				   = cstm_video_conferencing_zoom_api_get_user_transients();

$webinar_info              = json_decode( dcd_zoom_conference()->getWebinarInfo( $_GET['edit'] ) );

// echo '<pre>';
// print_r($webinar_info);
// echo '</pre>';


global $wpdb;
$metatable = $wpdb->prefix . 'postmeta';
$lesson_id = $wpdb->prepare( "SELECT post_id FROM $metatable where meta_key ='_webinar_ID' and meta_value like %s", $webinar_info->id );
$lesson_id = $wpdb->get_row( $lesson_id );
$course = learn_press_get_item_courses( $lesson_id->post_id );




$authorHost = '';

if(empty($authorHost)){
	$authorHost = get_post_meta( $lesson_id->post_id, '_lp_alternative_host', true );
}

if($course && empty($authorHost)){
	$course_author = $course[0]->post_author;
	$authorHost = get_user_meta($course_author, 'user_zoom_hostid', true);
}



$newcreatedtime = new DateTime("now", new DateTimeZone( $webinar_info->timezone ) );
$now = $newcreatedtime->format('Y-m-d H:i:s');
$webinarStartTime = date('Y-m-d H:i:s', strtotime($webinar_info->start_time));
$webinarEndTime = date('Y-m-d H:i:s', strtotime('+'.$webinar_info->duration.' minutes', strtotime($webinarStartTime)));
$webinarBeforeTen = date('Y-m-d H:i:s', strtotime('-10 minutes', strtotime($webinarStartTime)));

// $meetingId = 956905044;
// $recordedUrl = dcd_zoom_conference()->getMeetingRecordUrl($meetingid);

// $meetingInfo = dcd_zoom_conference()->getMeetingInfo($meetingId);
// $meetingInfo = json_decode($meetingInfo);
// return dcd_zoom_conference()->sendRequest( 'meetings/'.$meetingInfo->uuid.'/recordings', array(), "GET" );
// echo 'Recorded Vide <br/><pre>';
// print_r($webinar_info);
// echo '</pre>';


$option_host_video         = ! empty( $webinar_info->settings->host_video ) && $webinar_info->settings->host_video ? 'checked' : false;
$zoom_map_array            = get_option( 'zoom_api_meeting_options' );
$option_enforce_login      = isset( $zoom_map_array[ $webinar_info->id ]['enforce_login'] ) ? 'checked' : false;
$option_alternative_hosts  = $webinar_info->settings->alternative_hosts ? $webinar_info->settings->alternative_hosts : array();
if ( ! empty( $option_alternative_hosts ) ) {
	$option_alternative_hosts = explode( ', ', $option_alternative_hosts );
}



// $start_url = $webinar_info->start_url;
$start_url = get_post_meta($lesson_id->post_id, 'ah_start_link', true);

// echo 'start url: ' . $start_url . '<br/>';


// Creating WP Option for Meeting Password, to verify from front-end without making calls to API.
$password = $webinar_info->password;
$option   = 'zoom_password_meeting_' . $webinar_info->id;
update_option( $option, $password, true );
?>
<div class="wrap">
	<h1><?php _e( 'Edit a Webinar', 'video-conferencing-with-zoom-api' ); ?></h1> 
	<a href="<?php echo admin_url( 'admin.php?page=zoom-video-conferencing-webinars' ); ?>"><?php _e( 'Back to List', 'video-conferencing-with-zoom-api' ); ?></a>

	<div class="message">
		<?php








		$message = self::get_message();
		if ( isset( $message ) && ! empty( $message ) ) {
			echo $message;
		}

		if ( isset( $_GET['zoom_meeting_added'] ) ) {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php _e( 'Webinar Added To Zoom Successfully!', 'video-conferencing-with-zoom-api' ); ?></p>
			</div>
			<?php
		}

		?>
	</div>
	<?php if ( $webinar_info->id ) : ?>
		<div class='notice notice-success is-dismissible'>
		<h3><?php _e( 'Great! Now, add the below shortcode on any of your WordPress page/post to show the Zoom webinar window' ); ?></h3>
		<?php echo '<h4>[zoom_api_link meeting_id="' . esc_attr( $webinar_info->id ) . '" class="zoom-meeting-window" id="zoom-meeting-window" title="Your Page Title"]</h4>'; ?></div>
	<?php endif; ?>

	<form action="?page=zoom-video-conferencing-webinars&edit=<?php echo esc_attr( $_GET['edit'] ); ?>&host_id=<?php echo esc_attr( $_GET['host_id'] ); ?>" method="POST" class="zvc-meetings-form">
		<?php wp_nonce_field( '_zoom_update_webinar_nonce_action', '_zoom_update_webinar_nonce' ); ?>
		<input type="hidden" name="webinar_id" value="<?php echo $webinar_info->id; ?>">
		<input type="hidden" name="lesson_id" value="<?php echo $lesson_id->post_id; ?>">
		
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row"><label for="meetingTopic"><?php _e( 'Webinar Topic *', 'video-conferencing-with-zoom-api' ); ?></label></th>
				<td>
					<input type="text" readonly name="meetingTopic" size="100" class="regular-text" required value="<?php echo ! empty( $webinar_info->topic ) ? $webinar_info->topic : null; ?>">
					<p class="description" id="meetingTopic-description"><?php _e( 'Webinar topic. (Required).', 'video-conferencing-with-zoom-api' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="meetingAgenda"><?php _e( 'Webinar Agenda', 'video-conferencing-with-zoom-api' ); ?></label></th>
				<td>
					<input type="text" name="agenda" class="w100 regular-text" value="<?php echo ! empty( $webinar_info->agenda ) ? $webinar_info->agenda : null; ?>">
					<p class="description" id="meetingTopic-description"><?php _e( 'Webinar Description.', 'video-conferencing-with-zoom-api' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="userId"><?php _e( 'Webinar Host *', 'video-conferencing-with-zoom-api' ); ?></label></th>
				<td>

				
					<select name="userId" required class="zvc-hacking-select" disabled>
						<option value=""><?php _e( 'Select a Host', 'video-conferencing-with-zoom-api' ); ?></option>
						<?php foreach ( $users->users as $user ) : ?>
							<option value="<?php echo $user->id; ?>" <?php echo $webinar_info->host_id == $user->id ? 'selected' : null; ?>><?php echo $user->first_name . ' ( ' . $user->email . ' )'; ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description" id="userId-description">
						<?php _e( 'Cannot Update Host for an Existing Webinar.', 'video-conferencing-with-zoom-api' ); ?><br>
						<?php echo sprintf('Start Url: <a target="_blank" href="%s">Click hre</a>', $webinar_info->start_url); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="start_date"><?php _e( 'Start Date/Time *', 'video-conferencing-with-zoom-api' ); ?></label></th>
				<td>
					<?php
					$timezone = ! empty( $webinar_info->timezone ) ? $webinar_info->timezone : 'America/Los_Angeles';
					$tz       = new DateTimeZone( $timezone );
					$date     = new DateTime( $webinar_info->start_time );
					$date->setTimezone( $tz );
					?>
					<input readonly type="text" name="start_date" id="datetimepicker" data-existingdate="<?php echo $date->format( 'Y-m-d H:i:s' ); ?>" required class="regular-text">
					<p class="description" id="start_date-description"><?php _e( 'Starting Date and Time of the Webinar (Required).', 'video-conferencing-with-zoom-api' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="timezone"><?php _e( 'Timezone', 'video-conferencing-with-zoom-api' ); ?></label></th>
				<td>
					<?php $tzlists = zvc_get_timezone_options(); ?>
					<select readonly disabled id="timezone" name="timezone" class="zvc-hacking-select">
						<?php foreach ( $tzlists as $k => $tzlist ) { ?>
							<option value="<?php echo $k; ?>" <?php echo $webinar_info->timezone == $k ? 'selected' : null; ?>><?php echo $tzlist; ?></option>
						<?php } ?>
					</select>
					<p class="description" id="timezone-description"><?php _e( 'Webinar Timezone', 'video-conferencing-with-zoom-api' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="duration"><?php _e( 'Duration', 'video-conferencing-with-zoom-api' ); ?></label></th>
				<td>
					<input type="number" readonly name="duration" class="regular-text" value="<?php echo $webinar_info->duration ? $webinar_info->duration : null; ?>">
					<p class="description" id="duration-description"><?php _e( 'Webinar duration (minutes). (optional)', 'video-conferencing-with-zoom-api' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="password"><?php _e( 'Webinar Password', 'video-conferencing-with-zoom-api' ); ?></label></th>
				<td>
					<input type="text" name="password" class="regular-text" maxlength="10" data-maxlength="9" value="<?php echo ! empty( $webinar_info->password ) ? $webinar_info->password : false; ?>">
					<p><strong><?php _e( 'Zoom has added an extra layer of security, It is recommended that you set a Password for your webinars.', 'video-conferencing-with-zoom-api' ); ?></strong></p>
					<p class="description" id="email-description"><?php _e( 'Password to join the webinar. Password may only contain the following characters: [a-z A-Z 0-9]. Max of 10 characters.( Leave blank for no Password )', 'video-conferencing-with-zoom-api' ); ?></p>
				</td>
			</tr>
			
			<tr>
				<th scope="row"><label for="option_enforce_login"><?php _e( 'Enforce Login', 'video-conferencing-with-zoom-api' ); ?></label></th>
				<td>
					<p class="description" id="option_enforce_login"><input type="checkbox" <?php echo $option_enforce_login; ?> name="option_enforce_login" value="1" class="regular-text"><?php _e( 'Only signed-in WordPress users can join this webinar.', 'video-conferencing-with-zoom-api' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="option_auto_recording"><?php _e( 'Auto Recording', 'video-conferencing-with-zoom-api' ); ?></label></th>
				<td>
					<select id="option_auto_recording" name="option_auto_recording">
						<option value="none" <?php echo ! empty( $webinar_info->settings->auto_recording ) && $webinar_info->settings->auto_recording == 'none' ? 'selected' : false; ?>>No Recordings</option>
						<option value="local" <?php echo ! empty( $webinar_info->settings->auto_recording ) && $webinar_info->settings->auto_recording == 'local' ? 'selected' : false; ?>>Local</option>
						<option value="cloud" <?php echo ! empty( $webinar_info->settings->auto_recording ) && $webinar_info->settings->auto_recording == 'cloud' ? 'selected' : false; ?>>Cloud</option>
					</select>
					<!-- <a href="#"><?php //_e('Recording', 'webinar'); ?></a> -->
					<p><strong><?php _e( 'Please note that when conducting webinar from the web client only cloud recording feature is available if you are on a Pro or higher level Zoom account. If you are on a free account the recording is not possible.', 'video-conferencing-with-zoom-api' ); ?></strong></p>
					<p class="description" id="option_auto_recording_description"><?php _e( 'Set what type of auto recording feature you want to add. Default is none.', 'video-conferencing-with-zoom-api' ); ?></p>
					<p>
							<?php
							if($now > $webinarEndTime){ ?>
								<a download href="#"><?php _e('Download Recorded Video 1', 'webinar'); ?></a>		
							<?php }
							?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="settings_alternative_hosts"><?php _e( 'Alternative Hosts', 'video-conferencing-with-zoom-api' ); ?></label></th>
				<td>
					<div class="alternativeHosterWrap">
					<div class="pull-left w-50">
					<select name="alternative_host_ids" class="zvc-hacking-select">
						<option value=""><?php _e( 'Select a Host', 'video-conferencing-with-zoom-api' ); ?></option>
						<?php
						foreach ( $users->users as $user ) :
							$user_found = false;
							if ( in_array( $user->email, $option_alternative_hosts ) ) {
								$user_found = true;
							}
							if($user->id == $authorHost){
								$user_found = true;
							}
							?>
							<option value="<?php echo $user->id; ?>" <?php echo $user_found ? 'selected' : null; ?>><?php echo $user->first_name . ' ( ' . $user->email . ' )'; ?></option>
						<?php endforeach; ?>
					</select>
					</div>
					<div class="pull-left w-50">
						<div class="form-group">
							<label for="actiontozoom">
							<input type="checkbox" name="actiontozoom" value="1" id="actiontozoom">
							<?php _e('Action to Zoom ?', 'webinar'); ?></label>
						</div>
					</div>
					</div>
					<p class="description" id="settings_alternative_hosts"><?php _e( 'Alternative hosts IDs. Multiple value separated by comma.', 'video-conferencing-with-zoom-api' ); ?></p>
					<p class="description" id="settings_alternative_hosts"><strong><?php _e( 'This option is only available for a Licensed level Zoom account user. Check the Prerequisites for this feature <a target="_blank" href="https://support.zoom.us/hc/en-us/articles/208220166-Alternative-host">here</a>', 'video-conferencing-with-zoom-api' ); ?></strong>
						<?php if($start_url): ?>
                                    <br/><span class="view">
                                        <a href="<?php echo $start_url; ?>" rel="permalink" target="_blank"><?php  _e( 'Start Webinar', 'video-conferencing-with-zoom-api' ); ?></a>
                                    </span>
                            <?php endif; ?>
					</p>

					<!-- Show start link -->
					<p>
					<?php 
					if($webinarBeforeTen < $now && $webinarEndTime > $now ){ 
						echo sprintf('Start Url: <a target="_blank" href="%s">Click hre</a>', $webinar_info->start_url);
					}
					?>
					</p>
				</td>
			</tr>
			<tr>
			<th scope="row"><label for="settings_alternative_hosts"><?php _e( 'Participant Link', 'video-conferencing-with-zoom-api' ); ?></label></th>
			<td>
					<p>
					<?php echo sprintf('<a target="_blank" href="%s">Click hre</a>', $webinar_info->join_url); ?>
					</p>
			</td>
			</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="update_webinar" class="button button-primary" value="Update Webinar"></p>
	</form>
</div>
