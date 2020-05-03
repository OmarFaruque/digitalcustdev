<?php
use \Firebase\JWT\JWT;
/**
 * @author Deepen.
 * @created_on 7/2/19
 */


if ( ! class_exists( 'DigitalCustDev_Zoom_API' ) ) {

	class DigitalCustDev_Zoom_API extends Zoom_Video_Conferencing_Api {

		protected static $_instance;

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

		private function generateJWTKey() {
			$key    = zoom_conference()->zoom_api_key;
			$secret = zoom_conference()->zoom_api_secret;
	
			$token = array(
				"iss" => $key,
				"exp" => time() + 3600 //60 seconds as suggested
			);
	
			return JWT::encode( $token, $secret );
		}

		public function __construct() {
			parent::__construct();

			//Load the Credentials
			$this->zoom_api_key    = get_option( 'zoom_api_key' );
			$this->zoom_api_secret = get_option( 'zoom_api_secret' );

			add_filter( 'zvc_allow_zoom_host_id_user_role', array( $this, 'add_roles' ) );
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
			// return $this->sendRequest( 'webinars/' . $webinarId, $data, "PATCH" );
		echo 'inside omar fa';
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://api.zoom.us/v2/webinars/" . $webinarId,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "PATCH",
			  CURLOPT_POSTFIELDS => http_build_query($data),
			  CURLOPT_HTTPHEADER => array(
				"authorization: Bearer " . $this->generateJWTKey(),
				"content-type: application/json"
			  ),
			));
			
			$response = curl_exec($curl);
			$err = curl_error($curl);
			
			curl_close($curl);
			
			if ($err) {
			  echo "cURL Error #:" . $err;
			} else {
			  echo $response;
			}
			
		
		
		}

		public function deleteWebinar( $webinarId ) {
			return $this->sendRequest( 'webinars/' . $webinarId, false, "DELETE" );
		}

		public function webinarregistrantcreate( $webinarId, $data = array() ) {
			return $this->sendRequest( 'webinars/' . $webinarId . '/registrants', $data, "POST" );
		}

		function add_roles( $roles ) {
			$roles[] = 'lp_teacher';

			return $roles;
		}
	}

	function dcd_zoom_conference() {
		return DigitalCustDev_Zoom_API::instance();
	}

	dcd_zoom_conference();
}
