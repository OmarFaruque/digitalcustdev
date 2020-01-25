<script type="text/x-template" id="tmpl-e-course-curriculum">
    <div id="e-tab-content-curriculum">
        <div id="e-course-curriculum" class="e-curriculum" @keypress="onPressTitle" @keydown="onPressTitle">
<!--            <div class="e-curriculum-head">-->
<!--                <h3 class="e-curriculum-heading">-->
<!--					--><?php //_e( 'Curriculum', 'learnpress-frontend-editor' ); ?>
<!--                </h3>-->
<!--                <div class="e-curriculum-actions">-->
<!--                    <span class="count-items">{{countItems()}}</span>-->
<!--                </div>-->
<!--            </div>-->

            <ul class="e-course-sections e-sortable">
                <e-course-section v-for="(section, i) in $dataStore().sections" :section="section"
                                  :key="section.id"
                                  @delete-item="_deleteItem"
                                  @toggle-sections="_toggleSections"
                                  @add-new-section="_addNewSection"
                                  @delete-section="_deleteSection"
                                  @move-section="moveSection"
                                  @added-item="onAddedItem"
                                  @onFocusSection="onFocusSection"
                                  @onBlurSection="onBlurSection"
                                  @openItemSettings="openItemSettings">
                </e-course-section>
<!--                <e-course-section :section="{}" :placeholder="true"></e-course-section>-->

                <e-course-section-new :sections="$dataStore().sections"></e-course-section-new>
            </ul>

        </div>

        <e-course-item-settings :item="item" :item-data="item || {xxx: 0}" request="$request"
                                closeItemSettings="closeItemSettings"></e-course-item-settings>

    </div>
</script>