<?php


require get_stylesheet_directory() .'/inc/webinars/admin/inc/widgets/profile_menu.php'; //Profile menu

	
function lp_switch_view()
{
	if (!empty($_REQUEST['switch']))
	{
		$profilepageid = learn_press_get_page_id( 'profile' );
		update_user_meta( get_current_user_id(), "lp_switch_view", $_REQUEST['switch']);
		wp_redirect( get_permalink($profilepageid) ); exit();
	}
}

add_action('wp_loaded', 'lp_switch_view');

 
?>
