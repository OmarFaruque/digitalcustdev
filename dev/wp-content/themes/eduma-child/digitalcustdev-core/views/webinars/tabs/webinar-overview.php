<div class="col-md-12">
	<div class="form-group">
		<label for="topic"><?php _e( 'Title of your webinar', 'digitalcustdev-core' ); ?></label>
		<input type="text" class="form-control" name="topic" value="<?php echo ! empty( $webinar->post_title ) ? esc_attr( $webinar->post_title ) : false; ?>" required="">
	</div>
</div>
<div class="col-md-4">
	<div class="form-group">
		<label for="topic"><?php _e( 'Choose webinar category', 'digitalcustdev-core' ); ?></label>
		<?php
		$terms = get_terms( array(
			'taxonomy'   => 'course_category',
			'hide_empty' => false,
			'parent'     => 0
		) );
		?>
		<select name="category" class="form-control">
			<?php
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					?>
					<option value="<?php echo $term->term_id; ?>" <?php selected( $webinar_category[0]->term_id, $term->term_id ); ?>><?php echo $term->name; ?></option>
					<?php
				}
			}
			?>
		</select>
	</div>
</div>
<div class="col-md-4">
	<div class="form-group">
		<label for="topic"><?php _e( 'Select Language', 'digitalcustdev-core' ); ?></label>
		<?php $languages = json_decode( file_get_contents( DIGITALCUSTDEV_PLUGIN_PATH . 'assets/languages.json' ) ); ?>
		<select class="form-control webinar-select2" name="language">
			<?php foreach ( $languages as $language ) { ?>
				<option <?php selected( $language->name, $selected_language ); ?> value="<?php echo $language->name; ?>"><?php echo $language->name; ?></option>
			<?php } ?>
		</select>
	</div>
</div>
<div class="col-md-4">
	<div class="form-group">
		<label for="topic"><?php _e( 'Select Skill Level', 'digitalcustdev-core' ); ?></label>
		<select class="form-control" name="skill_level">
			<option value="Master" <?php selected( $skill_level, 'Master' ); ?>><?php _e( 'Master', 'digitalcustdev-core' ); ?></option>
			<option value="Intermediate" <?php selected( $skill_level, 'Intermediate' ); ?>><?php _e( 'Intermediate', 'digitalcustdev-core' ); ?></option>
			<option value="Advanced" <?php selected( $skill_level, 'Advanced' ); ?>><?php _e( 'Advanced', 'digitalcustdev-core' ); ?></option>
			<option value="Beginner" <?php selected( $skill_level, 'Beginner' ); ?>><?php _e( 'Beginner', 'digitalcustdev-core' ); ?></option>
		</select>
	</div>
</div>
<div class="col-md-12">
	<div class="form-group">
		<?php
		$tags = get_terms( array(
			'taxonomy'   => 'course_tag',
			'hide_empty' => false,
		) );

		if ( ! empty( $tags ) ) {
			$course_selected_tags = get_the_terms( $webinar->ID, 'course_tag' );
			if ( ! empty( $course_selected_tags ) ) {
				$selected_tags = array();
				foreach ( $course_selected_tags as $course_selected_tag ) {
					$selected_tags[] = $course_selected_tag->term_id;
				}
			}
			?>
			<label for="topic"><?php _e( 'Choose tags from the list - it\'s easier for users to find your course.', 'digitalcustdev-core' ); ?></label>
			<select class="form-control webinar-select2" name="webinar_tags[]" multiple="multiple">
				<?php foreach ( $tags as $tag ) { ?>
					<option value="<?php echo $tag->name; ?>" <?php echo ! empty( $selected_tags ) && in_array( $tag->term_id, $selected_tags ) ? 'selected' : false; ?>><?php echo $tag->name; ?></option>
				<?php } ?>
			</select>
		<?php } ?>
	</div>
</div>
<div class="col-md-12">
	<div class="form-group">
		<label for="start_time"><?php _e( 'Paste link from Youtube', 'digitalcustdev-core' ); ?></label>
		<input type="text" class="form-control" name="youtube_link" value="<?php echo ! empty( $youtube_link ) ? esc_attr( $youtube_link ) : false; ?>">
		<div class="message message-success mt-1 mb-1" role="alert">
			<p class="mb-1"><?php _e( 'Record max. 2 minutes video-intro for your webinar course.', 'digitalcustdev-core' ); ?></p>
			<p class="mb-0"><?php _e( 'It will be your business card presenting your course. Also, we\'ll use this video to advertise your course across community.', 'digitalcustdev-core' ); ?></p>
		</div>
	</div>
</div>
<div class="col-md-12">
	<div class="form-group">
		<label for="start_time"><?php _e( 'Webinar Course Length (Duration of the course.)', 'digitalcustdev-core' ); ?></label>
		<div class="row">
			<div class="col-sm-4">
				<input type="number" value="<?php echo ! empty( $duration_value ) ? $duration_value : false; ?>" min="1" max="32" class="form-control" name="webinar_duration_number">
			</div>
			<div class="col-sm-4">
				<select class="form-control" name="webinar_duration_term">
					<option <?php selected( 'minute', $duration_term ); ?> value="minute">Minute(s)</option>
					<option <?php selected( 'hour', $duration_term ); ?> value="hour">Hour(s)</option>
					<option <?php selected( 'day', $duration_term ); ?> value="day">Day(s)</option>
					<option <?php selected( 'week', $duration_term ); ?> value="week">Week(s)</option>
				</select>
			</div>
		</div>
	</div>
</div>
<div class="col-md-12">
	<div class="form-group">
		<label for="agenda"><?php _e( 'Agenda', 'digitalcustdev-core' ); ?></label>
		<?php wp_editor( ! empty( $webinar->post_content ) ? $webinar->post_content : false, 'agenda', array( 'media_buttons' => false, 'textarea_rows' => 6, 'teeny' => true ) ); ?>
	</div>
</div>
<div class="col-md-12">
	<div class="form-group pull-right omar2">
		<a href="javascript:void(0);" class="btn btn-primary webinar-add-next">Next</a>
	</div>
</div>