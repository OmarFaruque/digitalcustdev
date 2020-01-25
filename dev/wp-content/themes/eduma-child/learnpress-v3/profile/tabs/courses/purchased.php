<?php
/**
 * Template for displaying purchased courses in courses tab of user profile page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/courses/purchased.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  3.0.11.2
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
$course = LP_Global::course();
$user = LP_Global::user();
/*$instructor = $course->get_instructor();*/
$profile       = learn_press_get_profile();
$filter_status = LP_Request::get_string( 'filter-status' );
if ( $filter_status === "all" ) {
	$filter_status = false;
}
$query = $profile->query_courses( 'purchased', array( 'limit' => 5, 'status' => $filter_status ) );
?>

<div class="learn-press-subtab-content">

    <div class="form-group form-search-fields purchase om">
        <input type="text" name="s" class="form-control courses-search" placeholder="Search">
		<?php if ( $filters = $profile->get_purchased_courses_filters( $filter_status ) ) { ?>
            <select class="form-control" onchange="location = this.value;">
				<?php foreach ( $filters as $class => $link ) { ?>
                    <option value="?filter-status=<?php echo $class; ?>" <?php echo isset( $_GET['filter-status'] ) && $_GET['filter-status'] === $class ? 'selected' : false; ?>><?php echo $link; ?></option>
				<?php } ?>
            </select>
		<?php } ?>
    </div>
    <div class="clear"></div>

	<?php if ( $query['items'] ) { ?>
        <table class="lp-list-table profile-list-courses profile-list-table">
            <div class="container_wrap 1">
                <?php foreach ( $query['items'] as $user_course ) { ?>
                <?php $course = learn_press_get_course( $user_course->get_id() ); ?>
                <div class="row mt-3">
                    <div class="col-md-3">                        
                        <P>
                            <a href="<?php echo $course->get_permalink(); ?>"><?php echo $course->get_image( 'course_thumbnail' ); ?></a>
                        </P>
                        
                     </div>
                    <div class="col-md-5">
                        <div>
                            <a href="<?php echo $course->get_permalink(); ?>">
                                <h2 class="course-title mt-0 line-height-30"><?php echo $course->get_title(); ?></h2>
                            </a>
                        </div>
                        <div><span><?php _e('Instructor Name', 'webinar'); ?> : </span><?php
                        $author_id = get_post_field ('post_author', $course->get_id());
                        $display_name = get_the_author_meta( 'display_name' , $author_id ); 
                        echo $display_name;
                        ?></div>
                        <div><span><?php _e('Enrolled Date', 'webinar'); ?> : </span>
                            <?php  echo $user_course->get_start_time( 'd M Y' ); ?>
                        </div>
                        <!-- Duration -->
                        <div>
                        <span scope="col"><?php _e('Duration', 'webinar'); ?> : </span> <i class="fa fa-clock-o"></i> 
                        <span class="e-course-duration"><?php 
                                if ( $duration = e_post_duration($course->get_id()) ) { 
                                    echo $duration;    
                                } else { 
                                    esc_html_e( 'Not set', 'learnpress-frontend-editor' ); 
                                } 
                            ?> </span>
                        </div>
                        <!-- End Duration -->
                    </div>
                    <div class="col-md-4">             
                        <div class="course-progress">
                            <div class="lp-course-progress" data-value="0" data-passing-condition="<?php  echo $course->get_passing_condition( true ); ?>">
                                <label class="lp-course-progress-heading">course progress :
                                        <span class="value result"><?php  echo $course->get_passing_condition( true ); ?></span>
                                </label>
                            
                            <div class="lp-progress-bar value">
                                <div class="lp-progress-value percentage-sign" style="width: <?php  echo $user_course->get_percent_result(); ?>">   
                                </div>
                            </div>

                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            
            
            <div>
                <div class="list-table-nav">
                    <div colspan="2" class="nav-text">
                        <?php  echo $query->get_offset_text(); ?>
                    </div>
                    <div colspan="2" class="nav-pages">
                        <?php  $query->get_nav_numbers( true, $profile->get_current_url() ); ?>
                    </div>  
                </div>
            </div>
            
        </table>
	<?php } else {
		learn_press_display_message( __( 'No courses!', 'eduma' ) );
	} ?>
</div>
