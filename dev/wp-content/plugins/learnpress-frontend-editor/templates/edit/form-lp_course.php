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
$tabs = e_get_course_editor_tabs();
?>
    <div id="frontend-course-editor">

        <e-modal-select-items :item="item" v-if="xyz.show" :xyz="xyz" :modal-data="modalData"></e-modal-select-items>

        <div v-showx="!showSettings" class="e-tabs e-course-tabs">
			<?php foreach ( $tabs as $tab ) { ?>
                <div class="e-tab" data-name="<?php echo $tab['id']; ?>">
                    <h3 @click="selectTab" class="e-tab-label">
                        <span><?php echo $tab['name']; ?></span>
                    </h3>
                    <div class="e-tab-content">
						<?php
						if ( is_callable( $tab['callback'] ) ) {
							//e_course_editor_tab_curriculum();
							call_user_func( $tab['callback'] );
						}
						?>
                    </div>
                </div>
			<?php } ?>

        </div>
    </div>


<?php $frontend_editor->get_template( 'edit/editors/course/store-data' );
