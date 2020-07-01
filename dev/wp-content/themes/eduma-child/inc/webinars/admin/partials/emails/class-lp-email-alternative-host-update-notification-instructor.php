<?php
/**
 * Class LP_Email_Update_Alternative_Host_Notification_Instructor
 *
 * @author   ThimPress
 * @package  LearnPress/Assignments/Classes/Email
 * @version  3.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Email_Update_Alternative_Host_Notification_Instructor' ) ) {
	/**
	 * Class LP_Email_Update_Alternative_Host_Notification_Instructor.
	 */
	class LP_Email_Update_Alternative_Host_Notification_Instructor extends LP_Email {

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

			$this->id          = 'updatehost-notification-instructor';
			$this->title       = __( 'Instructor', 'webinar' );
			$this->description = __( 'Send this email to instructor when they have update webinar form from zoom menu.', 'webinar' );

			$this->template_base  = get_stylesheet_directory() . '/inc/webinars/admin/partials/email-template/';
			$this->template_html  = 'emails/evaluated-notification-admin.php';
			$this->template_plain = 'emails/plain/evaluated-notification-admin.php';

			$this->default_subject = __( '[{{site_title}}] You just got update for webinar lession ({{lesson_name}})', 'webinar' );
			$this->default_heading = __( 'Update Webinar Lesson on Zoom', 'webinar' );
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
					'{{user_email}}'
				)
			);

			add_action( 'zoom/zoom-notification-update-alternative-host-instructor', array( $this, 'trigger' ), 99, 2 );
		}

		/**
		 * @param $lesson_id
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
			$lesson = get_post( $post_id );

			$courses = learn_press_get_item_courses( $post_id );
			$course  = get_post( $courses[0]->ID );

			$this->object    = $this->get_common_template_data(
				$format,
				array(
					'lesson_id'    		=> $lesson_id,
					'lesson_name'  		=> $lesson->post_title,
					'lesson_url'   		=> learn_press_get_course_item_permalink($course->ID, $assignment_id),
					'course_id'        	=> $course->ID,
					'course_name'      	=> $course->post_title,
					'course_url'       	=> get_the_permalink( $course->ID ),
					'user_id'          	=> $user_id,
					'user_name'        	=> learn_press_get_profile_display_name( $user ),
					'user_email'       	=> $user->get_email(),
					'user_profile_url' 	=> learn_press_user_profile_link( $user_id ),
					'join_link'			=> ''
				)
			);
			$this->variables = $this->data_to_variables( $this->object );

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

            $this->recipient = $alternative_hoster;
			$return = $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			return $return;
		}
	}
}

return new LP_Email_Update_Alternative_Host_Notification_Instructor();