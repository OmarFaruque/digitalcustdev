<?php
/**
 * @author Deepen.
 * @created_on 8/6/19
 */
?>

<script type="text/x-template" id="tmpl-e-course-item-settings-lp_lesson">
    <div class="e-item-settings-extra">
        <ul class="e-form-field-table flex">
            <component v-for="i in getFields('lp_lesson')" :is="includeFormField(i)" :settings="settings"
                       :field="i"
                       v-if="drawComponent"
                       :item-data="itemData"
                       :item="item">
            </component>
        </ul>
    </div>
</script>