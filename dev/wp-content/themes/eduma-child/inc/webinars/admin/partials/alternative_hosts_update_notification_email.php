<?php

/**
 * Class LP_Settings_Webinar_Notification_Emails
 */
class LP_Alternative_Host_Update_Notification_Emails extends LP_Settings_Emails_Group {

	/**
	 * LP_Settings_Webinar_Notification_Emails constructor.
	 */
	public function __construct() {
		$this->group_id = 'updatehost-notification-emails';
		$this->items    = array(
			'updatehost-notification-admin',
			'updatehost-notification-instructor'
		);

		parent::__construct();
	}

	public function __toString() {
		return __('Update Alternative Host', 'learnpress');
	}
}

return new LP_Alternative_Host_Update_Notification_Emails();