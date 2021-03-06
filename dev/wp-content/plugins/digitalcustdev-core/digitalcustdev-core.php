<?php
/**
 * @link              http://www.deepenbajracharya.com.np
 * @since             1.0.0
 *
 * Plugin Name:       DigitalCustDev Core Theme Customizations
 * Plugin URI:        http://www.deepenbajracharya.com.np
 * Description:       Not to be used without core plugin i.e. Video conferencing with ZOOM API plugin as well as eduma theme
 * Version:           1.0.0--DEV
 * Author:            Deepen Bajracharya
 * Author URI:        http://www.deepenbajracharya.com.np
 * Domain Path:       /lang
 * Text Domain:       digitalcustdev-core
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( "Not Allowed Here !" );
}

define( 'DIGITALCUSTDEV_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'DIGITALCUSTDEV_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

class DigitalCustDev {

	private static $_instance = null;

	/**
	 * Create only one instance so that it may not Repeat
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {
		$this->load_dependencies();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'learn-press/frontend-editor/enqueue', array( $this, 'enqueue_scripts_learnpress' ), 20 );

		add_action( 'wp_print_scripts', array( $this, 'dequeue_scripts' ) );
	}

	function enqueue_scripts_learnpress() {
		wp_enqueue_script( 'dcd-frontend-course-editor', DIGITALCUSTDEV_PLUGIN_URL . 'assets/js/webinar-frontend-editor.js', array(
			'frontend-course-editor-custom'
		), time(), false );
		wp_localize_script( 'dcd-frontend-course-editor', 'dcd_fe_object', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			) );

		wp_enqueue_script( 'learn-press-assignment-custom', plugins_url( '/assets/js/assignment-front-editor.js', LP_ADDON_ASSIGNMENT_FILE ), array(
			'frontend-course-editor-custom'
		) );
		wp_localize_script( 'learn-press-assignment-custom', 'lpa_fe_object', array(
				'ajax_url'              => admin_url( 'admin-ajax.php' ),
				'resend_evaluated_mail' => __( 'Re-send email for student to notify assignment has been evaluated?', 'learnpress-assignments' ),
				'delete_submission'     => __( 'Allow delete user\'s assignment and user can send it again?', 'learnpress-assignments' ),
				'reset_result'          => __( 'Allow clear the result has evaluated?', 'learnpress-assignments' )
			) );
	}

	function enqueue_scripts() {
		if ( is_user_logged_in() ) {
			wp_register_style( 'digitalcustdev-datable', DIGITALCUSTDEV_PLUGIN_URL . 'assets/vendor/css/jquery.dataTables.min.css', false, '1.0.0' );
			wp_register_style( 'digitalcustdev-datetimepicker', DIGITALCUSTDEV_PLUGIN_URL . 'assets/vendor/css/jquery.datetimepicker.css', false, '1.0.0' );
			wp_register_style( 'digitalcustdev-magnific-popup', DIGITALCUSTDEV_PLUGIN_URL . 'assets/vendor/css/magnific-popup.min.css', false, '1.0.0' );

			wp_register_script( 'digitalcustdev-datable-js', DIGITALCUSTDEV_PLUGIN_URL . 'assets/vendor/js/jquery.dataTables.min.js', array( 'jquery' ), '1.0.0', true );
			wp_register_script( 'digitalcustdev-datetimepicker', DIGITALCUSTDEV_PLUGIN_URL . 'assets/vendor/js/jquery.datetimepicker.full.min.js', array( 'jquery' ), '1.0.0', true );
			wp_register_script( 'digitalcustdev-magnific-popup', DIGITALCUSTDEV_PLUGIN_URL . 'assets/vendor/js/magnific-popup.min.js', array( 'jquery' ), '1.0.0', true );

			wp_register_script( 'digitalcustdev-core', DIGITALCUSTDEV_PLUGIN_URL . 'assets/js/scripts.js', array( 'jquery', 'jquery-ui-accordion' ), '1.0.0', true );
			wp_localize_script( 'digitalcustdev-core', 'dcd', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'security' => wp_create_nonce( "_nonce_security" ) ) );

			if ( e_is_frontend_editor_page() ) {
				wp_enqueue_script( 'frontend-course-editor-custom', DIGITALCUSTDEV_PLUGIN_URL . 'assets/js/course-editor.php', array(
					'jquery-ui-draggable',
					'jquery-ui-droppable',
				) );
			}
		}
	}

	function dequeue_scripts() {
		wp_dequeue_script( 'frontend-course-editor' );
		wp_deregister_script( 'frontend-course-editor' );

		wp_dequeue_script( 'learn-press-assignment' );
		wp_deregister_script( 'learn-press-assignment' );
	}

	function admin_enqueue_scripts() {
		wp_register_script( 'digitalcustdev-core-admin', DIGITALCUSTDEV_PLUGIN_URL . 'assets/admin/scripts.js', array( 'jquery' ), '1.0.0', true );
	}

	function load_dependencies() {
		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/helpers.php';

		//Load core zoom api connection
		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/class-zoom-api-functions.php';

		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/class-learnpress-fields-metabox.php';
		
		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/class-courses-cpt-functions.php';
		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/class-learnpress-template-functions.php';
		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/class-webinar-zoom-functions.php';
		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/class-woocommerce-hooks.php';
		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/class-frontend-editor-functions.php';
		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/editors/course.php';

		if ( is_admin() ) {
			require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/admin/class-wp-list-table.php';
			require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/admin/class-webinar-post-type.php';
		}

		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/admin/acf-fields.php';
	}
}

add_action( 'plugins_loaded', array( 'DigitalCustDev', 'instance' ), 110 );
