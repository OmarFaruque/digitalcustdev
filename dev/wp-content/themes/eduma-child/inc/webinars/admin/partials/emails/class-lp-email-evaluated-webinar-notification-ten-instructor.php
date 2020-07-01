<?php
/**
 * Class LP_Email_Webinar_Notification_Ten_Instructor
 *
 * @author   ThimPress
 * @package  LearnPress/Assignments/Classes/Email
 * @version  3.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Email_Webinar_Notification_Ten_Instructor' ) ) {
	/**
	 * Class LP_Email_Webinar_Notification_Ten_Instructor.
	 */
	class LP_Email_Webinar_Notification_Ten_Instructor extends LP_Email {

		/**
		 * @var
		 */
		public $object;

		/**
		 * @var int
		 */
		public $course_id = 0;

		/**
		 * User ID
		 *
		 * @var int
		 */
		public $user_id = 0;

		/**
		 * @var int
		 */
		public $assignment_id = 0;

		/**
		 * LP_Email_Assignment_Evaluated_Admin constructor.
		 */
		public function __construct() {

			$this->id          = 'webinar-notification-ten-instructor';
			$this->title       = __( 'Instructor', 'webinar' );
			$this->description = __( 'Send this email to admin before start webinar in zoom.', 'webinar' );

			$this->template_base  = get_stylesheet_directory() . '/inc/webinars/admin/partials/email-template/';
			$this->template_html  = 'emails/evaluated-notification-instractor.php';
			$this->template_plain = 'emails/plain/evaluated-notification-instractor.php';

			$this->default_subject = __( '[{{site_title}}] Your webinar on zoom will start immmidiately ({{lesson_name}})', 'webinar' );
			$this->default_heading = __( 'Update Webinar Lession on Zoom', 'webinar' );
			$this->recipient       = LP()->settings->get( 'emails_' . $this->id . '.recipients', $this->_get_admin_email() );

			parent::__construct();

			$this->support_variables = array_merge(
				$this->general_variables,
				array(
					'{{lesson_id}}',
					'{{lesson_name}}',
					'{{lesson_url}}',
					'{{course_id}}',
					'{{course_name}}',
					'{{course_url}}',
					'{{user_id}}',
					'{{user_name}}',
					'{{user_email}}', 
					'{{webinars_curriculum_html}}', 
					'{{webinar_lesson_html}}'
				)
			);

			add_action( 'learn-press/zoom-notification-lession-ten-min-instructor', array( $this, 'trigger' ), 99, 2 );
		}

		/**
		 * @param $assignment_id
		 * @param $user_id
		 *
		 * @return bool
		 */
		public function trigger( $post_id, $user_id ) {
			if ( ! $this->enable ) {
				return false;
			}
			if ( ! ( $user = learn_press_get_user( $user_id ) ) ) {
				return false;
			}
			LP_Emails::instance()->set_current( $this->id );
			$format     = $this->email_format == 'plain_text' ? 'plain' : 'html';
			$post = get_post( $post_id );

			$courses = learn_press_get_item_courses( $post_id );
			$course  = get_post( $courses[0]->ID );
			$co_teachers = get_post_meta( $courses[0]->ID, '_lp_co_teacher', false );
			if(!$co_teachers){
				return false;
			}

			
			

			$zoom_api_meeting_link = get_post_meta( $post_id, '_webinar_details', true );

			$this->object    = $this->get_common_template_data(
				$format,
				array(
					'lesson_id'    		=> $post_id,
					'lesson_name'  		=> $post->post_title,
					'lesson_url'   		=> learn_press_get_course_item_permalink($course->ID, $assignment_id),
					'course_id'        	=> $course->ID,
					'course_name'      	=> $course->post_title,
					'course_url'       	=> get_the_permalink( $course->ID ),
					'user_id'          	=> $user_id,
					'user_name'        	=> learn_press_get_profile_display_name( $user ),
					'user_email'       	=> $user->get_email(),
					'user_profile_url' 	=> learn_press_user_profile_link( $user_id ),
					'zoom_url' 		   	=> $zoom_api_meeting_link->join_url,
					'webinars_curriculum_html' => webinar_curriculum_html($course->ID),
					'webinar_lesson_html' => webinar_lesson_html($post_id)
				)
			);
			$this->variables = $this->data_to_variables( $this->object );

			// foreach($co_teachers as $sins):
			// 	$author_obj = get_user_by('id', $sins);

				if(get_post_meta($post_id, '_lp_alternative_host', true)){
					$users = cstm_video_conferencing_zoom_api_get_user_transients();
					$users = $users->users;
					$userKey = array_search(get_post_meta($post_id, '_lp_alternative_host', true), array_column($users, 'id'));    
					$hosterMail = $users[$userKey]->email;
					$alternative_hoster = $hosterMail;
				}else{
					$course_author = get_userdata($courses[0]->post_author);
					$alternative_hoster = $course_author->data->user_email;
				}

				$userbymail = get_user_by('email', $alternative_hoster);

				$return = false;
				
				if(!get_user_meta($userbymail->ID, 'mail_ten_min_status' . $post_id, true)){
					$this->recipient = $alternative_hoster;
					$return = $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
					if($return){
						update_user_meta( $userbymail->ID, 'mail_ten_min_status' . $post_id, 1 );
					}
				}
			// endforeach;

			return $return;
		}
	}
}

return new LP_Email_Webinar_Notification_Ten_Instructor();