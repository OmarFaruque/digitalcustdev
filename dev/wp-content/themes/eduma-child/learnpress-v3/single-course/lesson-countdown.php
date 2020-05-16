<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( class_exists( 'WPEMS' ) ) {
    $item = LP_Global::course_item();
    $start_time = get_post_meta( $item->get_id(), '_lp_webinar_when', true );
	$change_sdate = str_replace( '/', '-', $start_time );
	$time   = date( 'Y-m-d H:i', strtotime( $change_sdate ) );
						
    $count_of_time = date( 'Y-m-d H:i', strtotime('-10 minutes') );

    // End Time 
    $duration   = get_post_meta( $item->get_id(), '_lp_duration', true );
    $endtime    = strtotime('+'.$duration, strtotime($time));
    $endtime    = strtotime('+5 minutes', $endtime);




    // echo 'current time: ' . $current_time . '<br/>';
    
    if( $time > $count_of_time ){
        ?>
        <div class="entry-countdown">
            <div class="tp_event_counter"
                 data-time="<?php echo esc_attr(date('M j, Y H:i:s O', strtotime($time))); ?>">
            </div>
        </div> 
        <?php
    }
    
    elseif($time < $count_of_time && date('Y-m-d H:i') > date('Y-m-d H:i', strtotime($endtime))  ){
        
        $webinarId = get_post_meta( $item->get_id(), '_webinar_ID', true );
        delete_post_meta( $item->get_id(), '_webinar_statis' );
        $webinarStatus = get_post_meta( $item->get_id(), '_webinar_statis', true );
        if(!$webinarStatus){
            $webinar_end = dcd_zoom_conference()->zoomWebinarStatusEnd($webinarId);
            $update_recording_setting = dcd_zoom_conference()->zoomRecordingSettingsUpdate($webinarId);
            
            
            // Deactivated author
            $course = learn_press_get_item_courses( $item->get_id() );
            $allAuthors = get_post_meta($course[0]->ID, '_lp_co_teacher', false);
            
            $price          = get_post_meta($course[0]->ID, '_lp_price', true);
            $sales_price    = get_post_meta( $course[0]->ID, '_lp_sale_price', true );
            $existPrice     = ($sales_price) ? $sales_price : $price;
            
            $lessons = get_course_lessons($course[0]->ID);

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
            echo 'webinar id: ' . $webinarId . '<br/>';
            echo 'Conference Meetings Recorded URL <pre>';
            print_r($record_url);
            echo '</pre>';

            update_post_meta( $item->get_id(), '_webinar_statis', 1 );
            }
            ?>

            <div class="entry-countdown no-record">
                <div class="inner-no-record">
                    <h3 class="text-center text-white"><?php _e('No record available', 'webinar'); ?></h3>
                </div>
            </div>
            <?php

        
    }
    else{
        $course = learn_press_get_item_courses( $item->get_id() );
		$allAuthors = get_post_meta($course[0]->ID, '_lp_co_teacher', false);
				
		$webinar_author = get_post_field( 'post_author', $course[0]->ID );
        array_push($allAuthors, $webinar_author);
        
        $webinar = dcd_zoom_conference()->getZoomWebinarDetails($item->get_id());
        $webinar = json_decode($webinar);
        // echo 'Webinar Array <br/><pre>';
        // print_r($webinar);
        // echo '</pre>';

        $join_url = '';
        $join_text = '';
        if(in_array(get_current_user_id(), $allAuthors)){
            $join_url = $webinar->start_url;
            $join_text = __('Start', 'webinar');
        }else{
            $join_url = $webinar->join_url;
            $join_text = __('Join', 'webinar');
        }
        ?>
         <div class="entry-countdown z-index-1">
        <div id="joinbutton" class="text-center">
            <div class="button-inner">
                <a target="_blank" href="<?php echo $join_url; ?>" class="btn font-30 btn-large btn-primary text-white"><?php echo $join_text; ?></a>
            </div>
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
