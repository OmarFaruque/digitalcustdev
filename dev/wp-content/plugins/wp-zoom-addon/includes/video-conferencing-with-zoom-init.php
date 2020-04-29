<?php
/**
 * Ready Main Class
 *
 * @since 2.1.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Allowed Here !' );
}

if ( ! class_exists( 'Video_Conferencing_With_Zoom' ) ) {
	class Video_Conferencing_With_Zoom {

		private static $_instance = null;

		/**
		 * Create only one instance so that it may not Repeat
		 *
		 * @since 2.0.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Constructor method for loading the components
		 *
		 * @since  2.0.0
		 */
		public function __construct() {
			$this->define_them_constants();
			$this->load_dependencies();
			$this->init_api();

			add_filter( 'plugin_action_links_' . ZVCW_ZOOM_BASE_PLUGIN_NAME, 'video_conferencing_zoom_api_settings_link' );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_backend' ) );
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'zvc_after_create_meeting', array( $this, 'zvc_after_create_meeting' ), 10, 3 );
			add_action( 'zvc_after_update_meeting', array( $this, 'zvc_after_update_meeting' ), 10, 3 );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param  string      $name
		 * @param  string|bool $value
		 *
		 * @since   2.0.0
		 *
		 * @author  Deepen Bajracharya
		 */
		protected function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * INitialize the hooks
		 *
		 * @since    2.0.0
		 * @modified 2.1.0
		 * @author   Deepen Bajracharya
		 */
		protected function init_api() {
			if ( isset( $_POST['zoom_api_key'] ) && isset( $_POST['zoom_api_secret'] ) ) {
				$zoom_api_key    = sanitize_text_field( $_POST['zoom_api_key'] );
				$zoom_api_secret = sanitize_text_field( $_POST['zoom_api_secret'] );
			} else {
				$zoom_api_key    = esc_html( get_option( 'zoom_api_key' ) );
				$zoom_api_secret = esc_html( get_option( 'zoom_api_secret' ) );
			}

			//Load the Credentials
			zoom_conference()->zoom_api_key    = $zoom_api_key;
			zoom_conference()->zoom_api_secret = $zoom_api_secret;
		}

		/**
		 * Defining the Constants Here
		 *
		 * @since   2.0.0
		 * @author  Deepen Bajracharya
		 */
		public function define_them_constants() {
			$this->define( 'ZOOM_VIDEO_CONFERENCE_PLUGIN_SLUG', 'video-conferencing-with-zoom-api' );
			$this->define( 'ZOOM_VIDEO_CONFERENCE_PLUGIN_DIR_PATH', plugin_dir_path( __DIR__ ) );
			$this->define( 'ZOOM_VIDEO_CONFERENCE_PLUGIN_CLASSES_PATH', plugin_dir_path( __DIR__ ) . 'classes' );
			$this->define( 'ZOOM_VIDEO_CONFERENCE_PLUGIN_VIEWS_PATH', plugin_dir_path( __DIR__ ) . 'views' );
			$this->define( 'ZOOM_VIDEO_CONFERENCE_PLUGIN_LANGUAGE_PATH', trailingslashit( basename( plugin_dir_path( __DIR__ ) ) ) . 'languages/' );
			$this->define( 'ZOOM_VIDEO_CONFERENCE_PLUGIN_INCLUDES_PATH', plugin_dir_path( __DIR__ ) . 'includes' );
			$this->define( 'ZOOM_VIDEO_CONFERENCE_PLUGIN_CSS_PATH', plugin_dir_url( __DIR__ ) . 'assets/admin/css' );
			$this->define( 'ZOOM_VIDEO_CONFERENCE_PLUGIN_FRONTEND_CSS_PATH', plugin_dir_url( __DIR__ ) . 'assets/frontend/css' );
			$this->define( 'ZOOM_VIDEO_CONFERENCE_PLUGIN_FRONTEND_JS_PATH', plugin_dir_url( __DIR__ ) . 'assets/frontend/js' );
			$this->define( 'ZOOM_VIDEO_CONFERENCE_PLUGIN_JS_PATH', plugin_dir_url( __DIR__ ) . 'assets/admin/js' );
			$this->define( 'ZOOM_VIDEO_CONFERENCE_PLUGIN_ADMIN_IMAGES_PATH', plugin_dir_url( __DIR__ ) . 'assets/admin/images' );
			$this->define( 'ZOOM_VIDEO_CONFERENCE_PLUGIN_URL_PATH', plugin_dir_url( __DIR__ ) );
			$this->define( 'ZOOM_VIDEO_CONFERENCE_APIVERSION', 2 );
		}

		/**
		 * Load the other class dependencies
		 *
		 * @since    2.0.0
		 * @modified 2.1.0
		 * @author   Deepen Bajracharya
		 */
		protected function load_dependencies() {
			//Include the Main Class
			require_once ZOOM_VIDEO_CONFERENCE_PLUGIN_CLASSES_PATH . '/class-zvc-zoom-api-v2.php';

			//Loading Includes
			require_once ZOOM_VIDEO_CONFERENCE_PLUGIN_INCLUDES_PATH . '/video-conferencing-with-zoom-helpers.php';
			require_once ZOOM_VIDEO_CONFERENCE_PLUGIN_INCLUDES_PATH . '/video-conferencing-with-zoom-shortcodes.php';

			//AJAX CALLS SCRIPTS
			require_once ZOOM_VIDEO_CONFERENCE_PLUGIN_CLASSES_PATH . '/class-zvc-zoom-ajax-calls.php';

			//Admin Classes
			require_once ZOOM_VIDEO_CONFERENCE_PLUGIN_CLASSES_PATH . '/class-zvc-zoom-admin-users.php';
			require_once ZOOM_VIDEO_CONFERENCE_PLUGIN_CLASSES_PATH . '/class-zvc-zoom-admin-meetings.php';
			require_once ZOOM_VIDEO_CONFERENCE_PLUGIN_CLASSES_PATH . '/class-zvc-zoom-admin-reports.php';
			require_once ZOOM_VIDEO_CONFERENCE_PLUGIN_CLASSES_PATH . '/class-zvc-zoom-admin-settings.php';
		}

		/**
		 * Enqueuing Scripts and Styles for Admin
		 *
		 * @since    2.0.0
		 * @modified 2.1.0
		 * @author   Deepen Bajracharya
		 */
		public function enqueue_scripts_backend() {
			wp_register_style( 'video-conferencing-with-zoom-api-timepick', ZOOM_VIDEO_CONFERENCE_PLUGIN_CSS_PATH . '/jquery.datetimepicker.css', false, time() );
			wp_register_style( 'video-conferencing-with-zoom-api-select2', ZOOM_VIDEO_CONFERENCE_PLUGIN_CSS_PATH . '/select2.min.css', false, time() );
			wp_register_style( 'video-conferencing-with-zoom-api-datable', ZOOM_VIDEO_CONFERENCE_PLUGIN_CSS_PATH . '/jquery.dataTables.min.css', false, time() );
			wp_register_style( 'jquery-ui-datepicker-zvc', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css' );
			wp_register_style( 'video-conferencing-with-zoom-api', ZOOM_VIDEO_CONFERENCE_PLUGIN_CSS_PATH . '/video-conferencing-with-zoom-api.css', false, time() );

			wp_register_script( 'video-conferencing-with-zoom-api-select2-js', ZOOM_VIDEO_CONFERENCE_PLUGIN_JS_PATH . '/select2.min.js', array( 'jquery' ), time(), true );
			wp_register_script( 'video-conferencing-with-zoom-api-timepicker-js', ZOOM_VIDEO_CONFERENCE_PLUGIN_JS_PATH . '/jquery.datetimepicker.full.min.js', array( 'jquery' ), time(), true );
			wp_register_script( 'video-conferencing-with-zoom-api-datable-js', ZOOM_VIDEO_CONFERENCE_PLUGIN_JS_PATH . '/jquery.dataTables.min.js', array( 'jquery' ), time(), true );
			wp_register_script( 'video-conferencing-with-zoom-api-js', ZOOM_VIDEO_CONFERENCE_PLUGIN_JS_PATH . '/video-conferencing-with-zoom-api.js', array( 'jquery' ), time(), true );

			wp_localize_script(
				'video-conferencing-with-zoom-api-js',
				'zvc_ajax',
				array(
					'ajaxurl'      => admin_url( 'admin-ajax.php' ),
					'zvc_security' => wp_create_nonce( '_nonce_zvc_security' ),
				)
			);
		}

		public function load_plugin_textdomain() {
			$domain = 'video-conferencing-with-zoom-api';
			apply_filters( 'plugin_locale', get_locale(), $domain );
			load_plugin_textdomain( $domain, false, ZOOM_VIDEO_CONFERENCE_PLUGIN_LANGUAGE_PATH );
		}

		/**
		 * Fire on Activation
		 */
		public static function activator() {
			global $wp_version;

			$min_wp_version = 4.8;
			$exit_msg       = sprintf( __( '%1$s requires %2$s or newer.' ), 'Video Conferencing with Zoom API', $min_wp_version );
			if ( version_compare( $wp_version, $min_wp_version, '<' ) ) {
				exit( $exit_msg );
			}

			//Comparing Version
			if ( version_compare( PHP_VERSION, 5.6, '<' ) ) {
				$exit_msg = '<div class="error"><h3>' . __( 'Warning! It is not possible to activate this plugin as it requires above PHP 5.4 and on this server the PHP version installed is: ' ) . '<b>' . PHP_VERSION . '</b></h3><p>' . __( 'For security reasons we <b>suggest</b> that you contact your hosting provider and ask to update your PHP to latest stable version.' ) . '</p><p>' . __( 'If they refuse for whatever reason we suggest you to <b>change provider as soon as possible</b>.' ) . '</p></div>';
				exit( $exit_msg );
			}

		}

		/**
		 * Runs after meeting added from WP admin
		 */
		public function zvc_after_create_meeting( $meeting_id, $host_id, $meeting_time ) {
			$zoom_map_array = get_option( 'zoom_api_meeting_options' );

			$zoom_map_array[ $meeting_id ]['time'] = $meeting_time;

			if ( isset( $_POST['option_enforce_login'] ) ) {
				$zoom_map_array[ $meeting_id ]['enforce_login'] = sanitize_text_field( $_POST['option_enforce_login'] );
			} else {
				unset( $zoom_map_array[ $meeting_id ]['enforce_login'] );
			}

			update_option( 'zoom_api_meeting_options', $zoom_map_array );
		}

		/**
		 * Runs after meeting updated from WP admin
		 */
		public function zvc_after_update_meeting( $meeting_id, $host_id, $meeting_time ) {
			$zoom_map_array = get_option( 'zoom_api_meeting_options' );

			$zoom_map_array[ $meeting_id ]['time'] = $meeting_time;

			if ( isset( $_POST['option_enforce_login'] ) ) {
				$zoom_map_array[ $meeting_id ]['enforce_login'] = sanitize_text_field( $_POST['option_enforce_login'] );
			} else {
				unset( $zoom_map_array[ $meeting_id ]['enforce_login'] );
			}

			update_option( 'zoom_api_meeting_options', $zoom_map_array );
		}
	}
} else {
	add_action( 'admin_notices', 'video_conferencing_zoom_api_general_admin_notice' );
	return;
}
