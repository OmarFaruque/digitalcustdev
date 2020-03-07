<?php
/**
 * JS template used for VueJS
 *
 * @since   3.0.0
 * @author  ThimPress
 * @package LearnPress/JSTemplate
 */
global $frontend_editor;
?>
    <script type="text/x-template" id="tmpl-e-course-item-settings">
        <div id="e-item-settings" :data-context="getContext()">
            <div class="e-settings-window">
                <div class="e-item-settings-content" v-if="itemData && itemData.id">

                    <ul class="e-form-field-table">
                        <li class="e-form-field">
                            <label><?php _e( 'Title', 'learnpress-frontend-editor' ); ?></label>
                            <div class="e-form-field-input e-item-heading-input">
                                <input type="text" v-model="itemData.title" class="wide-fat">
                            </div>
                        </li>
                        <li class="e-form-field">
                            <label><?php _e( 'Description', 'learnpress-frontend-editor' ); ?></label>
                            <div class="e-form-field-input">
                                <e-tinymce :id="'e-item-content'" v-model="itemData.content"></e-tinymce>
                            </div>
                        </li>
                    </ul>
                    <component :is="getComponentItemSettings()" :item="item" :item-data="itemData"
                               :request="request">
                    </component>
                </div>
                <div v-else
                     class="e-no-item-selected"><?php esc_html_e( 'Please select an item or add a new one', 'learnpress-frontend-editor' ); ?></div>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="tmpl-e-course-item-settings-lp_lesson">
        <div class="e-item-settings-extra">
            <ul class="e-form-field-table flex 9898">
                <component v-for="i in getFields('lp_lesson')" :is="includeFormField(i)" :settings="settings"
                           :field="i"
                           v-if="drawComponent"
                           :item-data="itemData">
                </component>
            </ul>
        </div>
    </script>

    <script type="text/x-template" id="tmpl-e-course-item-settings-lp_quiz">
        <div class="e-item-settings-extra">

            <ul class="e-form-field-table">
                <li id="e-quiz-editor-wrap" class="e-form-field">
                    <div class="e-section-heading"><?php esc_html_e( 'Questions', 'learnpress-frontend-editor' ); ?></div>
                    <e-quiz-editor :item="item" :item-data="itemData"
                                   @edit-question="_setEditQuestion"></e-quiz-editor>
                </li>
            </ul>

            <div class="e-section-heading"><?php esc_html_e( 'Settings', 'learnpress-frontend-editor' ); ?></div>
            <div xv-show="showSettingsBox">
                <ul class="e-form-field-table flex omar787" v-show="isCurrent('settings')">
                    <component v-for="i in getFields('lp_quiz')" :is="includeFormField(i)" :settings="settings"
                               :field="i" v-if="drawComponent" :item-data="itemData"></component>
                </ul>
            </div>

            <div class="e-edit-question-form" v-if="isEditingQuestion()">
                <e-question :question="question" :item-data="itemData" @load-question="loadQuestion"
                            :quiz="item"></e-question>
                <p>
                    <a href="" @click="_closeQuestion($event)"
                       class="back e-button"><?php _e( 'Back', 'learnpress-frontend-editor' ); ?></a>
                </p>
            </div>

        </div>
    </script>

    <script type="text/x-template" id="tmpl-e-quiz-editor">
        <div class="e-quiz-editor">
            <div class="e-list-questions-heading">
                <div class="e-list-questions-heading__name">
					<?php esc_html_e( 'Name', 'learnpress-frontend-editor' ); ?>
                </div>
                <div class="e-list-questions-heading__type">
					<?php esc_html_e( 'Type', 'learnpress-frontend-editor' ); ?>
                </div>
                <div class="e-list-questions-heading__mark">
					<?php esc_html_e( 'Mark', 'learnpress-frontend-editor' ); ?>
                </div>
                <div class="e-list-questions-heading__count">
					<?php esc_html_e( 'Actions', 'learnpress-frontend-editor' ); ?>
                </div>
            </div>
            <ul class="e-questions e-sortable" v-if="!hidden">

                <e-question-loop v-for="(question, index) in questions" :question="question" :item-data="itemData"
                                 :key="index"
                                 :index="index"
                                 @edit-question="editQuestion" :hidden="hidden"
                                 @update-question-title="_updateQuestion"
                                 @delete-question="_onDeleteQuestion"
                                 @press-question-title="_onKeydownQuestionTitle"></e-question-loop>

                <e-question-loop
                        :question="defaultQuestion"
                        :item-data="itemData"
                        :placeholder="true"
                        @change-question-type="_changeDefaultQuestionType"
                        @add-new-question="_addNewQuestion"
                        @press-question-title="_onKeydownQuestionTitle"></e-question-loop>
            </ul>


            <p class="e-edit-question-buttons" v-if="!isEditingQuestion()">
                <!--                <button class="e-button" type="button"-->
                <!--                        @click="_addNew">-->
				<?php //_e( 'Add new', 'learnpress-frontend-editor' ); ?><!--</button>-->
                <button class="e-button" type="button"
                        @click="_select"><?php _e( 'Select', 'learnpress-frontend-editor' ); ?></button>
            </p>

        </div>
    </script>

    <script type="text/x-template" id="tmpl-e-question-loop">
        <li :class="questionClass()" :data-id="question.id" v-if="!hidden">
            <div class="sort">
				<?php $frontend_editor->get_template( 'global/drag-icon' ); ?>
            </div>
            <div class="e-question-type">
                {{getQuestionIndexLabel()}}
            </div>
            <div class="e-item-input">
                <input type="text" v-model="question.title" @keydown="_onKeydown" class="question-loop-title"
                       ref="questionTitle"
                       :placeholder="'<?php esc_html_e( 'Question name', 'learnpress-frontend-editor' ); ?>'"
                       @blur="_onBlur">
            </div>

            <div class="e-question-type-name">
                <div class="e-question-type-switcher">
                    <span>{{getTypeName()}}</span>
                    <ul class="switch">
                        <li v-for="(typeObject, i) in $dataStore('question_types')"
                            :class="typeObject.type"
                            :data-type="typeObject.type"
                            @click="_switch"
                            :class="typeObject.type===question.type ? 'current' : ''">
                            {{typeObject.name}}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="e-question-mark">
                <input type="number" min="0.5" step="0.5"
                       placeholder="<?php esc_attr_e( 'Mark', 'learnpress-frontend-editor' ); ?>"
                       v-model="questionMark">
            </div>
            <div class="e-item-actions question-actions e-sortable-actions">

            <span @click="_delete($event, question.id)" class="e-item-action delete e-hold-down" @mousedown="_startAnim"
                  @mouseup="_stopAnim"></span>
                <span @click="edit" class="e-item-action more-action"></span>
            </div>
            <!--            <div class="count-items">{{countQuestions()}}</div>-->
        </li>
    </script>

    <script type="text/x-template" id="tmpl-e-question">
        <div class="e-question e-question-editor">
            <h3 class="e-question-head-nav">
                <a href="" @click="_back($event)"><?php esc_html_e( 'Quiz:', 'learnpress-frontend-editor' ); ?>
                    {{itemData.title}}</a>
                <span>/ {{getQuestionIndex()}}. {{question.title}}</span></h3>
            <ul class="e-form-field-table">
                <li class="e-form-field">
                    <label><?php _e( 'Title', 'learnpress-frontend-editor' ); ?></label>
                    <div class="e-form-field-input">
                        <input type="text" v-model="question.title" class="wide-fat">
                    </div>
                </li>
                <li class="e-form-field">
                    <label><?php _e( 'Content', 'learnpress-frontend-editor' ); ?></label>
                    <div class="e-form-field-input">
                        <e-tinymce :id="'e-question-content'" v-model="question.content"></e-tinymce>
                    </div>
                </li>

                <li class="e-form-field">
                    <template v-if="getQuestionAnswersComponent()!==undefined">
                        <component :is="getQuestionAnswersComponent()" :question="question"></component>
                    </template>
                    <template v-else>
                        <div class="e-section-heading"><?php esc_html_e( 'Answers', 'learnpress-frontend-editor' ); ?></div>

                        <div class="e-form-field-input">

                            <ul class="e-question-answers e-sortable" :class="2>=countAnswers() ? 'freeze-delete' : ''">
                                <li v-for="(answer, index) in answers" :class="answerClass(answer)"
                                    :data-id="answer.question_answer_id"
                                    :index="index"
                                    v-if="redraw">
                                    <div class="sort"><?php $frontend_editor->get_template( 'global/drag-icon' ); ?></div>
                                    <div class="e-item-index">{{getAnswerIndexLabel(index+1)}}</div>
                                    <div class="e-item-input">
                                        <input type="hidden" v-model="answer.value">
                                        <input type="text" v-model="answer.text" @keydown="onKeydown"
                                               @blur="_onBlurAnswerInput"
                                               class="e-question-answer-input">
                                    </div>
                                    <div class="e-answer-check">
                                        <input class="e-answer-check-input" :type="getAnswerCheckType()"
                                               @change="_setCheckAnswer($event, answer.question_answer_id)"
                                               :checked="isCheckedAnswer(answer)"
                                               :data-id="answer.question_answer_id"
                                               name="e-question-answer-check" :value="answer.question_answer_id">
                                    </div>
                                    <div v-if="isSupport('add_answer')" class="e-item-actions answer-actions">
                                <span v-show="!(2>=countAnswers())" class="e-item-action action-delete"
                                      @click="_deleteAnswer($event, answer.question_answer_id)"></span>
                                    </div>
                                </li>
                            </ul>

                            <button class="e-button" type="button"
                                    v-if="isSupport('add_answer')"
                                    @click="_addAnswer"><?php _e( 'Add option', 'learnpress-frontend-editor' ); ?></button>

                            <div class="e-question-type-switcher">
                                <span>{{getTypeName()}}</span>
                                <ul @click="_switch" class="switch">
                                    <li v-for="(typeObject, i) in $dataStore('question_types')"
                                        :class="typeObject.type"
                                        :data-type="typeObject.type"
                                        :class="typeObject.type===question.type ? 'current' : ''">
                                        {{typeObject.name}}
                                    </li>
                                </ul>
                            </div>

                            <div class="e-clearfix"></div>
                        </div>
                    </template>
                </li>
            </ul>

            <div class="e-section-heading"><?php esc_html_e( 'Settings', 'learnpress-frontend-editor' ); ?></div>
            <ul class="e-form-field-table flex mmtt">
                <e-form-field v-for="(field, i) in getDataStore().getters.questionFields" :field="field"
                              :item-datax="itemData"
                              :item-data="question"
                              :settings="question.settings">
                </e-form-field>
            </ul>

            <div class="e-question-nav">
                <div class="prev">
                    <a v-if="getPrevQuestionIndex()!=0" href="" @click="_loadQuestion($event, 'prev')">{{getPrevQuestionIndex(true)}}.
                        {{getPrevQuestionTitle()}}</a>
                </div>
                <div class="next">
                    <a v-if="getNextQuestionIndex()!=0" href="" @click="_loadQuestion($event, 'next')">{{getNextQuestionIndex(true)}}.
                        {{getNextQuestionTitle()}}</a>
                </div>
            </div>

        </div>
    </script>

    <script type="text/template" id="tmpl-e-course-category">
        <ul v-if="hasCategories()" class="e-course-category">
            <li v-for="(cat, catIndex) in categories">
                <label>
                    <input type="checkbox" v-model="cat.selected">
                    <span v-html="cat.name"></span>
                </label>

                <e-course-category
                        :categories="cat.nodes">
                </e-course-category>

            </li>
        </ul>
    </script>
    <script type="text/template" id="tmpl-e-course-category-option-deep">
        <template>
            <option v-for="(cat, catIndex) in categories" :value="1">
                {{cat.name}}
            </option>
            <option>asdasdasda</option>
        </template>
    </script>
<?php
do_action( 'learn-press/frontend-editor/item-settings-after' );
