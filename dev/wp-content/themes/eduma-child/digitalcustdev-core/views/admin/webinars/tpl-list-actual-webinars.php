<?php
/**
 * @author Deepen.
 * @created_on 7/16/19
 */

//Check if any transient by name is available
$users = video_conferencing_zoom_api_get_user_transients();

if ( isset( $_GET['host_id'] ) ) {
	$encoded_meetings = dcd_zoom_conference()->listWebinar( $_GET['host_id'] );
    $decoded_meetings = json_decode( $encoded_meetings );
    
    // echo '<pre>';
    // print_r($decoded_meetings);
    // echo '</pre>';
	$webinars         = false;
	if ( ! empty( $decoded_meetings ) ) {
		$webinars = $decoded_meetings->webinars;
	}
}
?>
<div class="wrap">
    <h2><?php _e( "Webinars", "video-conferencing-with-zoom-api" ); ?></h2>
	<?php $get_host_id = isset( $_GET['host_id'] ) ? $_GET['host_id'] : null; ?>
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
        <table id="zvc_meetings_list_table" class="display schub-table-display" width="100%">
            <thead>
            <tr>
                <th class="zvc-text-left"><?php _e( 'Meeting ID', 'video-conferencing-with-zoom-api' ); ?></th>
                <th class="zvc-text-left"><?php _e( 'Topic', 'video-conferencing-with-zoom-api' ); ?></th>
                <th class="zvc-text-left" class="zvc-text-left"><?php _e( 'Start Time', 'video-conferencing-with-zoom-api' ); ?></th>
                <th class="zvc-text-left"><?php _e( 'Host ID', 'video-conferencing-with-zoom-api' ); ?></th>
                <th class="zvc-text-left"><?php _e( 'Created On', 'video-conferencing-with-zoom-api' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php
			if ( ! empty( $webinars ) ) {
				foreach ( $webinars as $webinar ) {
					?>
                    <tr>
                        <td><?php echo $webinar->id; ?></td>
                        <td>
                            <a href="admin.php?page=zoom-video-conferencing-webinars&edit=<?php echo $webinar->id; ?>&host_id=<?php echo $webinar->host_id; ?>"><?php  echo $webinar->topic; ?></a>
                            <div class="row-actions s-webinar">
                                <span class="view">
                                <a href="<?php echo ! empty( $webinar->start_url ) ? $webinar->start_url : $webinar->join_url; ?>" rel="permalink" target="_blank"><?php _e( 'Start Meeting', 'video-conferencing-with-zoom-api' ); ?></a></span>
                            </div>
                        </td>
                        <td><?php
							$timezone = ! empty( $webinar->timezone ) ? $webinar->timezone : "America/Los_Angeles";
							$tz       = new DateTimeZone( $timezone );
							$date     = new DateTime( $webinar->start_time );
							$date->setTimezone( $tz );
							echo $date->format( 'F j, Y, g:i a ( e )' );
							?></td>
                        <td><?php echo $webinar->host_id; ?></td>
                        <td><?php echo date( 'F j, Y, g:i a', strtotime( $webinar->created_at ) ); ?></td>
                    </tr>
					<?php
				}
			} ?>
            </tbody>
        </table>
    </div>
</div>
