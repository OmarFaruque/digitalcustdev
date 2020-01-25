<div class="col-md-12">
	<h6><?php _e( 'Pricing', 'digitalcustdev-core' ); ?></h6>
	<div class="form-group">
		<label for="price"><?php _e( 'Settle the price for your webinar course', 'digitalcustdev-core' ); ?> (<?php echo get_woocommerce_currency_symbol(); ?>)</label>
		<input type="number" class="form-control webinar-price" value="<?php echo ! empty( $price ) ? esc_attr( $price ) : false; ?>" name="price" placeholder="1000">
	</div>
</div>
<div class="col-md-12">
	<h6><?php _e( 'Manage Passing Grade for your students', 'digitalcustdev-core' ); ?></h6>
	<div class="form-group">
		<label for="price"><?php _e( 'Put a passing grade for your course.', 'digitalcustdev-core' ); ?></label>
		<input type="number" class="form-control" value="<?php echo ! empty( $passing_grade ) ? $passing_grade : false; ?>" name="passing_grade" placeholder="20">
	</div>
</div>
<div class="col-md-12">
	<div class="form-group">
		<label for="price"><?php _e( 'Choose type of grading for the course completion.', 'digitalcustdev-core' ); ?></label>
		<div class="form-check">
			<input type="radio" class="form-check-input course-result-radio" <?php checked( 'evaluate_lesson', $course_result ) ?> value="evaluate_lesson" name="course_results"> <label class="form-check-label"><?php _e( 'Grade for Lessons', 'digitalcustdev-core' ); ?></label>
		</div>
		<div class="form-check">
			<input type="radio" class="form-check-input course-result-radio" <?php checked( 'evaluate_final_quiz', $course_result ) ?> value="evaluate_final_quiz" name="course_results"> <label class="form-check-label"><?php _e( 'Grade for Final Test', 'digitalcustdev-core' ); ?></label>
			<input type="text" class="form-control show-input-final-test" value="<?php echo ! empty( $quiz_passing_condition ) ? $quiz_passing_condition : false; ?>" style="display: none;" name="final_quiz_evaluation">
			<?php
			if ( $course_result == 'evaluate_final_quiz' && ! get_post_meta( $post_id, '_lp_final_quiz', true ) ) {
				?>
				<div class="message message-success">
					<?php echo __( '<strong>Note! </strong>No final quiz in course, please add a final quiz', 'digitalcustdev-core' ); ?>
				</div>
				<?php
			}
			?>
		</div>
		<div class="form-check">
			<input type="radio" class="form-check-input course-result-radio" <?php checked( 'evaluate_quizzes', $course_result ) ?> value="evaluate_quizzes" name="course_results"> <label class="form-check-label"><?php _e( 'Grade for Tests Results', 'digitalcustdev-core' ); ?></label>
		</div>
		<div class="form-check">
			<input type="radio" class="form-check-input course-result-radio" value="evaluate_passed_quizzes" <?php checked( 'evaluate_passed_quizzes', $course_result ) ?> name="course_results"> <label class="form-check-label"><?php _e( 'Grade for Passed Tests', 'digitalcustdev-core' ); ?></label>
		</div>
		<div class="form-check">
			<input type="radio" <?php checked( 'evaluate_quiz', $course_result ) ?> class="form-check-input course-result-radio" value="evaluate_quiz" name="course_results"> <label class="form-check-label"><?php _e( 'Grade for Tests', 'digitalcustdev-core' ); ?></label>
		</div>
		<div class="form-check">
			<input type="radio" <?php checked( 'evaluate_final_assignment', $course_result ) ?> class="form-check-input course-result-radio" value="evaluate_final_assignment" name="course_results"> <label class="form-check-label"><?php _e( 'Evaluate via results of the final assignment', 'digitalcustdev-core' ); ?></label>

			<input type="text" class="form-control show-input-final-assginment" style="display: none;" value="<?php echo ! empty( $passing_grade ) ? $passing_grade : false; ?>" name="final_assignment_grade">

		</div>
	</div>
</div>