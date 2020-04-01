<?php
$template_file = thim_template_path();
if ( ! is_search() && ( is_page_template( 'page-templates/homepage.php' ) || is_page_template( 'page-templates/maintenance.php' ) || is_page_template( 'page-templates/landing-page.php' ) || is_page_template( 'page-templates/blank-page.php' ) || is_singular( 'lpr_quiz' ) || is_singular( 'lp_quiz' ) || is_page_template( 'page-templates/landing-no-footer.php' ) ) ) {
	load_template( $template_file );

	return;
}
global $wp_query;
$vars = $wp_query->query_vars;
$user = wp_get_current_user();
	

$allowArray = array('administrator', 'lp_teacher');
$haveAccessProfile = !empty(array_intersect($allowArray, $user->roles));

$user     = LP_Global::user();
$profile = LP_Profile::instance();

if ( is_page( 'profile' ) && $profile->get_user_data( 'id' ) == get_current_user_id() && $haveAccessProfile) {
	// echo 'its profile';
	get_header( 'profile' );
}elseif ( is_page( 'assignment-evaluate' ) && $profile->get_user_data( 'id' ) == get_current_user_id() && $haveAccessProfile) {
	// echo 'its profile';
	get_header( 'profile' );
}else {
	get_header();
}
?>
    <section class="content-area">
		<?php
		// echo 'view: ' . $vars['view'] . '<br/>';
		$show_banner = true;
		if ( is_page( 'profile' )) {
			$show_banner = false;
		}
		if(is_page( 'profile' ) && $profile->get_user_data( 'id' ) != get_current_user_id()){
			$show_banner = true;
		}
		if(is_page('assignment-evaluate')){
			$show_banner = false;
		}
		$page_id = get_option( 'learn_press_frontend_editor_page_id' );
		if ( $page_id && is_page( $page_id ) ) 	$show_banner = false;

		if($show_banner){
			get_template_part( 'inc/templates/page-title' );
		} 
		do_action( 'thim_wrapper_loop_start' );
		load_template( $template_file );
		do_action( 'thim_wrapper_loop_end' );
		?>
    </section>
<?php

	get_footer();

