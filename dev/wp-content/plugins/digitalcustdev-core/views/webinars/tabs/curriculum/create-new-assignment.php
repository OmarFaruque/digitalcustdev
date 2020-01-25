<?php
/**
 * @author Deepen.
 * @created_on 7/24/19
 */
?>

<div class="white-popup-block mfp-hide" id="open-new-create-assignment-popup-<?php echo $curriculum['id'] ?>">
    <div class="form-group">
        <label><?php _e( 'Name of Assignment', 'digitalcustdev-core' ); ?></label>
        <input type="text" class="form-control" value="" name="assginment_name" placeholder="<?php _e( 'Type lesson name', 'digitalcustdev-core' ); ?>">
    </div>
     <div class="form-group">
        <label><?php _e( 'Attachment', 'digitalcustdev-core' ); ?></label>
    </div>
    
    <label><?php _e( 'Duration', 'digitalcustdev-core' ); ?></label>
    <div class="row">
        <div class="form-group col-md-6">
            <input type="number" class="form-control" value="" name="assginment_duration_text">
        </div>
        <div class="form-group col-md-6">
            <select class="form-control" name="assignment_duration_type">
                <option value="minute">Minute(s)</option>
                <option value="hour">Hour(s)</option>
                <option value="day">Days(s)</option>
                <option value="week">Week(s)</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label><?php _e( 'Description', 'digitalcustdev-core' ); ?></label>
		<?php wp_editor( '', 'assginment_description', array( 'media_buttons' => false, 'textarea_rows' => 5, 'tinymce' => false ) ); ?>
    </div>
   

    <div class="form-group">
        <input type="submit" data-sectionid="<?php echo $curriculum['id']; ?>" data-courseid="<?php echo $course_id; ?>" class="webinar-create-assignment-form btn btn-primary" value="Create">
    </div>
</div>
