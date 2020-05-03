<?php

/**
 * Class LP_Settings_Webinar_Update_Emails
 */
class LP_Settings_Webinar_Update_Emails extends LP_Settings_Emails_Group {

	/**
	 * LP_Settings_Webinar_Update_Emails constructor.
	 */
	public function __construct() {
		$this->group_id = 'webinar-update-emails';
		$this->items    = array(
			'webinar-update-admin',
			'webinar-update-instructor',
			'webinar-update-user',
			'webinar-update-guest'
		);

		parent::__construct();
	}

	public function __toString() {
		return __('Webinar Update', 'learnpress');
	}
}

return new LP_Settings_Webinar_Update_Emails();