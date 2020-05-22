<?php
/**
 * @link              http://www.deepenbajracharya.com.np
 * @since             1.0.0
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( "Not Allowed Here !" );
}

define( 'DIGITALCUSTDEV_PLUGIN_PATH', get_stylesheet_directory(  ) . '/digitalcustdev-core/' );
define( 'DIGITALCUSTDEV_PLUGIN_URL', get_stylesheet_directory_uri() . '/digitalcustdev-core/' );


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
		$type = '';
		$page_id = get_option( 'learn_press_frontend_editor_page_id' );
		if ( $page_id && is_page( $page_id ) ) {
			global $frontend_editor;
			$post_manage = $frontend_editor->post_manage;
			$post        = $post_manage->get_post();
			$type    = get_post_meta( $post->ID, '_course_type', true );
		}
		
		

		wp_enqueue_script( 'dcd-frontend-course-editor', DIGITALCUSTDEV_PLUGIN_URL . 'assets/js/webinar-frontend-editor.js', array(
			'frontend-course-editor-custom'
		), time(), false );
		wp_localize_script( 'dcd-frontend-course-editor', 'dcd_fe_object', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'course_type' => $type
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
			wp_register_style( 'digitalcustdev-datable-om', DIGITALCUSTDEV_PLUGIN_URL . 'assets/vendor/css/jquery.dataTables.min.css', false, '1.0.0' );
			wp_register_style( 'digitalcustdev-datetimepicker', DIGITALCUSTDEV_PLUGIN_URL . 'assets/vendor/css/jquery.datetimepicker.css', false, '1.0.0' );
			wp_register_style( 'digitalcustdev-magnific-popup', DIGITALCUSTDEV_PLUGIN_URL . 'assets/vendor/css/magnific-popup.min.css', false, '1.0.0' );

			wp_register_script( 'digitalcustdev-datable-js', DIGITALCUSTDEV_PLUGIN_URL . 'assets/vendor/js/jquery.dataTables.min.js', array( 'jquery' ), '1.0.0', true );
			wp_register_script( 'digitalcustdev-datetimepicker', DIGITALCUSTDEV_PLUGIN_URL . 'assets/vendor/js/jquery.datetimepicker.full.min.js', array( 'jquery' ), '1.0.0', true );
			wp_register_script( 'digitalcustdev-magnific-popup', DIGITALCUSTDEV_PLUGIN_URL . 'assets/vendor/js/magnific-popup.min.js', array( 'jquery' ), '1.0.0', true );
			// wp_register_script( 'webinar-list-data-table', get_stylesheet_directory_uri() . '/inc/webinars/admin/js/webinars-admin-list.js', array( 'jquery' ), time(), true );

			// wp_enqueue_style( 'digitalcustdev-bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css', false, '1.0.0' );
			

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
		// echo 'load dependencies: ' . DIGITALCUSTDEV_PLUGIN_PATH . 'includes/helpers.php';
		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/helpers.php';

		//Load core zoom api connection
		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/class-zoom-api-functions.php';

		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/class-learnpress-fields-metabox.php';
		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/class-learnpress-template-functions.php';
		require DIGITALCUSTDEV_PLUGIN_PATH . 'includes/class-courses-cpt-functions.php';
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

add_action( 'after_setup_theme', array( 'DigitalCustDev', 'instance' ), 110 );
