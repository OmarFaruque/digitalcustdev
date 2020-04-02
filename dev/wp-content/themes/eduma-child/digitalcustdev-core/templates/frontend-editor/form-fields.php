<?php
/**
 * Template for displaying form fields of post type settings
 *
 * @author ThimPress
 * @package Frontend_Editor/Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or die;
?>

<script type="text/x-template" id="tmpl-e-form-field-when-webinar">
    <li class="e-form-field when-webinar">
        <div class="rwmb-label">
            <label v-html="field.name"></label>
            <span class="tooltip">?<span class="tooltiptext"><?php _e('Tooltip text', 'webinars'); ?></span></span>
        </div>
        <div class="e-form-field-input">
            <i class="fa fa-calendar icon"></i>
            <input type="text" v-model="itemData.settings[field.id]" @click="_changeDatePicker($event)" class="form-control webinar_start_time">
        </div>
    </li>
</script>
<script type="text/x-template" id="tmpl-e-form-field-timezone">
    <li class="e-form-field timezone">
        <label v-html="field.name"></label>
        <div class="e-form-field-input">
			<?php $tzlists = zvc_get_timezone_options(); ?>
            <select class="select2-select" v-model="settingValue">
				<?php foreach ( $tzlists as $k => $tzlist ) { ?>
                    <option value="<?php echo $k; ?>"><?php echo $tzlist; ?></option>
				<?php } ?>
            </select>
        </div>
    </li>
</script>
<script type="text/x-template" id="tmpl-e-form-field-lesson-duration">
    <li class="e-form-field lesson_duration">
        <div class="rwmb-label">
            <label v-html="field.name"></label>
            <span class="tooltip">?<span class="tooltiptext"><?php _e('Tooltip text', 'webinars'); ?></span></span>
        </div>
        

        <?php 
        global $frontend_editor;
        $post_manage = $frontend_editor->post_manage;
        $post        = $post_manage->get_post();
        $course_type = get_post_meta( $post->ID, '_course_type', true );
        ?>
        <div class="o9 e-form-field-input">
            <?php if($course_type): ?>
            <select class="form-control select2-select" v-model="settingValue">
                <option value="30 minute"><?php esc_html_e( '30 Minute', 'learnpress-frontend-editor' ); ?></option>
                <option value="45 minute"><?php esc_html_e( '45 Minute', 'learnpress-frontend-editor' ); ?></option>
                <option value="1 hour"><?php esc_html_e( '1 Hour', 'learnpress-frontend-editor' ); ?></option>
                <option value="75 minute"><?php esc_html_e( '1 Hour 15 minute', 'learnpress-frontend-editor' ); ?></option>
                <option value="90 minute"><?php esc_html_e( '1 Hour 30 minute', 'learnpress-frontend-editor' ); ?></option>
                <option value="2 hour"><?php esc_html_e( '2 Hour', 'learnpress-frontend-editor' ); ?></option>
            </select>
            <?php else: ?>
                <div class="onlycourse_duration">
                    <div class="row">
                        <div class="col-sm-5 col-md-5 col-xs-5">
                            <div class="form-group">
                                <input type="number" name="course_length_input" id="course_length_input" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-7 col-md-7 col-xs-7">
                            <div class="form-group">
                                <select name="course_length_type" id="course_length_type" class="form-control select2-select">
                                    <option value="hour"><?php _e('Hour(s)', 'webinar'); ?></option>
                                    <option value="minute"><?php _e('Minute(s)', 'webinar'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <p class="e-form-field-desc" v-html="field.desc"></p>
        </div>
    </li>
</script>
