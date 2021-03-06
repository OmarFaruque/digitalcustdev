<?php
/**
 * @author Deepen.
 * @created_on 6/21/19
 */

class DigitalCustDev_LearpressTemplateFunctions {

	public function __construct() {
		//Tabs
		add_filter( 'learn-press/profile-tabs', array( $this, 'profile_tabs' ), 10, 3 );

		add_filter( 'learn_press_profile_webinars_tab_title', array( $this, 'tab_profile_filter_title' ), 100, 2 );
		add_filter( 'learn_press_profile_events_tab_title', array( $this, 'tab_profile_filter_title' ), 100, 2 );
		add_filter( 'learn_press_profile_settings_tab_title', array( $this, 'tab_profile_filter_title' ), 110, 2 );

		//Template
		add_filter( 'learn_press_locate_template', array( $this, 'template_override' ), 10, 3 );

		add_action( 'learn-press/frontend-editor/form-fields-after', array( $this, 'learn_press_dcd_fields' ) );

		add_action( 'learn-press/after-content-item-summary/lp_lesson', array( $this, 'output_webinar_btn' ) );

		//Vue Hooks
		add_action( 'learn-press/frontend-editor/item-extra-action', array( $this, 'learn_press_fe_duplicate_link' ) );
		add_action( 'learn-press/frontend-editor/item-settings-after', array( $this, 'learn_press_fe_settings' ) );
	}

	function profile_tabs( $tabs ) {
		// Only admin or instructor can view
		$tabs['webinars'] = array(
			'title'    => __( 'Webinars', 'digitalcustdev-core' ),
			'slug'     => 'webinars',
			'callback' => array( 'DigitalCustDev_CPT_Functions', 'list_webinars' ),
			'priority' => 20,
			'sections' => array(
				'list'         => array(
					'title'    => __( 'Webinars', 'digitalcustdev-core' ),
					'slug'     => 'list',
					'callback' => array( 'DigitalCustDev_CPT_Functions', 'list_webinars' ),
					'priority' => 10,
					'hidden'   => true
				),
				'add-webinars' => array(
					'title'    => __( 'Add Webinars', 'digitalcustdev-core' ),
					'slug'     => 'add',
					'callback' => array( 'DigitalCustDev_CPT_Functions', 'add_webinars' ),
					'priority' => 20,
					'hidden'   => true
				)
			)
		);

		$tabs['events'] = array(
			'title'    => __( 'Events', 'digitalcustdev-core' ),
			'slug'     => 'events',
			'callback' => array( $this, 'tab_events' ),
			'priority' => 11,
		);



		return $tabs;
	}

	function tab_profile_filter_title( $tab_title, $key ) {
		switch ( $key ) {
			case 'webinars':
				$tab_title = '<i class="fa fa-play-circle"></i><span class="text">' . esc_html__( 'Webinars', 'digitalcustdev-core' ) . '</span>';
				break;

			case 'events':
				$tab_title = '<i class="fa fa-calendar"></i><span class="text">' . esc_html__( 'Events', 'digitalcustdev-core' ) . '</span>';
				break;
			case 'settings':
				$tab_title = '<i class="fa fa-wrench"></i><span class="text">' . esc_html__( 'Profile Settings', 'eduma' ) . '</span>';
				break;
		}

		return $tab_title;
	}

	function tab_events() {

	}

	function template_override( $template, $template_name, $template_path ) {
		global $wp_query;
		$post_id = $wp_query->get( 'post-id' );
		$type    = get_post_meta( $post_id, '_course_type', true );
		if ( $type === "webinar" ) {
			if ( $template_name === "edit/form.php" ) {
				if ( class_exists( 'woocommerce' ) ) {
					wp_enqueue_style( 'select2' );
					wp_enqueue_script( 'select2' );
				}

				$template = DIGITALCUSTDEV_PLUGIN_PATH . 'templates/frontend-editor/edit/form.php';
			}
		}

		return $template;
	}

	function learn_press_dcd_fields() {
		wp_enqueue_script( 'digitalcustdev-datetimepicker' );
		wp_enqueue_style( 'digitalcustdev-datetimepicker' );
		dcd_core_get_template( 'frontend-editor/form-fields.php' );
	}

	function output_webinar_btn() {
		$user   = LP_Global::user();
		$course = LP_Global::course();
		$lesson = LP_Global::course_item();

		if ( ! $user->has_enrolled_course( $course->get_id() ) ) {
			return;
		}

		$webinar_exists = get_post_meta( $lesson->get_id(), '_webinar_ID', true );
		if ( ! empty( $webinar_exists ) ) {
			require DIGITALCUSTDEV_PLUGIN_PATH . 'templates/content-lesson/button-webinar.php';
		}
	}

	function learn_press_fe_duplicate_link() {
		$manager_page = get_option( 'assignment_students_man_page_id' );
		if ( $manager_page ) {
			$url  = get_page_link( $manager_page );
			$args = array(
				'manager_page' => $manager_page,
				'page_url'     => $url
			);

			dcd_core_get_template( 'frontend-editor/duplicate-link.php', $args );
		} else {
			return;
		}
	}

	function learn_press_fe_settings() {
		dcd_core_get_template( 'frontend-editor/item-settings.php' );
	}
}

new DigitalCustDev_LearpressTemplateFunctions();