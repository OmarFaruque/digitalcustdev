<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;}

video_conferencing_zoom_api_show_like_popup(); ?>
<div class="wrap">
	<h1><?php _e( 'Reports', 'video-conferencing-with-zoom-api' ); ?></h1>
	
	<h2><?php _e( 'ATTENTION: Zoom Account & Setting Prerequisites for Reports Section', 'video-conferencing-with-zoom-api' ); ?></h2>
	
	<ol>
		<li><?php _e( 'Pro, Business, Enterprise, Education, or API Account. Check more <a target="_blank" href="https://support.zoom.us/hc/en-us/articles/201363173-Account-Types">here</a>.', 'video-conferencing-with-zoom-api' ); ?></li>
		<li><?php _e( 'Account owner or admin permissions to access Usage Reports for all users.', 'video-conferencing-with-zoom-api' ); ?></li>
		<li><?php _e( 'Account Owner or a user is given the User activities reports <a target="_blank" href="https://support.zoom.us/hc/en-us/articles/115001078646">role</a>.', 'video-conferencing-with-zoom-api' ); ?></li>
	</ol>
		
	<h3 class="nav-tab-wrapper">
		<a href="?page=zoom-video-conferencing-reports&tab=zoom_daily_report" class="nav-tab <?php echo $active_tab == 'zoom_daily_report' ? 'nav-tab-active' : ''; ?>"><?php _e( '1. Daily Report', 'video-conferencing-with-zoom-api' ); ?></a>
		<a href="?page=zoom-video-conferencing-reports&tab=zoom_acount_report" class="nav-tab <?php echo $active_tab == 'zoom_acount_report' ? 'nav-tab-active' : ''; ?>"><?php _e( '2. Account Report', 'video-conferencing-with-zoom-api' ); ?></a>
	</h3>

	<?php if ( $active_tab == 'zoom_daily_report' ) : ?>
		<?php
		$result = zvc_reports()->get_daily_report_html();
		if ( isset( $_POST['zoom_check_month_year'] ) && empty( $_POST['zoom_month_year'] ) ) {
			?>
			<div id="message" class="notice notice-error">
				<?php if ( isset( $result->error ) ) { ?>
					<p><?php echo $result->error->message; ?></p>
				<?php } else { ?>
					<p><?php echo $result; ?></p>
				<?php } ?>
			</div>
		<?php } ?>
		<div class="zoom_dateinput_field">
			<form action="?page=zoom-video-conferencing-reports" class="zvc_daily_reports_check_form" method="POST">
				<label><?php _e( 'Enter the date to check:', 'video-conferencing-with-zoom-api' ); ?></label>
				<input name="zoom_month_year" id="reports_date"/>
				<input type="submit" name="zoom_check_month_year" value="Check">
			</form>
		</div>
		<table class="wp-list-table widefat fixed striped posts">
			<thead>
			<tr>
				<th><span><?php _e( 'Date', 'video-conferencing-with-zoom-api' ); ?></span></th>
				<th><span><?php _e( 'Meetings', 'video-conferencing-with-zoom-api' ); ?></span></th>
				<th><span><?php _e( 'New Users', 'video-conferencing-with-zoom-api' ); ?></span></th>
				<th><span><?php _e( 'Participants', 'video-conferencing-with-zoom-api' ); ?></span></th>
				<th><span><?php _e( 'Meeting Minutes', 'video-conferencing-with-zoom-api' ); ?></span></th>
			</tr>
			</thead>
			<tbody id="the-list">
			<?php
			if ( isset( $result->dates ) ) {
				$count = count( $result->dates );
				foreach ( $result->dates as $date ) {
					?>
					<tr>
						<td><?php echo date( 'F j, Y', strtotime( $date->date ) ); ?></td>
						<td><?php echo ( $date->meetings > 0 ) ? '<strong style="color: #4300FF; font-size: 16px;">' . $date->meetings . '</strong>' : '-'; ?></td>
						<td><?php echo ( $date->new_users > 0 ) ? '<strong style="color:#00A1B5; font-size: 16px;">' . $date->new_users . '</strong>' : '-'; ?></td>
						<td><?php echo ( $date->participants > 0 ) ? '<strong style="color:#00AF00; font-size: 16px;">' . $date->participants . '</strong>' : '-'; ?></td>
						<td><?php echo ( $date->meeting_minutes > 0 ) ? '<strong style="color:red; font-size: 16px;">' . $date->meeting_minutes . '</strong>' : '-'; ?></td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="5"><?php _e( 'Select a Date to Check...' ); ?></td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php
	elseif ( $active_tab == 'zoom_acount_report' ) :
		$meetings = zvc_reports()->get_account_report_html();

		if ( isset( $_POST['zoom_check_account_info'] ) ) {
			if ( empty( $_POST['zoom_account_from'] ) || empty( $_POST['zoom_account_to'] ) ) {
				?>
				<div id="message" class="notice notice-error">
					<?php if ( isset( $result->error ) ) { ?>
						<p><?php echo $result->error->message; ?></p>
					<?php } else { ?>
						<p><?php echo $result; ?></p>
					<?php } ?>
				</div>
			<?php } else { ?>
				<div id="message" class="notice notice-success">
					<ul class="zoom_acount_lists">
						<li><?php _e('Report Generated','video-conferencing-with-zoom-api') ?> </li>
					</ul>
				</div>
				<?php
			}
		}
		?>
		<div class="zoom_dateinput_field">
			<p><?php _e( 'Get account report for a specified period.', 'video-conferencing-with-zoom-api' ); ?> <a onclick="window.print();" href="javascript:void(0);"><?php _e( 'Print', 'video-conferencing-with-zoom-api' ); ?></a></p>
			<form action="?page=zoom-video-conferencing-reports&tab=zoom_acount_report" class="zvc_accounts_reports_check_form" method="POST">
				<label><?php _e( 'From', 'video-conferencing-with-zoom-api' ); ?></label>
				<input name="zoom_account_from" class="zoom_account_datepicker"/>
				<label><?php _e( 'To', 'video-conferencing-with-zoom-api' ); ?></label>
				<input name="zoom_account_to" class="zoom_account_datepicker"/>
				<input type="submit" name="zoom_check_account_info" value="Check">
			</form>
		</div>
		<table class="wp-list-table widefat fixed striped posts">
			<thead>
			<tr>
				<th><span><?php _e( 'By', 'video-conferencing-with-zoom-api' ); ?></span></th>
				<th><span><?php _e( 'Meeting Topic', 'video-conferencing-with-zoom-api' ); ?></span></th>
				<th><span><?php _e( 'Total Participants', 'video-conferencing-with-zoom-api' ); ?></span></th>
				<th><span><?php _e( 'Total Meeting Minutes', 'video-conferencing-with-zoom-api' ); ?></span></th>
				<th><span><?php _e( 'Meeting Start Time', 'video-conferencing-with-zoom-api' ); ?></span></th>
				<th><span><?php _e( 'Meeting End Time', 'video-conferencing-with-zoom-api' ); ?></span></th>
			</tr>
			</thead>
			<tbody id="the-list">
				<?php 
				if(isset($_POST['zoom_check_account_info'])){
					foreach($meetings as $meeting){?>
					<tr>
					<td><?php echo $meeting->user_name ?></td>
					<td><?php echo $meeting->topic ?></td>
					<td><?php echo $meeting->participants_count ?></td>
					<td><?php echo $meeting->total_minutes ?></td>
					<td><?php echo $meeting->start_time; ?></td>
					<td><?php echo $meeting->end_time; ?></td>
					</tr>
					<?php
					}
				}
				else{
					echo "<tr>Please input data to View Report";
				}
				?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
