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
			echo 'this pi key: ' . $this->api_url . '<br/>';
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
