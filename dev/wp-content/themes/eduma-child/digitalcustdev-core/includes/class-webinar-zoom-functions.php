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

		add_action( 'wp_ajax_check_webinar_existing_date_for_zoom', array( $this, 'check_webinars_for_zoom' ) );

		// add_action('admin_init', array($this, 'callbackTestFunction'));
	}

	public function callbackTestFunction(){
		// $this->updated_lesson(16257, '', '');

		$metas = get_post_meta( 16257, '_webinar_details', true );
		$metas->created_at = date('Y-m-d\TH:i:s');
		echo 'Pomsts metas:<pre>';
		print_r($metas);
		echo '</pre>';
	}

	function create_lesson_webinar( $id, $section_id, $course_id ) {
		if ( "webinar" != get_post_meta( $course_id, '_course_type', true ) ) {
			return;
		}

		$start_time   = get_post_meta( $id, '_webinar_start_time', true );
		$course_title = get_the_title( $id );
		$post_type    = get_post_type( $id );

		if ( $post_type === "lp_lesson" && ! empty( $course_title ) && ! empty( $start_time ) ) {
			// echo 'create omar 6<br>';
			$this->create_webinar( $id );
		} else {
			return;
		}
	}

	function updated_lesson( $post_id, $post_after, $post_before ) {
		$course_curd = new LP_Course_CURD();
		$course_id = $course_curd->get_course_by_item( $post_id );
		$course_id = $course_id[0];

		set_post_format( $post_id, 'video' );

		if ( "webinar" != get_post_meta( $course_id, '_course_type', true ) ) {
			return;
		}

		if(get_post_status($course_id) == 'publish' ){
			$start_time   = get_post_meta( $post_id, '_lp_webinar_when', true );
			$course_title = get_the_title( $post_id );
			$post_type    = get_post_type( $post_id );
			
			if ( $post_type === "lp_lesson" && ! empty( $course_title ) && ! empty( $start_time ) ) {
				// $this->update_webinar( $post_id );
			}
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
			// echo 'create omar 5<br>';
			$this->create_webinar( $post_id, $post_metas );
		}
	}

	
	
	function update_webinar( $post_id, $post_metas = array() ) {
		
		$post_type = get_post_type( $post_id );
		
		$get_administrator  = get_user_by( 'email', get_option( 'admin_email' ) );
		// $author_id = get_post_field( 'post_author', $post_id );
		$author_id = $get_administrator->ID;
		update_post_meta( $post_id, 'omsrauthorid', get_option( 'admin_email' ) );

		if ( "lp_lesson" != $post_type ) {
			return;
		}

		

		if ( get_post_status( $post_id ) === 'draft' && 'auto-draft' === get_post_status( $post_id ) && 'trash' === get_post_status( $post_id ) ) {
			return;
		}

		$lesson         = get_post( $post_id );
		$webinar_exists = get_post_meta( $post_id, '_webinar_ID', true );
		
			$timezone   = get_post_meta( $post_id, '_lp_timezone', true );
			$start_time = get_post_meta( $post_id, '_lp_webinar_when', true );
			if($start_time) $start_time = str_replace('/', '-', $start_time);
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

				//Updated
				$updateWebinar = dcd_zoom_conference()->updateWebinar( $webinar_id, $postData );
				$updateWebinar = json_decode( $updateWebinar );
									if ( ! empty( $updateWebinar ) ) {
										update_post_meta( $post_id, '_webinar_ID', $updateWebinar->id );
										update_post_meta( $post_id, '_webinar_details', $updateWebinar );
									}else{
										$metas = get_post_meta( $post_id, '_webinar_details', true );
										$metas->created_at = date('Y-m-d\TH:i:s');
										update_post_meta( $post_id, '_webinar_details', $metas );
									}
			}
		
	}
	
	
	function create_webinar( $post_id, $post_metas = array() ) {
		$post_type = get_post_type( $post_id );
		
		$get_administrator  = get_user_by( 'email', get_option( 'admin_email' ) );
		// $author_id = get_post_field( 'post_author', $post_id );
		$author_id = $get_administrator->ID;
		update_post_meta( $post_id, 'omsrauthorid', get_option( 'admin_email' ) );

		if ( "lp_lesson" != $post_type ) {
			return;
		}

		

		if ( get_post_status( $post_id ) === 'draft' && 'auto-draft' === get_post_status( $post_id ) && 'trash' === get_post_status( $post_id ) ) {
			return;
		}

		$lesson         = get_post( $post_id );
		$webinar_exists = get_post_meta( $post_id, '_webinar_ID', true );
		if ( empty( $webinar_exists ) ) {
			$timezone   = get_post_meta( $post_id, '_lp_timezone', true );
			$start_time = get_post_meta( $post_id, '_lp_webinar_when', true );
			$start_time = str_replace('/', '-', $start_time);
			$start_time = ! empty( $start_time ) ? date( "Y-m-d\TH:i:s", strtotime( $start_time ) ) : date( "Y-m-d\TH:i:s" );
			
			
			// $host_id    = get_user_meta( $author_id, 'user_zoom_hostid', true );
			$host_id = LP()->settings->get( 'zoom_master_host' );

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
					// 'agenda'     => $lesson->post_content,
					'settings'   => array(
						'host_video'        => false,
						'panelists_video'   => true,
						'approval_type'     => 0,
						'registration_type' => 1,
						'auto_recording'    => 'cloud'
					)
				);
				$created_webinar = dcd_zoom_conference()->createWebinar( $host_id, $postData );
				
				$created_webinar = json_decode( $created_webinar );
				if ( ! empty( $created_webinar ) ) {
					update_post_meta( $post_id, '_webinar_ID', $created_webinar->id );
					update_post_meta( $post_id, '_webinar_details', $created_webinar );
				}
			}
		} else {
			$timezone   = get_post_meta( $post_id, '_webinar_timezone', true );
			$start_time = get_post_meta( $post_id, '_webinar_start_time', true );
			if($start_time) $start_time = str_replace('/', '-', $start_time);
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

				//Updated
				dcd_zoom_conference()->updateWebinar( $webinar_id, $postData );
			}
		}
	}

	private function webinar_times() {
		return [ "00:00", "00:15", "00:30", "00:45", "01:00", "01:15", "01:30", "01:45", "02:00", "02:15", "02:30", "02:45", "03:00", "03:15", "03:30", "03:45", "04:00", "04:15", "04:30", "04:45", "05:00", "05:15", "05:30", "05:45", "06:00", "06:15", "06:30", "06:45", "07:00", "07:15", "07:30", "07:45", "08:00", "08:15", "08:30", "08:45", "09:00", "09:15", "09:30", "09:45", "10:00", "10:15", "10:30", "10:45", "11:00", "11:15", "11:30", "11:45", "12:00", "12:15", "12:30", "12:45", "13:00", "13:15", "13:30", "13:45", "14:00", "14:15", "14:30", "14:45", "15:00", "15:15", "15:30", "15:45", "16:00", "16:15", "16:30", "16:45", "17:00", "17:15", "17:30", "17:45", "18:00", "18:15", "18:30", "18:45", "19:00", "19:15", "19:30", "19:45", "20:00", "20:15", "20:30", "20:45", "21:00", "21:15", "21:30", "21:45", "22:00", "22:15", "22:30", "22:45", "23:00", "23:15", "23:30", "23:45" ];
	}

	//Check if there is existing webinars
	function check_webinars() {
		$curd = new LP_Course_CURD();
		$user_id = get_current_user_id();
		$host_id = get_user_meta( $user_id, 'user_zoom_hostid', true );
		$result  = array();

		
		$times            	= $this->webinar_times();
		$selected         	= filter_input( INPUT_POST, 'selected' );
		$selected_compare 	= str_replace( '/', '-', $selected );
		$selected_compare 	= date( 'Y/m/d', strtotime( $selected_compare ) );

		$args             = array(
			'post_type' => 'lp_lesson',
			'post_status' => array('publish','pending'),
			'meta_query' => array(
				array(
					'key' => '_lp_webinar_when',
					'value' => date( 'd/m/Y', strtotime( $selected_compare ) ),
					'compare' => 'LIKE'
				)
			)
		);
		// $lession_id 	  	= filter_input( INPUT_POST, 'lession_id' );
		$lessons          	= get_posts( $args );
		

		 
		$course_idss = array();
		$newtime 			= array();
		if ( ! empty( $lessons ) ) {
			foreach ( $lessons as $lesson ){
				$course_ids = $curd->get_course_by_item( $lesson->ID );
				$course_status = get_post($course_ids[0]);
				if($course_status->post_status != 'draft'):
					$start_time = get_post_meta( $lesson->ID, '_lp_webinar_when', true );
					$timezone   = get_post_meta( $lesson->ID, '_lp_timezone', true );
					$duration   = get_post_meta( $lesson->ID, '_lp_duration', true );
					
					
					$change_sdate = str_replace( '/', '-', $start_time );
					$start_time   = date( 'Y/m/d H:i', strtotime( $change_sdate ) );
					
					$end_time     = date( 'Y/m/d H:i', strtotime( $start_time . '+' . $duration ) );
					
					if ( $start_time <= $end_time && date( 'Y/m/d', strtotime( $change_sdate ) ) === $selected_compare ) {
						$time_starting = date( 'H:i', strtotime("-15 minutes", strtotime( $start_time )) );
						$time_ending   = date( 'H:i', strtotime("+15 minutes", strtotime( $end_time ) ) );
						foreach ( $times as $k => $time ) {
							if ( $time >= $time_starting && $time <= $time_ending ) {
								array_push($newtime, $time);
							}
						}
					}
				endif; // if($course_status->post_status != 'draft'):

			}
		}

		
		if ( count($times) <= 0 ) {
			$result = array( 
				'allowed_times' => false, 
				'date' => $selected, 
				'disabled_date' => $selected_compare 
			);
		} else {
			$newtime2 = $newtime;
			// $newtime = array_diff($times, $newtime);
			$result = array( 
				'allowed_times' => $times, 
				'hide_time' => $newtime2,
				'course_ids' => $course_idss,
				'date' => $selected,
				'lessons' => $lessons,
				'user_id' => $user_id
			);
		}

		wp_send_json( $result );
		wp_die();
	}


	public function get_timezone_offset($remote_tz, $origin_tz = null) {
		if($origin_tz === null) {
			if(!is_string($origin_tz = date_default_timezone_get())) {
				return false; // A UTC timestamp was returned -- bail out!
			}
		}
		$origin_dtz = new DateTimeZone($origin_tz);
		$remote_dtz = new DateTimeZone($remote_tz);
		$origin_dt = new DateTime("now", $origin_dtz);
		$remote_dt = new DateTime("now", $remote_dtz);
		$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
		return $offset;
	}


		//Check admin zoom occcupied time
		function check_webinars_for_zoom() {
			$curd = new LP_Course_CURD();
			$user_id = get_current_user_id();
			$host_id = get_user_meta( $user_id, 'user_zoom_hostid', true );
			$result  = array();
	
			
			$times            	= $this->webinar_times();
			$selected         	= filter_input( INPUT_POST, 'selected' );
			$timezone         	= filter_input( INPUT_POST, 'timezone' );
			$selected_compare 	= str_replace( '/', '-', $selected );

			$remote_dtz = new DateTimeZone($timezone);
			$origin_dt = new DateTime("now", $remote_dtz);
			$offsetLocalTime = $remote_dtz->getOffset($origin_dt);
			


			
			// $selected_compare 	= date( 'Y/m/d', strtotime( $selected_compare ) );
	
			$args             = array(
				'post_type' => 'lp_lesson',
				'post_status' => array('publish','pending'),
				'meta_query' => array(
					array(
						'key' => '_lp_webinar_when_gmt',
						'value' => date( 'Y-m-d', strtotime($selected_compare)),
						'compare' => 'LIKE'
					)
				)
			);
			// $lession_id 	  	= filter_input( INPUT_POST, 'lession_id' );
			$lessons          	= get_posts( $args );
			 
			$course_idss = array();
			$newtime 			= array();
			$test = '';
			$starttimes = array();
			if ( ! empty( $lessons ) ) {
				foreach ( $lessons as $lesson ){
					$course_ids = $curd->get_course_by_item( $lesson->ID );
					$course_status = get_post($course_ids[0]);
					if($course_status->post_status != 'draft'):
						$start_time = get_post_meta( $lesson->ID, '_lp_webinar_when_gmt', true );
						$dbtimezone   = get_post_meta( $lesson->ID, '_lp_timezone', true );
						$duration   = get_post_meta( $lesson->ID, '_lp_duration', true );
						


						
						$change_sdate = str_replace( '/', '-', $start_time );
						$start_time   = date( 'Y-m-d H:i', strtotime( $change_sdate ) );
						$start_time = strtotime( date('Y-m-d H:i:s', strtotime($start_time)) ) + $offsetLocalTime;
						$start_time = date('Y-m-d H:i:s', $start_time);
						array_push($starttimes, $start_time);


						
						// $dbtimezoneoffset = $this->get_timezone_offset($dbtimezone);
						// array_push($offestarray, $dbtimezoneoffset);
						// $offset_time = strtotime( date('Y-m-d H:i:s', strtotime($start_time)) ) + $dbtimezoneoffset;
						// $start_time = date('Y-m-d H:i', $offset_time);


						$end_time     = date( 'Y-m-d H:i', strtotime( $start_time . '+' . $duration ) );
						
						if ( $start_time <= $end_time && date( 'Y-m-d', strtotime( $change_sdate ) ) === date( 'Y-m-d', strtotime( $selected_compare ) ) ) {
							// $time_starting = date( 'H:i', strtotime( $start_time ));
							// $time_ending   = date( 'H:i', strtotime( $end_time ) );
							$time_starting = date( 'H:i', strtotime("-15 minutes", strtotime( $start_time )) );
							$time_ending   = date( 'H:i', strtotime("+15 minutes", strtotime( $end_time ) ) );
							// $test = 'omar';
							foreach ( $times as $k => $time ) {
								if ( $time >= $time_starting && $time <= $time_ending ) {
									array_push($newtime, $time);
								}
							}
						}
					endif; // if($course_status->post_status != 'draft'):
				}
			}
	
			
			if ( count($times) <= 0 ) {
				$result = array( 
					'allowed_times' => false, 
					'date' => $selected, 
					'disabled_date' => $selected_compare 
				);
			} else {
				$newtime2 = $newtime;
				// $newtime = array_diff($times, $newtime);
				$result = array( 
					'allowed_times' => $times, 
					'hide_time' => $newtime2,
					'course_ids' => $course_ids,
					'date' => $selected,
					'lessons' => $lessons,
					'end_time' => $end_time,
					'user_id' => $user_id,
					'change_sdate' => $change_sdate, 
					'test' => $test, 
					'start_time' => $starttimes,
					'end_time' => $end_time,
					'timezone' => $timezone,
					'timezoneoffset' => $timezoneoffset,
					'selected_compare' => date( 'Y-m-d', strtotime( $selected_compare )),
					'gDate' => $gDate
				);
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
