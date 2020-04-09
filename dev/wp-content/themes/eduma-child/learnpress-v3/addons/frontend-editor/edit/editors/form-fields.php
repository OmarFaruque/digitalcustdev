<?php
/**
 * Template for displaying form fields of post type settings
 *
 * @author ThimPress
 * @package Frontend_Editor/Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or die;

global $frontend_editor;

$post_manage = $frontend_editor->post_manage;
$post        = $post_manage->get_post();
$post_type  = get_post_meta($post->ID, '_course_type', 'true');
?>
    <script type="text/x-template" id="tmpl-e-form-field">
        <component :is="includeFormField()" :field="field" :item-data="itemData" :settings="settings || {}"></component>
    </script>



    <!-- <li class="e-form-field">
                            <label class="omr"><?php _e( 'Description', 'learnpress-frontend-editor' ); ?></label>
                            <div class="omf e-form-field-input">
                                <e-tinymce :id="'e-item-content'" v-model="itemData.content"></e-tinymce>
                            </div>
                        </li> -->
    <script type="text/x-template" id="tmpl-e-tinymce">

        <div :id="getEditorId()" class="omarf e-tinymce-wrap wp-content-wrap"
             class="wp-core-ui wp-editor-wrap tmce-active has-dfw OmarFaruque">
            <div id="wp-content-editor-tools" class="omar-tinymce wp-editor-tools hide-if-no-js">
                <!-- <div id="wp-content-media-buttons" class="wp-media-buttons"> -->
                    <!-- <button class="e-button" type="button" id="insert-media-button" class="button insert-media add_media"
                            :data-editor="id">
                        <span class="wp-media-buttons-icon"></span>
						<?php _e( 'Add Media', 'learnpress-frontend-editor' ); ?>
                    </button> -->

                <!-- </div> -->
                <div class="wp-editor-tabs">
                    <button class="e-button" type="button" :id="id+'-tmce'" class="wp-switch-editor switch-tmce"
                            :data-wp-editor-id="id">
						<?php _e( 'Visual', 'learnpress-frontend-editor' ); ?>
                    </button>
                    <button class="e-button" type="button" id="id+'-html'" class="wp-switch-editor switch-html"
                            :data-wp-editor-id="id">
						<?php _e( 'Text', 'learnpress-frontend-editor' ); ?>
                    </button>
                </div>
            </div>
            <div id="wp-content-editor-container" class="wp-editor-container">
                <div id="ed_toolbar" class="quicktags-toolbar"></div>
                <textarea class="wp-editor-area" style="height: 250px" autocomplete="off" cols="40" name="content" :id="id"
                          v-model="value"></textarea></div>
        </div>

    </script>
        <script type="text/x-template" id="tmpl-e-form-field-textarea">
        <li class="omar89 d-block e-form-field textarea textabove">
            <?php if($post_type != 'webinar'): ?>  
            <div class="single_sub_section add_video mb-2">
                <div class="rwmb-label">
                    <label><?php _e('Add Video', 'webinars'); ?></label>
                    <div class="tooltip">?<span class="tooltiptext"><?php _e('Add Media', 'webinars'); ?></span></div>
                </div>
                <div class="e-form-field-input-media">
                        <div id="wp-content-media-buttons" class="wp-media-buttons">                  
                            <button class="e-button" type="button" id="insert-media-button" class="button insert-media_cus add_media">
                                <span class="wp-media-buttons-icon"></span>
                                <?php _e( 'Add Media', 'learnpress-frontend-editor' ); ?>
                            </button>
                        </div>
                </div>
            </div>
            <?php endif; ?>
            <div class="single_sub_section">
                <div class="rwmb-label">
                    <label v-html="field.name"></label> 
                    <div class="tooltip">?<span class="tooltiptext">Tooltip text 1</span></div>
                </div>
                <div class="e-form-field-input">
                    <textarea id="lesson_media_url" v-model="itemData.settings[field.id]" style="height: 100px;"></textarea>
                    <p class="e-form-field-desc" v-html="field.desc"></p>
                </div>
            </div>
        </li>
    </script>




    <script type="text/x-template" id="tmpl-e-form-field-file-advanced">
	<li class="omar9 mt-29 e-form-field file-advanced">
        <label>{{field.name}}
         <span class="tooltip">?<span class="tooltiptext"><?php _e('Tooltip text', 'webinars'); ?></span></span>
        </label>
        
        <div class="e-form-field-input">

            <input class="rwmb-file_advanced" name="_lp_attachments" v-model="settings[field.id]" type="hidden" data-options="{&quot;mimeType&quot;:&quot;&quot;,&quot;maxFiles&quot;:0,&quot;forceDelete&quot;:false,&quot;maxStatus&quot;:true}">
            <div class="rwmb-media-view">
                <ul class="rwmb-media-list ui-sortable" id="fe-assignment-attach-contain">
                    <li v-for="(attachment, attachmentIndex) in attachments" :key="attachmentIndex" class="rwmb-media-item attachment" :id="'fe-assignment-attach-list-' + attachment.id">
                        <div class="rwmb-media-preview attachment-preview">
                            <div class="rwmb-media-content thumbnail">
                                <div class="centered">
                                    <img :src="attachment.icon">
                                </div>
                            </div>
                        </div>
                        <div class="rwmb-media-info">
                            <a :href="attachment.url" class="rwmb-media-title" target="_blank">
                                {{attachment.filename}}
                            </a>
                            <a @click="_remove_attact($event, attachment.id)" :data-attachid="attachment.id"><span class="dashicons dashicons-trash"></span></a>
                        </div>
                    </li>
                </ul>
                <div class="rwmb-media-status">

                </div><div class="rwmb-media-add">
                    <a class="button" @click="_selectMedia($event)">+ Add Media</a>
                </div>
                <input type="hidden" class="rwmb-field-name" value="_lp_attachments">
            </div><p class="e-form-field-desc">{{field.desc}}</p>
        </div>
	</li>
</script>

     <script type="text/x-template" id="tmpl-e-form-field-duration">
        <li class="omar2 e-form-field">
            <label v-html="field.name"></label>
            <div class="tooltip" style="margin-left: -215px;
                margin-top: -2px;
                margin-right: 27%;">?<span class="tooltiptext">Tooltip text 2</span>
            </div>
            <div class="e-form-field-input">
                <div class="row">
                    <div class="col-md-5 col-xs-12 col-sm-5">
                        <input type="number" placeholder="1" v-model="settingValue[1]">
                    </div>
                    <div class="col-md-7 col-xs-12 col-sm-7">
                    <select class="om5 select2-select">
                        <option class="option" value="week" selected><?php esc_html_e( 'week', 'learnpress-frontend-editor' ); ?></option>
                        <option class="option" value="hour"><?php esc_html_e( 'Hour', 'learnpress-frontend-editor' ); ?></option>
                        <option class="option" value="day"><?php esc_html_e( 'Day', 'learnpress-frontend-editor' ); ?></option>
                        <option class="option" value="month"><?php esc_html_e( 'Month', 'learnpress-frontend-editor' ); ?></option>
                    </select>
                    </div>
                </div>
                <p class="e-form-field-desc"  v-html="field.desc"></p>
                
            </div>
        </li>
    </script>
    
   
    <script type="text/x-template" id="tmpl-e-form-field-text">
        <li class="omar3 e-form-field text textdown">
            <label v-html="field.name">

            <span class="tooltip">?<span class="tooltiptext"><?php _e('Tooltip text', 'webinars'); ?></span></span>
            </label>
            
            <div class="e-form-field-input">
                <input :type="field.xType ? field.xType : field.type" v-model="itemData.settings[field.id]">
                <p class="e-form-field-desc" v-html="field.desc"></p>
            </div>
        </li>
    </script>


   
    <script type="text/x-template" id="tmpl-e-form-field-yes-no">
        <li class="omar4 e-form-field yes-no">
            <div class="rwmb-label">
                <label v-html="field.name"></label>
                 <span class="tooltip">?<span class="tooltiptext"><?php _e('Tooltip text', 'webinars'); ?></span></span>
            </div>
            <div class="e-form-field-input">
            <label class="switch mr-1">
                <input type="checkbox" v-model="settingValue" true-value="yes" false-value="no">
                


                
					
					<span class="slider"></span>
				</label>

                <p class="e-form-field-desc" v-html="field.desc"></p>
            </div>
        </li>
    </script>

    
<?php
do_action( 'learn-press/frontend-editor/form-fields-after' );
?>