<?php

/**
 * Class LP_Settings_Enrolled_Webinar_Emails
 */
class LP_Settings_Enrolled_Webinar_Emails extends LP_Settings_Emails_Group {

	/**
	 * LP_Settings_Enrolled_Webinar_Emails constructor.
	 */
	public function __construct() {

		$this->group_id = 'enrolled-webinar-emails';

		$this->items = apply_filters( 'learn-press/emails/enrolled-webinar', array(
				'enrolled-webinar-admin',
				'enrolled-webinar-instructor',
				'enrolled-webinar-user'
			)
		);

		parent::__construct();
	}

	public function __toString() {
		return __( 'Enrolled webinar', 'learnpress' );
	}
}

return new LP_Settings_Enrolled_Webinar_Emails();