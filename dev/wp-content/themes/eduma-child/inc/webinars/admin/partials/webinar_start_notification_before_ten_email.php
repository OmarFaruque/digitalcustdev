<?php

/**
 * Class LP_Settings_Webinar_Notification_Emails
 */
class LP_Settings_Webinar_Notification_Before_Ten_Emails extends LP_Settings_Emails_Group {

	/**
	 * LP_Settings_Webinar_Notification_Emails constructor.
	 */
	public function __construct() {
		$this->group_id = 'webinar-notification-ten-emails';
		$this->items    = array(
			'webinar-notification-ten-admin',
			'webinar-notification-ten-instructor',
			'webinar-notification-ten-user'
		);

		parent::__construct();
	}

	public function __toString() {
		return __('Webinar Start (10 Min)', 'learnpress');
	}
}

return new LP_Settings_Webinar_Notification_Before_Ten_Emails();