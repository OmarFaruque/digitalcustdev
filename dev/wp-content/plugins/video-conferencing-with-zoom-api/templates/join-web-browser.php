<?php
/**
 * The Template for joining meeting via browser
 *
 * This template can be overridden by copying it to yourtheme/video-conferencing-zoom/join-web-browser.php.
 *
 * @package    Video Conferencing with Zoom API/Templates
 * @since      3.0.0
 * @modified   3.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $zoom;

if ( video_conference_zoom_check_login() ) {
	if ( ! empty( $zoom['api']->state ) && $zoom['api']->state === "ended" ) {
		echo "<h3>" . __( 'This meeting has been ended by host.', 'video-conferencing-with-zoom-api' ) . "</h3>";
		die;
	}
	?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="format-detection" content="telephone=no">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <title><?php echo ! empty( $zoom['api']->topic ) ? $zoom['api']->topic : 'Join Meeting'; ?></title>
        <link rel='stylesheet' type="text/css" href="<?php echo ZVC_PLUGIN_VENDOR_ASSETS_URL . '/zoom/bootstrap.css?ver=' . ZVC_PLUGIN_VERSION; ?>" media='all'>
        <link rel='stylesheet' type="text/css" href="<?php echo ZVC_PLUGIN_VENDOR_ASSETS_URL . '/zoom/react-select.css?ver=' . ZVC_PLUGIN_VERSION; ?>" media='all'>
        <link rel='stylesheet' type="text/css" href="<?php echo ZVC_PLUGIN_PUBLIC_ASSETS_URL . '/css/main.min.css?ver=' . ZVC_PLUGIN_VERSION; ?>" media='all'>
    </head>
    <body class="join-via-browser-body">
    <div id="dpen-zoom-browser-meeting" class="dpen-zoom-browser-meeting-wrapper">
        <div id="dpen-zoom-browser-meeting--container">
            <div class="dpen-zoom-browser-meeting--info">
				<?php if ( ! is_ssl() ) { ?>
                    <p style="line-height: 1.5;">
                        <strong style="color:red;"><?php _e( '!!!ALERT!!!: ', 'video-conferencing-with-zoom-api' ); ?></strong><?php _e(
							'Browser did not detect a valid SSL certificate. Audio and Video for Zoom meeting will not work on a non HTTPS site, please install a valid SSL certificate to allow audio and video in your Meetings via browser.', 'video-conferencing-with-zoom-api' ); ?>
                    </p>
				<?php } ?>
                <div class="dpen-zoom-browser-meeting--info__browser"></div>
            </div>
            <form class="dpen-zoom-browser-meeting--meeting-form" id="dpen-zoom-browser-meeting-join-form" action="">
                <div class="form-group">
                    <input type="text" name="display_name" id="display_name" value="" placeholder="Your Name Here" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary" id="dpen-zoom-browser-meeting-join-mtg">
					<?php _e( 'Join', 'video-conferencing-with-zoom-api' ); ?>
                </button>
            </form>
        </div>
    </div>
	<?php
	wp_footer();
	?>
    </body>
    </html>
	<?php
} else {
	echo "<h3>" . __( 'You do not have enough priviledge to access this page. Please login to continue or contact administrator.', 'video-conferencing-with-zoom-api' ) . "</h3>";
	die;
}
