<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}



// echo 'from option: ' . get_option('gmt_offset') . '<br/>';


if ( class_exists( 'WPEMS' ) ) {
    $item = LP_Global::course_item();
    $start_time = get_post_meta( $item->get_id(), '_lp_webinar_when_gmt', true );
    $change_sdate = str_replace( '/', '-', $start_time );
    

    $time   = date( 'Y-m-d H:i:s', strtotime( $change_sdate ) );

    $master_token = get_option('zoom_master_host_token');
    $ah_start_link = get_post_meta($item->get_id(), 'ah_start_link', true);

    $details = get_post_meta( $item->get_id(), '_webinar_details', true );


    // echo 'ah_start_link: ' . $ah_start_link . '<br/>';

    // echo 'master url: ' . get_post_meta($item->get_id(), 'mh_start_link', true) . '<br/>';
    // echo 'm0aster token: ' . $master_token . '<br/>';
    
    // $timezone-- = get_post_meta($item->get_id(), '_lp_timezone', true);

    
    

                    


    $browserTimezone = new DateTimeZone('UTC');
    if(isset($_COOKIE['wb_timezone'])) $browserTimezone = new DateTimeZone($_COOKIE['wb_timezone']);
    

    $timezone = 'UTC';
    $newcreatedtime = new DateTime("now", new DateTimeZone( $timezone ) );
    $now = $newcreatedtime->format('Y-m-d H:i:s');
    
    // $offset = $browserTimezone->getOffset($newcreatedtime);
    // $now = date('Y-m-d H:i:s', strtotime($now) + $offset);
    
    
						
    $linkvisiable = date( 'Y-m-d H:i', strtotime('-10 minutes', strtotime( $time )) );

    // End Time 
    $duration   = get_post_meta( $item->get_id(), '_lp_duration', true );
    $endtime    = strtotime('+'.$duration, strtotime($time));
    $endtime    = strtotime('+5 minutes', $endtime);

    // echo 'end time : ' . date('Y-m-d H:i:s', $endtime) . '<br/>';

    
    // echo 'current user hoster: ' . get_user_meta(get_current_user_id(), 'user_zoom_hostid', true) . '<br/>';
    // echo 'webinar hoster: ' . get_post_meta($item->get_id(), '_lp_alternative_host', true) . '<br/>';

    
    
    

    
    // Thumb Image
    $course = learn_press_get_item_courses( $item->get_id() );
    $thumbImgSrc = (has_post_thumbnail($course[0]->ID)) ? wp_get_attachment_url( get_post_thumbnail_id($course[0]->ID) ) : get_stylesheet_directory_uri() . '/assets/images/presentaion-1.jpg';


    if( $now < $linkvisiable && $linkvisiable < date('Y-m-d H:i', $endtime)){
        update_post_meta($item->get_id(), 'zoom_status', 'inactive');
        // $time = date('Y-m-d H:i:s', strtotime('-3 hours', strtotime($time)));
        $date = new DateTime(date('Y-m-d H:i:s', strtotime($time)));
        
        ?>
        <div class="tp-event-top single_lp_lesson">
		    <div class="entry-thumbnail">
				<img width="1000" height="667" 
                src="<?php echo $thumbImgSrc; ?>" 
                class="attachment-post-thumbnail size-post-thumbnail wp-post-image" 
                alt="" srcset="<?php echo $thumbImgSrc; ?>" sizes="(max-width: 1000px) 100vw, 1000px">			    
			</div>
            <div class="entry-countdown">
                <!-- <div class="tp_event_counter"
                    data-time="<?php //echo esc_attr($date->format('M j, Y H:i:s O')); ?>">
                </div>  -->

                <div id="lesson_zoom_counter" class="text-white" data-time="<?php echo esc_attr($date->format('M j, Y H:i:s')); ?>">
                </div> 
            </div> 
        </div>
        <?php
    }
    
    elseif($now > date('Y-m-d H:i', $endtime)  ){
        $webinarId = get_post_meta( $item->get_id(), '_webinar_ID', true );
        

    




        $zoom_status = get_post_meta( $item->get_id(), 'zoom_status', true );
        $play_url = '';
        $video_error = '';

        if($details->settings->auto_recording == 'cloud'){
            if($zoom_status == 'passed'){
                $play_url_meta = get_post_meta( $item->get_id(), 'play_url', true );
                if(!$play_url_meta){
                    if(get_post_meta( $item->get_id(), 'cron_count', true ) < 2){
                        $userlocaltime = new DateTime("now", $browserTimezone);
                        $userlocaltime = $userlocaltime->format('Y-m-d H:i:s');
                        $nextEventSecond = wb_get_next_cron_time('webinar_5minafter');
                        $nextAvailableLink = date('Y-m-d H:i:s', strtotime('+'.$nextEventSecond.' seconds', strtotime($userlocaltime)));
                        
                        $video_error .= sprintf('Webinar %s %s %s finished <br/>Please be patient we are awaiting the webinar recording. The webinar recording will be available at %s, please reload the page.', get_the_title($item->get_id()), date('Y-m-d', strtotime($start_time)),  $duration, $nextAvailableLink);    
                    }
                }
            }else{
                    $userlocaltime = new DateTime("now", $browserTimezone);
                    $userlocaltime = $userlocaltime->format('Y-m-d H:i:s');
                    $nextEventSecond = wb_get_next_cron_time('webinar_5minafter');
                    $nextAvailableLink = date('Y-m-d H:i:s', strtotime('+'.$nextEventSecond.' seconds', strtotime($userlocaltime)));
                    
                    $video_error .= sprintf('Webinar %s %s %s finished <br/>Please be patient we are awaiting the webinar recording. The webinar recording will be available at %s, please reload the page.', get_the_title($item->get_id()), date('Y-m-d', strtotime($start_time)),  $duration, $nextAvailableLink);
            }
        }else{
            $video_error .= __('Webinar recording is not available.', 'webinar');
        }
            

           
            ?>

            <?php if( !empty($play_url_meta)): ?>
            <div id="video_player_zoom">
            <video controls>
                <source src="<?php echo $play_url_meta; ?>" type="video/mp4">
                Your browser does not support the video tag.
                </video>
            </div>

            <?php elseif(!empty($video_error)): ?>
                <div class="errorvideo">
                    <h3><?php echo $video_error; ?></h3>
                </div>
            <?php else: ?>
            <div class="entry-countdown no-record">
                <div class="inner-no-record">
                    <h3 class="mb-0"><?php echo sprintf('Webinar %s %s %s finished.', get_the_title($item->get_id()), date('Y-m-d', strtotime($start_time)),  $duration ); ?></h3>
                    <h3 class="text-center text-black"><?php _e('No record available', 'webinar'); ?></h3>
                </div>
            </div>
            <?php endif; ?>
            <?php

        
    }
    else{
        // echo 'post id: ' . $item->get_id() . '<br/>';
        update_post_meta($item->get_id(), 'zoom_status', 'active');
        $course = learn_press_get_item_courses( $item->get_id() );
        $allAuthors = get_post_meta($course[0]->ID, '_lp_co_teacher', false);
        
       
        // echo 'current user id: ' . get_current_user_id() . '<br/>';
				
		$webinar_author = get_post_field( 'post_author', $course[0]->ID );
        array_push($allAuthors, $webinar_author);
        
        $webinar = dcd_zoom_conference()->getZoomWebinarDetails($item->get_id());
        $webinar = json_decode($webinar);
        // echo 'Webinar Array <br/><pre>';
        // print_r($webinar);
        // echo '</pre>';


        $join_url = '';
        $join_text = '';
        $errorMsg = '';

        $hoster = false;
        $master_host = get_option('zoom_master_host');
        if(get_user_meta(get_current_user_id(), 'user_zoom_hostid', true) == get_post_meta($item->get_id(), '_lp_alternative_host', true)){
            $hoster = true;
        }
        if(get_user_meta(get_current_user_id(), 'user_zoom_hostid', true) == $master_host){
            $hoster = true;
        }


        if($ah_start_link && $hoster){
            $join_url = $webinar->start_url;
            $hoster_token = get_post_meta($item->get_id(), 'alternative_hoster_token', true);
            if($hoster_token){
                $join_url = get_post_meta($item->get_id(), 'ah_start_link', true);
                if(!$join_url){
                    $errorMsg = __('Be patient expect start link ...', 'webinar');
                }
            }

            if(get_user_meta(get_current_user_id(), 'user_zoom_hostid', true) == $master_host){
                $join_url = get_post_meta($item->get_id(), 'mh_start_link', true);
                
                if(!get_post_meta($item->get_id(), 'mh_start_link', true)){
                    $errorMsg = __('ZOOM Start Link is not available', 'webinar');
                }
            }
            $join_text = __('Start', 'webinar');
        }
        elseif(in_array(get_current_user_id(), $allAuthors)){
            $errorMsg = __('Sorry, but you are not a hoster of the lesson..', 'webinar');
        }
        else{
            $join_url = $webinar->join_url;
            $join_text = __('Join', 'webinar');
        }

        
        $userlocaltime = new DateTime("now", $browserTimezone);
        $userlocaltime = $userlocaltime->format('Y-m-d H:i:s');
        $nextEventSecond = wb_get_next_cron_time('webinar_10mbefore');
        $nextAvailableLink = date('Y-m-d H:i:s', strtotime('+'.$nextEventSecond.' seconds', strtotime($userlocaltime)));

        if(!$ah_start_link && $hoster && get_user_meta(get_current_user_id(), 'user_zoom_hostid', true) != $master_host ){
            $errorMsg = sprintf('Please be patient we are awaiting webinar connection link. Your link will be available at %s, please reload the page.', $nextAvailableLink);
        }

        
        
        ?>
        <?php ?>
        <div class="entry-countdown z-index-1">
            <div id="joinbutton" class="text-center">
                <?php if(empty($errorMsg)): ?>
                <div class="button-inner">
                    <a target="_blank" href="<?php echo $join_url; ?>" class="btn font-30 btn-large btn-primary text-white"><?php echo $join_text; ?></a>
                </div>
                <?php else: ?>
                    <div class="errorMsg">
                        <h3><?php echo $errorMsg; ?></h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php }
} else {
	if ( version_compare( TP_EVENT_VER, '2.0', '>=' ) ) {
		$current_time = date( 'Y-m-d H:i' );
		$time         = tp_event_get_time( 'Y-m-d H:i', null, false );
		if( $time > $current_time ) {
            ?>
            <div class="entry-countdown">
                <?php $date = new DateTime(date('Y-m-d H:i', strtotime($time))); ?>
                <div class="tp_event_counter"
                     data-time="<?php echo esc_attr($date->format('M j, Y H:i:s O')); ?>"></div>
            </div>
            <?php
        }
	} else {
        $current_time = date( 'Y-m-d H:i' );
        $time         = tp_event_get_time( 'Y-m-d H:i', null, false );
        if( $time > $current_time ) {
            ?>
            <div class="entry-countdown">
                <div class="tp_event_counter"
                     data-time="<?php echo esc_attr(tp_event_get_time('M j, Y H:i:s O', null, false)); ?>">
                </div>
            </div>

            <p style="clear:both"></p>
            <?php
        }
	}
}
