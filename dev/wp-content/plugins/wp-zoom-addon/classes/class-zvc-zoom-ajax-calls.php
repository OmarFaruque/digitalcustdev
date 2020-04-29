<?php

/**
 * Class for all the administration ajax calls
 *
 * @since   2.0.0
 * @author  Deepen
 */
class Zoom_Video_Conferencing_Ajax_Calls {

	public function __construct() {
		//Get Users List AJAX
		add_action( 'wp_ajax_zvc_get_users_tinymce', array( $this, 'aj_ajax_get_users_data_tinymce' ) );

		//GEt user info ajax
		add_action( 'wp_ajax_zvc_get_user_info', array( $this, 'get_user_info' ) );

		//Delete User
		add_action( 'wp_ajax_zvc_delete_user', array( $this, 'delete_user' ) );

		//Delete Meeting
		add_action( 'wp_ajax_zvc_delete_meeting', array( $this, 'delete_meeting' ) );
		add_action( 'wp_ajax_zvc_bulk_meetings_delete', array( $this, 'delete_bulk_meeting' ) );

		// End Meetings with Ajax 
		add_action( 'wp_ajax_zvc_end_meeting', array($this, 'end_meeting') );
	}


	// End Meeting with AJax
	public function end_meeting(){
		check_ajax_referer( '_nonce_zvc_security', 'security' );

		$meeting_id = $_POST['meeting_id'];
		if ( $meeting_id ) {
			zoom_conference()->endMeetingStatus( $meeting_id );		
			wp_send_json( array( 'error' => 0, 'msg' => __( "Meeting Ended.", "video-conferencing-with-zoom-api" ) ) );
		} else {
			wp_send_json( array( 'error' => 1, 'msg' => __( "An Error Occured Ending Meeting. Please Try Again", "video-conferencing-with-zoom-api" ) ) );
		}

		wp_die();
	}

	/**
	 * Get All Users AJX Response here related to TinyMCE
	 *
	 * @return array
	 */

	public function aj_ajax_get_users_data_tinymce() {
		$check = $_POST['do'];
		if ( $check == 'listUsers' ) {
			//Get users List
			$encoded_users = zoom_conference()->listUsers();
			if ( isset( json_decode( $encoded_users )->error ) ) {
				//Nothing Here so Exit
				exit;
			} else {
				//Decoding
				$decoded_users = json_decode( $encoded_users )->users;

				//Send Users Data
				wp_send_json( $decoded_users );
			}
		}

		if ( $check == 'getMeetings' ) {
			$host_id = $_POST['host_id'];
			//Get Meetings List
			$encoded_meetings = zoom_conference()->listMeetings( $host_id );
			if ( isset( json_decode( $encoded_meetings )->error ) ) {
				//Nothing Here so Exit
				exit;
			} else {
				//Decoding
				$decoded_meetings = json_decode( $encoded_meetings )->meetings;

				//Send Users Data
				wp_send_json( $decoded_meetings );
			}
		}

		wp_die();
	}

	/**
	 * Get a Users Info
	 *
	 * @author  Deepen
	 * @since   2.0.0
	 */
	public function get_user_info() {
		check_ajax_referer( '_nonce_zvc_security', 'security' );
		$user_info = json_decode( zoom_conference()->getUserInfo( $_POST['userId'] ) );
		if ( $user_info ) {
			//Send Response of Fetched Data
			wp_send_json( $user_info );
		}

		wp_die();
	}

	/**
	 * Delete a Users
	 *
	 * @author  Deepen
	 * @since   2.0.0
	 */
	public function delete_user() {
		check_ajax_referer( '_nonce_zvc_security', 'security' );
		$userID = isset( $_POST['user_id'] ) ? $_POST['user_id'] : false;
		if ( $userID ) {
			$deleted = json_decode( zoom_conference()->deleteAUser( $userID ) );
			if ( $deleted ) {
				wp_send_json( array( "status" => 1, "msg" => "Successfully deleted user" ) );
			} else {
				wp_send_json( array( "status" => 0, "msg" => "An Error Occured while trying to delete this User. Please try Again!" ) );
			}

			//Fetch latest Data.
			delete_transient( '_zvc_user_lists' );
		} else {
			wp_send_json( array( "status" => 0, "msg" => "An Error Occured while trying to delete this User. Please try Again!" ) );
		}

		wp_die();
	}

	/**
	 * Delete a Meeting
	 *
	 * @author   Deepen
	 * @since    2.0.0
	 * @modified 2.1.0
	 */
	public function delete_meeting() {
		check_ajax_referer( '_nonce_zvc_security', 'security' );

		$meeting_id = $_POST['meeting_id'];
		$host_id    = $_POST['host_id'];
		if ( $meeting_id && $host_id ) {
			zoom_conference()->deleteAMeeting( $meeting_id, $host_id );
			wp_send_json( array( 'error' => 0, 'msg' => __( "Deleted meeting.", "video-conferencing-with-zoom-api" ) ) );
		} else {
			wp_send_json( array( 'error' => 1, 'msg' => __( "An error occured. Host ID and Meeting ID not defined properly.", "video-conferencing-with-zoom-api" ) ) );
		}

		wp_die();
	}

	/**
	 * Delete Meeting in Bulk
	 *
	 * @since    1.0.0
	 * @modified 2.1.0
	 */
	public function delete_bulk_meeting() {
		check_ajax_referer( '_nonce_zvc_security', 'security' );

		$deleted     = false;
		$meeting_ids = $_POST['meetings_id'];
		$host_ids = $_POST['host_ids'];

		if ( $meeting_ids ) {
			$meeting_count = count( $meeting_ids );
			foreach ( $meeting_ids as $key => $meeting_id ) {
				json_decode( zoom_conference()->deleteAMeeting( $meeting_id, $host_ids[$key] ) );
				$deleted = true;
			}

			if ( $deleted ) {
				wp_send_json( array( 'error' => 0, 'msg' => sprintf( __( "Deleted %d Meeting(s).", "video-conferencing-with-zoom-api" ), $meeting_count ) ) );
			}
		} else {
			wp_send_json( array( 'error' => 1, 'msg' => __( "You need to select a data in order to initiate this action." ) ) );
		}

		wp_die();
	}
}

new Zoom_Video_Conferencing_Ajax_Calls();
