<?php
/**
 * Plugin Name: LearnPress Enhance
 * Plugin URI: 
 * Description: This will add some new features / widgets to learnpress.
 * Version: 0.1
 * Author: Ikechukwu Mbilitem
 * Author URI: https://www.linkedin.com/in/xtremeleo/
 * License: GPL2
 */
 
define( 'LPE__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

 
	if ( in_array( 'learnpress/learnpress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
	{
		 /**
		 * Load Widgets.
		 */
		require LPE__PLUGIN_DIR.'inc/widgets/profile_menu.php'; //Profile menu
		
	}
	
function lp_switch_view()
{
	if (!empty($_REQUEST['switch_to']))
	{
		$view = htmlspecialchars($_REQUEST['switch_to']);
		$url = htmlspecialchars($_REQUEST['r']);
		
		$current_user = wp_get_current_user();
		$switched_view = get_user_meta($profile_id,"lp_switch_view", true);
		update_user_meta( $current_user->ID, "lp_switch_view", $view, "");
		
		wp_redirect( $url );
	}
	
	//exit;
}

add_action('init', 'lp_switch_view');

 
?>
