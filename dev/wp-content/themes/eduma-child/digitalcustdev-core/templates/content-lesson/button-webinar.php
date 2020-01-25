<?php
/**
 * Template for displaying complete button in content lesson.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/content-lesson/button-complete.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
$course = LP_Global::course();
$user   = LP_Global::user();
$item   = LP_Global::course_item();

if ( $item->is_preview() ) {
	return;
}

$zoom_api_meeting_link = get_post_meta( $item->get_id(), '_webinar_details', true );
$actual_link           = get_post_meta( $item->get_id(), '_lp_webinar_registration_user' . $user->get_id(), true );
if ( empty( $zoom_api_meeting_link ) && empty( $zoom_api_meeting_link->id ) ) {
	return;
}

if ( ! empty( $actual_link ) ) {
	$actual_link = json_decode( $actual_link );
	$join_link   = $actual_link->join_url;
	?>
    <a href="<?php echo $join_link; ?>" class="btn btn-primary btn-join-webinar">JOIN WEBINAR</a>
	<?php
}