<?php 
		$getmetas = get_user_meta( get_current_user_id() , 'lp_switch_view', true );
		if($getmetas == 'student'){ ?>
			<script>
					document.getElementById('studentPurchasedCourseLink').click();
			</script>
<?php } ?>

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
defined( 'ABSPATH' ) || exit();


$profile       = learn_press_get_profile();
$user_id = $profile->get_user()->get_id();



$filter_status = LP_Request::get_string( 'filter-status' );
if ( $filter_status === "all" ) {
	$filter_status = false;
}

$query = query_own_courses_custom( $user_id, array( 
	'limit' => 5, 
	'status' => $filter_status,
	'orderby' => 'post_date',
    'order'   => 'DESC'
) );
// $query         = $profile->query_courses( 'own', 
// 	array( 
// 		'limit' => 5, 
// 		'status' => $filter_status,
// 		'order_by' => 'date',
// 		'order'   => 'DESC'
// 	) 
// );

$draft_counter = query_draft_counter($user_id, '');

?>

<div class="learn-press-subtab-content om5">

    <h3 class="profile-heading">
		<?php _e( 'Courses', 'eduma' ); ?>
    </h3>
<!--     <div class="thim-switch-roles-content">
		<button data-security="<?php// echo wp_create_nonce( 'switching-role' ); ?>" class="profile-switch-roles"><?php //echo in_array( 'lp_teacher', $current_user->roles ) ? 'Owned' : 'Owned'; ?></button>
		</div> -->
    <div class="form-search-fields_custom row mb-2">
		<?php if ( $draft_counter > 3 ) { ?>
            <div class="col-md-12">
                <div class="message message-info mb-0">
                    <p><?php _e('You cannot create new courses because you already have 3 courses in draft.', 'webinar'); ?></p>
                </div>
            </div>
		<?php } ?>
		</div>
	<div class="row mb-4">
        <div class="<?php echo ($profile->is_current_user() && $draft_counter < 3 ) ? 'col-md-5' : 'col-md-6';  ?>  no-padding-left">
            <input type="text" name="s" class="form-control courses-search" data-security="<?php echo wp_create_nonce( 'search-once' ); ?>" placeholder="Search course...">
        </div>

        <div class="<?php echo ($profile->is_current_user() && $draft_counter < 3 ) ? 'col-md-5' : 'col-md-6';  ?> no-padding">
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
                    	<a class="btn btn-primary btn-block e-button-icon e-button add digitalcustDev-create-post" data-nonce="<?php echo wp_create_nonce( 'e-new-post' ); ?>" data-type="lp_course" href="<?php echo home_url( 'sozdat-kurs/edit-post?post-type=lp_course' ); ?>">New Course</a>
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
        <div class=" omar thim-course-list profile-courses-list">
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
				learn_press_get_template( 'content-profile-course.php' );
				
			}
			wp_reset_postdata();
			?>
        </div>

		<?php $query->get_nav( '', true, $profile->get_current_url() ); ?>

	<?php } ?>
</div>
<div class="col-md-6">
	<iframe width="853" height="350" src="https://www.youtube.com/embed/y9RBCz7kjEE" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

</div> 
<div class="col-md-6 crse-bx">
	<p class="alcrse-txt">How to create course useful tips for instructor:</p>
<button class="strt">Get Started</button>
</div> 
