<?php

/**
 *
 * @link       http://www.deepenbajracharya.com.np
 * @since      1.0.0
 *
 * @package    Video Conferencing with Zoom API
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$plugin_options = array( 'zoom_api_key', 'zoom_api_secret', 'zoom_api_meeting_options', 'zoom_vanity_url', 'zoom_help_text_disable', 'zoom_compatiblity_text_disable', 'zoom_api_dismiss_share_notice', 'zoom_alternative_join', 'zoom_btn_css_class', 'zoom_width_meeting_window', 'zoom_height_meeting_window', 'zoom_ssl_message' );
			
foreach ( $plugin_options as $option ) {
	delete_option( $option );
}
