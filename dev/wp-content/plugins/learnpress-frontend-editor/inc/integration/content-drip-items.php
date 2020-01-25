<?php
ob_start();
if ( ! $postManager = frontend_editor()->post_manage ) {
	return;
}

$post = $postManager->get_post();
?>
    <fe-content-drip-items
            :course-data="formData"
            :sections="sections"
            inline-template>
        <div>
            <button type="button"
                    data-text="<?php esc_attr_e( 'Save items', 'learnpress-frontend-editor' ); ?>"
                    data-text-update="<?php esc_attr_e( 'Updating', 'learnpress-frontend-editor' ); ?>"
                    :disabled="status==='updating'"
                    ref="btn-update"
                    @click="_save($event, <?php echo $post->ID; ?>)">{{getButtonUpdateLabel()}}
            </button>

            <table class="e-content-drip-items">
                <thead>
                <tr>
                    <th class="col-drip-item-name"><?php esc_html_e( 'Name', 'learnpress-frontend-editor' ); ?></th>
                    <th class="col-drip-item-type"><?php esc_html_e( 'Type', 'learnpress-frontend-editor' ); ?></th>
                    <th v-if="isPrerequisite()"
                        class="col-drip-item-prerequisite"><?php esc_html_e( 'Prerequisite', 'learnpress-frontend-editor' ); ?></th>
                    <th class="col-drip-item-settings">
						<?php esc_html_e( 'Delay access', 'learnpress-frontend-editor' ); ?>
                        <div class="fe-quick-settings-drip-items">
                            <a href=""
                               @click="_showModal($event)"><?php esc_html_e( 'Settings', 'learnpress-frontend-editor' ); ?></a>
                            <a href=""
                               @click="_reset($event)"><?php esc_html_e( 'Reset', 'learnpress-frontend-editor' ); ?></a>
                        </div>
                        <div v-if="showModal" class="fe-modal-settings-content-drip-a54de1">
                            <div class="fe-modal-settings-content-drip-a54de1__overlay" @click="_close()"></div>
                            <div class="fe-modal-settings-content-drip-a54de1__window">
                                <div class="quick-settings-form">
                                    <p>
                                        <label><?php _e( 'Start', 'learnpress-content-drip' ); ?></label>
                                        <input type="number" name="start" v-model="quickSettings.start" min="0"
                                               max="100" step="0.5">
                                    </p>
                                    <p>
                                        <label><?php _e( 'Step', 'learnpress-content-drip' ); ?></label>
                                        <input type="number" name="step" v-model="quickSettings.step" min="0.5"
                                               max="100" step="0.5">
                                    </p>
                                    <p>
                                        <label><?php _e( 'Type', 'learnpress-content-drip' ); ?></label>
                                        <select name="type" v-model="quickSettings.type">
											<?php $intervals = learn_press_get_course_duration_support();
											foreach ( $intervals as $k => $v ) {
												echo sprintf( '<option value="%s">%s</option>', $k, $v );
											} ?>
                                        </select>
                                    </p>
                                    <p>
                                        <button class="button button-primary apply-quick-settings"
                                                type="button"
                                                @click="_updateSettings()"><?php _e( 'Apply', 'learnpress-content-drip' ); ?></button>
                                        <button class="button close-quick-settings"
                                                type="button"
                                                @click="_close()"><?php _e( 'Close', 'learnpress-content-drip' ); ?></button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, itemIndex) in dripItems">
                    <td class="col-drip-item-name">{{item.title}}</td>
                    <td class="col-drip-item-type">{{item.type}}</td>
                    <td v-show="isPrerequisite()" class="col-drip-item-prerequisite">
                        <select multiple="multiple" ref="prerequisite" v-model="item.settings.prerequisite">
                            <option v-for="(item2, itemIndex2) in dripItems" v-html="item2.title" :value="item2.id"
                                    :selected="isSelectedItem(item, itemIndex, item2, itemIndex2)">

                            </option>
                        </select>
                    </td>
                    <td class="col-drip-item-settings">
                        <select class="delay-type" v-model="item.settings.type">
                            <option value="immediately"><?php esc_html_e( 'Immediately', 'learnpress-frontend-editor' ); ?></option>
                            <option value="interval"><?php esc_html_e( 'After...', 'learnpress-frontend-editor' ); ?></option>
                            <option value="specific"><?php esc_html_e( 'Specific date', 'learnpress-frontend-editor' ); ?></option>
                        </select>

                        <div v-show="item.settings.type=='interval'" class="delay-interval">
                            <input type="number" class="delay-interval-0" value="0" min="0" step="0.5"
                                   v-model="item.settings.delay_interval_0">
                            <select class="delay-interval-1" v-model="item.settings.delay_interval_1">
                                <option value="minute"><?php esc_html_e( 'Minute(s)', 'learnpress-frontend-editor' ); ?></option>
                                <option value="hour"><?php esc_html_e( 'Hour(s)', 'learnpress-frontend-editor' ); ?></option>
                                <option value="day"><?php esc_html_e( 'Day(s)', 'learnpress-frontend-editor' ); ?></option>
                                <option value="week"><?php esc_html_e( 'Week(s)', 'learnpress-frontend-editor' ); ?></option>
                            </select>
                        </div>
                        <div v-show="item.settings.type=='specific'" class="delay-specific">
                            <input type="text" class="item-datepicker" v-model="item.settings.date" data-date="">
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>



        </div>
    </fe-content-drip-items>

    <script type="text/html"
            id="fe-drip-content-items-config"
            ref="itemsConfig">
                    <?php echo json_encode( array(
			'dripItems' => get_post_meta( $post->ID, '_lp_drip_items', true ),
			'dripType'  => get_post_meta( $post->ID, '_lp_content_drip_drip_type', true )
		), JSON_PRETTY_PRINT ); ?></script>
<?php
return ob_get_clean();
