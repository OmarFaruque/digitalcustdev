<?php
use \Firebase\JWT\JWT;
/**
 * @author Deepen.
 * @created_on 7/2/19
 */


if ( ! class_exists( 'DigitalCustDev_Zoom_API' ) && class_exists('Zoom_Video_Conferencing_Api') ) {

	class DigitalCustDev_Zoom_API extends Zoom_Video_Conferencing_Api {

		protected static $_instance;


		/**
		 * API endpoint base
		 *
		 * @var string
		 */
		private $api_url = 'https://api.zoom.us/v2/';

		
		/**
		 * Create only one instance so that it may not Repeat
		 *
		 * @since 2.0.0
		 */
		
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function __construct() {
			parent::__construct();

			//Load the Credentials
			$this->zoom_api_key    = get_option( 'zoom_api_key' );
			$this->zoom_api_secret = get_option( 'zoom_api_secret' );

			add_filter( 'zvc_allow_zoom_host_id_user_role', array( $this, 'add_roles' ) );
		}

		private function generateJWTKey() {
			$key    = zoom_conference()->zoom_api_key;
			$secret = zoom_conference()->zoom_api_secret;
	
			$token = array(
				"iss" => $key,
				"exp" => time() + 3600 //60 seconds as suggested
			);
	
			return JWT::encode( $token, $secret );
		}

	
		/**
		 * Create a Webinar
		 *
		 * @param $userId
		 * @param array $data
		 *
		 * @return bool|mixed
		 */
		public function createWebinar( $userId, $data = array() ) {
			return $this->sendRequest( 'users/' . $userId . '/webinars', $data, "POST" );
		}

		public function updateWebinar( $webinarId, $data = array() ) {
			return $this->sendRequest( 'webinars/' . $webinarId, $data, "PATCH" );
		}

		public function deleteWebinar( $webinarId ) {
			return $this->sendRequest( 'webinars/' . $webinarId, false, "DELETE" );
		}

		public function webinarregistrantcreate( $webinarId, $data = array() ) {
			return $this->sendRequest( 'webinars/' . $webinarId . '/registrants', $data, "POST" );
		}
		public function getWebinarRegistrantList($webinarId){
			// echo 'this pi key: ' . $this->api_url . '<br/>';
			$curl = curl_init();

			curl_setopt_array($curl, array(
			CURLOPT_URL => $this->api_url . "webinars/".$webinarId."/registrants?page_number=1&page_size=30&status=approved",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"authorization: Bearer " . $this->generateJWTKey()
			),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				return "cURL Error #:" . $err;
			} else {
				return $response;
			}
		}


		public function enableUserStatistoActive($hostid, $status){
			if ( !empty( $hostid ) ) {

				$data = array(
					'action' => $status
				);
				return $this->sendRequest( 'users/'.$hostid.'/status', $data, "PUT" );

			} 
		}



		/*
		* Get Recorded meeting id
		*/
		public function getMeetingRecordUrl($meetingId){
			return $this->sendRequest( 'meetings/'.$meetingId.'/recordings', array(), "GET" );
		}


		/*
		* Set webinar status deactive
		* Source @ https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/webinarstatus
		*/
		public function zoomWebinarStatusEnd($webinarId){

			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.zoom.us/v2/webinars/".$webinarId."/status",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "PUT",
			CURLOPT_POSTFIELDS => "{\"action\":\"end\"}",
			CURLOPT_HTTPHEADER => array(
				"authorization: Bearer ".$this->generateJWTKey()."",
				"content-type: application/json"
			),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				return "cURL Error #:" . $err;
			} else {
				return $response;
			}
		}


		/*
		* Zoom Meeting Recording Settings update
		*/
		public function zoomRecordingSettingsUpdate($webinarId){
			$data = array(
				// 'authentication_domains' => get_home_url( ),
				'share_recording' => 'publicly',
				'viewer_download' => false,
				'on_demand' => true,
				'approval_type' => 0
			);
			// $data = json_encode($data);
			return $this->sendRequest( 'meetings/'.$webinarId.'/recordings/settings', $data, "PATCH" );
		}


		/*
		* Webinar Start Token
		*/
		public function zoomWebinarStartToken($hostid){
			// $host_id = get_user_meta( $user_id, 'user_zoom_hostid', true );
			$host_id = $hostid;
			$curl = curl_init();

			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.zoom.us/v2/users/".$host_id."/token",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"authorization: Bearer ".$this->generateJWTKey().""
			),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				return "cURL Error #:" . $err;
			} else {
				return $response;
			}
		}

		/*
		Update User type
		*/
		public function updateZoomUserType($hostid, $type){
			$host_id = $hostid;
			if($host_id):
			$curl = curl_init();
			$data = array();
			$data['type'] = $type;
			$postFields = json_encode($data);
			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.zoom.us/v2/users/" . $host_id,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "PATCH",
			CURLOPT_POSTFIELDS => $postFields,
			CURLOPT_HTTPHEADER => array(
				"authorization: Bearer ".$this->generateJWTKey()."",
				"content-type: application/json"
			),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				return "cURL Error #:" . $err;
			} else {
				return 'success';
			}
		else:
			return false;
		endif;
		}


		/*
		* Get Single Webinar Details
		 @Source: https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/webinar
		*/
		public function getZoomWebinarDetails($post_id){
			$webinarId = get_post_meta( $post_id, '_webinar_ID', true );
			if($webinarId):
				$curl = curl_init();
				curl_setopt_array($curl, array(
				CURLOPT_URL => "https://api.zoom.us/v2/webinars/" . $webinarId,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"authorization: Bearer ".$this->generateJWTKey().""
				),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
				return "cURL Error #:" . $err;
				} else {
				return $response;
				}
			endif;
		}


		/**
		 * User Function to List
		 *
		 * @return array
		 */
		public function listUsersByStatus($status) {
			$listUsersArray              = array(
				'status' => $status
			);
			$listUsersArray['page_size'] = 300;

			return $this->sendRequest( 'users', $listUsersArray, "GET" );
		}


		/**
		 * Get a Meeting Info
		 *
		 * @param  [INT] $id
		 * @param  [STRING] $host_id
		 *
		 * @return array
		 */
		public function getWebinarInfo( $id ) {
			$getMeetingInfoArray = array();
			$getMeetingInfoArray = apply_filters( 'vczapi_getMeetingInfo', $getMeetingInfoArray );

			return $this->sendRequest( 'webinars/' . $id, $getMeetingInfoArray, "GET" );
		}



		function add_roles( $roles ) {
			$roles[] = 'lp_teacher';

			return $roles;
		}


		/*
		* Delete Zoom recording while delete wp webinar
		*/
		public function deleteRecording($webinarid){
			return $this->sendRequest( 'meetings/' . $webinarid . '/recordings?action=delete', false, "DELETE" );
		}


		/*
		* Create User function for set picture
		*/
		public function createUserPicture($data){
			$id = $data['hostid'];
			$avatar_url = get_avatar_url( $data['user_id'] );
			
			$image = $avatar_url;
			$fields = [
				'pic_file' => new \CurlFile($image, 'image/png', 'avatar_03')
			];
			// echo 'file url: ' . $avatar_url . '<br/>';
			$postFields = json_encode($daraArray);
				$curl = curl_init();

				curl_setopt_array($curl, array(
				CURLOPT_URL => $this->api_url . "users/".$id."/picture",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $fields,
				CURLOPT_HTTPHEADER => array(
					"authorization: Bearer ".$this->generateJWTKey()."",
					"content-type: multipart/form-data;"
				),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
					return "cURL Error #:" . $err;
				} else {
					return $response;
				}


		}
	}

	function dcd_zoom_conference() {
		return DigitalCustDev_Zoom_API::instance();
	}

	dcd_zoom_conference();
}
