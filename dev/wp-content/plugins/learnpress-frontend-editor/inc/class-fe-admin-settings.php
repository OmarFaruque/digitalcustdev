<?php

/**
 * Class Frontend_Editor_Admin_Settings
 */
class Frontend_Editor_Admin_Settings extends LP_Abstract_Settings_Page {
	/**
	 * Frontend_Editor_Admin_Settings constructor.
	 */
	public function __construct() {
		$this->id   = 'frontend-editor';
		$this->text = __( 'Frontend Editor', 'learnpress' );

		parent::__construct();
	}

	/**
	 * @return array
	 */
//	public function get_sections() {
//		$sections = array(
//			'general'  => __( 'General', 'learnpress' ),
//			'advanced' => __( 'Advanced', 'learnpress' )
//		);
//
//		return apply_filters( 'learn-press/admin/user-dashboard-settings/sections', $sections, $this );
//	}

	/**
	 * @param string $section
	 * @param string $tab
	 *
	 * @return array
	 */
	public function get_settings( $section = '', $tab = '' ) {
		return apply_filters(
			'learn-press/admin/frontend-editor-settings/general',
			array(
				array(
					'title'   => __( 'Frontend Editor Page', 'learnpress-frontend-editor' ),
					'id'      => 'frontend_editor_page_id',
					'default' => '',
					'type'    => 'pages-dropdown'
				),
				array(
					'title'   => __( 'Disable WP Admin', 'learnpress-frontend-editor' ),
					'id'      => 'frontend_editor_disable_admin',
					'default' => 'yes',
					'type'    => 'yes-no',
					'desc'    => __( 'Prevent instructors from visiting wp-admin.', 'learnpress-frontend-editor' )
				)
			),
			$this
		);
	}
}

return new Frontend_Editor_Admin_Settings();