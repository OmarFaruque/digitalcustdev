<?php
/**
 * @author Deepen.
 * @created_on 7/24/19
 */
?>
<div class="webinar-create-new-lesson white-popup-block mfp-hide" id="open-new-create-lesson-popup-<?php echo $curriculum['id'] ?>">
    <div class="form-group">
        <label><?php _e( 'Create lesson name, content and schedule webinar session', 'digitalcustdev-core' ); ?></label>
        <input type="text" class="form-control" value="" name="lesson_name" placeholder="<?php _e( 'Type lesson name', 'digitalcustdev-core' ); ?>">
    </div>
    <div class="form-group">
		<?php wp_editor( '', 'lesson_description', array( 'media_buttons' => false, 'textarea_rows' => 5, 'teeny' => true, 'tinymce' => false ) ); ?>
    </div>
    <div class="form-group">
        <label><?php _e( 'When', 'digitalcustdev-core' ); ?></label>
        <input type="text" class="om form-control webinar_start_time" value="<?php echo date('Y/m/d H:i'); ?>" name="lesson_date"">
    </div>
    <div class="form-group">
        <label><?php _e( 'Duration', 'digitalcustdev-core' ); ?></label>
        <select class="form-control" name="lesson_duration">
            <option value="45 minute">45 minutes</option>
            <option value="1 hour">1 hour</option>
            <option value="75 minute">1:15 minutes</option>
            <option value="90 minute">1:30 minutes</option>
            <option value="2 hour">2 hours</option>
        </select>
    </div>
    <div class="form-group">
        <label><?php _e( 'Timezone', 'digitalcustdev-core' ); ?></label>
		<?php $tzlists = zvc_get_timezone_options(); ?>
        <select class="form-control webinar-select2" name="lesson_timezone">
			<?php foreach ( $tzlists as $k => $tzlist ) { ?>
                <option value="<?php echo $k; ?>"><?php echo $tzlist; ?></option>
			<?php } ?>
        </select>
    </div>
    <div class="form-group">
        <input type="submit" data-sectionid="<?php echo $curriculum['id']; ?>" data-courseid="<?php echo $course_id; ?>" class="webinar-create-lesson-form btn btn-primary" value="Create">
    </div>
</div>
