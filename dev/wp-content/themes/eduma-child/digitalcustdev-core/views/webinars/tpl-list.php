<?php
/**
 * Template for displaying own courses in courses tab of user profile page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/courses/own.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  3.0.11.2
 */

/**
 * Prevent loading this file directly
 */

// $dcd_webinars = new DigitalCustDev_CPT_Functions;
defined( 'ABSPATH' ) || exit();

$profile       = learn_press_get_profile();
$user_id = $profile->get_user()->get_id();
$filter_status = LP_Request::get_string( 'filter-status' );
if ( $filter_status === "all" ) {
	$filter_status = false;
}
$query         = dcd_webinars()->query_own_courses_webinars( 
	$user_id, 
	array( 
		'status' => $filter_status,
		'limit' => 5,
		'orderby' => 'post_date',
		'order' => 'DESC'
		) 
);



// $dcd_webinars->testFunction();

$draft_counter = query_draft_counter($user_id, 'webinar');

?>

<div class="learn-press-subtab-content om3">

    <h3 class="profile-heading">
		<?php _e( 'Webinar Courses', 'eduma' ); ?>
    </h3>
		<?php if ( $draft_counter >= 3 ) { ?>
            <div class="row">
				<div class="col-md-12">
					<div class="message message-info mb-0">
						<p>You cannot create new courses because you already have 3 courses in draft.</p>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="form-search-fields row Om2">
        <div class="<?php echo ($profile->is_current_user() && $draft_counter < 3) ? 'col-md-5':'col-md-6'; ?> no-padding-left">
            <input data-data_type="own-webinars" type="text" name="s" data-searchtype="webinar" class="form-control courses-search" data-security="<?php echo wp_create_nonce( 'search-once' ); ?>" placeholder="Search">
        </div>
        <div class="<?php echo ($profile->is_current_user() && $draft_counter < 3) ? 'col-md-5':'col-md-6'; ?> no-padding">
			<?php if ( $filters = get_own_courses_filters_custom( $filter_status ) ) { ?>
                <select class="form-control" onchange="location = this.value;">
					<?php foreach ( $filters as $class => $link ) { ?>
                        <option value="?filter-status=<?php echo $class; ?>" <?php echo isset( $_GET['filter-status'] ) && $_GET['filter-status'] === $class ? 'selected' : false; ?>><?php echo $link; ?></option>
					<?php } ?>
                </select>
			<?php } ?>
        </div>
			<?php
			if ( $profile->is_current_user() ) {
				if ( $draft_counter < 3 ) {
					?>
					<div class="col-md-2 no-padding">
                    	<a class="btn btn-primary e-button-icon create-webinar pull-right mb-1" data-nonce="<?php echo wp_create_nonce( 'e-new-post' ); ?>" data-type="lp_course" href="<?php echo home_url( 'sozdat-kurs/edit-post?post-type=lp_course' ); ?>">New Webinar</a>
					</div>
					<?php
				}
			}
			?>
        
    </div>
    <div class="clear"></div>

	<?php if ( ! $query['total'] ) {
		learn_press_display_message( __( 'No courses!', 'eduma' ) );
	} else { ?>

        <div class="thim-course-list profile-courses-list pr-1">
			<?php
			global $post;



			foreach ( $query['items'] as $item ) {
				if ( get_post_status( $item ) == 'pending' ) {
					$viewing_user = $profile->get_user();
					if ( $viewing_user->get_id() != get_current_user_id() ) {
						continue;
					}
				}
				$course = learn_press_get_course( $item );
				$post   = get_post( $item );
				setup_postdata( $post );
				dcd_core_get_template( 'content-profile-webinar.php' );
			}
			wp_reset_postdata();
			?>
        </div>

		<?php $query->get_nav( '', true, $profile->get_current_url() ); ?>

	<?php } ?>
</div>

<div class="col-md-6">
	<iframe width="789" height="350" src="https://www.youtube.com/embed/YyZKtVLO-CE" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

</div> 
<div class="col-md-6 crse-bx">
	<p class="alcrse-txt">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
<button class="strt">Get Started</button>
</div> 