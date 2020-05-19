<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'dump' ) ) {
	function dump( $var ) {
		echo '<pre>';
		var_dump( $var );
		echo '</pre>';
	}
}

if ( ! function_exists( 'zvc_get_timezone_options' ) ) {
	function zvc_get_timezone_options() {
		return array(
			'UTC'                            => 'UTC',
			'Pacific/Midway'                 => 'Pacific/Midway',
			'Pacific/Pago_Pago'              => 'Pacific/Pago_Pago',
			'Pacific/Honolulu'               => 'Pacific/Honolulu',
			'America/Anchorage'              => 'America/Anchorage',
			'America/Vancouver'              => 'America/Vancouver',
			'America/Los_Angeles'            => 'America/Los_Angeles',
			'America/Tijuana'                => 'America/Tijuana',
			'America/Phoenix'                => 'America/Phoenix',
			'America/Edmonton'               => 'America/Edmonton',
			'America/Denver'                 => 'America/Denver',
			'America/Mazatlan'               => 'America/Mazatlan',
			'America/Regina'                 => 'America/Regina',
			'America/Guatemala'              => 'America/Guatemala',
			'America/El_Salvador'            => 'America/El_Salvador',
			'America/Managua'                => 'America/Managua',
			'America/Costa_Rica'             => 'America/Costa_Rica',
			'America/Tegucigalpa'            => 'America/Tegucigalpa',
			'America/Winnipeg'               => 'America/Winnipeg',
			'America/Chicago'                => 'America/Chicago',
			'America/Mexico_City'            => 'America/Mexico_City',
			'America/Panama'                 => 'America/Panama',
			'America/Bogota'                 => 'America/Bogota',
			'America/Lima'                   => 'America/Lima',
			'America/Caracas'                => 'America/Caracas',
			'America/Montreal'               => 'America/Montreal',
			'America/New_York'               => 'America/New_York',
			'America/Indianapolis'           => 'America/Indianapolis',
			'America/Puerto_Rico'            => 'America/Puerto_Rico',
			'America/Santiago'               => 'America/Santiago',
			'America/Halifax'                => 'America/Halifax',
			'America/Montevideo'             => 'America/Montevideo',
			'America/Araguaina'              => 'America/Araguaina',
			'America/Argentina/Buenos_Aires' => 'America/Argentina/Buenos_Aires',
			'America/Sao_Paulo'              => 'America/Sao_Paulo',
			'Canada/Atlantic'                => 'Canada/Atlantic',
			'America/St_Johns'               => 'America/St_Johns',
			'America/Godthab'                => 'America/Godthab',
			'Atlantic/Cape_Verde'            => 'Atlantic/Cape_Verde',
			'Atlantic/Azores'                => 'Atlantic/Azores',
			'Etc/Greenwich'                  => 'Etc/Greenwich',
			'Atlantic/Reykjavik'             => 'Atlantic/Reykjavik',
			'Africa/Nouakchott'              => 'Africa/Nouakchott',
			'Europe/Dublin'                  => 'Europe/Dublin',
			'Europe/London'                  => 'Europe/London',
			'Europe/Lisbon'                  => 'Europe/Lisbon',
			'Africa/Casablanca'              => 'Africa/Casablanca',
			'Africa/Bangui'                  => 'Africa/Bangui',
			'Africa/Algiers'                 => 'Africa/Algiers',
			'Africa/Tunis'                   => 'Africa/Tunis',
			'Europe/Belgrade'                => 'Europe/Belgrade',
			'CET'                            => 'CET',
			'Europe/Oslo'                    => 'Europe/Oslo',
			'Europe/Copenhagen'              => 'Europe/Copenhagen',
			'Europe/Brussels'                => 'Europe/Brussels',
			'Europe/Berlin'                  => 'Europe/Berlin',
			'Europe/Amsterdam'               => 'Europe/Amsterdam',
			'Europe/Rome'                    => 'Europe/Rome',
			'Europe/Stockholm'               => 'Europe/Stockholm',
			'Europe/Vienna'                  => 'Europe/Vienna',
			'Europe/Luxembourg'              => 'Europe/Luxembourg',
			'Europe/Paris'                   => 'Europe/Paris',
			'Europe/Zurich'                  => 'Europe/Zurich',
			'Europe/Madrid'                  => 'Europe/Madrid',
			'Africa/Harare'                  => 'Africa/Harare',
			'Europe/Warsaw'                  => 'Europe/Warsaw',
			'Europe/Prague'                  => 'Europe/Prague',
			'Europe/Budapest'                => 'Europe/Budapest',
			'Africa/Tripoli'                 => 'Africa/Tripoli',
			'Africa/Cairo'                   => 'Africa/Cairo',
			'Africa/Johannesburg'            => 'Africa/Johannesburg',
			'Europe/Helsinki'                => 'Europe/Helsinki',
			'Africa/Nairobi'                 => 'Africa/Nairobi',
			'Europe/Sofia'                   => 'Europe/Sofia',
			'Europe/Istanbul'                => 'Europe/Istanbul',
			'Europe/Athens'                  => 'Europe/Athens',
			'Europe/Bucharest'               => 'Europe/Bucharest',
			'Asia/Nicosia'                   => 'Asia/Nicosia',
			'Asia/Beirut'                    => 'Asia/Beirut',
			'Asia/Damascus'                  => 'Asia/Damascus',
			'Asia/Jerusalem'                 => 'Asia/Jerusalem',
			'Asia/Amman'                     => 'Asia/Amman',
			'Europe/Moscow'                  => 'Europe/Moscow',
			'Asia/Baghdad'                   => 'Asia/Baghdad',
			'Asia/Kuwait'                    => 'Asia/Kuwait',
			'Asia/Riyadh'                    => 'Asia/Riyadh',
			'Asia/Bahrain'                   => 'Asia/Bahrain',
			'Asia/Qatar'                     => 'Asia/Qatar',
			'Asia/Aden'                      => 'Asia/Aden',
			'Africa/Khartoum'                => 'Africa/Khartoum',
			'Africa/Djibouti'                => 'Africa/Djibouti',
			'Africa/Mogadishu'               => 'Africa/Mogadishu',
			'Europe/Kiev'                    => 'Europe/Kiev',
			'Asia/Dubai'                     => 'Asia/Dubai',
			'Asia/Muscat'                    => 'Asia/Muscat',
			'Asia/Tehran'                    => 'Asia/Tehran',
			'Asia/Kabul'                     => 'Asia/Kabul',
			'Asia/Baku'                      => 'Asia/Baku',
			'Asia/Yekaterinburg'             => 'Asia/Yekaterinburg',
			'Asia/Tashkent'                  => 'Asia/Tashkent',
			'Asia/Calcutta'                  => 'Asia/Calcutta',
			'Asia/Kolkata'                   => 'Asia/Kolkata',
			'Asia/Kathmandu'                 => 'Asia/Kathmandu',
			'Asia/Novosibirsk'               => 'Asia/Novosibirsk',
			'Asia/Almaty'                    => 'Asia/Almaty',
			'Asia/Dacca'                     => 'Asia/Dacca',
			'Asia/Dhaka'                     => 'Asia/Dhaka',
			'Asia/Krasnoyarsk'               => 'Asia/Krasnoyarsk',
			'Asia/Bangkok'                   => 'Asia/Bangkok',
			'Asia/Saigon'                    => 'Asia/Saigon',
			'Asia/Jakarta'                   => 'Asia/Jakarta',
			'Asia/Irkutsk'                   => 'Asia/Irkutsk',
			'Asia/Shanghai'                  => 'Asia/Shanghai',
			'Asia/Hong_Kong'                 => 'Asia/Hong_Kong',
			'Asia/Taipei'                    => 'Asia/Taipei',
			'Asia/Kuala_Lumpur'              => 'Asia/Kuala_Lumpur',
			'Asia/Singapore'                 => 'Asia/Singapore',
			'Australia/Perth'                => 'Australia/Perth',
			'Asia/Yakutsk'                   => 'Asia/Yakutsk',
			'Asia/Seoul'                     => 'Asia/Seoul',
			'Asia/Tokyo'                     => 'Asia/Tokyo',
			'Australia/Darwin'               => 'Australia/Darwin',
			'Australia/Adelaide'             => 'Australia/Adelaide',
			'Asia/Vladivostok'               => 'Asia/Vladivostok',
			'Pacific/Port_Moresby'           => 'Pacific/Port_Moresby',
			'Australia/Brisbane'             => 'Australia/Brisbane',
			'Australia/Sydney'               => 'Australia/Sydney',
			'Australia/Hobart'               => 'Australia/Hobart',
			'Asia/Magadan'                   => 'Asia/Magadan',
			'SST'                            => 'SST',
			'Pacific/Noumea'                 => 'Pacific/Noumea',
			'Asia/Kamchatka'                 => 'Asia/Kamchatka',
			'Pacific/Fiji'                   => 'Pacific/Fiji',
			'Pacific/Auckland'               => 'Pacific/Auckland',
		);
	}
}

/**
 * Get Users using transients
 *
 * @since  2.1.0
 * @author Deepen
 */
if ( ! function_exists( 'video_conferencing_zoom_api_get_user_transients' ) ) {
	function video_conferencing_zoom_api_get_user_transients() {
		//Check if any transient by name is available
		$check_transient = get_transient( '_zvc_user_lists' );
		if ( $check_transient ) {
			$users = $check_transient->users;
		} else {
			$encoded_users = zoom_conference()->listUsers();
			$decoded_users = json_decode( $encoded_users );
			
			if ( ! empty( $decoded_users->code ) && $decoded_users->code == 300 ) {
				$users = false;
			} else {
				//storing data to transient and getting those data for fast load by setting to fetch every 15 minutes
				set_transient( '_zvc_user_lists', $decoded_users, 900 );
				$users = $decoded_users->users;
			}
		}

		return $users;
	}
}

add_action( 'wp_ajax_video_conferencing_zoom_api_dismissed_notice_handler', 'video_conferencing_zoom_api_ajax_notice_handler' );

function video_conferencing_zoom_api_ajax_notice_handler() {
	// Pick up the notice "type" - passed via jQuery (the "data-notice" attribute on the notice)
	if ( isset( $_POST['type'] ) && $_POST['type'] == 'remove-share-text' ) {
		update_option( 'zoom_api_dismiss_share_notice', 'disable' );
	}
}

if ( ! function_exists( 'video_conferencing_zoom_api_show_like_popup' ) ) {
	function video_conferencing_zoom_api_show_like_popup() {
		?>
		<div class="zoom-status-notice notice notice-warning is-dismissible">
			<h3><?php _e( 'ZOOM SERVICES STATUS', 'video-conferencing-with-zoom-api' ); ?></h3>
			<p>
				<?php
					printf( esc_html__( 'Experiencing issues with the Zoom meeting window? Please check the status of Zoom services under Zoom Website -> Web Client %1$s', 'video-conferencing-with-zoom-api' ), '<a target="_blank" href="https://status.zoom.us/">here</a>' );
				?>
			</p>
		</div>
		<?php

		if ( get_option( 'zoom_api_dismiss_share_notice' ) == 'disable' ) {
			return;
		}
		$current_user = wp_get_current_user();
		if ( isset( $current_user->display_name ) ) {
			$admin_name = $current_user->display_name;
		} else {
			$admin_name = 'Admin';
		}

		?>
		<div data-notice="remove-share-text" class="zoom-help-notice notice notice-warning is-dismissible">
			<h3><?php printf( esc_html__( 'Hey %1$s, want to support the development efforts?', 'video-conferencing-with-zoom-api' ), $admin_name ); ?></h3>
			<p>
				<?php
					printf( esc_html__( 'Show your support for the development of this FREE plugin %1$s', 'video-conferencing-with-zoom-api' ), '<a target="_blank" href="https://elearningevolve.com/products/support-zoom-plugin-development">here</a>' );
				?>
			</p>
			<p>
				<?php
					printf( esc_html__( 'Do not miss an important update from us by subscribing to our list %1$s', 'video-conferencing-with-zoom-api' ), '<a target="_blank" href="https://elearningevolve.com/subscription-form/">here</a>' );
				?>
			</p>
			<p>
				
				<?php
					printf( esc_html__( 'Check %s for shortcode reference.', 'video-conferencing-with-zoom-api' ), '<a href="?page=zoom-video-conferencing-settings">settings</a>' );
				?>
			</p>
		</div>
		<?php
	}
}
