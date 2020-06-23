<?php
use \Firebase\JWT\JWT;
// require WP_PLUGIN_DIR  . '/video-conferencing-with-zoom-api/vendor/autoload.php';


/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://t.me/NSA007
 * @since      1.0.0
 *
 * @package    Webinars
 * @subpackage Webinars/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Webinars
 * @subpackage Webinars/admin
 * @author     Dennis Mwaniki <theguy3474@gmail.com>
 */


class Webinars_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
	public static $message = '';

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->init();

	}
	private function generateJWTKey() {
		$key    = zoom_conference()->zoom_api_key;
		$secret = zoom_conference()->zoom_api_secret;

		$token = array(
			"iss" => $key,
			"exp" => time() + 3600 //60 seconds as suggested
		);

		return JWT::encode( $token, $secret );
	}
	protected function init(){
		// echo 'omar tst omar';
		add_filter( 'learn-press/course-settings-fields/archive', array($this, 'settingsFilters') );

		/* Change course title */
		add_filter( 'display_post_states', array($this, 'ecs_add_post_state'), 10, 2 );
		add_filter( 'manage_lp_course_posts_columns', array( $this, 'manage_course_posts_columns' ) );
		
		//Lesson new column to admin
		add_filter( 'manage_lp_lesson_posts_columns', array( $this, 'manage_lesson_posts_columns' ) );
		add_filter( 'manage_edit-lp_lesson_sortable_columns', array( $this, 'manage_lesson_posts_columns' ) );
		add_action( 'manage_lp_lesson_posts_custom_column' , array($this, 'zoomDateCallbacktoColumn'), 20, 2 );
		// add_action( 'pre_get_posts', 'smashing_posts_orderby' );

		add_filter( 'wp_insert_post_empty_content', array($this, 'allow_empty_post'), 10, 2 );
		add_action( 'admin_menu', array( $this, 'notify_new_course' ) );
		add_action( 'pre_get_posts', array($this, 'course_duration_by_lesson_total_duration') );
		add_action( 'add_meta_boxes', array($this, 'wpdocs_register_meta_boxes') );
		add_filter( 'custom_menu_order', array($this, 'changeAdminSubMenuOrderForLearnPress') );
		add_filter( 'learn_press_admin_tabs_on_pages', array( $this, 'admin_tabs_pages' ), 10, 2 );
		add_filter( 'learn_press_admin_tabs_info', array( $this, 'admin_tab' ), 10, 2 );
		// add_action('restrict_manage_posts', array($this, 'add_post_formats_filter_to_post_administration'));
		// add_action('quick_edit_custom_box',  array($this, 'misha_quick_edit_fields'), 10, 2);
		add_filter('learn-press/payment-settings/general', array($this, 'addFreeEnrollCheckboxToSettingsGeneral'));
		// add_filter('the_content', array($this, 'testcontent'));
		add_filter('learn-press/course-require-enrollment', array($this, 'customRequiredEnroll'));

		// Add countdown to single lession 
		add_filter('embed_oembed_html', array($this, 'addcountdowntoembedvideoforlessionVideo'), 10, 4);
	
		// Course update hook 
		add_action( 'save_post', array( $this, 'courseSaveCallbackFunction' ), 10, 3 );

		add_action( 'post_updated', array($this, 'postUpdateCallback'), 10, 3 );
	
		add_action( 'admin_footer', array($this, 'testFunction') );

		// Add Emall Template to Admin settings for Email
		add_filter('learn-press/email-section-classes', array($this, 'addEmailTemplateForUpdateLession'));
		
		// Load custom email template
		add_action( 'learn-press/register-emails', array( $this, 'custom_emails_setting' ), 20, 2 );
		add_action( 'admin_init', array( $this, 'backward_load_emails' ) );

		//Add Email New Action 
		add_filter('learn-press/email-actions', array($this, 'customizeEmailAction'));
		add_action( 'learn-press/zoom-webinar-lession-update/notification', array( $this, 'trigger' ), 99, 3 );
		
		
	

		// Add host field to learnpress general section 
		add_filter('learn_press_general_settings', array($this, 'addHostCallback'));

		// LearnPress order complete status
		
		add_action('learn_press_payment_complete', array($this, 'completeLearnPressCallback'));
		add_action('learn-press/payment-complete', array($this, 'completeLearnPressCallback'));

		// Schedule Event for webinar notification to user/co-instructor/Admin

		// Override Sub menu 
		add_action( 'admin_menu', array( $this, 'zoom_video_conference_menus' ) );
		
		// CountDown Section 
		add_action( 'learn-press/before-content-item-summary/lp_lesson', array($this, 'learn_press_content_item_lesson_countdown'), 20 );
		

		// add_action('admin_init', array($this, 'tetF'));
	}


	public function get_timezone_offset($remote_tz, $origin_tz = null) {
		if($origin_tz === null) {
			if(!is_string($origin_tz = date_default_timezone_get())) {
				return false; // A UTC timestamp was returned -- bail out!
			}
		}
		$origin_dtz = new DateTimeZone($origin_tz);
		$remote_dtz = new DateTimeZone($remote_tz);
		$origin_dt = new DateTime("now", $origin_dtz);
		$remote_dt = new DateTime("now", $remote_dtz);

		 echo '<pre>';
		 print_r($remote_dt);
		 echo '</pre>';

		 echo 'get offset: ' . $remote_dtz->getOffset($remote_dt) . '<br/>';
		 echo 'get origin offset: ' . $origin_dtz->getOffset($origin_dt) . '<br/>';

		$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
		echo 'offset: ' . $offset . '<br/>';
		return $offset;
	}

	public function tetF(){
		
		$post_id = 18043;

		delete_post_meta( $post_id, 'start_url' );
			$lesson = get_post($post_id);
			$webinar_id = get_post_meta( $post_id, '_webinar_ID', true );
			$timezone   = get_post_meta( $post_id, '_lp_timezone', true );
			$duration = get_post_meta( $sl, '_lp_duration', true );
			$duration = (int)$duration;
			$start_time = get_post_meta( $post_id, '_lp_webinar_when', true );
			
			// UPdate alternative host
			$authorhostid = get_user_meta($author_id, 'user_zoom_hostid', true);
			update_post_meta( $post_id, '_lp_alternative_host', $authorhostid );


			$start_time = str_replace('/', '-', $start_time);
			$start_time = ! empty( $start_time ) ? date( "Y-m-d\TH:i:s", strtotime( $start_time ) ) : date( "Y-m-d\TH:i:s" );

			if ( ! empty( $webinar_id ) ) {

				// Start url for master host 
				$webinarDetails = dcd_zoom_conference()->getZoomWebinarDetails($post_id);
				$webinarDetails = json_decode($webinarDetails);

				echo 'webinar details <pre>';
				print_r($webinarDetails);
				echo '</pre>';

				update_post_meta( $post_id, 'mh_start_link', $webinarDetails->start_url );


				//Create webinar here f
				$postData = array(
					'topic'      => $lesson->post_title,
					'type'       => 5,
					'start_time' => $start_time,
					'duration' 	=> $duration,
					'timezone'   => ! empty( $timezone ) ? $timezone : '',
					'agenda'     => $lesson->post_content,
					'settings'   => array(
						'host_video'        => false,
						'panelists_video'   => true,
						'approval_type'     => 0,
						'registration_type' => 1,
						'auto_recording'    => 'cloud'
					)
				);

				//Updated
				dcd_zoom_conference()->updateWebinar( $webinar_id, $postData );
				$user_id = get_current_user_id();
				do_action( 'learn-press/zoom-update-lession-instructor', $post_id, $user_id );
				do_action( 'learn-press/zoom-update-lession-user', $post_id, $user_id );
				do_action( 'learn-press/zoom-update-lession-admin', $post_id, $user_id );
			}



	}

	public function zoomDateCallbacktoColumn($column, $post_id){
		
		switch ( $column ) {
			case 'meta-zoom_date':
				$when = get_post_meta($post_id, '_lp_webinar_when', true);
				$gmt_time = get_post_meta($post_id, '_lp_webinar_when_gmt', true);
				
				if($when){
					$when = str_replace('/', '-', $when);
					$when = date('Y/m/d H:i', strtotime($when));
					if($gmt_time) $when = date('Y/m/d H:i', strtotime($gmt_time)) . ' (GMT)<br/><hr/>' . $when;
					echo '<span class="'.get_post_meta($post_id, 'zoom_status', true).'">'. $when . '<br/>';
					echo '('. get_post_meta($post_id, '_lp_timezone', true) . ')</span>';

				}else{
					echo '—';
				}
				
			break;
		}
	}


	public function learn_press_content_item_lesson_countdown(){
		learn_press_get_template( 'single-course/lesson-countdown.php' );
	}

	public function zoom_video_conference_menus(){
		global $submenu;
		remove_submenu_page( 'zoom-video-conferencing', 'zoom-video-conferencing-list-users' );
		remove_submenu_page( 'themes.php', 'customize.php?return=/dev/wp-admin/admin.php?page=zoom-video-conferencing-list-users' );
		remove_submenu_page( 'themes.php', 'admin.php?page=zoom-video-conferencing-list-users' );

		
		remove_submenu_page( 'zoom-video-conferencing', 'zoom-video-conferencing-settings' );
		remove_submenu_page( 'themes.php', 'customize.php?return=/dev/wp-admin/admin.php?page=zoom-video-conferencing-settings' );
		remove_submenu_page( 'themes.php', 'admin.php?page=zoom-video-conferencing-settings' );
		
		add_submenu_page( 'zoom-video-conferencing', 'Users', __( 'Users', 'video-conferencing-with-zoom-api' ), 'manage_options', 'cst-zoom-video-conferencing-list-users', array( __CLASS__, 'list_users' ) );

		
		add_submenu_page( 'zoom-video-conferencing', 'Settings', __( 'Settings', 'video-conferencing-with-zoom-api' ), 'manage_options', 'cst-zoom-video-conferencing-settings', array( __CLASS__, 'zoom_video_conference_api_zoom_settings' ) );
	}



	/*
	* Override zoom settings page from child-theme
	*/	
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
			$zoom_master_host 				= filter_input( INPUT_POST, 'zoom_master_host' );

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
			update_option( 'zoom_master_host', $zoom_master_host );

			$token = dcd_zoom_conference()->zoomWebinarStartToken($zoom_master_host);
			update_option( 'zoom_master_host_token', $token );



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
		// require_once ZOOM_VIDEO_CONFERENCE_PLUGIN_VIEWS_PATH . '/admin/tpl-settings.php';
		require_once get_stylesheet_directory() . '/inc/webinars/admin/partials/tpl-settings.php';
	}

	/**
	 * List meetings page
	 *
	 * @since   1.0.0
	 * @changes in CodeBase
	 * @author  Deepen Bajracharya <dpen.connectify@gmail.com>
	 */
	public static function list_users() {
		
		wp_enqueue_script( 'video-conferencing-with-zoom-api-js' );
		wp_enqueue_script( 'video-conferencing-with-zoom-api-datable-js' );

		wp_enqueue_style( 'video-conferencing-with-zoom-api' );
		wp_enqueue_style( 'video-conferencing-with-zoom-api-datable' );

		//Check if any transient by name is available
		$check_transient = get_transient( '_zvc_user_lists' );
		if ( isset( $_GET['flush'] ) == true ) {
			if ( $check_transient ) {
				delete_transient( '_zvc_user_lists' );
				self::set_message( 'updated', __( "Flushed User Cache!", "video-conferencing-with-zoom-api" ) );
			}
		}

		//Get Template  ffldfg
		//Requiring Thickbox
		add_thickbox();
		// echo '<br/>user list omar Custom<br/>';
		// learn_press_get_template( 'single-course/lesson-countdown.php' );
		require_once get_stylesheet_directory() . '/inc/webinars/admin/partials/tpl-list-users.php';
	}


	static function get_message() {
		return self::$message;
	}

	static function set_message( $class, $message ) {
		self::$message = '<div class=' . $class . '><p>' . $message . '</p></div>';
	}
	
	public function testFunction(){
		// $this->callbackScheduleEventForWebinarFunction();
	
	}
	


	

	










	public function completeLearnPressCallback($order_id){
		// update_post_meta( $order_id, 'omarworking', 'test update' );
		DigitalCustDev_WooCommerce_Hooks::register_into_webinar_using_learnpress_order_id($order_id);
	}

	/*
	* Add a custom field to learnpress general section 
	*/
	public function addHostCallback($callback){
		$callback[] = array(
			'title' => 'Zoom Master Host ID',
			'id' => 'zoom_master_host',
			'type' => 'text',
			'default' => 'RKLktIrtSG-RKL_pPSYk_w'
		);
		return $callback;
	}



	/*
	* send Email for zoom webinar lesson update
	*/
	// do_action( 'learn-press/user-enrolled-course', $course_id, $user_id, $user_course );
	public function trigger($course_id, $user_id, $user_item_id){
		$this->course_id    = $course_id;
		$this->user_id      = $user_id;
		$this->user_item_id = $user_item_id;

		LP_Emails::instance()->set_current( $this->id );
	}

	/*
	* Action 
	*/
	public function customizeEmailAction($triggers){
		$triggers[] = 'learn-press/zoom-webinar-lession-update';
		return $triggers;
	}


	/**
	* Add email setting class.
	*/
	
	public function backward_load_emails() {
		if ( class_exists( 'LP_Emails' ) ) {
			$emails = LP_Emails::instance()->emails;
			custom_emails_setting( $emails );
		}
	}

	public function addEmailTemplateForUpdateLession($groups){
		array_push(
			$groups,
			include get_stylesheet_directory() . '/inc/webinars/admin/partials/webinar_update_email.php'
		);
		array_push(
			$groups,
			include get_stylesheet_directory() . '/inc/webinars/admin/partials/webinar_start_notification_email.php'
		);

		// Notification before 10 minutes
		array_push(
			$groups,
			include get_stylesheet_directory() . '/inc/webinars/admin/partials/webinar_start_notification_before_ten_email.php'
		);
		return $groups;
	}

	

	public function createZoomUserWhileUPdateWebinar($post_id){
		$postid = $post_id;
		$metas = get_post_meta( $postid, '_lp_co_teacher', false );
		$post_author_id = get_post_field( 'post_author', $postid );
		$authors = array_merge($metas, array($post_author_id));

		foreach($authors as $user_id):
			$action 	= 'custCreate';
			$user_data 	= get_user_by( 'id', $user_id );
			$metas 		= get_user_meta( $user_id );
			$firstname 	= (get_user_meta( $user_id, 'first_name', true )) ? get_user_meta( $user_id, 'first_name', true ) : get_user_meta( $user_id, 'nickname', true );
			$lastname 	= (get_user_meta( $user_id, 'last_name', true )) ? get_user_meta( $user_id, 'last_name', true ) : '';
			$user_email = $user_data->data->user_email;


			$postData = array(
				'action'     => $action,
				'email'      => $user_email,
				'first_name' => $firstname,
				'last_name'  => $lastname,
				'type'       => 1
			);

			$created_user = zoom_conference()->createAUser( $postData );
			$result       = json_decode( $created_user );
			
			if ( empty( $result->code ) ) {
					update_user_meta( $user_id, 'user_zoom_hostid', $result->id );
					$curl = curl_init();
					curl_setopt_array($curl, array(
					CURLOPT_URL => "https://api.zoom.us/v2/users/".$result->id."/status",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "PUT",
					CURLOPT_POSTFIELDS => "{\"action\":\"deactivate\"}",
					CURLOPT_HTTPHEADER => array(
						"authorization: Bearer ".$this->generateJWTKey()."",
						"content-type: application/json"
					),
					));

					$response = curl_exec($curl);

				
					$err = curl_error($curl);

					curl_close($curl);

			} 
		endforeach;

		// Delete Transation 
		delete_transient( '_zvc_user_lists' );
	}



	/*
	* Delete Zoom webinar if gotes to trash
	*/
	public function postUpdateCallback($post_ID, $post_after, $post_before){
		if($post_after->post_status == 'trash' && get_post_type( $post_ID ) == 'lp_course' && "webinar" == get_post_meta( $post_ID, '_course_type', true )){
				//Delete attachemnt
				$post_id = $post_ID;
				$post_author_id = get_post_field( 'post_author', $post_id );

				if ( has_post_thumbnail( $post_id ) ) {
					$attachment_id = get_post_thumbnail_id( $post_id );
					wp_delete_attachment( $attachment_id, true );

					// $post_id = get_transient( get_current_user_id() . 'e_post_id' );
					delete_transient( $post_author_id . 'e_post_id' );
				}

				/* Delete Folder */
				if($post_id){
					$upload_dir = wp_get_upload_dir();
					$user = get_user_by( 'id', $post_author_id );
					$param = array();
					$param['path'] = $upload_dir['basedir'] .'/'. $user->data->user_nicename . '/' . $post_id;
					removeDirectory($param['path']);
				}
				

				/*
				* Delete related items
				*/
				$course    = learn_press_get_course( $post_id );
				$items     = $course->get_items();
				foreach($items as $lesson){
					$post_type = get_post_type( $lesson );
					if($post_type == 'lp_quiz'){
						$questions = learn_press_get_quiz_questions($lesson);
						foreach($questions as $singleQueston){
							wp_delete_post( $singleQueston, true );	
						}
					}
					//Delete Webinar Details
					$webinar_id = get_post_meta( $lesson, '_webinar_ID', true );
					if ( $webinar_id ) {
						dcd_zoom_conference()->deleteWebinar( $webinar_id );
					}
					wp_delete_post( $lesson, true );	
				}
				wp_delete_post( $post_id, true );
		}
	}

	/*
	* Update LP course and update zoom user
	*/
	public function courseSaveCallbackFunction($post_id, $post, $update){
		// Processs Lp-order
		if('lp-completed' == get_post_status($post_id) && get_post_type($post_id) == 'lp_order' ){
			$this->completeLearnPressCallback($post_id);
		}
		// End process LP-order




		$post_type = get_post_type( $post_id );
		$author_id = get_post_field( 'post_author', $post_id );
		
		// For Course post type
		if($post_type == 'lp_course' && "webinar" == get_post_meta( $post_id, '_course_type', true )):
			if(get_post_status($post_id) == 'publish' ){
				$this->createZoomUserWhileUPdateWebinar($post_id);
				$host_id = get_option('zoom_master_host');
				// Create or update webinar in zooom 
				$lessons = get_course_lessons($post_id);
				if ( ! empty( $host_id ) ) {
					foreach($lessons as $sl):
						// echo 'single lession: ' . $sl. '<br/>';
						$lesson         = get_post( $sl );
						$webinar_exists = get_post_meta( $sl, '_webinar_ID', true );
						if ( empty( $webinar_exists ) ) {
							$timezone   = get_post_meta( $sl, '_lp_timezone', true );
							$duration = get_post_meta( $sl, '_lp_duration', true );
							$duration = (int)$duration;
							$start_time = get_post_meta( $sl, '_lp_webinar_when', true );
							if($start_time) $start_time = str_replace('/', '-', $start_time);
							if($start_time) $start_time = date( "Y-m-d\TH:i:s", strtotime( $start_time ) );
							
								//Create webinar here
								$postData        = array(
									'topic'      => $lesson->post_title,
									'type'       => 5,
									'duration' => $duration,
									'start_time' => ! empty( $start_time ) ? $start_time : date( "Y-m-d\TH:i:s" ),
									'timezone'   => ! empty( $timezone ) ? $timezone : '',
									// 'agenda'     => $lesson->post_content,
									'settings'   => array(
										'host_video'        => false,
										'panelists_video'   => true,
										'approval_type'     => 0,
										'registration_type' => 1,
										'auto_recording'    => 'cloud',
										'registrants_email_notification' => false
									)
								);

								if($start_time && $timezone){
									$created_webinar = dcd_zoom_conference()->createWebinar( $host_id, $postData );
									$created_webinar = json_decode( $created_webinar );
									if ( ! empty( $created_webinar ) ) {
										update_post_meta( $sl, '_webinar_ID', $created_webinar->id );
										update_post_meta( $sl, '_webinar_details', $created_webinar );
										update_post_meta( $sl, 'mh_start_link', $created_webinar->start_url );
									}
								}
						} else {
							
							// Update Alternative Host
							$authorhostid = get_user_meta($author_id, 'user_zoom_hostid', true);
							update_post_meta( $sl, '_lp_alternative_host', $authorhostid );


							$timezone   = get_post_meta( $sl, '_lp_timezone', true );
							$start_time = get_post_meta( $sl, '_lp_webinar_when', true );
							$duration = get_post_meta( $sl, '_lp_duration', true );
							$duration = (int)$duration;
							if($start_time) $start_time = str_replace('/', '-', $start_time);
							if($start_time) $start_time = date( "Y-m-d\TH:i:s", strtotime( $start_time ) );

							

							$webinar_id = get_post_meta( $sl, '_webinar_ID', true );
							if ( ! empty( $webinar_id ) ) {
								//Create webinar here
								$postData = array(
									'topic'      => $lesson->post_title,
									'type'       => 5,
									'start_time' => $start_time,
									'duration' 	=> $duration,
									'timezone'   => ! empty( $timezone ) ? $timezone : '',
									'agenda'     => $lesson->post_content,
									'settings'   => array(
										'host_video'        => false,
										'panelists_video'   => true,
										'approval_type'     => 0,
										'registration_type' => 1,
										'auto_recording'    => 'cloud',
										'registrants_email_notification' => false
									)
								);

								if($start_time && $timezone){
									//Updated
									$updateWebinar = dcd_zoom_conference()->updateWebinar( $webinar_id, $postData );
									$updateWebinar = json_decode( $updateWebinar );
									if ( ! empty( $updateWebinar ) ) {
										// update_post_meta( $sl, '_webinar_ID', $updateWebinar->id );
										update_post_meta( $sl, '_webinar_details', $updateWebinar );
									}else{
										$metas = get_post_meta( $sl, '_webinar_details', true );
										$newcreatedtime = new DateTime("now", new DateTimeZone( $timezone ) );
										$metas->created_at = $newcreatedtime->format('Y-m-d H:i:s');
										update_post_meta( $sl, '_webinar_details', $metas );
									}
								}
							}
						}
					endforeach;
				}


			}

			// if webinar set as draft
			if( get_post_status($post_id) == 'draft'){
				$lessons = get_course_lessons($post_id);
				foreach($lessons as $sl):
					$webinar_id = get_post_meta( $sl, '_webinar_ID', true );
					if ( $webinar_id ){
						dcd_zoom_conference()->deleteWebinar( $webinar_id );
						delete_post_meta( $sl, '_webinar_ID' );
					}
				endforeach;
			}
		endif;



		// For Lession post type
		if ( $post_type === "lp_lesson" && !empty( get_the_title( $post_id ) ) && !empty( get_post_meta( $post_id, '_lp_webinar_when', true ) ) ):
			delete_post_meta( $post_id, 'start_url' );
			delete_post_meta( $post_id, 'zoom_status' );
			delete_post_meta( $post_id, '_webinar_statis' );
			
			$lesson = get_post($post_id);
			$webinar_id = get_post_meta( $post_id, '_webinar_ID', true );
			$timezone   = get_post_meta( $post_id, '_lp_timezone', true );
			$duration = get_post_meta( $sl, '_lp_duration', true );
			$duration = (int)$duration;
			$start_time = get_post_meta( $post_id, '_lp_webinar_when', true );
			
			// UPdate alternative host
			$authorhostid = get_user_meta($author_id, 'user_zoom_hostid', true);
			update_post_meta( $post_id, '_lp_alternative_host', $authorhostid );


			$start_time = str_replace('/', '-', $start_time);
			$start_time = ! empty( $start_time ) ? date( "Y-m-d\TH:i:s", strtotime( $start_time ) ) : date( "Y-m-d\TH:i:s" );

			if ( ! empty( $webinar_id ) ) {

				// Start url for master host 
				$webinarDetails = dcd_zoom_conference()->getZoomWebinarDetails($post_id);
				$webinarDetails = json_decode($webinarDetails);
				update_post_meta( $post_id, 'mh_start_link', $webinarDetails->start_url );


				//Create webinar here f
				$postData = array(
					'topic'      => $lesson->post_title,
					'type'       => 5,
					'start_time' => $start_time,
					'duration' 	=> $duration,
					'timezone'   => ! empty( $timezone ) ? $timezone : '',
					'agenda'     => $lesson->post_content,
					'settings'   => array(
						'host_video'        => false,
						'panelists_video'   => true,
						'approval_type'     => 0,
						'registration_type' => 1,
						'auto_recording'    => 'cloud', 
						'registrants_email_notification' => false
					)
				);

				//Updated
				dcd_zoom_conference()->updateWebinar( $webinar_id, $postData );
				$user_id = get_current_user_id();
				do_action( 'learn-press/zoom-update-lession-instructor', $post_id, $user_id );
				do_action( 'learn-press/zoom-update-lession-user', $post_id, $user_id );
				do_action( 'learn-press/zoom-update-lession-admin', $post_id, $user_id );
			}else{
				
				// $host_id    = get_user_meta( $author_id, 'user_zoom_hostid', true );
				
				// if ( ! empty( $host_id ) ) {
				// 	//Create webinar here
					
				// 	$postData        = array(
				// 		'topic'      => $lesson->post_title,
				// 		'type'       => 5,
				// 		'start_time' => ! empty( $start_time ) ? $start_time : date( "Y-m-d\TH:i:s" ),
				// 		'timezone'   => ! empty( $timezone ) ? $timezone : '',
				// 		// 'agenda'     => $lesson->post_content,
				// 		'settings'   => array(
				// 			'host_video'        => false,
				// 			'panelists_video'   => true,
				// 			'approval_type'     => 0,
				// 			'registration_type' => 1,
				// 			'auto_recording'    => 'cloud'
				// 		)
				// 	);
					
				// 	$created_webinar = dcd_zoom_conference()->createWebinar( $host_id, $postData );
					
				// 	update_post_meta( $post_id, 'host_id', $created_webinar );

				// 	$created_webinar = json_decode( $created_webinar );
				// 	if ( ! empty( $created_webinar ) ) {
				// 		update_post_meta( $post_id, '_webinar_ID', $created_webinar->id );
				// 		update_post_meta( $post_id, '_webinar_details', $created_webinar );
				// 	}
				// }

			}
		endif;
		
	}

	public function addcountdowntoembedvideoforlessionVideo($html, $url, $attr, $post_ID){
		$show = false;
		if(is_singular( 'lp_course' ) && !get_post_meta( $post_ID, '_lp_lesson_video_intro_internal', true )){
			global $wp_query;
			$vars = $wp_query->query_vars;
			if(isset($vars['item-type']) && $vars['item-type'] == 'lp_lesson'){

				$start_time = get_post_meta( $post_ID, '_lp_webinar_when', true );
				$change_sdate = str_replace( '/', '-', $start_time );
				$time   = date( 'Y-m-d H:i', strtotime( $change_sdate ) );
									
				$current_time = date( 'Y-m-d H:i' );
				
				if($start_time &&  $time > $current_time ) {
					$show = true;
				}
			}
		}
		

		if (strpos($url, 'youtube.com')!==false || strpos($url, 'youtu.be')!==false) {
			/*  YOU CAN CHANGE RESULT HTML CODE HERE */			
			if($show){
				ob_start();
				?>
				<div class="tp-event-top lp-lesson position-relative">
				<?php learn_press_get_template( 'single-course/lesson-countdown.php' ); ?>
				<div class="youtube-wrap"><?php echo $html; ?></div>
				</div>
				<?php
				$html = ob_get_clean();
			}
		}
		return $html;

	}

	public function customRequiredEnroll($return){
		$allowfreeEnroll = LP()->settings()->get( 'free_enroll_allow' );
		if($allowfreeEnroll == 'yes') $return = true;
		return $return;
	}

	public function testcontent($content){
		// return 'test o';
	}

	public function addFreeEnrollCheckboxToSettingsGeneral($settings){
		// echo 'general Settings<br/><pre>';
		// print_r($settings);
		// echo '</pre>';
		$settings[] = array(
				'title'   => __( 'Enable Enroll button for free Courses/Webinars', 'learnpress' ),
				'id'      => 'free_enroll_allow',
				'default' => 'no',
				'type'    => 'yes-no',
				'desc'    => __( 'Enable user enroll course / webinar as free.', 'learnpress' )
		);
		return $settings;
	}
	public function misha_quick_edit_fields(){
		echo '<div>sfsfsf</div>';
	}





		/**
		 * @param $pages
		 *
		 * @return array
		 */
		public function admin_tabs_pages( $pages ) {
			$allowArray = array('learnpress_page_zoom-webinars', 'edit-webinar_tag', 'edit-webinar_categories');
			if ( in_array(get_current_screen()->id, $allowArray) )
			{
				$pages = $allowArray;
			}	
			return $pages;
		}


		/**
		 * @param $tabs
		 *
		 * @return array
		 */
		public function admin_tab( $tabs ) {
			$allowArray = array('learnpress_page_zoom-webinars', 'edit-webinar_categories', 'edit-webinar_tag');
			$links = array(
				'admin.php?page=zoom-webinars',
				'edit-tags.php?taxonomy=webinar_categories&post_type=lp_course',
				'edit-tags.php?taxonomy=webinar_tag&post_type=lp_course'
			);
			$names = array(
				__( "Webinars", "learnpress" ),
				__( "Webinar Category", "learnpress" ),
				__( "Webinar Tags", "learnpress" )
			);

			if ( in_array(get_current_screen()->id, $allowArray) ){
				$tabs = array();
				foreach($allowArray as $k => $sa){
					$tabs[] = array(
						"link" => $links[$k],
						"name" => $names[$k],
						"id"   => $sa
					);
				}	
			}

			return $tabs;
		}


	/*
	* Change sub-menu order
	*/
	public function changeAdminSubMenuOrderForLearnPress($menu_ord){
		global $submenu;
		$newarray = array();
		$newarray[] = $submenu['learn_press'][0];
		$newarray[] = $submenu['learn_press'][10];
		$newarray[] = $submenu['learn_press'][1];
		$newarray[] = $submenu['learn_press'][2];
		$newarray[] = $submenu['learn_press'][3];
		$newarray[] = $submenu['learn_press'][4];
		$newarray[] = $submenu['learn_press'][5];
		$newarray[] = $submenu['learn_press'][6];
		$newarray[] = $submenu['learn_press'][7];
		$newarray[] = $submenu['learn_press'][8];
		$newarray[] = $submenu['learn_press'][9];
		$submenu['learn_press'] = $newarray;
	}


	/*
	* Lession metabox for lession video intro
	*/
	public function wpdocs_register_meta_boxes() {
		add_meta_box( 'lession-video-data', __( 'Lesson Video', 'textdomain' ), array($this, 'lp_lession_metabox'), 'lp_lesson', 'side', 'core' );
	}
	
	public function lp_lession_metabox($post){
		
		$internal_video = get_post_meta( $post->ID, '_lp_lesson_video_intro_internal', true );
		$internal_video = json_decode($internal_video, true);
		
		// echo '<pre>';
		// print_r($internal_video);
		// echo '</pre>';

		echo '<input type="hidden" name="_lp_lesson_video_intro_internal" value="'.$internal_video.'" />';
		
		?>
		<div class="acf-field acf-field-file acf-field-5d52623d7778a" data-name="upload_intro_video" data-type="file" data-key="field_5d52623d7778a">
		<div class="acf-label">
		<label for="acf-field_5d52623d7778a"><?php _e('Upload Lesson Video', 'webinar'); ?></label></div>
		
		
		<div class="acf-input">
				<div class="acf-file-uploader <?= ($internal_video) ? 'has-value':''; ?>" data-library="uploadedTo" data-mime_types="mp4" data-uploader="wp">
			
			<?php if($internal_video): ?>
				<div class="show-if-value file-wrap">
				<div class="file-icon">
					<img data-name="icon" src="<?php echo $internal_video['icon']; ?>" alt="">
				</div>
				<div class="file-info">
					<p>
						<strong data-name="title"><?php echo $internal_video['title']; ?></strong>
					</p>
					<p>
						<strong><?php _e('File name', 'webinar') ?>:</strong>
						<a data-name="filename" href="<?php echo $internal_video['url']; ?>" target="_blank"><?php echo $internal_video['filename']; ?></a>
					</p>
					<p>
						<strong><?php _e('File size', 'webinar'); ?>:</strong>
						<span data-name="filesize"><?php echo $internal_video['filesizeHumanReadable']; ?></span>
					</p>
				</div>
				<div class="acf-actions -hover">
					<!-- <a class="acf-icon -pencil dark" data-name="edit" href="#" title="Edit"></a> -->
					<a href="#" data-action="delete_admin_intro" title="Remove" class="acf-icon -cancel remove_lesson_media_attachment dark"></a>
				</div>
			</div>
			<?php endif; ?>
			<div class="hide-if-value">
							
					<p>No file selected 
					<a data-id="intro_video_lesson" class="acf-button button" href="#">Add File</a>
					</p>
			</div>
		</div>
		</div>
		</div>

		<?php
		
	}


	
	/*
	* Change course duration by total lession duration
	*/
	public function course_duration_by_lesson_total_duration($query){
		global $post;
		
		// For LP_LESSON POST type
		if(is_admin() && $_REQUEST['post_type'] == 'lp_lesson' ) {
			if ( 'Zoom Date' === $_REQUEST['orderby'] ) {
				$query->set( 'orderby', 'meta_value' );
				$query->set( 'meta_key', '_lp_webinar_when_gmt' );
				$query->set( 'meta_type', 'DATETIME' );
				$query->set( 'order', $_REQUEST['order'] );
			}
		}



		// for course post type
		if(is_admin() && $_REQUEST['action'] == 'edit' && get_post_type( $post ) == 'lp_course' ) {
			$lessons = get_course_lessons($post->ID);
			$course_type = get_post_meta( $post->ID, '_course_type', true );
			
			if($course_type != 'webinar'){
			$_lp_duration = 0;
			foreach($lessons as $sl){
				if(get_post_meta( $sl, '_lp_duration', true )){
					$duration_array = get_post_meta( $sl, '_lp_duration', true );
					$duration_array = preg_split('/(?<=[0-9]) (?=[a-z]+)/i',$duration_array);                                                               
					switch($duration_array[1]){
						case 'minute':
							$_lp_duration += $duration_array[0];
						break;
						case 'hour':
							$mint = $duration_array[0] * 60;
							$_lp_duration += $mint;
						break;
						case 'week':
							$mint = $duration_array[0] * 7;
							$mint = $mint * 24;
							$mint = $mint * 60;
							$_lp_duration += $mint;
						break;
						case 'day':
							$mint = $duration_array[0] * 24;
							$mint = $mint * 60;
							$_lp_duration += $mint;
						break;
					}
				}
			}
			update_post_meta( $post->ID, '_lp_duration', $_lp_duration . ' minute' );
			}
		}
	}

	/*
	 * Notify an administrator with pending courses
	 */
	public function notify_new_course() {
		global $menu;
		global $submenu;


		$current_user = wp_get_current_user();
		if ( ! in_array( 'administrator', $current_user->roles ) ) {
			return;
		}

		$args = array(   
			'post_type'   => LP_COURSE_CPT,  
			'posts_per_page' => -1,
			'post_status' => 'pending',
			'meta_query' => array(
				array(
					'key'     => '_course_type',
					'compare' => 'NOT EXISTS'
				)
			)
		);  
		$query = new WP_Query( $args );

		$awaiting_mod    = $query->post_count;

		// echo 'print sub menu <br/><pre>';
		// print_r($submenu);
		// echo '</pre>';
		// $submenu['learn_press'][0][2] .= '&post_status=all'; 
		$submenu['learn_press'][0][0] .= " <span class='custom awaiting-mod count-$awaiting_mod'><span class='pending-count'>" . number_format_i18n( $awaiting_mod ) . "</span></span>";
	}


	/*
	* Allow empty post to delete
	*/
	public function allow_empty_post( $maybe_empty, $postarr ) {
        if ( ! $maybe_empty ) {
                return $maybe_empty;
        }

        if ( 'lp_course' === $postarr['post_type']
             && in_array( $postarr['post_status'], array( 'inherit', 'draft', 'trash', 'auto-draft' ) )
        ) {
                $maybe_empty = false;
        }

        return $maybe_empty;
	}

	/**
	 * Add grade book column to course page in admin.
	 *
	 * @param  array $column
	 *
	 * @return array
	 */
	function manage_course_posts_columns( $column ) {
		unset($column['taxonomy-course_category']);
		unset($column['taxonomy-webinar_categories']);
			
		return $column;
	}


	/*
	* Lesson column filter
	*/
	public function manage_lesson_posts_columns($column){
		$column = array_slice($column, 0, 6, true) +
		array("meta-zoom_date" => __('Zoom Date', 'webinar')) +
		array_slice($column, 6, count($column) - 1, true) ;
		return $column;
	}

	/*
	* Course column customization 
	*/	
	public function ecs_add_post_state( $post_states, $post ) {
		$post_status = get_post_status($post);
			if($post_status == 'publish'){
				$post_states[] = ucfirst($post_status);
			}
		return $post_states;
	}

	/*
	* Set webner page to learnpress settings
	*/
	public function settingsFilters($settings){
		
		$new = array(
			'title'   => __( 'Webinars page', 'learnpress' ),
			'id'      => 'webinar_page_id',
			'default' => '',
			'type'    => 'pages-dropdown'
		);


		$new1 = array(
			'title'   => __( 'Webinars per page', 'learnpress' ),
			'id'      => 'wcount_page_id',
			'default' => 8,
			'type'    => 'number'
		);


		array_push($settings, $new);
		array_push($settings, $new1);
		return $settings;


	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Webinars_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Webinars_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, get_stylesheet_directory_uri() . '/inc/webinars/admin/css/webinars-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Webinars_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Webinars_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		wp_enqueue_script( $this->plugin_name, get_stylesheet_directory_uri() . '/inc/webinars/admin/js/webinars-admin.js', array( 'jquery', 'video-conferencing-with-zoom-api-datable-js'), $this->version, true );

	}
	function add_custom_taxonomies() {
		register_taxonomy('webinar_categories', 'lp_course', array(
		  // Hierarchical taxonomy (like categories)
		  'hierarchical' => true,
		  'query_var'         => true,
			'public'            => true,
					
			'show_ui'           => true,
			'show_in_menu'      => 'learn_press',
			'show_admin_column' => true,
			'show_in_admin_bar' => true,
			'show_in_nav_menus' => true,
		  // This array of options controls the labels displayed in the WordPress Admin UI
		  'labels' => array(
			'name' => _x( 'Webinar Categories', 'taxonomy general name' ),
			'singular_name' => _x( 'Webinar category', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Webinar categories' ),
			'all_items' => __( 'All Webinar categories' ),
			'parent_item' => __( 'Parent Webinar categories' ),
			'parent_item_colon' => __( 'Parent Webinar categories:' ),
			'edit_item' => __( 'Edit Webinar categories' ),
			'update_item' => __( 'Update Webinar categories' ),
			'add_new_item' => __( 'Add New Webinar categories' ),
			'new_item_name' => __( 'New Webinar category Name' ),
			'menu_name' => __( 'Webinar categories' ),
		  ),
		  // Control the slugs used for this taxonomy
		  'rewrite' => array(
			'slug' => 'webinars', 
			'with_front' => true, 
			'hierarchical' => true 
		  ),
		));
		register_taxonomy( 
            'webinar_tag', 
            'lp_course', 
            array( 
                'hierarchical'  => false, 
                'label'         => __( 'Webinar Tags'), 
                'singular_name' => __( 'Webinar Tag'), 
                'rewrite'       => true, 
                'query_var'     => true 
            )  
		);
	  }


	  function action_edit_post(){
		$screen = get_current_screen();
		$post_type = $screen->post_type;
		$post_id = (isset($_GET['post'])) ? $_GET['post'] : '';
		$courseType = get_post_meta( $post_id, '_course_type', true );
		if(isset($_REQUEST['_course_type']) && $_REQUEST['_course_type'] == 'webinar') $courseType = $_REQUEST['_course_type'];
		if($courseType == 'webinar' && $post_type == 'lp_course'){
			echo '<script>
				jQuery(window).load(function(){
					jQuery("div#course_categorydiv, div#tagsdiv-course_tag").remove();
				});
			</script>';
		}else{
			echo '<script>
				jQuery(window).load(function(){
					jQuery("div#webinar_categoriesdiv, div#tagsdiv-webinar_tag").remove();
				});
				
			</script>';
		}
	  }
}

/*
* Webinars Category
*/
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/webinars-category.php';

/*
* Webinars Latest Webinars
*/
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/latest-webinars.php';