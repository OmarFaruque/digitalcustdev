<?php
/**
 * @author Deepen.
 * @created_on 7/24/19
 */
?>

<div class="webinar-curriculum-edit-section mt-2 white-popup-block mfp-hide" id="open-section-edit-popup-<?php echo $curriculum['id'] ?>">
    <div class="row">
        <div class="col-md-12">
            <h3>Edit Section</h3>
            <div class="form-group">
                <input type="text" class="form-control" value="<?php echo esc_attr( $curriculum['title'] ); ?>" name="section_name" placeholder="<?php _e( 'Section Name', 'digitalcustdev-core' ); ?>">
            </div>
            <div class="form-group">
				<?php wp_editor( esc_attr( $curriculum['description'] ), 'section_content', array( 'media_buttons' => false, 'textarea_rows' => 5, 'tinymce' => false ) ); ?>
            </div>
            <div class="form-group">
                <input type="submit" data-courseid="<?php echo esc_attr( $curriculum['course_id'] ); ?>" data-sectionid="<?php echo esc_attr( $curriculum['id'] ); ?>" class="webinar-create-section-form btn btn-primary" value="Update section">
            </div>
        </div>
    </div>
</div>
