<?php
/**
 * Template for displaying button for editing course in single course page
 *
 * @author  ThimPress
 * @package Frontend_Editor/Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or die;

$post_manage = frontend_editor()->post_manage;
?>

<div class="e-edit-course-button">
    <a class="e-button" href="<?php echo $post_manage->get_edit_post_link(); ?>"><?php esc_html_e( 'Edit', 'learnpress-frontend-editor' ); ?></a>
</div>
