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

//  echo 'current user id: ' . get_current_user_id() . '<br/>';

defined( 'ABSPATH' ) || exit();
// require_once WP_PLUGIN_DIR . 'learnpress/inc/class-lp-global.php';
$course = LP_Global::course();
$user = LP_Global::user();
$card       = new LP_User_CURD_THEME();
/*$instructor = $course->get_instructor();*/
$profile       = learn_press_get_profile();
$filter_status = LP_Request::get_string( 'filter-status' );
if ( $filter_status === "all" ) {
	$filter_status = false;
}


// purchased course 
$query = $card->query_purchased_courses_theme($user->get_id(), array( 'limit' => 5, 'status' => $filter_status ));

$orders = $card->get_orders( $user->get_id(), array( 'status' => 'completed' ) );

?>

<div class="learn-press-subtab-content om8">
<h3 class="profile-heading"><?php _e('My Courses', 'webinar'); ?></h3>
    <div class="mr-0">
    <div class="form-group form-search-fields purchase om">
        <input type="text" data-data_type="purchased_courses" name="s" class="form-control courses-search" placeholder="Search">
		<?php if ( $filters = get_purchased_courses_filters( $filter_status ) ) {
                unset($filters['not-enrolled']);
            ?>
            <select class="form-control" onchange="location = this.value;">
				<?php foreach ( $filters as $class => $link ) { ?>
                    <option value="?filter-status=<?php echo $class; ?>" <?php echo isset( $_GET['filter-status'] ) && $_GET['filter-status'] === $class ? 'selected' : false; ?>><?php echo $link; ?></option>
				<?php } ?>
            </select>
		<?php } ?>
    </div>
    </div>
    <div class="clear"></div>

	<?php if ( $query['items'] ) { ?>
        <table class="lp-list-table profile-list-courses profile-list-table">
            <div class="container_wrap  thim-course-list profile-courses-list">
                <?php foreach ( $query['items'] as $user_course ) {
                $lessons = get_course_lessons($user_course->get_id());   
                $status = '';
                foreach($lessons as $l => $sl){
                    // $status = $user->get_item_grade( $sl, $user_course->get_id() );
                    $status = get_item_grade_from_theme($sl, $user_course->get_id());
                } 

                    
                ?>
                <?php $course = learn_press_get_course( $user_course->get_id() ); ?>
                <div class="mt-3">
                    <div class=" course-item">                        
                        <!-- <P>
                            <a href="<?php // echo $course->get_permalink(); ?>"><?php // echo $course->get_image( 'course_thumbnail' ); ?></a>
                        </P> -->

                        <?php //do_action( 'thim_courses_loop_item_thumb' ); ?>
                        <div class="course-thumbnail">
                            <a class="thumb" href="<?php echo $course->get_permalink(); ?>">
                            <?php
                                echo thim_get_feature_image( get_post_thumbnail_id( $course->get_id() ), 'full', apply_filters( 'thim_course_thumbnail_width', 400 ), apply_filters( 'thim_course_thumbnail_height', 320 ), $course->get_title() );
                            ?>
                            </a>
                        <div class="course-wishlist-box">
                            <span class="fa fa-heart course-wishlist" data-id="11588" data-nonce="221da73a28" title="Add this course to your wishlist"><span class="text">Wishlist</span></span></div><a class="course-readmore" href="https://digitalcustdev.ru/dev/?post_type=lp_course&amp;p=11588">Read More</a>
                        </div>
                        
                    <div class="thim-course-content position-relative om">
                    <div class="information col-md-7 col-sm-7">
                        <div>
                            <a href="<?php echo $course->get_permalink(); ?>">
                                <h2 class="course-title mt-0 line-height-30 mb-1"><?php echo $course->get_title(); ?></h2>
                            </a>
                        </div>
                        <div><span><?php _e('Instructor Name', 'webinar'); ?> : </span>
                        <a href="<?php echo esc_url( learn_press_user_profile_link( get_post_field( 'post_author', $course->get_id() ) ) ); ?>">
							    <?php echo get_the_author_meta( 'display_name', $course->post_author ); ?>
						</a>
                        </div>
                        <div><span><?php _e('Purchase Date', 'webinar'); ?> : </span>
                            <?php 
                             $purchaseddate = $orders[$course->get_id()][0];
                            //echo $user_course->get_start_time( 'd M Y' ); 
                            echo get_the_date( 'F j, Y, g:i a', $purchaseddate ); 
                            ?>
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



                    <div class="col-md-5 col-sm-5">             
                        <div class="course-progress newstyle mt-6">
                            <div class="lp-course-progress" data-value="0" data-passing-condition="<?php  echo $course->get_passing_condition( true ); ?>">
                            
                            
                            <label class="lp-course-progress-heading color-green mb-0">
                                <?php _e('Passing Grade', 'webinar'); ?> :
                                <span class="value result"><?php echo $course->get_passing_condition( true ); // echo $course->get_passing_condition( true ); ?></span>
                            </label>
                            <div class="lp-progress-bar value">
                                <div class="lp-progress-value passingle-percentage percentage-sign" style="width: <?php  echo $course->get_passing_condition( true ); ?>"></div>
                                <div class="lp-progress-value percentage-sign newstyle" style="width: <?php  echo $user_course->get_percent_result(); ?>"></div>
                            </div>
                            <label class="lp-course-progress-heading color-blue"><?php _e('course progress', 'webinar'); ?> :
                                <span class="value result"><?php echo $user_course->get_percent_result(); // echo $course->get_passing_condition( true ); ?></span>
                            </label>
                            <label class="lp-course-progress-heading color-blue pull-right"><?php _e('Status', 'webinar'); ?> :
                                <span class="value result"><?php echo $status; // echo $course->get_passing_condition( true ); ?></span>
                            </label>

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
