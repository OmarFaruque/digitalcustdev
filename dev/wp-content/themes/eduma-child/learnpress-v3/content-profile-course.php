<?php
/**
 * Template for displaying course content within the loop.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/content-course.php
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
$draft_counter = query_draft_counter(get_current_user_id(), '');


if ( wp_doing_ajax() ) {
	$user = wp_get_current_user();
} else {
	$user = LP_Global::user();
}

$theme_options_data         = get_theme_mods();
$course_item_excerpt_length = intval( get_theme_mod( 'thim_learnpress_excerpt_length', 25 ) );

$class = isset( $theme_options_data['thim_learnpress_cate_grid_column'] ) && $theme_options_data['thim_learnpress_cate_grid_column'] ? 'course-grid-' . $theme_options_data['thim_learnpress_cate_grid_column'] : 'course-grid-5';
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$class .= ' lpr_course';

$course = LP_Global::course();
$count  = $course->get_users_enrolled();

global $frontend_editor;
$post_manage = $frontend_editor->post_manage;
// $type        = get_post_meta( get_the_ID(), '_course_type', true );
	?>

    <div id="post-<?php the_ID(); ?>" <?php post_class( $class ); ?>>

		<?php
		// @deprecated
		do_action( 'learn_press_before_courses_loop_item' );
		?>

        <div class="course-item">

			<?php
			// @since 3.0.0
			do_action( 'learn-press/before-courses-loop-item' );
			?>

			<?php
			// @thim
			do_action( 'thim_courses_loop_item_thumb' );
			?>

            <div class="thim-course-content position-relative om 1">
            	<div class="row">
                <div class="course-title-wrapper">
					<?php $instructor = $course->get_instructor(); 
						// echo 'status: ' . $course->get_post_status() . '<br/>';
					?>
					<?php if ( $course->get_post_status() === "pending" || $course->get_post_status() === "publish" ) {
						?>
                        <h2 class="course-title 1"><a href="<?php echo $course->get_permalink();  ?>" rel="bookmark"><?php echo empty( get_the_title() ) ? 'New Webinar' : get_the_title(); ?></a></h2>
						<?php
					} else { ?>
                        <h2 class="course-title 2"><a target="_blank" href="<?php echo ! empty( $instructor->get_id() ) && $instructor->get_id() === $user->get_id() ? esc_url( $post_manage->get_edit_post_link( get_post_type(), get_the_id() ) ) : get_permalink(); ?>" rel="bookmark"><?php echo empty( get_the_title() ) ? 'New Course Title' : get_the_title(); ?></a></h2>
					<?php } ?>
                </div>
                <div class="course-meta">
					<?php //thim_course_ratings(); ?>
                    <div class="course-students">
                    	<div class="row">
						<div class="col-md-3 col-xs-12 col-sm-3">
                    		<span class="e-label <?php echo esc_attr( get_post_status() ); ?>" ><?php if(e_post_status( $post )=="Draft"){
                    			echo "Draft Created";
                    		}
                    		elseif(e_post_status( $post )=="Publish"){
                    			echo "Published";
                    		}
                    		else{
                    			echo "Pending";
                    		} ; ?>
                      		</span>&nbsp;
                      		<div class="value"><i class="fa fa-group"></i>
								<span><?php echo esc_html( $count ); ?>&nbsp;<?php echo $count > 1 ? sprintf( __( 'users', 'learnpress' ) ) : sprintf( __( 'user', 'learnpress' ) ); ?></span>
							</div>
                    	
						</div>
                    	
						<div class="<?php echo ( $course->get_post_status() !== "publish") ? 'col-md-4 col-xs-12 col-sm-4': 'col-md-6 col-xs-12 col-sm-6'; ?>">
								<span><?php echo "<span><i class='fa fa-calendar'></i></span>&nbsp;".get_the_date( 'F j, Y', strtotime( $start_time ) ); ?></span>
								<div>
									<i class="fa fa-clock-o"></i>     
									<span class="e-course-duration"><?php if ( !empty($duration) && $duration = e_post_duration() ) { 	echo $duration;	} else { esc_html_e( 'Not set', 'learnpress-frontend-editor' );	} ?> </span>
								</div>
						</div>
						

						<?php if ( $course->get_post_status() !== "pending" && $course->get_post_status() !== "publish" ) { ?>
                		<div class="f course-progress col-md-5 col-sm-5 col-xs-12 pull-right">
                            <div class="lp-course-progress" data-value="0" data-passing-condition="80%">
                            <div class="lp-progress-bar value">
                                <div class="lp-progress-value percentage-sign" style="width: 80%">   
                                </div>
                            </div>
							<label class="lp-course-progress-heading"><?php _e('Course  creation progress', 'webinar'); ?> :</label>
                            </div>
                        </div>
                        <?php } ?>

						</div>
						
				
                    </div>
                </div>


                <div class="course-published-status pull-left">
                    <span class="course-published <?php echo $course->get_post_status() === "draft" ? 'active' : 'inactive'; ?>">Draft</span><span class="course-pending <?php echo $course->get_post_status() === "pending" ? 'active' : 'inactive' ?>">Pending Review</span><span class="course-published <?php echo $course->get_post_status() === "publish" ? 'active' : 'inactive' ?>">Publish</span>
                    
                </div>
               
                </div>

                <div class="course-readmore position-absulate-bottom om5" >

					<?php if ( $course->get_post_status() !== "pending" && $course->get_post_status() !== "publish" ) { ?>
                        <a data-toggle="tooltip" data-placement="top" title="View" href="<?php echo esc_url( get_permalink() ); ?>"><i class="fa fa-eye"></i></a>
					<?php }
					if($draft_counter < 3){
					?>
                    	<a data-toggle="tooltip" data-placement="top" title="Copy" class="diuplicate_frontend" data-post-id="<?php echo get_the_ID(); ?>" href="#"><i class="fa fa-clone"></i></a>
					<?php }
					if ( $course->get_post_status() !== "pending" && $course->get_post_status() !== "publish" ) { ?>
                        <a data-toggle="tooltip" data-placement="top" title="Delete" href="javascript:void(0);" data-security="<?php echo wp_create_nonce( 'deleting-post' ); ?>" data-postid="<?php echo get_the_ID(); ?>" class="delete-post-course-frontend"><i class="fa fa-trash"></i></a>
					<?php } ?>
                    
                </div>

                <div class="clear"></div>
            </div>

			<?php
			// @since 3.0.0
			do_action( 'learn-press/after-courses-loop-item' );
			?>

        </div>

		<?php
		// @deprecated
		do_action( 'learn_press_after_courses_loop_item' );
		?>

    </div>