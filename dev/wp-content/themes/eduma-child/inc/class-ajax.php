<?php
/**
 * @author Deepen.
 * @created_on 6/19/19
 */

class DigitalCustDev_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_create_new_course', array( $this, 'create_new_post' ) );
		// add_action( 'wp_ajax_nopriv_create_new_course', array( $this, 'create_new_course' ) );

		add_action( 'wp_ajax_switch_role', array( $this, 'change_role' ) );

		add_action( 'wp_ajax_search_courses', array( $this, 'search_courses' ) );

		add_action( 'wp_ajax_delete_course_frontend', array( $this, 'delete_course' ) );
	}

	

	/**
	 * Create a new post type.
	 */
	public static function create_new_post() {
		$post_type = LP_Request::get_string( 'type' );
		$nonce     = LP_Request::get_string( 'nonce' );

		if ( ! wp_verify_nonce( $nonce, 'e-new-post' ) ) {
			wp_die( 'Opp' );
		}

		$post      = get_default_post_to_edit( $post_type, true );
		$post_data = array(
			'ID'          => $post->ID,
			'post_status' => 'draft',
			'post_title'  => $post->ID
		);
		$id        = wp_update_post( $post_data, true );

		// Remove default title as it is the ID of post
		if ( ! is_wp_error( $id ) ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_title = '' WHERE ID = %d", $id ) );
		}

		$manage = new LP_Addon_Frontend_Editor_Post_Manage( 'lp_course' );
		wp_send_json( array(
			'redirect' => $manage->get_edit_post_link( $post_type, $post->ID )
		) );

		wp_die();
	}


	/**
	 * Change User role here
	 */
	function change_role() {
		if ( is_user_logged_in() ) {
			global $current_user;
			$nonce = LP_Request::get_string( 'security' );

			if ( ! wp_verify_nonce( $nonce, 'switching-role' ) ) {
				wp_die( 'Opp' );
			}

			if ( in_array( 'lp_teacher', $current_user->roles ) ) {
				update_user_meta( $current_user->ID, '_user_switch', 'student' );
				update_user_meta( $current_user->ID, '_user_main_role', 'lp_teacher' );

				$current_user->remove_role( 'lp_teacher' );
				$current_user->add_role( 'subscriber' );
			} else {
				$role = get_user_meta( $current_user->ID, '_user_switch', true );
				if ( ! empty( $role ) ) {
					switch ( $role ) {
						case 'teacher':
							update_user_meta( $current_user->ID, '_user_switch', 'student' );
							$current_user->remove_role( 'lp_teacher' );
							$current_user->add_role( 'subscriber' );
							break;

						case 'student':
							update_user_meta( $current_user->ID, '_user_switch', 'teacher' );
							$current_user->remove_role( 'subscriber' );
							$current_user->add_role( 'lp_teacher' );
							break;

						default:
							break;
					}
				}
			}

			wp_send_json( array(
				'redirect' => home_url()
			) );
		}

		wp_die();
	}

	function search_courses() {
		$search      = filter_input( INPUT_POST, 's' );
		$search_type = filter_input( INPUT_POST, 'type' );
		$data_type = filter_input( INPUT_POST, 'data_type' );

		$profile     = learn_press_get_profile();
		$author      = $profile->get_user_data( 'ID' );


		if(!isset($data_type)){
			$postDataArr = array(
				'posts_per_page'     => -1,
				'post_type'          => 'lp_course',
				's'                  => esc_attr( $search ),
				'author'             => $author,
				'course_ajax_search' => true
			);

			if ( $search_type === "webinar" ) {
				$postDataArr['meta_key']   = '_course_type';
				$postDataArr['meta_value'] = 'webinar';
			}

			$posts = new WP_Query( $postDataArr );

			// $posts = $posts->posts;
			if ( $posts->have_posts() ) {
				while ( $posts->have_posts() ) {
					$posts->the_post();
					// $post_id = get_the_ID();
					// dcd_core_get_template( 'content-profile-webinar.php' );
					// $post   = get_post( $post_id );
					// setup_postdata( $post );
					learn_press_get_template( 'content-profile-course.php' );
				}
				wp_reset_postdata();
			} else {
				learn_press_display_message( __( 'No courses!', 'eduma' ) );
			}
		}else{
			/*
			* DATA Type webinar / course
			*/
			switch($data_type){
				case 'purchased_courses':
					// purchased_courses
					learn_press_get_template('profile/tabs/courses/purchased-ajax.php', array('ajax' => true, 's' => esc_attr( $search )));
				break;
				case 'purchased_webinars':
					learn_press_get_template('profile/tabs/courses/purchased-webinar-ajax.php', array('ajax' => true, 's' => esc_attr( $search )));
				break;
				case 'own-course':
					$profile       = learn_press_get_profile();
					$user_id = $profile->get_user()->get_id();
					$query = query_own_courses_custom( $user_id, array( 
						'limit' => 10, 
						's' => esc_attr( $search ),
						'status' => $filter_status,
						'orderby' => 'post_date',
						'order'   => 'DESC'
					) );
					global $post;
					foreach ( $query['items'] as $item ) {
						if ( get_post_status( $item ) == 'pending' ) {
							
							$viewing_user = $profile->get_user();
							if ( $viewing_user->get_id() != get_current_user_id() ) {
								continue;
							}
						}
						$course = learn_press_get_course( $item );
						$post   = get_post( $item );
						setup_postdata( $post );
						learn_press_get_template( 'content-profile-course.php' );
						
					}
					wp_reset_postdata();
				break;
				case 'own-webinars':

					

					$profile       = learn_press_get_profile();
					$user_id = $profile->get_user()->get_id();
					
					$query         = dcd_webinars()->query_own_courses_webinars( 
						$user_id, 
						array( 
							'status' => $filter_status,
							'limit' => 10,
							's' => esc_attr( $search ),
							'orderby' => 'post_date',
							'order' => 'DESC'
							) 
					);
					global $post;
					foreach ( $query['items'] as $item ) {
						if ( get_post_status( $item ) == 'pending' ) {
							$viewing_user = $profile->get_user();
							if ( $viewing_user->get_id() != get_current_user_id() ) {
								continue;
							}
						}
						$course = learn_press_get_course( $item );
						$post   = get_post( $item );
						setup_postdata( $post );
						dcd_core_get_template( 'content-profile-webinar.php' );
					}
					wp_reset_postdata();
				break;
			}
			

		}
		wp_die();
	}

	function delete_course() {
		$nonce = LP_Request::get_string( 'security' );
		if ( ! wp_verify_nonce( $nonce, 'deleting-post' ) ) {
			wp_die( 'Not Allowed !' );
		}

		$post_id = filter_input( INPUT_POST, 'post_id' );

		//Delete attachemnt
		if ( has_post_thumbnail( $post_id ) ) {
			$attachment_id = get_post_thumbnail_id( $post_id );
			wp_delete_attachment( $attachment_id, true );

			$upload_dir = wp_get_upload_dir();
			$param = array();
			$user = wp_get_current_user();
			// $post_id = get_transient( get_current_user_id() . 'e_post_id' );
			if($post_id){
				$param['path'] = $upload_dir['basedir'] .'/'. $user->data->user_nicename . '/' . $post_id;
			}
			removeDirectory($param['path']);
			delete_transient( get_current_user_id() . 'e_post_id' );
		}

		/*
		* Delete related items
		*/
		$course    = learn_press_get_course( $post_id );
		$items     = $course->get_items();
		foreach($items as $lesson){
			$post_type = get_post_type( $lesson );
			if($post_type == 'lp_quiz'){
				$questions = learn_press_get_quiz_questions($lesson);
				foreach($questions as $singleQueston){
					wp_delete_post( $singleQueston, true );	
				}
			}
			wp_delete_post( $lesson, true );	
		}

		//Delete Webinar Details
		$webinar_id = get_post_meta( $post_id, '_webinar_ID', true );
		if ( $webinar_id ) {
			dcd_zoom_conference()->deleteWebinar( $webinar_id );
		}

		wp_delete_post( $post_id, true );

		wp_die();
	}
}

new DigitalCustDev_Ajax();