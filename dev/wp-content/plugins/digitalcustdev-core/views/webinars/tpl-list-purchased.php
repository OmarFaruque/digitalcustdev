<?php

include(plugin_dir_path( __DIR__ ). 'learn-press/course/result-heading');
include(plugin_dir_path( __DIR__ ).'learnpress-v2/single-course');

$profile  = LP_Profile::instance();

$user     = LP_Global::user();
$course = LP_Global::course();



$course_item   = LP_Global::course_item();
$webinars = dcd_webinars()->get_purchased_webinars( $user->get_id() );
$profile  = LP_Profile::instance();
$user     = LP_Global::user();
$webinars = dcd_webinars()->get_purchased_webinars( $user->get_id() );

?>

<div class="form-group form-search-fields">
        <input type="text" name="s" class="form-control courses-search" placeholder="Search">
        <?php if ( $filters = $profile->get_purchased_courses_filters( $filter_status ) ) { ?>
            <select class="form-control" onchange="location = this.value;">
                <?php foreach ( $filters as $class => $link ) { ?>
                    <option value="?filter-status=<?php echo $class; ?>" <?php echo isset( $_GET['filter-status'] ) && $_GET['filter-status'] === $class ? 'selected' : false; ?>><?php echo $link; ?></option>
                <?php } ?>
            </select>
        <?php } ?>
    </div>
<table class="table table-striped table-bordered render-webinars-frontend">
    <div class="container">
    
    <?php
    if ( ! empty( $webinars['items'] ) ) {
        foreach ( $webinars['items'] as $webinar ) {
            $course         = learn_press_get_course( $webinar->get_id() );
            $course_type    = get_post_meta( $webinar->get_id(), '_course_type', true );
            $timezone       = get_post_meta( $webinar->get_id(), '_webinar_timezone', true );
            $start_time     = get_post_meta( $webinar->get_id(), '_webinar_start_time', true );
            $webinar_exists = get_post_meta( $webinar->get_id(), '_webinar_ID', true );
            $webinar_detail = get_post_meta( $webinar->get_id(), '_webinar_details', true );
            $course_data       = $user->get_course_data( $webinar->get_id() );
            $course_results    = $course_data->get_results( false );

            $passing_condition = $course->get_passing_condition();



            if ( ! empty( $course_type ) && $course_type === "webinar" ) {
                ?>
                <div class="row">
                <div class="col-md-3">                        
                        <P>
                            <a href="<?php echo $course->get_permalink(); ?>"><?php echo $course->get_image( 'course_thumbnail' ); ?></a>
                        </P>
                        
                     </div>
                <div class="col-md-5"> 
                    <div><span scope="col">Title : </span><a href="<?php echo $course->get_permalink(); ?>"><span scope="row"><?php echo $course->get_title(); ?></span></a></div>
                    <div><span scope="col">Author : </span><a ><span scope="row"><?php 
                    $author_id = get_post_field ('post_author', $webinar->get_id());

                    echo get_the_author_meta( 'display_name' , $author_id );  ?></span></a></div>
                    <!-- <div><span scope="col">Start Time : </span><span><?php// echo date( 'F j, Y, g:i a', strtotime( $start_time ) ); ?></span></div> -->
                    <div><span scope="col">Date : </span><span><?php $date = get_post_field ('post_date', $webinar->get_id());
                     echo date( 'F j, Y, g:i a', strtotime( $date ) ); ?></span></div>
                     <div><span scope="col">Duration : </span> <i class="fa fa-clock-o"></i> 
<span class="e-course-duration"><?php if ( $duration = e_post_duration() ) { echo $duration;    } else { esc_html_e( 'Not set', 'learnpress-frontend-editor' ); } ?> </span>

</div>
                    <div><span scope="col">Timezone : </span><span><?php echo $timezone; ?></span></div>
                    <div><span scope="col">Join : </span><span><?php echo ! empty( $webinar_exists ) && ! empty( $webinar_detail ) ? '<a href="'.$webinar_detail->join_url.'">Join Webinar</a>' : "N/A"; ?></span></div>
                    <div><span scope="col">Action : </span><span><a href="<?php echo get_the_permalink( $webinar->get_id() ); ?>"><i class="fa fa-eye"></i></a></span></div>
                </div>
                <div class="col-md-4"> 
                        <div class="course-progress">
                            <div class="lp-course-progress" data-value="0" data-passing-condition="80%">
                               <!-- <label class="lp-course-progress-heading">Course Creation Progress :
                                        
                                </label>-->
                                <div class="course-progress">

                                        <?php if ( false !== ( $heading = apply_filters( 'learn-press/course/result-heading', __( 'Course Creation Progress :', 'learnpress' ) ) ) ) { ?>
                                            <h4 class="lp-course-progress-heading">
                                                <?php echo esc_html( $heading ); ?>
                                            </h4>
                                        <?php } ?>

                                        <div class="lp-course-status">
                                            <span class="number"><?php echo round( $course_results['result'], 2 ); ?><span
                                                        class="percentage-sign">%</span></span>
                                            <?php if ( $grade = $course_results['grade'] ) { ?>
                                                <span class="lp-label grade <?php echo esc_attr( $grade ); ?>">
                                                <?php learn_press_course_grade_html( $grade ); ?>
                                                </span>
                                            <?php }
                                             ?>
                                        </div>

                                               

                                        <div class="learn-press-progress lp-course-progress '<?php echo $course_data->is_passed() ? ' passed' : ''; ?>"
                                             data-value="<?php echo $course_results['result']; ?>"
                                             data-passing-condition="<?php echo $passing_condition; ?>">
                                            <div class="progress-bg lp-progress-bar">
                                                <div class="progress-active lp-progress-value" style="left: <?php echo $course_results['result']; ?>%;">
                                                </div>
                                            </div>
                                            <div class="lp-passing-conditional"
                                                 data-content="<?php printf( esc_html__( 'Passing condition: %s%%', 'learnpress' ), $passing_condition ); ?>"
                                                 style="left: <?php echo $passing_condition; ?>%;">
                                            </div>
                                        </div>
                                    </div>
                            
                            <!--<div class="lp-progress-bar value">
                                <div class="lp-progress-value percentage-sign" style="width: 80%">   
                                </div>
                            </div>-->

                            </div>
                        </div>
                </div>
                
            </div>
            <button style="float: right; margin-right: 250px;"><a style="color:#fff;" href="<?php echo $course->get_permalink(); ?>"> Goto Webinar</a></button>
                <?php
            }
        }

    }
    ?>

    </div>
</div>
</table>