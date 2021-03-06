<?php
/**
 * @author Deepen.
 * @created_on 6/24/19
 */

class DigitalCustDev_Webinars {

	public function __construct() {
		add_action( 'learn-press/after-new-section-item', array( $this, 'create_lesson_webinar' ), 5, 3 );

		add_action( 'learn-press/removed-item-from-section', array( $this, 'delete_webinar_when_section_removed' ), 10, 2 );

		add_action( 'added_post_meta', array( $this, 'after_post_meta' ), 10, 4 );
		add_action( 'updated_post_meta', array( $this, 'after_post_meta' ), 10, 4 );

		add_action( 'before_delete_post', array( $this, 'delete_webinar' ) );
		add_action( 'post_updated', array( $this, 'updated_lesson' ), 10, 3 );

		add_action( 'wp_ajax_check_webinar_existing_date', array( $this, 'check_webinars' ) );
	}

	function create_lesson_webinar( $id, $section_id, $course_id ) {
		if ( "webinar" != get_post_meta( $course_id, '_course_type', true ) ) {
			return;
		}

		$start_time   = get_post_meta( $id, '_webinar_start_time', true );
		$course_title = get_the_title( $id );
		$post_type    = get_post_type( $id );

		if ( $post_type === "lp_lesson" && ! empty( $course_title ) && ! empty( $start_time ) ) {
			$this->create_webinar( $id );
		} else {
			return;
		}
	}

	public function updated_lesson( $post_id, $post_after, $post_before ) {
		$course_id = LP_Request::get_string( 'course_ID' );

		set_post_format( $post_id, 'video' );

		update_post_meta( $post_id, 'omar2', 'ttttttt' );
		if ( "webinar" != get_post_meta( $course_id, '_course_type', true ) ) {
			return;
		}

		
		$start_time   = get_post_meta( $post_id, '_webinar_start_time', true );
		$course_title = get_the_title( $post_id );
		$post_type    = get_post_type( $post_id );
		if ( $post_type === "lp_lesson" && ! empty( $course_title ) && ! empty( $start_time ) ) {
			$this->create_webinar( $post_id );
		}
	}

	function delete_webinar_when_section_removed( $item_id, $cid ) {
		$post_type = get_post_type( $item_id );
		if ( $post_type != 'lp_lesson' ) {
			return;
		}

		$this->delete_webinar( $item_id );
	}

	function delete_webinar( $postid ) {
		$post_type = get_post_type( $postid );
		if ( $post_type != 'lp_lesson' ) {
			return;
		}

		$webinar_id = get_post_meta( $postid, '_webinar_ID', true );
		if ( $webinar_id ) {
			dcd_zoom_conference()->deleteWebinar( $webinar_id );
		}
	}

	function after_post_meta( $meta_id, $post_id, $meta_key, $meta_value ) {
		$post_type = get_post_type( $post_id );
		if ( $post_type != "lp_lesson" ) {
			return;
		}

		$post_metas = LP_Request::get( 'postMeta' );
		if ( ! empty( $post_metas ) ) {
			$this->create_webinar( $post_id );
		}
	}

	function create_webinar( $post_id, $post_metas = array() ) {
		$post_type = get_post_type( $post_id );
		//$author_id = learn_press_get_current_user_id();

		$get_administrator  = get_user_by( 'email', get_option( 'admin_email' ) );
		// $author_id = get_post_field( 'post_author', $post_id );
		$author_id = $get_administrator->ID;
		update_post_meta( $post_id, 'omsrauthorid', $author_id );

		if ( "lp_lesson" != $post_type ) {
			return;
		}

		if ( get_post_status( $post_id ) === 'draft' && 'auto-draft' === get_post_status( $post_id ) && 'trash' === get_post_status( $post_id ) ) {
			return;
		}

		$lesson         = get_post( $post_id );
		$webinar_exists = get_post_meta( $post_id, '_webinar_ID', true );
		if ( empty( $webinar_exists ) ) {
			update_post_meta( $post_id, 'omar2', 'inside if' );
			$timezone   = get_post_meta( $post_id, '_webinar_timezone', true );
			$start_time = get_post_meta( $post_id, '_webinar_start_time', true );
			if($start_time) $start_time = date( "Y-m-d\TH:i:s", strtotime( $start_time ) );
			$host_id    = get_user_meta( $author_id, 'user_zoom_hostid', true );

			if ( ! empty( $post_metas ) ) {
				$timezone   = ! empty( $post_metas['_lp_timezone'] ) ? $post_metas['_lp_timezone'] : $timezone;
				$start_time = ! empty( $post_metas['_lp_webinar_when'] ) ? date( "Y-m-d\TH:i:s", strtotime( $post_metas['_lp_webinar_when'] ) ) : date( "Y-m-d\TH:i:s" );
			}

			if ( ! empty( $host_id ) ) {
				//Create webinar here
				$postData        = array(
					'topic'      => $lesson->post_title,
					'type'       => 5,
					'start_time' => ! empty( $start_time ) ? $start_time : date( "Y-m-d\TH:i:s" ),
					'timezone'   => ! empty( $timezone ) ? $timezone : '',
					'agenda'     => $lesson->post_content,
					'settings'   => array(
						'host_video'        => false,
						'panelists_video'   => true,
						'approval_type'     => 0,
						'registration_type' => 1,
						'auto_recording'    => 'cloud'
					)
				);
			
				if($timezone && $start_time){
					$created_webinar = dcd_zoom_conference()->createWebinar( $host_id, $postData );
					$created_webinar = json_decode( $created_webinar );
					// update_post_meta( $post_id, 'hostfrom', $created_webinar );
					if ( ! empty( $created_webinar ) ) {
						update_post_meta( $post_id, '_webinar_ID', $created_webinar->id );
						update_post_meta( $post_id, '_webinar_details', $created_webinar );
					}
				}
			}
		} else {
			// update_post_meta( $post_id, 'omar2', 'inside else' );
			$timezone   = get_post_meta( $post_id, '_webinar_timezone', true );
			$start_time = get_post_meta( $post_id, '_webinar_start_time', true );
			if($start_time) $start_time = date( "Y-m-d\TH:i:s", strtotime( $start_time ) );

			if ( ! empty( $post_metas ) ) {
				$timezone   = ! empty( $post_metas['_lp_timezone'] ) ? $post_metas['_lp_timezone'] : $timezone;
				$start_time = ! empty( $post_metas['_lp_webinar_when'] ) ? date( "Y-m-d\TH:i:s", strtotime( $post_metas['_lp_webinar_when'] ) ) : date( "Y-m-d\TH:i:s" );
			}

			$webinar_id = get_post_meta( $post_id, '_webinar_ID', true );
			if ( ! empty( $webinar_id ) ) {
				//Create webinar here
				$postData = array(
					'topic'      => $lesson->post_title,
					'type'       => 5,
					'start_time' => $start_time,
					'timezone'   => ! empty( $timezone ) ? $timezone : '',
					'agenda'     => $lesson->post_content,
					'settings'   => array(
						'host_video'        => false,
						'panelists_video'   => true,
						'approval_type'     => 0,
						'registration_type' => 1,
						'auto_recording'    => 'cloud'
					)
				);

				if($timezone && $start_time){
					//Updated
					$updateWebinar = dcd_zoom_conference()->updateWebinar( $webinar_id, $postData );
					$updateWebinar = json_decode( $updateWebinar );
									if ( ! empty( $updateWebinar ) ) {
										update_post_meta( $post_id, '_webinar_ID', $updateWebinar->id );
										update_post_meta( $post_id, '_webinar_details', $updateWebinar );
									}
				}
			}
		}
	}

	private function webinar_times() {
		return [ "00:00", "00:15", "00:30", "00:45", "01:00", "01:15", "01:30", "01:45", "02:00", "02:15", "02:30", "02:45", "03:00", "03:15", "03:30", "03:45", "04:00", "04:15", "04:30", "04:45", "05:00", "05:15", "05:30", "05:45", "06:00", "06:15", "06:30", "06:45", "07:00", "07:15", "07:30", "07:45", "08:00", "08:15", "08:30", "08:45", "09:00", "09:15", "09:30", "09:45", "10:00", "10:15", "10:30", "10:45", "11:00", "11:15", "11:30", "11:45", "12:00", "12:15", "12:30", "12:45", "13:00", "13:15", "13:30", "13:45", "14:00", "14:15", "14:30", "14:45", "15:00", "15:15", "15:30", "15:45", "16:00", "16:15", "16:30", "16:45", "17:00", "17:15", "17:30", "17:45", "18:00", "18:15", "18:30", "18:45", "19:00", "19:15", "19:30", "19:45", "20:00", "20:15", "20:30", "20:45", "21:00", "21:15", "21:30", "21:45", "22:00", "22:15", "22:30", "22:45", "23:00", "23:15", "23:30", "23:45" ];
	}

	//Check if there is existing webinars
	function check_webinars() {
		$user_id = get_current_user_id();
		$host_id = get_user_meta( $user_id, 'user_zoom_hostid', true );
		$result  = array();

		$args             = array(
			'post_type' => 'lp_lesson',
			'author'    => $user_id
		);
		$lessons          = get_posts( $args );
		$times            = $this->webinar_times();
		$selected         = filter_input( INPUT_POST, 'selected' );
		$selected_compare = str_replace( '/', '-', $selected );
		$selected_compare = date( 'Y/m/d', strtotime( $selected_compare ) );
		if ( ! empty( $lessons ) ) {
			foreach ( $lessons as $lesson ) {
				$start_time = get_post_meta( $lesson->ID, '_lp_webinar_when', true );
				$timezone   = get_post_meta( $lesson->ID, '_lp_timezone', true );
				$duration   = get_post_meta( $lesson->ID, '_lp_duration', true );

				$change_sdate = str_replace( '/', '-', $start_time );
				$start_time   = date( 'Y/m/d H:i', strtotime( $change_sdate ) );
				$end_time     = date( 'Y/m/d H:i', strtotime( $start_time . '+' . $duration ) );
				if ( $start_time < $end_time && date( 'Y/m/d', strtotime( $change_sdate ) ) === $selected_compare ) {
					$time_starting = date( 'H:i', strtotime( $start_time ) );
					$time_ending   = date( 'H:i', strtotime( $end_time ) );
					foreach ( $times as $k => $time ) {
						if ( $time >= $time_starting && $time <= $time_ending ) {
							unset( $times[ $k ] );
						}
					}
				}
			}

			if ( empty( $times ) ) {
				$result = array( 'allowed_times' => false, 'date' => $selected, 'disabled_date' => $selected_compare );
			} else {
				$result = array( 'allowed_times' => $times, 'date' => $selected );
			}
		}

		wp_send_json( $result );
		wp_die();
	}

	/*function check_webinars() {
		$user_id = get_current_user_id();
		$host_id = get_user_meta( $user_id, 'user_zoom_hostid', true );
		$result  = array();
		if ( ! empty( $host_id ) ) {
			$display          = filter_input( INPUT_POST, 'display' );
			$selected         = filter_input( INPUT_POST, 'selected' );
			$selected_compare = date( 'Y-m-d H', strtotime( $selected ) );
			$webinars         = json_decode( zoom_conference()->listWebinar( $host_id ) );
			if ( ! empty( $webinars ) && ! empty( $webinars->webinars ) ) {
				if ( $display === "inline" ) {
					foreach ( $webinars->webinars as $webinar ) {
						$start_time = date( 'Y-m-d H:i:s +00', strtotime( $webinar->start_time ) );
						$date       = new DateTime( $start_time );
						$date->setTimezone( new DateTimeZone( $webinar->timezone ) );

						if ( $date->format( 'Y-m-d H' ) === $selected_compare ) {
							$result = array(
								'msg'     => 'Selected time slot: ' . $date->format( 'Y-m-d H:i a' ) . ' + 1 hour is already booked. Please select another time slot !',
								'success' => false,
							);
						}
					}
				} else {
					foreach ( $webinars->webinars as $webinar ) {
						$start_time = date( 'Y-m-d H:i:s +00', strtotime( $webinar->start_time ) );
						$date       = new DateTime( $start_time );
						$date->setTimezone( new DateTimeZone( $webinar->timezone ) );

						$result[] = array( 'id' => $webinar->id, 'start_date' => $date->format( 'Y-m-d' ), 'start_time' => $date->format( 'H:i' ), 'date_time' => $date->format( 'Y-m-d H:i:s' ), 'duration' => $webinar->duration, 'timezone' => $webinar->timezone, 'success' => true );
					}
				}
			}
		} else {
			$result = array(
				'msg'     => 'You have not be approved as a host in zoom by administrator.',
				'success' => false,
			);
		}

		wp_send_json( $result );
		wp_die();
	}*/
}

new DigitalCustDev_Webinars();
