<?php
/**
 * Template for displaying form for editing a post type.
 *
 * @author  ThimPress
 * @package LearnPress/Frontend-Editor/Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or die;

global $frontend_editor;
$post_manage = $frontend_editor->post_manage;
$post        = $post_manage->get_post();
?>
<ul class="e-form-field-table">

    <li class="e-form-field">
        <label><?php esc_html_e( 'Title', 'learnpress-frontend-editor' ); ?></label>
        <div class="e-form-field-input">
            <input name="post_title" type="text" value="<?php echo $post->post_title; ?>"
                   placeholder="<?php esc_attr_e( 'Course name', 'learnpress-frontend-editor' ); ?>">
        </div>
    </li>

    <li class="e-form-field">
        <label><?php esc_html_e( 'Description', 'learnpress-frontend-editor' ); ?></label>
        <div class="e-form-field-input">
			<?php wp_editor( $post->post_content, 'post_content', array( 'rows' => 50 ) ); ?>
        </div>
    </li>

    <li class="e-form-field">
        <label><?php esc_html_e( 'Status', 'learnpress-frontend-editor' ); ?></label>
        <div class="e-form-field-input">
            <select name="post_status">
                <option value="draft"<?php selected( ! in_array( $post->post_status, array(
					'pending',
					'publish'
				) ) ); ?>><?php _e( 'Draft', 'learnpress-frontend-editor' ); ?></option>
                <option value="pending"<?php selected( $post->post_status === 'pending' ); ?>><?php _e( 'Pending Review', 'learnpress-frontend-editor' ); ?></option>
                <option v-if="currentUserCanPublishCourse()"
                        value="publish"<?php selected( $post->post_status === 'publish' ); ?>><?php _e( 'Publish', 'learnpress-frontend-editor' ); ?></option>
            </select>
        </div>
    </li>

	<?php if ( post_type_supports( $post_manage->get_post_type(), 'thumbnail' ) ) { ?>
        <li class="e-form-field">
            <label><?php esc_html_e( 'Featured Image', 'learnpress-frontend-editor' ); ?></label>
            <div class="e-form-field-input">

				<?php $frontend_editor->get_template( 'edit/form-image' ); ?>

            </div>
        </li>
	<?php } ?>

	<?php if ( post_type_supports( $post_manage->get_post_type(), 'thumbnail' ) ) { ?>
        <li class="e-form-field">
            <label><?php esc_html_e( 'Category', 'learnpress-frontend-editor' ); ?></label>
            <div class="e-form-field-input">

				<?php $frontend_editor->get_template( 'edit/form-category' ); ?>

            </div>
        </li>
	<?php } ?>
</ul>