<?php
if ( isset( $_GET['id'] ) ) {
	$webinar = dcd_webinars()->get_webinars( $_GET['id'] );
	if ( ! empty( $webinar ) ) {
		$price             = get_post_meta( $webinar->ID, '_lp_price', true );
		$duration          = get_post_meta( $webinar->ID, '_lp_duration', true );
		$selected_language = get_post_meta( $webinar->ID, 'thim_course_language', true );
		$skill_level       = get_post_meta( $webinar->ID, 'thim_course_skill_level', true );
		$youtube_link      = get_post_meta( $webinar->ID, '_lp_course_youtube_link', true );
		$course_result     = get_post_meta( $webinar->ID, '_lp_course_result', true );
		$passing_grade     = get_post_meta( $webinar->ID, '_lp_passing_condition', true );
		#$quiz_passing_condition = get_post_meta( $webinar->ID, '_lp_course_result_final_quiz_passing_condition', true );
		$webinar_category = get_the_terms( $webinar->ID, 'course_category' );

		$quiz_passing_condition = false;
		if ( $course = learn_press_get_course( $webinar->ID ) ) {
			if ( $final_quiz = $course->get_final_quiz() ) {
				if ( $quiz = learn_press_get_quiz( $final_quiz ) ) {
					$quiz_passing_condition = $quiz->get_passing_grade();
					$quiz_passing_condition = explode( '%', $quiz_passing_condition )[0];
				}
			}
		}

		$duration_value = false;
		$duration_term  = false;
		if ( ! empty( $duration ) ) {
			$duration       = explode( ' ', $duration );
			$duration_value = $duration[0];
			$duration_term  = $duration[1];
		}
	}
}
?>
<h3>Add Webinar</h3>
<div class="webinar-tabs">
    <ul class="nav nav-tabs">
        <li><a href="#step-one">Step 1: Webinar Overview</a></li>
        <li><a href="#step-two">Step 2: Create Curriculum</a></li>
        <li><a href="#step-three">Step 3: Price</a></li>
    </ul>
    <div class="row">
        <div class="webinar-tab-content">
            <form method="POST" action="<?php echo isset( $_GET['id'] ) ? '?save=webinar&id=' . $_GET['id'] : '?save=webinar' ?>" id="webinar_add_post">
				<?php wp_nonce_field( '_save_webinar', 'webinar_create' ); ?>
                <div class="webinar-step-wrapper tab-pane active" id="step-one">
					<?php require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tabs/webinar-overview.php'; ?>
                </div>
                <div class="webinar-step-wrapper tab-pane" id="step-two">
	                <?php require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tabs/curriculum.php'; ?>
                </div>
                <div class="webinar-step-wrapper tab-pane" id="step-three">
	                <?php require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tabs/price.php'; ?>
                    <div class="col-md-12">
                        <div class="form-group pull-right omar1">
                            <a href="javascript:void(0);" class="btn btn-primary webinar-add-prev">Prev</a>
                            <input type="submit" name="submit_webinar" value="<?php echo isset( $_GET['id'] ) && get_post_status( $_GET['id'] ) === "publish" ? 'Save' : 'Submit for Review'; ?>">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>