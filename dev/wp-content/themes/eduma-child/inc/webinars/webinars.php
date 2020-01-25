<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WEBINARS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-webinars-activator.php
 */
function activate_webinars() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-webinars-activator.php';
	Webinars_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-webinars-deactivator.php
 */
function deactivate_webinars() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-webinars-deactivator.php';
	Webinars_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_webinars' );
register_deactivation_hook( __FILE__, 'deactivate_webinars' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-webinars.php';
require get_stylesheet_directory() . '/inc/webinars/override-functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_webinars() {

	$plugin = new Webinars();
	$plugin->run();

}
run_webinars();
