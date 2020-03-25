<?php
/**
 * Template for displaying form to edit post
 *
 * @author  ThimPress
 * @package Frontend_Editor/Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or die;

global $frontend_editor;

$post_manage = $frontend_editor->post_manage;
$post        = $post_manage->get_post();

/*
* Set Default section to front-end editor
*/
$course_id = $post->ID;
$curd = new LP_Course_CURD();
$course_sections = $curd->get_course_sections( $course_id, 'ids' );
if(count($course_sections) <= 0){
    /*
    * function write on override-functions.php
    */
    set_default_section_to_editor($course_id);
}

$course_status = get_post_status($course_id);





list( $permalink, $post_name ) = get_sample_permalink( $post->ID );

if ( LP_Request::get( 'updated' ) ) {
	learn_press_display_message( __( 'Course saved', 'learnpress-frontend-editor' ) );
}

?>

<div class="e-edit-slug-box mb-0">
    <?php if($course_status == 'pending'): ?>
        <div id="pendingpopup">
            <h2><span><?php _e('Congratulation! You\'ve submitted a course!', 'webinar'); ?></span></h2>
        </div>
    <?php endif; ?>
    <span id="e-sample-permalink-editable" class="e-hidden"><?php echo trailingslashit( dirname( $permalink ) ); ?>
        <input id="e-edit-slug-input" value="<?php echo basename( $permalink ); ?>">/</span>
    <button class="e-button e-hidden"
            id="e-button-save-slug"><?php esc_html_e( 'Save', 'learnpress-frontend-editor' ); ?></button>

    <a class="e-hidden" id="e-button-cancel-slug"><?php esc_html_e( 'Cancel', 'learnpress-frontend-editor' ); ?></a>
    <input type="hidden" id="samplepermalinknonce" name="samplepermalinknonce"
           value="<?php echo esc_attr( wp_create_nonce( 'samplepermalink' ) ); ?>">
</div>
<form id="e-edit-post" class="om" method="post">

	<?php
	$template = $frontend_editor->locate_template( 'edit/form-' . $post_manage->get_post_type() . '.php' );
	if ( file_exists( $template ) ) {
		include $template;
	} else {
		$frontend_editor->get_template( 'edit/form' );
	}

	?>

    <input type="hidden" name="post_ID" value="<?php echo $post->ID; ?>">
    <input type="hidden" id="post_name" name="post_name" value="<?php echo esc_attr( $post_name ); ?>">
    <input type="hidden" name="_e_post_nonce" value="<?php echo wp_create_nonce( 'e_save_post' ); ?>">

</form>
