<?php

class DigitalCustDev_WooCommerce_Hooks {

	public function __construct() {
		// echo 'inside construction omar';
		//Fire when a vendor sets order is completed
		add_action( 'woocommerce_order_status_completed', array( $this, 'register_into_webinar' ), 10, 1 );
		add_action( 'woocommerce_order_status_processing', array( $this, 'register_into_webinar' ), 10, 1 );


		// add_action('admin_init', array($this, 'thisistestunction'));

		
	}

	public function thisistestunction(){
		$orderid = 16341;
		// $this->register_into_webinar($orderid);
	}


/*
With direct learnpress order id
*/
public function register_into_webinar_using_learnpress_order_id( $order_id ) {
	// $lp_order_id    = get_post_meta( $order_id, '_learn_press_order_id', true );
	$lp_order       = learn_press_get_order( $order_id );
	$lp_order_items = $lp_order->get_items();
	$user_id        = $lp_order->get_user_id();
	$customer       = get_userdata( $user_id );

	if ( ! empty( $lp_order_items ) ) {
		foreach ( $lp_order_items as $order ) {
			$course_id = $order['course_id'];
			
			$item      = learn_press_get_course( $course_id );

			$lessons = $item->get_items();
			if ( ! empty( $lessons ) ) {
				foreach ( $lessons as $lesson ) {
					$lpt = get_post_type( $lesson );
					if ( $lpt === "lp_lesson" ) {
						$webinar_exists = get_post_meta( $lesson, '_webinar_ID', true );

						$registered = json_decode( get_post_meta( $lesson, '_lp_webinar_registration_user', true ) );
						if ( empty( $registered ) ) {
							$postData   = array(
								'email'      => $customer->user_email,
								'first_name' => $customer->first_name,
								'last_name'  => $customer->last_name
							);
							$registered = dcd_zoom_conference()->webinarregistrantcreate( $webinar_exists, $postData );
							update_post_meta( $lesson, '_lp_webinar_registration_user' . $user_id, $registered );
						}
					}
				}
			}
		}
	}
}


	/**
	 * Register customer into the webinar sessions
	 *
	 * @param $order_id
	 */
	public function register_into_webinar( $order_id ) {
		$lp_order_id    = get_post_meta( $order_id, '_learn_press_order_id', true );
		$lp_order       = learn_press_get_order( $lp_order_id );
		// echo 'lp order: ' . $lp_order . '<br/>';
		// echo 'omar farurque';
		$lp_order_items = $lp_order->get_items();
		$user_id        = $lp_order->get_user_id();
		$customer       = get_userdata( $user_id );

		// echo '<pre>';
		// print_r($lp_order_items);
		// echo '</pre>';

		if ( ! empty( $lp_order_items ) ) {
			foreach ( $lp_order_items as $order ) {
				$course_id = $order['course_id'];
				
				$item      = learn_press_get_course( $course_id );

				$lessons = $item->get_items();
				if ( ! empty( $lessons ) ) {
					foreach ( $lessons as $lesson ) {
						$lpt = get_post_type( $lesson );
						if ( $lpt === "lp_lesson" ) {
							$webinar_exists = get_post_meta( $lesson, '_webinar_ID', true );

							// echo '<pre>';
							// print_r(get_post_meta($lesson));
							// echo '</pre>';

							// echo 'webinar id: ' . $webinar_exists . '<br/>';
							// echo 'lesson id: ' . $lesson . '<br/>';
							$registered = json_decode( get_post_meta( $lesson, '_lp_webinar_registration_user' . $user_id, true ) );
							
							// echo 'user id: ' . $user_id . '<br/>';
							if ( empty( $registered ) ) {
								$postData   = array(
									'email'      => $customer->user_email,
									'first_name' => $customer->first_name,
									'last_name'  => $customer->last_name
								);
								$registered = dcd_zoom_conference()->webinarregistrantcreate( $webinar_exists, $postData );
								// echo 'registered: ' . $registered . '<br/>';
								update_post_meta( $lesson, '_lp_webinar_registration_user' . $user_id, $registered );
							}
						}
					}
				}
			}
		}
	}
}

new DigitalCustDev_WooCommerce_Hooks();