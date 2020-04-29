<?php

/**
 * Registering the Pages Here
 *
 * @since   2.0.0
 * @author  Deepen
 */
class Zoom_Video_Conferencing_Admin_Views {

	public static $message = '';
	public $settings;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'zoom_video_conference_menus' ) );
	}

	/**
	 * Register Menus
	 *
	 * @since   1.0.0
	 * @changes in CodeBase
	 * @author  Deepen Bajracharya <dpen.connectify@gmail.com>
	 */
	public function zoom_video_conference_menus() {
		add_menu_page( 'Zoom', 'Zoom Meetings', 'manage_options', 'zoom-video-conferencing', array( 'Zoom_Video_Conferencing_Admin_Meetings', 'list_meetings' ), 'dashicons-video-alt2', 5 );
		if ( get_option( 'zoom_api_key' ) && get_option( 'zoom_api_secret' ) ) {
				add_submenu_page( 'zoom-video-conferencing', 'Meeting', __( 'Add Meeting', 'video-conferencing-with-zoom-api' ), 'manage_options', 'zoom-video-conferencing-add-meeting', array( 'Zoom_Video_Conferencing_Admin_Meetings', 'add_meeting' ) );
				add_submenu_page( 'zoom-video-conferencing', 'Users', __( 'Users', 'video-conferencing-with-zoom-api' ), 'manage_options', 'zoom-video-conferencing-list-users', array( 'Zoom_Video_Conferencing_Admin_Users', 'list_users' ) );
				add_submenu_page( 'zoom-video-conferencing', 'Add Users', __( 'Add Users', 'video-conferencing-with-zoom-api' ), 'manage_options', 'zoom-video-conferencing-add-users', array( 'Zoom_Video_Conferencing_Admin_Users', 'add_zoom_users' ) );
				add_submenu_page( 'zoom-video-conferencing', 'Reports', __( 'Reports', 'video-conferencing-with-zoom-api' ), 'manage_options', 'zoom-video-conferencing-reports', array( 'Zoom_Video_Conferencing_Reports', 'zoom_reports' ) );
				add_submenu_page(
					'zoom-video-conferencing',
					__( 'Assign Host ID', 'video-conferencing-with-zoom-plank' ),
					__( 'Assign Host ID', 'video-conferencing-with-zoom-plank' ),
					'manage_options',
					'zoom-video-conferencing-host-id-assign',
					array( 'Zoom_Video_Conferencing_Admin_Users', 'assign_host_id' )
				);
		}
		add_submenu_page( 'zoom-video-conferencing', 'Settings', __( 'Settings', 'video-conferencing-with-zoom-api' ), 'manage_options', 'zoom-video-conferencing-settings', array( $this, 'zoom_video_conference_api_zoom_settings' ) );
		add_submenu_page( 'zoom-video-conferencing', 'Subscribe', __( 'Subscribe to Updates', 'video-conferencing-with-zoom-api' ), 'manage_options', 'zoom-video-conferencing-subscribe_updates', array( $this, 'zoom_video_conference_api_zoom_subscribe_updates' ) );
	}


	/**
	 * Zoom Settings View File
	 *
	 * @since   1.0.0
	 * @changes in CodeBase
	 * @author  Deepen Bajracharya <dpen.connectify@gmail.com>
	 */


	public function zoom_video_conference_api_zoom_subscribe_updates() {
		echo '<!-- Begin Mailchimp Signup Form -->
	 <link href="//cdn-images.mailchimp.com/embedcode/classic-10_7.css" rel="stylesheet" type="text/css">
	 <style type="text/css">
		 #mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
		 /* Add your own Mailchimp form style overrides in your site stylesheet or in this style block.
			We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
	 </style>
	 <style type="text/css">
		 #mc-embedded-subscribe-form input[type=checkbox]{display: inline; width: auto;margin-right: 10px;}
		 #mergeRow-gdpr {margin-top: 20px;}
		 #mergeRow-gdpr fieldset label {font-weight: normal;}
		 #mc-embedded-subscribe-form .mc_fieldset{border:none;min-height: 0px;padding-bottom:0px;}
	 </style>
	 <div id="mc_embed_signup">
	 <form action="https://elearningevolve.us3.list-manage.com/subscribe/post?u=0b76eeb4e810cec265e082dca&amp;id=5f1f69bf5e" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
		 <div id="mc_embed_signup_scroll">
		 <img src="https://elearningevolve.com/wp-content/uploads/2019/06/logo.png">
		 <h2>Subscribe for Updates.</h2>
	 <div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
	 <div class="mc-field-group">
		 <label for="mce-EMAIL">Email Address  <span class="asterisk">*</span>
	 </label>
		 <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
	 </div>
	 <div class="mc-field-group">
		 <label for="mce-FNAME">First Name  <span class="asterisk">*</span>
	 </label>
		 <input type="text" value="" name="FNAME" class="required" id="mce-FNAME">
	 </div>
	 <div class="mc-field-group">
		 <label for="mce-LNAME">Last Name </label>
		 <input type="text" value="" name="LNAME" class="" id="mce-LNAME">
	 </div>
	 <div class="mc-field-group input-group">
		 <strong>Subscription </strong>
		 <ul><li><input type="checkbox" value="1" name="group[26789][1]" id="mce-group[26789]-26789-0"><label for="mce-group[26789]-26789-0">Zoom WordPress Integration Updates</label></li>
	 <li><input type="checkbox" value="2" name="group[26789][2]" id="mce-group[26789]-26789-1"><label for="mce-group[26789]-26789-1">Zoom WordPress Integration Beta Testers</label></li>
	 <li><input type="checkbox" value="4" name="group[26789][4]" id="mce-group[26789]-26789-2"><label for="mce-group[26789]-26789-2">LearnDash Student Voice Updates</label></li>
	 <li><input type="checkbox" value="8" name="group[26789][8]" id="mce-group[26789]-26789-3"><label for="mce-group[26789]-26789-3">Development Service Updates</label></li>
	 <li><input type="checkbox" value="16" name="group[26789][16]" id="mce-group[26789]-26789-4"><label for="mce-group[26789]-26789-4">Published Blog Posts</label></li>
	 </ul>
	 </div>
	 <div id="mergeRow-gdpr" class="mergeRow gdpr-mergeRow content__gdprBlock mc-field-group">
		 <div class="content__gdpr">
			 <label>Marketing Permissions</label>
			 <p>Please select all the ways you would like to hear from eLearning evolve:</p>
			 <fieldset class="mc_fieldset gdprRequired mc-field-group" name="interestgroup_field">
			 <label class="checkbox subfield" for="gdpr_35845"><input type="checkbox" id="gdpr_35845" name="gdpr[35845]" value="Y" class="av-checkbox "><span>Email</span> </label>
			 </fieldset>
			 <p>You can unsubscribe at any time by clicking the link in the footer of our emails. For information about our privacy practices, please visit our website.</p>
		 </div>
		 <div class="content__gdprLegal">
			 <p>We use Mailchimp as our marketing platform. By clicking below to subscribe, you acknowledge that your information will be transferred to Mailchimp for processing. <a href="https://mailchimp.com/legal/" target="_blank">Learn more about Mailchimp\'s privacy practices here.</a></p>
		 </div>
	 </div>
		 <div id="mce-responses" class="clear">
			 <div class="response" id="mce-error-response" style="display:none"></div>
			 <div class="response" id="mce-success-response" style="display:none"></div>
		 </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
		 <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_0b76eeb4e810cec265e082dca_5f1f69bf5e" tabindex="-1" value=""></div>
		 <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
		 </div>
	 </form>
	 </div>
	 <script type=\'text/javascript\' src=\'//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js\'></script><script type=\'text/javascript\'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]=\'EMAIL\';ftypes[0]=\'email\';fnames[1]=\'FNAME\';ftypes[1]=\'text\';fnames[2]=\'LNAME\';ftypes[2]=\'text\';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
	 <!--End mc_embed_signup-->';
	}


	public function zoom_video_conference_api_zoom_settings() {
		wp_enqueue_script( 'video-conferencing-with-zoom-api-js' );
		wp_enqueue_style( 'video-conferencing-with-zoom-api' );

		if ( isset( $_POST['save_zoom_settings'] ) ) {
			//Nonce
			check_admin_referer( '_zoom_settings_update_nonce_action', '_zoom_settings_nonce' );
			$zoom_api_key                   = filter_input( INPUT_POST, 'zoom_api_key' );
			$zoom_api_secret                = filter_input( INPUT_POST, 'zoom_api_secret' );
			$vanity_url                     = esc_url( filter_input( INPUT_POST, 'vanity_url' ) );
			$zoom_alternative_join          = esc_html( filter_input( INPUT_POST, 'zoom_alternative_join' ) );
			$zoom_btn_css_class             = esc_html( filter_input( INPUT_POST, 'zoom_btn_css_class' ) );
			$zoom_help_text_disable         = filter_input( INPUT_POST, 'zoom_help_text_disable' );
			$zoom_compatiblity_text_disable = filter_input( INPUT_POST, 'zoom_compatiblity_text_disable' );
			$zoom_width_window              = filter_input( INPUT_POST, 'zoom_width_meeting_window' );
			$zoom_height_window             = filter_input( INPUT_POST, 'zoom_height_meeting_window' );
			$zoom_ssl_message               = filter_input( INPUT_POST, 'zoom_ssl_message' );

			update_option( 'zoom_api_key', $zoom_api_key );
			update_option( 'zoom_api_secret', $zoom_api_secret );
			update_option( 'zoom_vanity_url', $vanity_url );
			update_option( 'zoom_alternative_join', $zoom_alternative_join );
			update_option( 'zoom_btn_css_class', $zoom_btn_css_class );
			update_option( 'zoom_help_text_disable', $zoom_help_text_disable );
			update_option( 'zoom_compatiblity_text_disable', $zoom_compatiblity_text_disable );
			update_option( 'zoom_width_meeting_window', $zoom_width_window );
			update_option( 'zoom_height_meeting_window', $zoom_height_window );
			update_option( 'zoom_ssl_message', $zoom_ssl_message );

			//After user has been created delete this transient in order to fetch latest Data.
			delete_transient( '_zvc_user_lists' );

			if ( get_option( 'zoom_api_key' ) && get_option( 'zoom_api_secret' ) ) {
				$encoded_users = zoom_conference()->listUsers();
				$decoded       = json_decode( $encoded_users );
				
				if ( $decoded && isset( $decoded->code ) ) {
					$code_family = substr( $decoded->code, 0, 1 );
					if ( $code_family != 2 ) {
						?>
						<div id="message" class="notice notice-error">
							<p><strong><?php _e( 'ZOOM API ERROR: ', 'video-conferencing-with-zoom-api' ); ?></strong>
								<?php esc_html_e( $decoded->message ); ?><br />
							<?php if( $decoded->code == 124 ): ?>
								<?php _e( 'This means Something is wrong with the entered API KEYS, Please follow this <a target="_blank" href="https://elearningevolve.com/blog/zoom-api-keys">guide</a> to correctly generate the API key values added below.', 'video-conferencing-with-zoom-api' ); ?>
							<?php else: ?>
								<?php _e( 'This means Something is wrong with calling the Zoom API with your configuration, please contact <a target="_blank" href="https://support.zoom.us/">Zoom support</a> or report on <a target="_blank" href="https://devforum.zoom.us/">Zoom forum</a> with the above error message for further assistance.', 'video-conferencing-with-zoom-api' ); ?>
							<?php endif; ?>
							</p>
						</div>
						<?php
					} else {
						?>
						<div id="message" class="notice notice-success is-dismissible">
							<p><?php _e( 'Successfully Updated. Please refresh this page.', 'video-conferencing-with-zoom-api' ); ?></p>
							<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'video-conferencing-with-zoom-api' ); ?></span></button>
						</div>
						<?php
					}
				}
			}
		}

		//Get Template
		require_once ZOOM_VIDEO_CONFERENCE_PLUGIN_VIEWS_PATH . '/admin/tpl-settings.php';
	}

	static function get_message() {
		return self::$message;
	}

	static function set_message( $class, $message ) {
		self::$message = '<div class=' . $class . '><p>' . $message . '</p></div>';
	}
}

new Zoom_Video_Conferencing_Admin_Views();
