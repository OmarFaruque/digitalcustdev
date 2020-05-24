<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
video_conferencing_zoom_api_show_like_popup();

$users = cstm_video_conferencing_zoom_api_get_user_transients();

// echo 'Users <pre>';
// print_r($users);
// echo '</pre>';

?>
<div id="zvc-cover" style="display: none;"></div>
<div class="wrap">
	<h1><?php _e( 'Users', 'video-conferencing-with-zoom-api' ); ?></h1> <a href="?page=zoom-video-conferencing-list-users&flush=true"><?php _e( 'Flush User Cache', 'video-conferencing-with-zoom-api' ); ?></a>
	<h2><?php _e( 'ATTENTION: Zoom Account Prerequisites for User Management', 'video-conferencing-with-zoom-api' ); ?></h2>
	
	<ol>
		<li><?php _e( 'Free with Credit Card, Pro, Business, Education, or Enterprise account. Make sure you are able to access <a target="_blank" href="https://zoom.us/account/user">this area</a> if you are using a Free Zoom account.', 'video-conferencing-with-zoom-api' ); ?></li>
		<li><?php _e( 'Owner or admin privileges.', 'video-conferencing-with-zoom-api' ); ?></li>
	</ol>
	
	<div id="message" style="display:none;" class="notice notice-success show_on_user_delete_success"></div>
	<div class="message">
		<?php
		$message = self::get_message();
		if ( isset( $message ) && ! empty( $message ) ) {
			echo $message;
		}
		?>
	</div>

	<div class="zvc_listing_table omar">
		<table id="zvc_users_list_table" class="display om1" width="100%">
			<thead>
			<tr>
				<th class="zvc-text-left"><?php _e( 'SN', 'video-conferencing-with-zoom-api' ); ?></th>
				<th class="zvc-text-left"><?php _e( 'Host ID', 'video-conferencing-with-zoom-api' ); ?></th>
				<th class="zvc-text-left"><?php _e( 'Email', 'video-conferencing-with-zoom-api' ); ?></th>
				<th class="zvc-text-left"><?php _e( 'Name', 'video-conferencing-with-zoom-api' ); ?></th>
				<th class="zvc-text-left"><?php _e( 'Syncranization date', 'video-conferencing-with-zoom-api' ); ?></th>
				<th class="zvc-text-left"><?php _e( 'Status', 'video-conferencing-with-zoom-api' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
			$count = 1;
			if ( ! empty( $users->users ) ) {
				foreach ( $users->users as $user ) {
					?>
					<tr>
						<td><?php echo $count ++; ?></td>
						<td><?php echo $user->id; ?></td>
						<td><?php echo $user->email; ?></td>
						<td><?php echo $user->first_name . ' ' . $user->last_name; ?></td>
						<td><?php echo ! empty( $user->created_at ) ? date( 'F j, Y, g:i a', strtotime( $user->created_at ) ) : 'N/A'; ?></td>
						<div id="zvc_getting_user_info" style="display:none;">
							<div class="zvc_getting_user_info_content"></div>
						</div>
						<td><?php echo $user->status;  ?></td>
					</tr>
					<?php
				}
			}
			?>
			</tbody>
		</table>
	</div>
</div>
