<?php
/**
 * Template for section loop
 *
 * @version 3.0.0
 */
defined( 'ABSPATH' ) or die;
global $frontend_editor;
?>
<script type="text/x-template" id="tmpl-e-course-section-new">
<li :class="sectionClasses()">
    <h4 class="e-section-head">
        <div class="sort">
			<?php $frontend_editor->get_template( 'global/drag-icon' ); ?>
        </div>
        <div class="e-section-title">
            <input type="text" v-model="section.title" data-context="section-title" class="section-title"
                   @keypress="_onKeyPress($event)"
                   ref="sectionTitle"
                   placeholder="<?php _e( 'Section name', 'learnpress-frontend-editor' ); ?>">
        </div>
    </h4>
</li>
</script>
<script type="text/x-template" id="tmpl-e-course-section">
    <li :class="sectionClasses()" tabindex="0" @focus="onFocus" @click="onFocus" @blur="onBlur"
        :data-id="section.id || ''">
        <h4 class="e-section-head">
            <div class="sort">
				<?php $frontend_editor->get_template( 'global/drag-icon' ); ?>
            </div>
            <div class="e-section-title">
                <input type="text" v-model="section.title" data-context="section-title" class="section-title"
                       placeholder="<?php _e( 'Section name', 'learnpress-frontend-editor' ); ?>">
            </div>
            <div class="e-sortable-actions e-section-actions">
                <span class="edit-desc" @click="_toggleDesc"
                      title="<?php esc_attr_e( 'Edit section content', 'learnpress-frontend-editor' ); ?>"></span>
                <span class="delete e-hold-down" @click="_deleteSection" @mousedown="_startAnim" @mouseup="_stopAnim"
                      title="<?php esc_attr_e( 'Delete this section', 'learnpress-frontend-editor' ); ?>"></span>
                <span class="toggle e-hold-down" @click="_toggle" @mousedown="_startAnim" @mouseup="_stopAnim"
                      title="<?php esc_attr_e( 'Show/Hide items list', 'learnpress-frontend-editor' ); ?>"></span>
            </div>
            <span class="count-items">{{countItems}}</span>
        </h4>
        <div class="e-section-desc">
            <textarea v-model="section.description"
                      placeholder="<?php esc_attr_e( 'Describe about this section', 'learnpress-frontend-editor' ); ?>"></textarea>
            <p class="e-section-desc-actions">
                <a href="" @click="_saveDesc"><?php _e( 'Save', 'learnpress-frontend-editor' ); ?></a>
                <a href="" @click="_discardChangeDesc"><?php _e( 'Cancel', 'learnpress-frontend-editor' ); ?></a>
            </p>
        </div>
        <ul class="e-section-content e-sortable" v-show="!isHidden()">
            <e-course-item v-for="(item, i) in sortedItems"
                           :item="item" :section="section"
                           :key="item.id"
                           :selected="isSelectedItem(item.id)!==-1"
                           :position="i"
                           @add-items="_addItems"
                           @move-item="_moveItem"
                           @delete-item="_deleteItem"
                           @select-item="_selectItem"
                           @deselect-item="_deselectItem"
                           @onFocusItem="onFocusItem"
                           @onBlurItem="onBlurItem" @openItemSettings="openItemSettings"></e-course-item>
            <e-course-item :item="defaultItem" :placeholder="true"></e-course-item>
            <li>
                <button type="button" class="e-button e-select-items" v-show="!placeholder"
                        @click="_addItems"><?php esc_html_e( 'Select Items', 'learnpress-frontend-editor' ); ?></button>
            </li>
        </ul>
    </li>
</script>