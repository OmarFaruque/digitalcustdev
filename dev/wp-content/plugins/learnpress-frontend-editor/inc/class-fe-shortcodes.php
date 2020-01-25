<?php

/**
 * Class Frontend_Editor_Shortcodes
 */
class Frontend_Editor_Shortcodes {

	/**
	 * Initialize
	 */
	public static function init() {
		add_shortcode( 'courses_manage', array( __CLASS__, 'courses_manage' ) );
	}

	/**
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function courses_manage( $atts, $content = '' ) {
		return 'ahihi';
	}
}

// Init shortcodes
Frontend_Editor_Shortcodes::init();