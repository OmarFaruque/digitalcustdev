<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Shortcodes
 *
 * @author   Deepen
 * @modified 2.1.0
 * @since    2.0.0
 */

//Adding Shortcode
add_action( 'init', 'video_conferencing_zoom_register_shortcodes' );
add_action( 'admin_head', 'video_conferencing_zoom_button_render' );

function video_conferencing_zoom_register_shortcodes() {
	if ( is_admin() ) {
		return;
	}
	add_shortcode( 'zoom_api_link', 'video_conferencing_zoom_render_shortcode' );
}

/**
 * Rendering Shortcode Output
 * @return string
 */

function video_conferencing_zoom_render_shortcode( $atts, $content = null ) {
	add_action( 'wp_enqueue_scripts', 'video_conferencing_zoom_enqueue_scripts' );
	do_action( 'wp_enqueue_scripts' );

	// Allow addon devs to perform action before window rendering
	do_action( 'video_conferencing_zoom_before_render_widnow' );

	ob_start();
	$args = shortcode_atts(
		array(
			'meeting_id' => 'javascript:void(0);',
			'title'      => '',
			'id'         => 'zoom-meeting-window',
			'class'      => 'zoom-meeting-window',
		),
		$atts
	);

	$meeting_id = (int) $args['meeting_id'];
	$title      = $args['title'];
	$id         = $args['id'];
	$class      = $args['class'];

	if ( ! $meeting_id ) {
		$content = '<h4 class="no-meeting-id"><strong style="color:red;">' . __( 'ERROR: ', 'video-conferencing-with-zoom-api' ) . '</strong>' . __( 'No meeting id set in the shortcode', 'video-conferencing-with-zoom-api' ) . '</h4>';
		return;
	}

	$zoom_map_array                 = get_option( 'zoom_api_meeting_options' );
	$zoom_vanity_url                = get_option( 'zoom_vanity_url' );
	$zoom_alternative_join          = get_option( 'zoom_alternative_join' );
	$zoom_btn_css_class             = get_option( 'zoom_btn_css_class' );
	$zoom_help_text_disable         = get_option( 'zoom_help_text_disable' );
	$zoom_compatiblity_text_disable = get_option( 'zoom_compatiblity_text_disable' );
	$user_id                        = get_current_user_id();
	$visitor_name                   = '';

	if ( $user_id ) {
		$user_data = get_userdata( $user_id );
		if ( $user_data->display_name ) {
			$visitor_name = $user_data->display_name;
		} elseif ( $user_data->first_name ) {
			$visitor_name = $user_data->first_name . ' ' . $user_data->last_name;
		}
	}
	
	// Password Functionality
	$password_from_db  = get_option( 'zoom_password_meeting_' . $meeting_id );

	// if password is not there, then call the api and store password in database
	if ( ! $password_from_db ) {
		$response_from_api = zoom_conference()->getMeetingStatus( $meeting_id );
		$decoded_reponse   = json_decode( $response_from_api );
		
		if ( ! isset( $decoded_reponse->password ) ) {
			$meeting_password = 'no_password';
		} else {
			$meeting_password = $decoded_reponse->password;
		}
		// now lets store the password into our databse
		update_option( 'zoom_password_meeting_' . $meeting_id, $meeting_password );
		
		$password_from_db = $meeting_password;
	}
	
	if ( isset( $_POST['_wpnonce'] ) && isset( $_POST['user_meeting_name_val'] ) ) {
		$retrieved_nonce = $_POST['_wpnonce'];
		if ( wp_verify_nonce( $retrieved_nonce, 'user_meeting_name' ) ) {
			$visitor_name = sanitize_text_field( $_POST['user_meeting_name_val'] );
		}
		
		if ( isset( $_POST['meeting_password'] ) ) {
			$user_entered_password = $_POST['meeting_password'];
			if ( $user_entered_password != $password_from_db ) {
				_e( 'Error: The meeting password you entered was incorrect.', 'video-conferencing-with-zoom-api' );
			}
		}
	}

	if ( ! $zoom_vanity_url ) {
		$zoom_url        = 'https://zoom.us' . '/wc/' . $meeting_id;
		$mobile_zoom_url = 'https://zoom.us' . '/j/' . $meeting_id;
	} else {
		$zoom_url        = $zoom_vanity_url . '/wc/' . $meeting_id;
		$mobile_zoom_url = $zoom_vanity_url . '/j/' . $meeting_id;
	}

	if ( $visitor_name ) {
		$zoom_display_name = base64_encode( $visitor_name );
	} else {
		$zoom_display_name = 'bmFtZQ==';
	}

	$zoom_url .= '/join' . '?prefer=1&un=' . esc_attr( $zoom_display_name );
	
	if ( $password_from_db != 'no_password' ) {
		$zoom_url .= '&pwd=' . $password_from_db;
	}
	
	$zoom_host_url = 'https://zoom.us' . '/wc/' . $meeting_id . '/start';
	$zoom_host_url = apply_filters( 'video_conferencing_zoom_join_url_host', $zoom_host_url );
	
	$host_users = video_conferencing_zoom_api_get_user_transients();

	$zoom_url = apply_filters( 'video_conferencing_zoom_join_url', $zoom_url );

	if ( isset( $zoom_map_array[ $meeting_id ]['time'] ) ) {
		$meeting_time_arr = explode( ' ', $zoom_map_array[ $meeting_id ]['time'] );
		$meeting_zone     = end( $meeting_time_arr );
		try {
			$meeting_timezone_time = new DateTime( 'now', new DateTimeZone( $meeting_zone ) );
			$meeting_time_check    = new DateTime( $zoom_map_array[ $meeting_id ]['time'], new DateTimeZone( $meeting_zone ) );
		} catch ( Exception $e ) {
			error_log( $e->getMessage() );
		}
	}

	if ( $zoom_map_array[ $meeting_id ]['time'] && $meeting_time_check > $meeting_timezone_time ) {
		$content .=
		'<ul class="countdown" data-meeting-time="' . esc_attr( $zoom_map_array[ $meeting_id ]['time'] ) . '">
        <li>
            <span class="days">00</span>
            <p class="days_ref" data-translate-plural="' . esc_attr__( 'days', 'video-conferencing-with-zoom-api' ) . '" data-translate-single="' . esc_html__( 'day', 'video-conferencing-with-zoom-api' ) . '">' . esc_html__( 'days', 'video-conferencing-with-zoom-api' ) . '</p>
        </li>
        <li class="seperator">:</li>
        <li>
            <span class="hours">00</span>
            <p class="hours_ref" data-translate-plural="' . esc_attr__( 'hours', 'video-conferencing-with-zoom-api' ) . '" data-translate-single="' . esc_html__( 'hour', 'video-conferencing-with-zoom-api' ) . '">' . esc_html__( 'hours', 'video-conferencing-with-zoom-api' ) . '</p>
        </li>
        <li class="seperator">:</li>
        <li>
            <span class="minutes">00</span>
            <p class="minutes_ref" data-translate-plural="' . esc_attr__( 'minutes', 'video-conferencing-with-zoom-api' ) . '" data-translate-single="' . esc_html__( 'minute', 'video-conferencing-with-zoom-api' ) . '">' . esc_html__( 'minutes', 'video-conferencing-with-zoom-api' ) . '</p>
        </li>
        <li class="seperator">:</li>
        <li>
            <span class="seconds">00</span>
            <p class="seconds_ref" data-translate-plural="' . esc_attr__( 'seconds', 'video-conferencing-with-zoom-api' ) . '" data-translate-single="' . esc_html__( 'second', 'video-conferencing-with-zoom-api' ) . '">' . esc_html__( 'seconds', 'video-conferencing-with-zoom-api' ) . '</p>
        </li>
    </ul>';
	} elseif ( isset( $zoom_map_array[ $meeting_id ]['enforce_login'] ) && ! is_user_logged_in() ) {
		$content .= '<h3>' . esc_html__( 'Restricted access, please login to continue', 'video-conferencing-with-zoom-api' ) . '</h3>';
		$content .= '<a href="' . esc_url( wp_login_url( get_permalink() ) ) . '" alt="' . esc_attr__( 'Login', 'video-conferencing-with-zoom-api' ) . '">';
		$content .= __( 'Login', 'video-conferencing-with-zoom-api' ) . '</a>';
	} else {
		if ( ! $visitor_name || ( isset( $_POST['meeting_password'] ) && $user_entered_password != $password_from_db ) ) :
			?>
			<form method="post" class="zoom-meeting-step1">
				<h4><?php _e( 'Enter the details below to join the Zoom meeting' ); ?></h4>
				<input class="join-meeting-field" type="text" value="<?php esc_attr_e( $visitor_name ); ?>"
					name="user_meeting_name_val" placeholder="<?php _e( 'Your name', 'video-conferencing-with-zoom-api' ); ?>" /><br />
						<?php wp_nonce_field( 'user_meeting_name' ); ?>
				<?php if( $password_from_db != 'no_password' ) :?>
					<input type="password" class="join-meeting-field" name="meeting_password" placeholder="<?php _e( 'Meeting password', 'video-conferencing-with-zoom-api' ) ?>"/><br />
				<?php endif; ?>
				
				<input class="join-meeting-btn <?php esc_attr_e( $zoom_btn_css_class ); ?>" type="submit" name="join_meeting"
					value="<?php _e( 'Join Meeting', 'video-conferencing-with-zoom-api' ); ?>" />
			</form>
			<?php
		else :
			//Detect special conditions devices
			$iPod    = stripos( $_SERVER['HTTP_USER_AGENT'], 'iPod' );
			$iPhone  = stripos( $_SERVER['HTTP_USER_AGENT'], 'iPhone' );
			$iPad    = stripos( $_SERVER['HTTP_USER_AGENT'], 'iPad' );
			$Android = stripos( $_SERVER['HTTP_USER_AGENT'], 'Android' );

			//do something with this information
			if ( $iPod || $iPhone || $iPad ) {
				$app_store_link = 'https://apps.apple.com/app/zoom-cloud-meetings/id546505307';
			} elseif ( $Android ) {
				$app_store_link = 'https://play.google.com/store/apps/details?id=us.zoom.videomeetings';
			} else {
				$app_store_link = 'https://zoom.us/support/download';
			}

			$note_text = esc_html__( 'Note: If you are having trouble joining the meeting below, enter Meeting ID: ', 'video-conferencing-with-zoom-api' ) . '<strong>' . esc_html( $meeting_id ) . '</strong> ' . esc_html__( 'and join via Zoom App.', 'video-conferencing-with-zoom-api' );

			$content .= '<div class="zoom-window-wrap">
			<h1>' . esc_html( $title ) . '</h1>';

			if ( $zoom_help_text_disable != 1 ) {
				$content .= '<span class="zoom-app-notice">' .
				'<p>' . $note_text . '</p>' .
				'<span class="zoom-links">
							<ul>
								<li><a href="' . esc_url( $mobile_zoom_url ) . '" class="join-link retry-url">' . __( 'Join via Zoom App', 'video-conferencing-with-zoom-api' ) . '</a></li>
								<li><a href="' . esc_url( $app_store_link ) . '" class="download-link">' . __( 'Download App from Store', 'video-conferencing-with-zoom-api' ) . '</a></li>
								<li><a href="https://zoom.us/client/latest/zoom.apk" class="download-link">' . __( 'Download from Zoom', 'video-conferencing-with-zoom-api' ) . '</a></li>
							</ul>
						</span>
					</span>';
			}

			if ( $zoom_compatiblity_text_disable != 1 ) {
				if ( ! current_user_can( 'administrator' ) ) {
					$meeting_action = '/join';
					if ( ! $zoom_alternative_join ) {
						$zoom_alternative_join = __( 'JOIN MEETING VIA ALTERNATIVE WAY', 'video-conferencing-with-zoom-api' );
					}

					$content .= '<a class="button incompatiblity-notice-btn ' . esc_attr( $zoom_btn_css_class ) . '" target="_blank" href="https://zoom.us/wc/' . esc_html( $meeting_id ) . esc_html( $meeting_action ) . '" class="join-link">' . esc_html( $zoom_alternative_join ) . '</a>';
				}
			}

			foreach ( $host_users as $host ) {
				if ( isset( $user_data->user_email ) && $user_data->user_email == $host->email ) {
					$content .= '<h3>' . __( 'If the meeting is not started yet you have to click the below button as a HOST to start the meeting, once started you can join from the window below.', 'video-conferencing-with-zoom-api' ) . '</h3>';
					$content .= '<a class="button start-meeting-btn ' . esc_attr( $zoom_btn_css_class ) . '" target="_blank" href="' . esc_url( $zoom_host_url ) . '" class="join-link">' . __( 'START MEETING AS HOST', 'video-conferencing-with-zoom-api' ) . '</a>';
				}
			}

				$hide_ssl_message = get_option( 'zoom_ssl_message' );
			if ( current_user_can( 'administrator' ) ) {
				if ( ! is_ssl() && ! $hide_ssl_message ) {
					$content .= '<h4 class="ssl-alert"><strong style="color:red;">' . __( 'ALERT: ', 'video-conferencing-with-zoom-api' ) . '</strong>' . __( 'Audio and Video for Zoom meeting will not work on a non HTTPS site, please install a valid SSL certificate on your site to allow participants use audio and video during Zoom meeting: ', 'video-conferencing-with-zoom-api' ) . '</h4>';
				}
			}
				$width    = esc_attr( get_option( 'zoom_width_meeting_window' ) );
				$height   = esc_attr( get_option( 'zoom_height_meeting_window' ) );
				$content .= '<div style="width:' . $width . 'px; height:' . $height . 'px" id="' . esc_attr( $id ) . '" class="zoom-iframe-container ' . esc_attr( $class ) . '">
					<iframe scrolling="no" sandbox="allow-forms allow-scripts allow-same-origin" allowfullscreen="allowfullscreen" mozallowfullscreen="mozallowfullscreen" msallowfullscreen="msallowfullscreen" oallowfullscreen="oallowfullscreen" webkitallowfullscreen="webkitallowfullscreen" allow="encrypted-media; autoplay; microphone; camera" src="' . esc_url( $zoom_url ) . '" frameborder="0">
					</iframe>
				</div>
			</div>';
		endif;
	}

	$content .= ob_get_clean();

	// Allow addon devs to perform filter before window rendering
	$content = apply_filters( 'video_conferencing_zoom_before_window_content', $content );

	return $content;
}


function video_conferencing_zoom_button_render() {
	//Abort early if the user will never see TinyMCE
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) && get_user_option( 'rich_editing' ) == 'true' ) {
		return;
	}

	//Add a callback to regiser our tinymce plugin
	add_filter( 'mce_external_plugins', 'video_conferencing_zoom_register_tinymce_scripts' );

	// Add a callback to add our button to the TinyMCE toolbar
	add_filter( 'mce_buttons', 'video_conferencing_zoom_add_btn_tinmyce' );
}

//This callback registers our plug-in
function video_conferencing_zoom_register_tinymce_scripts( $plugin_array ) {
	$plugin_array['zvc_shortcode_button'] = ZOOM_VIDEO_CONFERENCE_PLUGIN_JS_PATH . '/video-conferencing-with-zoom-api-shortcode.js';

	return $plugin_array;
}

//This callback adds our button to the toolbar
function video_conferencing_zoom_add_btn_tinmyce( $buttons ) {
	//Add the button ID to the $button array
	$buttons[] = 'zvc_shortcode_button';

	return $buttons;
}

//Enqueuing Scripts and Styles for Frontend
function video_conferencing_zoom_enqueue_scripts() {
	wp_register_style( 'video-conferencing-with-zoom-api-iframe', ZOOM_VIDEO_CONFERENCE_PLUGIN_FRONTEND_CSS_PATH . '/zoom-iframe.css', false, time() );
	wp_enqueue_style( 'video-conferencing-with-zoom-api-iframe' );

	if ( is_rtl() ) {
		wp_register_style( 'video-conferencing-with-zoom-api-iframe-rtl', ZOOM_VIDEO_CONFERENCE_PLUGIN_FRONTEND_CSS_PATH . '/zoom-iframe.rtl.css', false, time() );
		wp_enqueue_style( 'video-conferencing-with-zoom-api-iframe-rtl' );
	}

	wp_register_script( 'video-conferencing-with-zoom-api-countdownjs', ZOOM_VIDEO_CONFERENCE_PLUGIN_FRONTEND_JS_PATH . '/jquery.downcount.min.js', array( 'jquery' ), time() );
	wp_enqueue_script( 'video-conferencing-with-zoom-api-countdownjs' );

	wp_register_script( 'video-conferencing-with-zoom-api-iframe-js', ZOOM_VIDEO_CONFERENCE_PLUGIN_FRONTEND_JS_PATH . '/zoom-iframe.js', array( 'jquery' ), time() );
	wp_enqueue_script( 'video-conferencing-with-zoom-api-iframe-js' );
}
