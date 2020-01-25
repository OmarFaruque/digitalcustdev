<?php
/**
 * @var LP_Addon_Frontend_Editor $frontend_editor
 */
global $frontend_editor;
?>
<script type="text/x-template" id="tmpl-e-course-item">
    <li :class="itemClasses()" tabindex="0" @focus="onFocus" @click="onFocus($event)" @blur="onBlur" :data-id="item.id || ''"
        :data-position="position">
        <div class="sort" @click="_removeSelected($event, item.id)">
			<?php $frontend_editor->get_template( 'global/drag-icon' ); ?>
        </div>
        <div class="item-switch" @mousleavex="_toggleMoreTypes($event, 'out')">
            <span v-for="(itemType, i) in itemTypes" :class="itemSwitchClass(itemType)"
                  :title="itemType.name" @click="_switchType($event, itemType.type)">
            </span>
            <span class="more-types" @click="_toggleMoreTypes"></span>
        </div>
        <div class="item-title-input">
            <input type="text" v-model="item.title" data-context="item-title" class="item-title"
                   @mousedown="_select"
                   :placeholder="getInputPlaceholderText('<?php _e( 'Item name', 'learnpress-frontend-editor' ); ?>')">
        </div>
        <div class="e-sortable-actions e-item-actions">
            <span v-if="supportPreview()" class="preview" :class="item.settings && item.settings['_lp_preview'] === 'yes' ? 'on' : ''" @click="_preview"
                  title="<?php esc_attr_e( 'Turn this item is preview on or off', 'learnpress-frontend-editor' ); ?>">
            </span>
            <?php do_action( 'learn-press/frontend-editor/item-extra-action' ); ?>
            <span class="delete e-hold-down" @click="_delete" @mousedown="_startAnim" @mouseup="_stopAnim"
                  title="<?php esc_attr_e( 'Delete this item', 'learnpress-frontend-editor' ); ?>">
            </span>
        </div>
        <ul class="e-quick-drop">
            <li class="cancel"></li>
            <li class="delete-permanently"></li>
            <li class=""></li>
        </ul>
    </li>
</script>

<script type="text/x-template" id="tmpl-e-course-item-new">
    <li :class="itemClasses()" tabindex="0" @focus="onFocus" @click="onFocus" @blur="onBlur" :data-id="item.id || ''"
        :data-position="position">
        <div class="sort" @click="_removeSelected($event, item.id)">
			<?php $frontend_editor->get_template( 'global/drag-icon' ); ?>
        </div>
        <div class="item-switch" @mousleavex="_toggleMoreTypes($event, 'out')">
            <span v-for="(itemType, i) in itemTypes" :class="itemSwitchClass(itemType)"
                  :title="itemType.name" @click="_switchType($event, itemType.type)">
            </span>
            <span class="more-types" @click="_toggleMoreTypes"></span>
        </div>
        <div class="item-title-input">
            <input type="text" v-model="item.title" data-context="item-title" class="item-title"
                   @mousedown="_select"
                   :placeholder="getInputPlaceholderText('<?php _e( 'Item name', 'learnpress-frontend-editor' ); ?>')">
        </div>
    </li>
</script>