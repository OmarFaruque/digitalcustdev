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
        <label v-html="field.name"></label>
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
            <select v-model="settingValue">
				<?php foreach ( $tzlists as $k => $tzlist ) { ?>
                    <option value="<?php echo $k; ?>"><?php echo $tzlist; ?></option>
				<?php } ?>
            </select>
        </div>
    </li>
</script>
<script type="text/x-template" id="tmpl-e-form-field-lesson-duration">
    <li class="e-form-field lesson_duration">
        <label v-html="field.name"></label>
        <div class="e-form-field-input">
            <select v-model="settingValue">
                <option value="30 minute"><?php esc_html_e( '30 Minute', 'learnpress-frontend-editor' ); ?></option>
                <option value="45 minute"><?php esc_html_e( '45 Minute', 'learnpress-frontend-editor' ); ?></option>
                <option value="1 hour"><?php esc_html_e( '1 Hour', 'learnpress-frontend-editor' ); ?></option>
                <option value="75 minute"><?php esc_html_e( '1 Hour 15 minute', 'learnpress-frontend-editor' ); ?></option>
                <option value="90 minute"><?php esc_html_e( '1 Hour 30 minute', 'learnpress-frontend-editor' ); ?></option>
                <option value="2 hour"><?php esc_html_e( '2 Hour', 'learnpress-frontend-editor' ); ?></option>
            </select>
            <p class="e-form-field-desc" v-html="field.desc"></p>
        </div>
    </li>
</script>
