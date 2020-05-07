<?php

/**
 * Class LP_Settings_Webinar_Notification_Emails
 */
class LP_Settings_Webinar_Notification_Emails extends LP_Settings_Emails_Group {

	/**
	 * LP_Settings_Webinar_Notification_Emails constructor.
	 */
	public function __construct() {
		$this->group_id = 'webinar-notification-emails';
		$this->items    = array(
			'webinar-notification-admin',
			'webinar-notification-instructor',
			'webinar-notification-user'
		);

		parent::__construct();
	}

	public function __toString() {
		return __('Webinar Start Notification', 'learnpress');
	}
}

return new LP_Settings_Webinar_Notification_Emails();