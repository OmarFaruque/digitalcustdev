<?php
/**
 * @link              https://elearningevolve.com
 * @since             3.1.5
 * @package           Zoom Video Conferencing on WordPress
 *
 * Plugin Name:       Zoom Video Conferencing on WordPress
 * Plugin URI:        http://elearningevolve.com/products/wordpress-zoom-addon
 * Description:       An extended version of Video Conferencing with Zoom API to allow Zoom meetings directly on your WordPress site
 * Version:           3.1.5
 * Author:            Deepen Bajracharya/Adeel Raza
 * Author URI:        https://elearningevolve.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       video-conferencing-with-zoom-api
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Allowed Here !' );
}

function video_conferencing_zoom_api_general_admin_notice() {
	echo "<div class='notice notice-error is-dismissible'>
		<p>
			<a target='_blank' href='https://elearningevolve.com/products/wordpress-zoom-integration/'>Zoom Video Conferencing on WordPress</a> is an extended version of the plugin already installed on your site <span style='color:red;'>Video Conferencing with Zoom API</span> and cannot be activated along with this plugin. Please disable plugin <span style='color:red;'>'Video Conferencing with Zoom API'</span> first to proceed.
		</p>
	</div>";
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

$eevolve_constants = array(
	'ZVCW_ZOOM_STORE_URL'        => 'https://elearningevolve.com',
	'ZVCW_ZOOM_ITEM_NAME'        => 'Zoom Video Conferencing on WordPress',
	'ZVCW_ZOOM_ITEM_ID'          => 2117,
	'ZVCW_ZOOM_PLUGIN_VER'       => '3.1.5',
	'ZVCW_ZOOM_PLUGIN_NAME'      => 'Zoom Video Conferencing on WordPress',
	'ZVCW_ZOOM_BASE_PLUGIN_NAME' => plugin_basename( __FILE__ ),
);

foreach ( $eevolve_constants as $constant => $value ) {
	if ( ! defined( $constant ) ) {
		define( $constant, $value );
	}
}

if ( ! class_exists( 'Zoom_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/Zoom_SL_Plugin_Updater.php' );
}

register_activation_hook( __FILE__, 'video_conferencing_zoom_api_activation' );
register_uninstall_hook( __FILE__, 'video_conferencing_zoom_api_uninstall' );

function video_conferencing_zoom_api_activation() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
}

function video_conferencing_zoom_api_uninstall() {
	$plugin_options = array( 'zoom_api_key', 'zoom_api_secret', 'zoom_api_meeting_options', 'zoom_vanity_url' );

	foreach ( $plugin_options as $option ) {
		delete_option( $option );
	}
}

function video_conferencing_zoom_api_settings_link( $links ) {
	$settings_link = '<a href="admin.php?page=zoom-video-conferencing-settings">' . __( 'Settings' ) . '</a>';
	array_push( $links, $settings_link );
	return $links;
}

require_once dirname( __FILE__ ) . '/includes/video-conferencing-with-zoom-init.php';

add_action( 'plugins_loaded', array( 'Video_Conferencing_With_Zoom', 'instance' ), 99 );

register_activation_hook( __FILE__, array( 'Video_Conferencing_With_Zoom', 'activator' ) );

// setup the updater
$zoom_updater = new Zoom_SL_Plugin_Updater(
	ZVCW_ZOOM_STORE_URL,
	__FILE__,
	array(
		'version'   => ZVCW_ZOOM_PLUGIN_VER,        // current version number
		'license'   => 'cb8d8e26d857e43646dfe201bd8f60f7',  // license key (used get_option above to retrieve from DB)
		'item_name' => ZVCW_ZOOM_ITEM_NAME,     // name of this plugin
		'author'    => 'Adeel Raza',  // author of this plugin
		'url'       => home_url(),
	)
);
