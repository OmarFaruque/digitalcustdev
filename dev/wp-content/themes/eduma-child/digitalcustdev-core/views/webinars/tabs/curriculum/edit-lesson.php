<?php
/**
 * @author Deepen.
 * @created_on 7/24/19
 */

$lesson     = LP_Lesson::get_lesson( $item['id'] );
$duration   = get_post_meta( $lesson->get_id(), '_lp_duration', true );
$timezone   = get_post_meta( $lesson->get_id(), '_lp_timezone', true );
$start_time = get_post_meta( $lesson->get_id(), '_lp_start_time', true );
?>
<div class="webinar-create-new-lesson white-popup-block mfp-hide" id="open-lesson-edit-popup-<?php echo $item['id']; ?>">
    <div class="form-group">
        <label><?php _e( 'Create lesson name, content and schedule webinar session', 'digitalcustdev-core' ); ?></label>
        <input type="text" class="form-control" value="<?php echo $lesson->get_title(); ?>" name="lesson_name" placeholder="<?php _e( 'Type lesson name', 'digitalcustdev-core' ); ?>">
    </div>
    <div class="form-group">
		<?php wp_editor( $lesson->get_content(), 'lesson_description', array( 'media_buttons' => false, 'textarea_rows' => 5, 'teeny' => true, 'tinymce' => false  ) ); ?>
    </div>
    <div class="form-group">
        <label><?php _e( 'When', 'digitalcustdev-core' ); ?></label>
        <input type="text" class="form-control webinar_start_time" value="<?php echo ! empty( $start_time ) ? $start_time : date('Y/m/d H:i'); ?>" name="lesson_date"">
    </div>
    <div class="form-group">
        <label><?php _e( 'Duration', 'digitalcustdev-core' ); ?></label>
        <select class="form-control" name="lesson_duration">
            <option value="45 minute" <?php echo ! empty( $duration ) && $duration === "45 minute" ? 'selected' : false; ?>>45 minutes</option>
            <option value="1 hour" <?php echo ! empty( $duration ) && $duration === "1 hour" ? 'selected' : false; ?>>1 hour</option>
            <option value="75 minute" <?php echo ! empty( $duration ) && $duration === "75 minute" ? 'selected' : false; ?>>1:15 minutes</option>
            <option value="90 minute" <?php echo ! empty( $duration ) && $duration === "90 minute" ? 'selected' : false; ?>>1:30 minutes</option>
            <option value="2 hour" <?php echo ! empty( $duration ) && $duration === "2 hour" ? 'selected' : false; ?>>2 hours</option>
        </select>
    </div>
    <div class="form-group">
        <label><?php _e( 'Timezone', 'digitalcustdev-core' ); ?></label>
		<?php $tzlists = zvc_get_timezone_options(); ?>
        <select class="form-control webinar-select2" name="lesson_timezone">
			<?php foreach ( $tzlists as $k => $tzlist ) { ?>
                <option value="<?php echo $k; ?>" <?php selected( $k, $timezone ); ?>><?php echo $tzlist; ?></option>
			<?php } ?>
        </select>
    </div>
    <div class="form-group">
        <input type="submit" data-courseid="<?php echo $course_id; ?>" data-lessonid="<?php echo $lesson->get_id(); ?>" class="webinar-create-lesson-form btn btn-primary" value="Update">
    </div>
</div>
