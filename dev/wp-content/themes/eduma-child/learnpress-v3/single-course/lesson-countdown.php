<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( class_exists( 'WPEMS' ) ) {
    $item = LP_Global::course_item();
    $start_time = get_post_meta( $item->get_id(), '_lp_webinar_when_gmt', true );
    $change_sdate = str_replace( '/', '-', $start_time );
    

    $time   = date( 'Y-m-d H:i:s', strtotime( $change_sdate ) );
    
    // $timezone = get_post_meta($item->get_id(), '_lp_timezone', true);

    // echo '<pre>';
    // print_r($_COOKIE);
    // echo '</pre>';

    $browserTimezone = 'UTC';
    if(isset($_COOKIE['wb_timezone'])) $browserTimezone = new DateTimeZone($_COOKIE['wb_timezone']);

    
    $timezone = 'UTC';
    $newcreatedtime = new DateTime("now", new DateTimeZone( $timezone ) );
    $now = $newcreatedtime->format('Y-m-d H:i:s');
    $offset = $browserTimezone->getOffset($newcreatedtime);
    $now = date('Y-m-d H:i:s', strtotime($now) + $offset);
    
    $time = date('Y-m-d H:i:s', strtotime($time) + $offset);
    
						
    $linkvisiable = date( 'Y-m-d H:i', strtotime('-10 minutes', strtotime( $time )) );

    // End Time 
    $duration   = get_post_meta( $item->get_id(), '_lp_duration', true );
    $endtime    = strtotime('+'.$duration, strtotime($time));
    $endtime    = strtotime('+5 minutes', $endtime);

    // echo 'end time : ' . date('Y-m-d H:i:s', $endtime) . '<br/>';

    $nextEventTime = wb_get_next_cron_time('webinar_10mbefore');
    
    // Thumb Image
    $course = learn_press_get_item_courses( $item->get_id() );
    $thumbImgSrc = (has_post_thumbnail($course[0]->ID)) ? wp_get_attachment_url( get_post_thumbnail_id($course[0]->ID) ) : get_stylesheet_directory_uri() . '/assets/images/presentaion-1.jpg';


    if( $now < $linkvisiable && $linkvisiable < date('Y-m-d H:i', $endtime)){
        update_post_meta($item->get_id(), 'zoom_status', 'inactive');
        $time = date('Y-m-d H:i:s', strtotime('-3 hours', strtotime($time)));
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
                <div class="tp_event_counter"
                    data-time="<?php echo esc_attr($date->format('M j, Y H:i:s O')); ?>">
                </div>
               
                
            </div> 
        </div>
        <?php
    }
    
    elseif($now > date('Y-m-d H:i', $endtime)  ){
        update_post_meta($item->get_id(), 'zoom_status', 'inactive');
        $webinarId = get_post_meta( $item->get_id(), '_webinar_ID', true );
        delete_post_meta( $item->get_id(), '_webinar_statis' );
        $webinarStatus = get_post_meta( $item->get_id(), '_webinar_statis', true );
        if(!$webinarStatus){
            $webinar_end = dcd_zoom_conference()->zoomWebinarStatusEnd($webinarId);
            $update_recording_setting = dcd_zoom_conference()->zoomRecordingSettingsUpdate($webinarId);
            
            
            // Deactivated author
            $allAuthors = get_post_meta($course[0]->ID, '_lp_co_teacher', false);
            
            $price          = get_post_meta($course[0]->ID, '_lp_price', true);
            $sales_price    = get_post_meta( $course[0]->ID, '_lp_sale_price', true );
            $existPrice     = ($sales_price) ? $sales_price : $price;
            
            $lessons = get_course_lessons($course[0]->ID);
            // echo 'lesssons <br/><pre>';
            // print_r($lessons);
            // echo '</pre>';


               

            $newPrice = $existPrice - (0.5 * $existPrice) / count($lessons);
            // Re-calculate price
            if($sales_price){
                update_post_meta( $course[0]->ID, '_lp_sale_price', $newPrice );
            }else{
                update_post_meta( $course[0]->ID, '_lp_price', $newPrice );
            }
            
			$webinar_author = get_post_field( 'post_author', $course[0]->ID );
			array_push($allAuthors, $webinar_author);

            foreach($allAuthors as $sauthor):
                if(get_user_meta( $sauthor, 'user_zoom_hostid', true )){
                    $updateStatus = dcd_zoom_conference()->enableUserStatistoActive($sauthor, 'deactivate');
                }
            endforeach;
            
            $record_url = dcd_zoom_conference()->getMeetingRecordUrl($webinarId);
            $record_url = json_decode($record_url);
            // echo 'webinar id: ' . $webinarId . '<br/>';
            // echo 'Conference Meetings Recorded URL <pre>';
            // print_r($record_url);
            // echo '</pre>'; 
            // echo '<a class="btn btn-primary recorded_url text-center" target="_blank" href="#">Recorded Url</a>';

            update_post_meta( $item->get_id(), '_webinar_statis', 1 );
            }

           
            ?>

            <div class="entry-countdown no-record">
                <div class="inner-no-record">
                    <h3 class="text-center text-black"><?php _e('No record available', 'webinar'); ?></h3>
                </div>
            </div>
            <?php

        
    }
    else{
        // echo 'post id: ' . $item->get_id() . '<br/>';
        update_post_meta($item->get_id(), 'zoom_status', 'active');
        $course = learn_press_get_item_courses( $item->get_id() );
        $allAuthors = get_post_meta($course[0]->ID, '_lp_co_teacher', false);
        
        // echo '<pre>';
        // print_r($allAuthors);
        // echo '</pre>';
        // echo 'current user id: ' . get_current_user_id() . '<br/>';
				
		$webinar_author = get_post_field( 'post_author', $course[0]->ID );
        // array_push($allAuthors, $webinar_author);
        
        $webinar = dcd_zoom_conference()->getZoomWebinarDetails($item->get_id());
        $webinar = json_decode($webinar);
        // echo 'Webinar Array <br/><pre>';
        // print_r($webinar);
        // echo '</pre>';

        // echo 'next event time: ' . $nextEventTime . '<br/>';
        // $timeleft =  strtotime($time) - time();
        // echo 'start time left: ' . $timeleft . '<br/>';

        $join_url = '';
        $join_text = '';
        $errorMsg = '';
        if(get_current_user_id() == $webinar_author){
            $join_url = $webinar->start_url;
            $join_text = __('Start', 'webinar');
        }
        elseif(in_array(get_current_user_id(), $allAuthors)){
            $errorMsg = __('Sorry, but you are not a hoster of the lesson..', 'webinar');
        }
        else{
            $join_url = $webinar->join_url;
            $join_text = __('Join', 'webinar');
        }

        
        ?>
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
