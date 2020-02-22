<?php
/**
 * Template for displaying user profile tabs.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/profile/tabs.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$profile = LP_Profile::instance();

$profile_id = $profile->get_user()->get_id();
$tabs = $profile->get_tabs()->tabs();

if (!user_can($profile_id, 'edit_lp_courses')) {
	unset($tabs['instructor']);
}

$user     = LP_Global::user();
global $wp_query;
$vars = $wp_query->query_vars;
$current_user = wp_get_current_user();
	
$allowArray = array('administrator', 'lp_teacher');
$haveAccessProfile = !empty(array_intersect($allowArray, $current_user->roles));

$profile2 = LP_Profile::instance();
if($profile2->get_user_data( 'id' ) != get_current_user_id()){
    $tabs = array(
        'courses' => array(
            'title' => 'this is title'
		),
		'webinars' => array(
			'title' => 'Webinars tab'
		)
    );
}else{
    $tabs = array();
}
// $tabs = $profile->get_tabs()->tabs();



$course_query = query_own_courses_custom( $profile_id, array( 
	'limit' => 10, 
	'status' => 'publish',
	'orderby' => 'post_date',
    'order'   => 'DESC'
) );


$webinar_query   = dcd_webinars()->query_own_courses_webinars( 
	$profile_id, 
	array( 
		'status' => 'publish',
		'limit' => 10,
		'orderby' => 'post_date',
		'order' => 'DESC'
		) 
);
?>

<?php do_action( 'learn-press/before-profile-nav', $profile ); ?>

	<ul class="nav nav-tabs unauthorizedtab" role="tablist">
		<?php
		foreach ( $tabs as $tab_key => $tab_data ) {
			$publis = false;
			switch($tab_key){
				case 'courses':
					if($course_query['total'] > 0) $publis = true;					
				break;
				case 'webinars':
					if($webinar_query['total'] > 0) $publis = true;
				break;
				default:
				$publis = true;
			}

			// if ( $tab_data->is_hidden() || ! $tab_data->user_can_view() ) {
			// 	continue;
			// }

			$slug        = $profile->get_slug( $tab_data, $tab_key );
			$link        = $profile->get_tab_link( $tab_key, true );
			// echo 'Link: ' . $link . '<br/>';
			$tab_classes = array( esc_attr( $tab_key ) );

			if ( $profile->is_current_tab( $tab_key ) ) {
				$tab_classes[] = 'active';
			}
			if($publis){
			?>

			<li class="<?php echo join( ' ', $tab_classes ) ?>">
				<!--tabs 12-->
				<a href="<?php echo esc_attr( $link ); ?>" data-slug="<?php echo esc_attr( $link ); ?>">
					<?php echo apply_filters( 'learn_press_profile_' . $tab_key . '_tab_title', esc_html( $tab_data['title'] ), $tab_key ); ?>
				</a>
			</li>
		<?php 
			} //if($publis)
			} ?>

	</ul>
	<?php if($profile2->get_user_data( 'id' ) != get_current_user_id()){ ?>
	<script>
		jQuery(document).ready(function(){
			if(jQuery('ul.unauthorizedtab').length){
				if(!jQuery('ul.unauthorizedtab li.active').length){
					var href = jQuery('ul.unauthorizedtab li:first-child').find('a').attr('href');
					location.replace(href);
				}
			}
		});
	</script>
	<?php } ?>

<?php do_action( 'learn-press/after-profile-nav', $profile ); ?>