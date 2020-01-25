<?php
/**
 * @author Deepen.
 * @created_on 6/21/19
 */

class DigitalCustDev_FE_Course {

	public function __construct() {
		add_filter( 'learn_press_course_assessment_metabox', array( $this, 'assessment_metabox' ) );
		add_filter( 'learn_press_course_payment_meta_box_args', array( $this, 'payment_metabox' ) );
	}

	function assessment_metabox( $meta_box ) {
		$meta_box['fields'][0]['options']['evaluate_lesson']         = __( 'Grade for Lessons', 'digitalcustdev-core' );
		$meta_box['fields'][0]['options']['evaluate_final_quiz']     = __( 'Grade for Final Test', 'digitalcustdev-core' );
		$meta_box['fields'][0]['options']['evaluate_quizzes']        = __( 'Grade for Tests Results', 'digitalcustdev-core' );
		$meta_box['fields'][0]['options']['evaluate_passed_quizzes'] = __( 'Grade for Passed Tests', 'digitalcustdev-core' );
		$meta_box['fields'][0]['options']['evaluate_quiz']           = __( 'Grade for Tests', 'digitalcustdev-core' );

		return $meta_box;
	}

	function payment_metabox( $meta_box ) { //Remove Sale Price
		unset( $meta_box['fields'][4] ); //Remove No requirement for enroll

		if ( $meta_box['fields'][0]['id'] === "_lp_price" ) {
			$meta_box['fields'][0]['min'] = 1000;
			$meta_box['fields'][0]['std'] = 2000;
		}

		return $meta_box;
	}
}

new DigitalCustDev_FE_Course();