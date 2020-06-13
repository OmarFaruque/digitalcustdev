<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//Defining Varaibles
$zoom_api_key                   = get_option( 'zoom_api_key' );
$zoom_api_secret                = get_option( 'zoom_api_secret' );
$zoom_url_enable                = get_option( 'zoom_url_enable' );
$zoom_vanity_url                = get_option( 'zoom_vanity_url' );
$zoom_alternative_join          = get_option( 'zoom_alternative_join' );
$zoom_help_text_disable         = get_option( 'zoom_help_text_disable' );
$zoom_compatiblity_text_disable = get_option( 'zoom_compatiblity_text_disable' );
$zoom_subscribe_link            = get_option( 'zoom_subscribe_link' );

?>
<div class="wrap">
	<h1><?php _e( 'Settings', 'video-conferencing-with-zoom-api' ); ?></h1>
	
	<?php video_conferencing_zoom_api_show_like_popup(); ?>
	<div class="zvc-row">
		<div class="zvc-position-floater-left">
			<form action="?page=cst-zoom-video-conferencing-settings" method="POST">
				<?php wp_nonce_field( '_zoom_settings_update_nonce_action', '_zoom_settings_nonce' ); ?>
				<h2><strong><?php _e( 'Please follow', 'video-conferencing-with-zoom-api' ); ?> <a target="_blank" href="https://elearningevolve.com/blog/zoom-api-keys/"><?php _e( 'this guide', 'video-conferencing-with-zoom-api' ); ?> </a> <?php _e( 'to generate the below API values from your Zoom account', 'video-conferencing-with-zoom-api' ); ?></strong></h2>
				<table class="form-table">
					<tbody>
						<tr>
							<th><label><?php _e( 'API Key', 'video-conferencing-with-zoom-api' ); ?></label></th>
							<td><input type="password" style="width: 400px;" name="zoom_api_key" id="zoom_api_key" value="<?php echo ! empty( $zoom_api_key ) ? esc_html( $zoom_api_key ) : ''; ?>"> <a href="javascript:void(0);" class="toggle-api">Show</a></td>
						</tr>
						<tr>
							<th><label><?php _e( 'API Secret Key', 'video-conferencing-with-zoom-api' ); ?></label></th>
							<td><input type="password" style="width: 400px;" name="zoom_api_secret" id="zoom_api_secret" value="<?php echo ! empty( $zoom_api_secret ) ? esc_html( $zoom_api_secret ) : ''; ?>"> <a href="javascript:void(0);" class="toggle-secret">Show</a></td>
						</tr>
						<tr>
							<th><label><?php _e( 'Vanity URL', 'video-conferencing-with-zoom-api' ); ?></label></th>
							<td>
								<input type="url" name="vanity_url" class="regular-text" value="<?php echo ( $zoom_vanity_url ) ? esc_attr( $zoom_vanity_url ) : ''; ?>">
								<p class="description">If you are using Zoom Vanity URL then please insert it here else leave it empty.</p>
								<a href="https://support.zoom.us/hc/en-us/articles/215062646-Guidelines-for-Vanity-URL-Requests">Read more about Vanity URLs</a>
							</td>
						</tr>
						<tr class="alternative-btn-text">
							<th><label><?php _e( 'Text for alternative meeting join button', 'video-conferencing-with-zoom-api' ); ?></label></th>
							<td>
								<input type="text" name="zoom_alternative_join" class="regular-text" value="<?php echo ( $zoom_alternative_join ) ? esc_html( $zoom_alternative_join ) : ''; ?>" placeholder="JOIN MEETING VIA ALTERNATIVE WAY">
								<p class="description"><?php _e( 'You can change the text on alternative meeting join button displayed on meeting page with this field.', 'video-conferencing-with-zoom-api' ); ?></p>
							</td>
						</tr>
						<tr class="zoom-btn-class">
							<th><label><?php _e( 'CSS classes to add on buttons', 'video-conferencing-with-zoom-api' ); ?></label></th>
							<td>
								<input type="text" name="zoom_btn_css_class" class="regular-text" value="<?php echo ( $zoom_btn_css_class ) ? esc_html( $zoom_btn_css_class ) : ''; ?>" placeholder="my-theme-btn-class my-theme-btn-class2 my-theme-btn-class3">
								<p class="description"><?php _e( 'You can add CSS classes separated by a SINGLE SPACE used in your theme for button styling here.', 'video-conferencing-with-zoom-api' ); ?></p>
							</td>
						</tr>
						<tr>
							<th><label><?php _e( 'Hide Join via App', 'video-conferencing-with-zoom-api' ); ?></label></th>
							<td>
								<select name="zoom_help_text_disable">
									<option <?php echo ( $zoom_help_text_disable == 0 ) ? 'selected="selected"' : ''; ?> name="no" value="0"><?php _e( 'No', 'video-conferencing-with-zoom-api' ); ?></option>
									<option <?php echo ( $zoom_help_text_disable == 1 ) ? 'selected="selected"' : ''; ?> name="yes" value="1"><?php _e( 'Yes', 'video-conferencing-with-zoom-api' ); ?></option>
								</select>
								<p class="description">Show/Hide Join via Zoom App above the Zoom Window on the page where you place Zoom shortcode.</p>
							</td>
						</tr>
						<tr>
							<th><label><?php _e( 'Hide alternative meeting join button', 'video-conferencing-with-zoom-api' ); ?></label></th>
							<td>
								<select name="zoom_compatiblity_text_disable">
									<option <?php echo ( $zoom_compatiblity_text_disable == 0 ) ? 'selected="selected"' : ''; ?> name="no" value="0"><?php _e( 'No', 'video-conferencing-with-zoom-api' ); ?></option>
									<option <?php echo ( $zoom_compatiblity_text_disable == 1 ) ? 'selected="selected"' : ''; ?> name="yes" value="1"><?php _e( 'Yes', 'video-conferencing-with-zoom-api' ); ?></option>
								</select>
								<p class="description">Show/Hide alternative meeting join button above the Zoom Window on the page where you place the shortcode.</p>
							</td>
						</tr>
						<tr>
							<th><label><?php _e( 'Hide Subscribe Submenu', 'video-conferencing-with-zoom-api' ); ?></label></th>
							<td>
								<select name="zoom_subscribe_link">
									<option <?php echo ( $zoom_subscribe_link == 0 ) ? 'selected="selected"' : ''; ?> name="no" value="0"><?php _e( 'No', 'video-conferencing-with-zoom-api' ); ?></option>
								<option <?php echo ( $zoom_subscribe_link == 1 ) ? 'selected="selected"' : ''; ?> name="yes" value="1"><?php _e( 'Yes', 'video-conferencing-with-zoom-api' ); ?></option>
								</select>
								<p class="description">Show/Hide Subscribe Submenu in plugin.</p>
							</td>
						</tr>
						<tr>
							<th><label><?php _e( 'Change Dimensions of Zoom Meeting Window', 'video-conferencing-with-zoom-api' ); ?></label></th>
							<td>
								<input type="number" name="zoom_width_meeting_window" placeholder="<?php _e( 'Width', 'video-conferencing-with-zoom-api' ); ?>"  value="<?php echo esc_attr_e( get_option( 'zoom_width_meeting_window' ) ); ?>">
								<input type="number" name="zoom_height_meeting_window" placeholder="<?php _e( 'Height', 'video-conferencing-with-zoom-api' ); ?>" value="<?php echo esc_attr_e( get_option( 'zoom_height_meeting_window' ) ); ?>">
								<p class="description">This will update the Dimensions of your Meeting Window.</p>
								<p class="description"><strong><?php _e( 'Note: It is recommended to use the default width and height set by the plugin as it is tested on all the screen sizes. If you want to set your custom width and height please make sure to not set the width below 500 and height below 600', 'video-conferencing-with-zoom-api' ); ?><strong></p>
							</td>
						</tr>
						<tr>
							<th><label><?php _e( 'Hide SSL Alert on Front-End for Admin', 'video-conferencing-with-zoom-api' ); ?></label></th>
							<td>
								<select name="zoom_ssl_message">
									<option <?php echo ( $zoom_ssl_message == 0 ) ? 'selected="selected"' : ''; ?> name="no" value="0"><?php _e( 'No', 'video-conferencing-with-zoom-api' ); ?></option>
									<option <?php echo ( $zoom_ssl_message == 1 ) ? 'selected="selected"' : ''; ?> name="yes" value="1"><?php _e( 'Yes', 'video-conferencing-with-zoom-api' ); ?></option>
								</select>
								<p class="description">Show/Hide SSL Message on Front-End.</p>
							</td>
						</tr>

						<!-- Master Hoster -->
						<tr>
							<th><label><?php _e( 'Master Host', 'video-conferencing-with-zoom-api' ); ?></label></th>
							<td class="zoom_master_host">
								<select name="zoom_master_host" class="zvc-hacking-select">
									<option value=""><?php _e( 'Select a Host', 'video-conferencing-with-zoom-api' ); ?></option>
									<?php
									$users = cstm_video_conferencing_zoom_api_get_user_transients();
									foreach ( $users->users as $user ) :
										// echo '<pre>';
										// print_r($user);
										// echo '</pre>';
										$user_found = get_option('zoom_master_host');
																				?>
										<option value="<?php echo $user->id; ?>" <?php echo $user_found == $user->id ? 'selected' : null; ?>><?php echo $user->first_name . ' ( ' . $user->email . ' )'; ?></option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php _e('Master host', 'webinar'); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit"><input type="submit" name="save_zoom_settings" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'inactive-logout' ); ?>"></p>
			</form>

			<hr>
			<section class="zoom-api-example-section">
				<h3>Using Shortcode Example</h3>
				<p>Below are few examples of how you can add shortcodes manually into your posts.</p>

				<div class="zoom-api-basic-usage">
					<h3>Basic Usage:</h3>
					<code>[zoom_api_link meeting_id="123456789" class="zoom-meeting-window" id="zoom-meeting-window" title="" countdown-title="Meeting starts in" width="1000" height="500"]</code>
					<div class="zoom-api-basic-usage-description">
						<label>Parameters:</label>
						<ul>
							<li><strong>meeting_id</strong> : Your meeting ID on meeting list page.</li>
							<li><strong>class</strong> : CSS class</li>
							<li><strong>id</strong> : CSS ID</li>
							<li><strong>title</strong> : Title of the zoom window.</li>
							<li><strong>width</strong> : Width of the zoom window, please note that this will override the global width set in settings.</li>
							<li><strong>height</strong> : Height of the zoom window, please note that this will override the global height set in settings.</li>
							<li><strong>countdown-title</strong> : Meeting starts in.</li>
						</ul>
					</div>
				</div>
			</section>
		</div>

		<div class="zvc-position-floater-right">
			<ul class="zvc-information-sec">
				
				<li><strong><?php _e( 'IMPORTANT', 'video-conferencing-with-zoom-api' ); ?>: </strong><br /> <?php _e( 'Please note that we do not guarantee to provide individual/dedicated support for the Free version of the plugin. We only offer support for bug fixes in the plugin (i.e not specific to your site but general issues for all plugin users)', 'video-conferencing-with-zoom-api' ); ?>
				<li><a target="_blank" href="http://elearningevolve.com/products/wordpress-zoom-integration#video-guide"><?php _e( 'Plugin guide', 'video-conferencing-with-zoom-api' ); ?></a></li>
				<li><a target="_blank" href="http://elearningevolve.com/contact"><?php _e( 'Report a bug in plugin', 'video-conferencing-with-zoom-api' ); ?></a></li>
				Please add subject line "Zoom WordPress Plugin Bug Report"
			</ul>
			<div class="zvc-information-sec">
				<h3>Did you know?</h3>
				<p>You can improve your <a target="_blank" href="https://learndash.idevaffiliate.com/544.html">LearnDash</a> course by encouraging your students to become active contributors on your courses via my <a target="_blank" href="https://elearningevolve.com/products/learndash-student-voice/">LearnDash Student Voice Addon </a></p>
			</div>
		</div>
	</div>
	<div class="zvc-position-clear"></div>
</div>
