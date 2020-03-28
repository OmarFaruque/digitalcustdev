<?php
/**
 * Template for displaying form for editing course.
 *
 * @author  ThimPress
 * @package LearnPress/Frontend-Editor/Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or die;

global $frontend_editor, $wp_filter;
$post_status = $frontend_editor->post_manage->get_post()->post_status;
$tabs        = e_get_course_editor_tabs();
$profile     = learn_press_get_profile();
$course_type = get_post_meta( $frontend_editor->post_manage->get_post()->ID, '_course_type', true );
set_transient( get_current_user_id() . 'e_post_id', $frontend_editor->post_manage->get_post()->ID, 86400 );
?>
    <div id="frontend-course-editor">

        <e-modal-select-items :item="item" v-if="xyz.show" :xyz="xyz" :modal-data="modalData"></e-modal-select-items>

        <div v-showx="!showSettings" class="e-tabs e-course-tabs">
			<?php foreach ( $tabs as $tab ) { ?>
                <div class="e-tab" data-name="<?php echo $tab['id']; ?>">
                    <h3 class="e-tab-label">
                        <span><?php echo $tab['name']; ?></span>
                    </h3>
                    <div class="e-tab-content">
						<?php
						if ( is_callable( $tab['callback'] ) ) {
							//e_course_editor_tab_curriculum();
							call_user_func( $tab['callback'] );

							?>
                            <div class="e-form-field col-md-12 mt-2">
								<?php if ( $tab['callback'] !== "e_course_editor_tab_general" ) { ?>
                                    <div class="form-group pull-left">
                                        <a href="javascript:void(0);" data-id="<?php echo $tab['id']; ?>" class="btn btn-primary dcd-course-prev-btn">Prev</a>
                                    </div>
								<?php } ?>
                                <div class="form-group pull-right with-loader position-relative">


                                <div class="foooter_loading">
                                    <div id="e-update-activity" v-if="activity" :class="[activityType||'updating']">
                                        <span class="e-update-activity__icon"></span>
                                        <p v-if="activity!==true" class="e-update-activity__message">{{activity}}</p>
                                    </div>
                                </div>

									<?php
									if ( $tab['callback'] === "dcd_e_course_editor_tab_settings" ) { ?>
                                        <?php if(visiable_review_submit($course_type)): ?>
                                        <a href="javascript:void(0);" data-redirect="<?php echo isset( $course_type ) && $course_type === "webinar" ? $profile->get_tab_link( 'webinars', true ) : $profile->get_tab_link(); ?>" data-id="<?php echo $tab['id']; ?>" class="btn btn-primary dcd-course-next-save submit-for-review"><?php echo $post_status === "draft" ? 'Submit Review' : 'Publish'; ?></a>
                                        <?php else: ?>
                                        <span id="desable_submit_review" class="text-right d-block error"><?php _e('less than 24 hours have passed since the previous course submission for approval - come back later', 'webinar'); ?></span>
                                        <?php endif; ?>
									<?php } else if ( $tab['callback'] === "e_course_editor_tab_curriculum" ) { ?>
                                        <a href="javascript:void(0);" data-id="<?php echo $tab['id']; ?>" class="btn btn-primary dcd-course-next-save dcd-course-next-curriculum dcd-course-next-btn">Next</a>
									<?php } else { ?>
                                        <a href="javascript:void(0);" data-id="<?php echo $tab['id']; ?>" class="btn btn-primary dcd-course-next-save dcd-course-next-btn">Next</a>
									<?php } ?>
                                </div>
                            </div>
							<?php
						}
						?>
                    </div>
                </div>
			<?php } ?>


        </div>
    </div>


<?php $frontend_editor->get_template( 'edit/editors/course/store-data' );
