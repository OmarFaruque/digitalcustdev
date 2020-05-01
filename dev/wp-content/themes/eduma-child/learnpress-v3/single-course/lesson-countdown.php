<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( class_exists( 'WPEMS' ) ) {
    global $post;
    echo 'this is from countdown';
    echo 'post ids: ' . $post->ID . '<br/>';


    $start_time = get_post_meta( $post->ID, 'zoom_date', true );
	$change_sdate = str_replace( '/', '-', $start_time );
	$time   = date( 'Y-m-d H:i', strtotime( $change_sdate ) );
						
    $current_time = date( 'Y-m-d H:i' );
    echo 'print time: ' . wpems_get_time('M j, Y H:i:s O', null, false) . '<br/>';
    echo 'custom print time: ' . date('M j, Y H:i:s O', strtotime($time)) . '<br/>';
    // $time         = wpems_get_time( 'Y-m-d H:i', null, false );
    echo 'time: ' . $time . '<br/>';
    if( $time > $current_time ) {
        echo 'inside entry countdown <br/>';
        ?>
        <div class="entry-countdown">
            <div class="tp_event_counter"
                 data-time="<?php echo esc_attr(date('M j, Y H:i:s O', strtotime($time))); ?>">
            </div>
        </div>
        <?php
    }
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
