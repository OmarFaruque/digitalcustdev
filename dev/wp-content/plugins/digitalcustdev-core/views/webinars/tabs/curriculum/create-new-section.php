<?php
/**
 * @author Deepen.
 * @created_on 7/24/19
 */
?>

<div class="col-md-12">
    <div class="message message-success create-new-sections-webinar"><a href="#open-section-create-popup" class="magnific-popup-webinar">Create new section</a></div>
    <div class="webinar-curriculum-create-new-section mt-2 white-popup-block mfp-hide" id="open-section-create-popup">
        <div class="row">
            <div class="col-md-12">
                <h3>New Section</h3>
                <div class="form-group">
                    <input type="text" class="form-control" value="" name="section_name" placeholder="<?php _e( 'Section Name', 'digitalcustdev-core' ); ?>">
                </div>
                <div class="form-group">
	                <?php wp_editor( '', 'section_content', array( 'media_buttons' => false, 'textarea_rows' => 5, 'tinymce' => true ) ); ?>
                </div>
                <div class="form-group">
                    <input type="submit" data-courseid="<?php echo $course_id; ?>" class="webinar-create-section-form btn btn-primary" value="Create new section">
                </div>
            </div>
        </div>
    </div>
</div>
