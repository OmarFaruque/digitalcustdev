<?php

include(plugin_dir_path( __DIR__ ). 'learn-press/course/result-heading');
include(plugin_dir_path( __DIR__ ).'learnpress-v2/single-course');

$profile  = LP_Profile::instance();

$user     = LP_Global::user();
$course = LP_Global::course();


$course_item   = LP_Global::course_item();
$profile  = LP_Profile::instance();
$user     = LP_Global::user();
$card       = new LP_User_CURD();
$webinars = dcd_webinars()->get_purchased_webinars( $user->get_id(), 
    array(
        'limit' => 5
    )
);


$orders = $card->get_orders( $user->get_id(), array( 'status' => 'completed' ) );

?>
<div class="row">
    <div class="form-search-fields om-56">
        <div class="col-md-12 col-sm-12">
            <h3 class="profile-heading"><?php _e('My Webinars', 'webinar'); ?></h3>
        </div>
        <div class="form-group col-md-6">
            <input type="text" name="s" class="form-control courses-search" placeholder="Search">
        </div>
        <?php if ( $filters = get_purchased_webinar_filters( $filter_status ) ) { ?>
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
    <div class="container_wrap 2 thim-course-list profile-courses-list">
    
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
            $status = '';
            foreach($lessons as $l => $sl){

                $status = $user->get_item_grade( $sl, $webinar->get_id() );

                $webinar_details = get_post_meta( $sl, '_webinar_details', true );
                $webinar_when = get_post_meta( $sl, '_lp_webinar_when', true );
                // echo 'webinar when: ' . $webinar_when . '<br/>';
                $webinar_when = str_replace('/', '-', $webinar_when);
                
                $timezone = (isset($webinar_details->timezone)) ? $webinar_details->timezone : $timezone;
                
                $currentdate = strtotime("now");
                if(strtotime($webinar_when) > strtotime("now")){
                    // echo 'big <br/>';
                    $nextdate = ($previousd != '' && strtotime($previousd) > strtotime($webinar_when)) ? $webinar_when : $previousd; 
                    $nextid = ($previousd != '' && strtotime($previousd) > strtotime($webinar_when)) ? $sl : $nextlessonid; 
                    $lessionNumber = ($previousd != '' && strtotime($previousd) > strtotime($webinar_when)) ? $l + 1 : $lessionNumber; 


                    $nextlesson = __('Next Webinar', 'webinar') . ': ' . get_the_title($nextid) . ', <br/>Date: ' . $nextdate;
                    if($timezone != '') $nextlesson .= ', ' . $timezone;
                    $previousd = $webinar_when;
                    $nextlessonid = $sl;
                    // break;
                }
            // $start_time = $webinar_details->start_time;
            }

            // echo 'next lession date: ' . $nextlesson . '<br/>';
    
            
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
            
            if($leftduration != '') $nextlesson .= __('<br/>Time Left: ', 'webinar') . '<span class="duartionleft">' . custom_to_timer($leftduration, $format, true ) . '</span>';
            // $duartiond = ($leftduration != '') ? custom_to_timer($leftduration, $format, true ) . ' left' : '';

            
            ?>
                <div class=" mt-3">
                <div class="course-item">
                    <div class="course-thumbnail">
                            <a class="thumb" href="<?php echo $course->get_permalink(); ?>">
                            <?php
                                echo thim_get_feature_image( get_post_thumbnail_id( $course->get_id() ), 'full', apply_filters( 'thim_course_thumbnail_width', 400 ), apply_filters( 'thim_course_thumbnail_height', 320 ), $course->get_title() );
                            ?>
                            </a>
                        </div>
                    <div class="thim-course-content position-relative om">
                    <div class="information col-md-6 col-sm-6">
                    <div><span scope="col"></span>
                        <a href="<?php echo $course->get_permalink(); ?>">
                            <h2 class="course-title mt-0 line-height-30"><?php echo $course->get_title(); ?></h2>
                        </a>
                    </div>
                    <div><span scope="col"><?php _e('Instructor', 'webinar'); ?> : </span>
                    <a href="<?php echo esc_url( learn_press_user_profile_link( get_post_field( 'post_author', $course->get_id() ) ) ); ?>">
							    <?php echo get_the_author_meta( 'display_name', $course->post_author ); ?>
					</a>
                    </div>
                    <!-- <div><span scope="col">Start Time : </span><span><?php// echo date( 'F j, Y, g:i a', strtotime( $start_time ) ); ?></span></div> -->
                    <div>
                        <span scope="col"><?php _e('Purchased Date', 'webinar'); ?> : </span>
                        <?php 
                            // $enrolled_date = $webinar->get_start_time();
                            $purchaseddate = $orders[$webinar->get_id()][0];
                            // $date = get_post_field ('post_date', $webinar->get_id());
                            echo get_the_date( 'F j, Y, g:i a', $purchaseddate ); 
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
                    <!-- <div><span scope="col">Join : </span><span><?php // echo ! empty( $webinar_exists ) && ! empty( $webinar_detail ) ? '<a href="'.$webinar_detail->join_url.'">Join Webinar</a>' : "N/A"; ?></span></div> -->
                    <!-- <div><span scope="col">Action : </span><span><a href="<?php // echo get_the_permalink( $webinar->get_id() ); ?>"><i class="fa fa-eye"></i></a></span></div> -->
                </div>
                <div class="col-md-6"> 
                        <div class="course-progress newstyle">
                            <div class="lp-course-progress" data-value="0" data-passing-condition="80%">
                               <!-- <label class="lp-course-progress-heading">Course Creation Progress :
                                        
                                </label>-->
                                <div class="course-progress">

                                        <?php if ( false !== ( $heading = apply_filters( 'learn-press/course/result-heading', __( 'Course Creation Progress :', 'learnpress' ) ) ) ) { ?>
                                            <!-- <h4 class="lp-course-progress-heading">
                                                <?php // echo esc_html( $heading ); ?>
                                            </h4> -->
                                        <?php } ?>

                                    


                                        <div class="course-progress mt-0">
                                            <div class="lp-course-progress" data-value="<?php  echo $webinar->get_percent_result(); ?>" data-passing-condition="<?php  echo $course->get_passing_condition( true ); ?>">
                                                <label class="lp-course-progress-heading color-green mb-0">
                                                    <?php _e('Passing Grade', 'webinar'); ?> :
                                                        <span class="value result"><?php echo $course->get_passing_condition( true ); // echo $course->get_passing_condition( true ); ?></span>
                                                </label>
                                            <div class="lp-progress-bar value">
                                                <div class="lp-progress-value passingle-percentage" style="width: <?php  echo $course->get_passing_condition( true ); ?>"></div>
                                                <div class="lp-progress-value percentage-sign newstyle" style="width: <?php  echo $webinar->get_percent_result(); ?>"></div>
                                            </div>
                                                <label class="lp-course-progress-heading color-blue"><?php _e('Course Progress', 'webinar'); ?> :
                                                        <span class="value result"><?php echo $webinar->get_percent_result(); // echo $course->get_passing_condition( true ); ?></span>
                                                </label>
                                                <label class="lp-course-progress-heading color-blue ml-5"><?php _e('Status', 'webinar'); ?> :
                                                        <span class="value result"><?php echo $status; // echo $course->get_passing_condition( true ); ?></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                            

                            <?php if($course->get_post_status() === "publish" && $nextdate != ''){ ?>
                                <div class="nextlesson student mt-1"><?php echo $nextlesson; ?></div>
                            <?php } ?>
                           

                            </div>
                            </div>
                            </div>
                            <?php if(!empty($lessionID) && $course->get_post_status() === "publish" && $nextdate != ''): ?>
								<a class="mt-2 next-webinar-lesson" href="<?php echo  get_the_permalink( $lessionID ); ?> ">
									<button class="mt-1"><?php echo sprintf('Go to Webinar lession#%d', $lessionNumber); ?></button>
								</a>
						<?php endif; ?>
                        </div>
                       
                        

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