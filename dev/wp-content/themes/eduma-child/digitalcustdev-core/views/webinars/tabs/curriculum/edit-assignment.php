<?php
/**
 * @author Deepen.
 * @created_on 7/24/19
 */

wp_enqueue_editor();

$assignment = LP_Assignment::get_assignment( $item['id'] );
$duration   = get_post_meta( $assignment->get_id(), '_lp_duration', true );
if ( ! empty( $duration ) ) {
	$duration = explode( ' ', $duration );
}
?>
<div class="white-popup-block mfp-hide" id="open-assignment-edit-popup-<?php echo $item['id'] ?>">
    <div class="form-group">
        <label><?php _e( 'Name of Assignment', 'digitalcustdev-core' ); ?></label>
        <input type="text" class="form-control" value="<?php echo esc_attr( $assignment->get_title() ); ?>" name="assginment_name" placeholder="<?php _e( 'Type lesson name', 'digitalcustdev-core' ); ?>">
    </div>
    <div class="form-group">
        <label><?php _e( 'Description', 'digitalcustdev-core' ); ?></label>
		<?php wp_editor( $assignment->get_content(), 'assginment_description', array( 'media_buttons' => false, 'textarea_rows' => 5, 'tinymce' => false ) ); ?>
    </div>
    <div class="form-group">
        <label><?php _e( 'Attachment', 'digitalcustdev-core' ); ?></label>
    </div>

    <label><?php _e( 'Duration', 'digitalcustdev-core' ); ?></label>
    <div class="row">
        <div class="form-group col-md-6">
            <input type="number" class="form-control" value="<?php echo $duration[0]; ?>" name="assginment_duration_text">
        </div>
        <div class="form-group col-md-6">
            <select class="form-control" name="assignment_duration_type">
                <option value="minute" <?php selected( 'minute', $duration[1] ); ?>>Minute(s)</option>
                <option value="hour" <?php selected( 'hour', $duration[1] ); ?>>Hour(s)</option>
                <option value="day" <?php selected( 'day', $duration[1] ); ?>>Days(s)</option>
                <option value="week" <?php selected( 'week"', $duration[1] ); ?>>Week(s)</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <input type="submit" data-assignmentid="<?php echo $assignment->get_id(); ?>"  class="webinar-create-assignment-form btn btn-primary" value="Update">
    </div>
</div>
