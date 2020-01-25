<?php

include(plugin_dir_path( __DIR__ ). 'learn-press/course/result-heading');
include(plugin_dir_path( __DIR__ ).'learnpress-v2/single-course');

$profile  = LP_Profile::instance();

$user     = LP_Global::user();
$course = LP_Global::course();


$course_item   = LP_Global::course_item();
$profile  = LP_Profile::instance();
$user     = LP_Global::user();
$webinars = dcd_webinars()->get_purchased_webinars( $user->get_id(), 
    array(
        'limit' => 5
    )
);

?>
<div class="row">
    <div class="form-search-fields om-56">
        <div class="form-group col-md-6">
            <input type="text" name="s" class="form-control courses-search" placeholder="Search">
        </div>
        <?php if ( $filters = $profile->get_purchased_courses_filters( $filter_status ) ) { ?>
            <div class="form-group col-md-6">
                <select class="form-control" onchange="location = this.value;">
                    <?php foreach ( $filters as $class => $link ) { ?>
                        <option value="?filter-status=<?php echo $class; ?>" <?php echo isset( $_GET['filter-status'] ) && $_GET['filter-status'] === $class ? 'selected' : false; ?>><?php echo $link; ?></option>
                    <?php } ?>
                </select>
            </div>
        <?php } ?>
    </div>
</div>
<table class="table table-striped table-bordered render-webinars-frontend">
    <div class="container_wrap 2">
    
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



            /*
            * Get Webinar lessons
            */
            $lessons = get_course_lessons($webinar->get_id());

            $nextlesson = '';
            $previousd = '';
            $nextlessonid = '';
            $timezone = '';
            
            $nextdate = '';
            $lessionNumber = 1;
            
            foreach($lessons as $l => $sl){
                $webinar_details = get_post_meta( $sl, '_webinar_details', true );
                $webinar_when = get_post_meta( $sl, '_lp_webinar_when', true );
                $webinar_when = str_replace('/', '-', $webinar_when);
                
                $timezone = (isset($webinar_details->timezone)) ? $webinar_details->timezone : $timezone;
                
                $currentdate = strtotime("now");
                if(strtotime($webinar_when) > strtotime("now")){
                    $nextdate = ($previousd != '' && strtotime($previousd) > strtotime($webinar_when)) ? $webinar_when : $previousd; 
                    $nextid = ($previousd != '' && strtotime($previousd) > strtotime($webinar_when)) ? $sl : $nextlessonid; 
                    $lessionNumber = ($previousd != '' && strtotime($previousd) > strtotime($webinar_when)) ? $l + 1 : $lessionNumber; 
                    
                    $nextlesson = __('Next Webinar', 'webinar') . ': ' . get_the_title($nextid) . ', Date: ' . $nextdate . ', ' . $timezone;
                    $previousd = $webinar_when;
                    $nextlessonid = $sl;
                    
                    // break;
                }
            // $start_time = $webinar_details->start_time;
            }

            
            $lessionID = ($lessionNumber > 0) ? $lessons[$lessionNumber - 1] : $lessons[$lessionNumber];
            // echo 'lession ID:  ' . $lessionID . '<br/>';
            
            $format = array(
                'day'    => _x( '%s day', 'duration', 'eduma' ),
                'hour'   => _x( '%s hour', 'duration', 'eduma' ),
                'minute' => _x( '%s min', 'duration', 'eduma' ),
                'second' => _x( '%s sec', 'duration', 'eduma' ),
            );

            $leftduration = '';
            if($nextdate) $leftduration =  strtotime($nextdate) - strtotime('now');
            
            
            $duartiond = ($leftduration != '') ? custom_to_timer($leftduration, $format, true ) . ' left' : '';

            
            ?>
                <div class="row mt-3">
                <div class="col-md-3">                        
                        <P>
                            <a href="<?php echo $course->get_permalink(); ?>"><?php echo $course->get_image( 'course_thumbnail' ); ?></a>
                        </P>
                        
                     </div>
                <div class="col-md-5"> 
                    <div><span scope="col"></span>
                        <a href="<?php echo $course->get_permalink(); ?>">
                            <h2 class="course-title mt-0 line-height-30"><?php echo $course->get_title(); ?></h2>
                        </a>
                    </div>
                    <div><span scope="col"><?php _e('Instructor', 'webinar'); ?> : </span>
                    <a>
                        <?php 
                        $author_id = get_post_field ('post_author', $webinar->get_id());
                            echo get_the_author_meta( 'display_name' , $author_id );  
                        ?>
                    </a>
                    </div>
                    <!-- <div><span scope="col">Start Time : </span><span><?php// echo date( 'F j, Y, g:i a', strtotime( $start_time ) ); ?></span></div> -->
                    <div>
                        <span scope="col"><?php _e('Enrolled Date', 'webinar'); ?> : </span>
                        <?php 
                            $enrolled_date = $webinar->get_start_time();
                            // $date = get_post_field ('post_date', $webinar->get_id());
                            echo date( 'F j, Y, g:i a', strtotime( $enrolled_date ) ); 
                        ?>
                    </div>
                    <div>
                        <span scope="col">Duration : </span> <i class="fa fa-clock-o"></i> 
                        <span class="e-course-duration"><?php 
                                if ( $duration = e_post_duration($webinar->get_id()) ) { 
                                    echo $duration;    
                                } else { 
                                    esc_html_e( 'Not set', 'learnpress-frontend-editor' ); 
                                } 
                            ?> </span>
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

                                               

                                        <div class="learn-press-progress lp-course-progress <?php echo $course_data->is_passed() ? ' passed' : ''; ?>"
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

                            <?php if($course->get_post_status() === "publish" && $nextdate != ''){ ?>
                                <div class="nextlesson student mt-1"><?php echo $nextlesson; ?></div>
                            <?php } ?>
                            <?php if(!empty($duartiond)){ ?>
									<span class="duartionleft"><?php echo $duartiond; ?></span>
							<?php } ?>

                            </div>
                        </div>
                        <?php if(!empty($lessionID)): ?>
								<a class="mt-2" href="<?php echo  get_the_permalink( $lessionID ); ?> ">
									<button class="mt-1"><?php echo sprintf('Go to Webinar lession#%d', $lessionNumber); ?></button>
								</a>
						<?php endif; ?>
                        

                </div>
                
            </div>
            
        <?php   
        }
    }
    ?>
    </div>
    <div>
                <div class="list-table-nav">
                    <div colspan="2" class="nav-text">
                        <?php  echo $webinars->get_offset_text(); ?>
                    </div>
                    <div colspan="2" class="nav-pages">
                        <?php  $webinars->get_nav_numbers( true, $profile->get_current_url() ); ?>
                    </div>  
                </div>
            </div>
            

</div>
</table>