<?php

/*
Plugin Name: LearnPress - Frontend Editor
Plugin URI: http://thimpress.com/learnpress
Description: Create your course in frontend.
Author: ThimPress
Version: 3.0.7
Author URI: http://thimpress.com
Tags: learnpress, lms
Text Domain: learnpress-frontend-editor
Domain Path: /languages/
*/

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

define( 'LP_ADDON_FRONTEND_EDITOR_FILE', __FILE__ );
define( 'LP_ADDON_FRONTEND_EDITOR_PATH', dirname( __FILE__ ) );
define( 'LP_ADDON_FRONTEND_EDITOR_VER', '3.0.7' );
define( 'LP_ADDON_FRONTEND_EDITOR_REQUIRE_VER', '3.2.5' );

/**
 * Class LP_Addon_Frontend_Editor_Preload
 */
class LP_Addon_Frontend_Editor_Preload {

	/**
	 * LP_Addon_Frontend_Editor_Preload constructor.
	 */
	public function __construct() {
		add_action( 'learn-press/ready', array( $this, 'load' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		register_activation_hook( __FILE__, array( $this, 'init' ) );
	}

	public function init() {
		global $wpdb;

		$query = $wpdb->prepare( "
		    SELECT option_value
		    FROM {$wpdb->options}
		    WHERE option_name = %s
		", 'learn_press_frontend_editor_disable_admin' );

		if ( ! $wpdb->get_var( $query ) ) {
			$wpdb->insert(
				$wpdb->options,
				array(
					'option_name'  => 'learn_press_frontend_editor_disable_admin',
					'option_value' => 'yes',
					'autoload'     => 'yes'
				)
			);
		}
	}

	/**
	 * Load addon
	 */
	public function load() {
		LP_Addon::load( 'LP_Addon_Frontend_Editor', 'inc/load.php', __FILE__ );
		remove_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Admin notice
	 */
	public function admin_notices() {
		?>
        <div class="error">
            <p><?php echo wp_kses(
					sprintf(
						__( '<strong>%s</strong> addon version %s requires %s version %s or higher is <strong>installed</strong> and <strong>activated</strong>.', 'learnpress-coupon' ),
						__( 'LearnPress Coupon', 'learnpress-coupon' ),
						LP_ADDON_FRONTEND_EDITOR_VER,
						sprintf( '<a href="%s" target="_blank"><strong>%s</strong></a>', admin_url( 'plugin-install.php?tab=search&type=term&s=learnpress' ), __( 'LearnPress', 'learnpress-coupon' ) ),
						LP_ADDON_FRONTEND_EDITOR_REQUIRE_VER
					),
					array(
						'a'      => array(
							'href'  => array(),
							'blank' => array()
						),
						'strong' => array()
					)
				); ?>
            </p>
        </div>
		<?php
	}
}

new LP_Addon_Frontend_Editor_Preload();