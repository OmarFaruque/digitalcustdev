<?php
/**
 * @var LP_Addon_Frontend_Editor $frontend_editor
 */

global $frontend_editor;

if ( ! $frontend_editor->post_manage ) {
	return '';
}

$profile        = learn_press_user_profile_link( get_current_user_id() );
$post_manage    = $frontend_editor->post_manage;
$post_type_list = $post_manage->get_post_type_list();

?>

    <div id="frontend-editor">
        <div id="e-page" class="top-0">
            <div id="e-main" class="container site-content">
				<?php if ( has_action( 'learn-press/frontend-editor/dashboard' ) ) {
					do_action( 'learn-press/frontend-editor/dashboard' );
				} else {
					$frontend_editor->get_template( 'list/list-table-lp_course' );
				} ?>
            </div>
        </div>

        <!-- <div class="omartestomar 2">
            <div id="e-update-activity" v-if="activity" :class="[activityType||'updating']">
                <span class="e-update-activity__icon"></span>
                <p v-if="activity!==true" class="e-update-activity__message">{{activity}}</p>
            </div>
        </div> -->
    </div>
<?php

$frontend_editor->get_template( 'edit/editors/course/curriculum' );
$frontend_editor->get_template( 'edit/editors/course/section' );
$frontend_editor->get_template( 'edit/editors/course/item' );
$frontend_editor->get_template( 'edit/editors/item-settings' );
$frontend_editor->get_template( 'edit/editors/form-fields' );
$frontend_editor->get_template( 'edit/editors/modal-items' );