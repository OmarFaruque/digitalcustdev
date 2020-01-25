<?php
/**
 * Detect addon coming-soon is installed.
 *
 * @since 3.0.0
 *
 * @return bool
 */
function e_is_active_coming_soon() {
	return defined( 'LP_COMING_SOON_VER' ) && version_compare( LP_COMING_SOON_VER, '3.0.1', '>=' ) && is_plugin_active( 'learnpress-coming-soon-courses/learnpress-coming-soon-courses.php' );
}

/**
 * Detect addon sorting-choice is installed.
 *
 * @since 3.0.0
 *
 * @return bool
 */
function e_is_active_sorting_choice() {
	return defined( 'LP_ADDON_SORTING_CHOICE_VER' ) && version_compare( LP_ADDON_SORTING_CHOICE_VER, '3.0.1', '>=' ) && is_plugin_active( 'learnpress-sorting-choice/learnpress-sorting-choice.php' );
}

/**
 * Detect addon fill-in-blank is installed.
 *
 * @since 3.0.0
 *
 * @return bool
 */
function e_is_active_fill_in_blank() {
	return defined( 'LP_ADDON_FILL_IN_BLANK_VER' ) && version_compare( LP_ADDON_FILL_IN_BLANK_VER, '3.0.4', '>=' ) && is_plugin_active( 'learnpress-fill-in-blank/learnpress-fill-in-blank.php' );
}

/**
 * Detect addon assignments is activated.
 *
 * @since 3.0.0
 *
 * @return bool
 */
function e_is_active_assignments() {
	return defined( 'LP_ADDON_ASSIGNMENT_VER' ) && version_compare( LP_ADDON_ASSIGNMENT_VER, '3.1.0', '>=' ) && is_plugin_active( 'learnpress-assignments/learnpress-assignments.php' );
}

/**
 * Detect addon co-instructor is activated.
 *
 * @since 3.0.0
 *
 * @return bool
 */
function e_is_active_co_instructor() {
	return defined( 'LP_ADDON_CO_INSTRUCTOR_VER' ) && version_compare( LP_ADDON_CO_INSTRUCTOR_VER, '3.0.5', '>=' ) && is_plugin_active( 'learnpress-co-instructor/learnpress-co-instructor.php' );
}

/**
 * Detect addon certificates is activated.
 *
 * @since 3.0.0
 *
 * @return bool
 */
function e_is_active_certificates() {
	return defined( 'LP_ADDON_CERTIFICATES_VERSION' ) && version_compare( LP_ADDON_CERTIFICATES_VERSION, '3.0.5', '>=' ) && is_plugin_active( 'learnpress-certificates/learnpress-certificates.php' );
}

/**
 * Detect PMP addon is activated.
 *
 * @since 3.0.0
 *
 * @return bool
 */
function e_is_active_pmp() {
	return defined( 'LP_ADDON_PMPRO_VER' ) && version_compare( LP_ADDON_PMPRO_VER, '3.0.0', '>=' ) && is_plugin_active( 'learnpress-paid-membership-pro/learnpress-paid-memberships-pro.php' );
}

/**
 * Detect bbPress addon is activated.
 *
 * @since 3.0.0
 *
 * @return bool
 */
function e_is_active_bbpress() {
	return defined( 'LP_ADDON_BBPRESS_VER' ) && version_compare( LP_ADDON_BBPRESS_VER, '3.0.0', '>=' ) && is_plugin_active( 'learnpress-bbpress/learnpress-bbpress.php' );
}

/**
 * Add new question type.
 *
 * @since 3.0.0
 *
 * @param array $types
 *
 * @return array
 */
function e_add_other_question_types( $types ) {

	if ( e_is_active_sorting_choice() ) {
		$types[] = array(
			'type'     => 'sorting_choice',
			'name'     => __( 'Sorting Choices', 'learnpress-frontend-editor' ),
			'supports' => array(
				'add_answer' => 'yes'
			)
		);
	}

	if ( e_is_active_fill_in_blank() ) {
		$types[] = array(
			'type'     => 'fill_in_blank',
			'name'     => __( 'Fill In Blank', 'learnpress-frontend-editor' ),
			'supports' => array(
				'add_answer' => 'no'
			)
		);
	}

	return $types;
}

add_filter( 'e-question-types', 'e_add_other_question_types' );

function e_integration_scripts() {
	if ( e_is_active_fill_in_blank() ) {
		wp_enqueue_script( 'fib-admin', plugins_url( '/assets/js/admin.fib.js', LP_ADDON_FILL_IN_BLANK_FILE ) );
		wp_enqueue_style( 'fib-admin', plugins_url( '/assets/css/admin.fib.css', LP_ADDON_FILL_IN_BLANK_FILE ) );
	}

	if ( e_is_active_certificates() ) {
		wp_enqueue_style( 'certificates-admin', plugins_url( '/assets/css/admin.certificates.css', LP_ADDON_CERTIFICATES_FILE ) );
	}

	wp_enqueue_style( 'fe-integration', plugins_url( '/assets/css/integration.css', LP_ADDON_FRONTEND_EDITOR_FILE ) );
	//wp_enqueue_script( 'fe-integration', plugins_url( '/assets/js/integration.js', LP_ADDON_FRONTEND_EDITOR_FILE ) );
}

add_action( 'wp_enqueue_scripts', 'e_integration_scripts' );

function e_ajax_update_fib() {
	// question id
	$question_id = ! empty( $_POST['question_id'] ) ? $_POST['question_id'] : false;

	// answers
	$answer = ! empty( $_POST['answer'] ) ? $_POST['answer'] : false;

	if ( is_string( $answer ) ) {
		$answer = json_decode( wp_unslash( $answer ), true );
	}

	if ( ! ( $question_id && $answer ) ) {
		return false;
	}

	$curd = new LP_Question_CURD();
	$curd->update_answer_title( $question_id, $answer );

	die();
}

add_action( 'wp_ajax', 'e_ajax_update_fib' );

add_action( 'wp_footer', 'e_load_integration_view' );

include_once "integration/assignments.php";

function e_load_integration_view() {

	// @since 3.0.1
	if ( ! e_is_frontend_editor_page() ) {
		return;
	}

	include_once "integration/view.php";
}

/**
 * Check if current page is Frontend Course Editor
 *
 * @since 3.0.1
 *
 * @return bool
 */
function e_is_frontend_editor_page() {
	global $wp_query;

	return $wp_query->get( 'frontend-editor' );
}

/**
 * @param WP_Admin_Bar $wp_admin_bar
 */
function e_change_edit_course_admin_bar_link( $wp_admin_bar ) {
	if ( $node = $wp_admin_bar->get_node( 'edit' ) ) {
		//$wp_admin_bar->remove_node( 'edit' );
	}
}

add_action( 'admin_bar_menu', 'e_change_edit_course_admin_bar_link', 100 );

/**
 * Filter authors in list to exclude the user is own of course.
 * Why this happen? Not know.
 *
 * @param array $meta_box
 *
 * @return mixed
 */
function e_course_author_meta_box_filter_users( $meta_box ) {
	$post_manage = frontend_editor()->post_manage;

	if ( ! isset( $meta_box['fields'] ) || ! $post_manage ) {
		return $meta_box;
	}

	foreach ( $meta_box['fields'] as $k => $field ) {
		if ( empty( $field['id'] ) || $field['id'] !== '_lp_co_teacher' ) {
			continue;
		}

		$post = frontend_editor()->post_manage->get_post();

		if ( isset( $field['options'][ $post->post_author ] ) ) {
			unset( $field['options'][ $post->post_author ] );
		}

		$meta_box['fields'][ $k ] = $field;
	}

	return $meta_box;
}

add_filter( 'learn_press_course_author_meta_box', 'e_course_author_meta_box_filter_users', 20 );

/**
 * Add post meta from other addons such as: co-instructor, certificates, content-drip, etc...
 *
 * @since 3.0.0
 *
 * @param array $props
 *
 * @return array
 */
function e_update_course_meta_data_props( $props ) {

	$course_id = LP_Request::get_int( 'course_ID' );

	if ( get_post_type( $course_id ) !== LP_COURSE_CPT ) {
		return $props;
	}

	// Co-Instructor
	//var_dump( e_is_active_co_instructor() );

	if ( e_is_active_co_instructor() ) {
		delete_post_meta( $course_id, '_lp_co_teacher' );
		if ( isset( $_POST['_lp_co_teacher'] ) ) {
			foreach ( $_POST['_lp_co_teacher'] as $co_id ) {
				add_post_meta( $course_id, '_lp_co_teacher', $co_id );
			}
		}
	}

	// Content drip
	if ( isset( $_POST['nonce_content-drip'] ) ) {
		$props[] = '_lp_content_drip_enable';
		$props[] = '_lp_content_drip_drip_type';
	}

	// Prerequisites
	if ( isset( $_POST['_lp_prerequisite_allow_purchase'] ) ) {
		$props[] = '_lp_prerequisite_allow_purchase';

		delete_post_meta( $course_id, '_lp_course_prerequisite' );

		if ( isset( $_POST['_lp_course_prerequisite'] ) ) {
			foreach ( $_POST['_lp_course_prerequisite'] as $prerequisite_id ) {
				add_post_meta( $course_id, '_lp_course_prerequisite', $prerequisite_id );
			}
		}
	}

	// Membership
	if ( e_is_active_pmp() ) {
		delete_post_meta( $course_id, '_lp_pmpro_levels' );
		if ( isset( $_POST['_lp_pmpro_levels'] ) ) {
			foreach ( $_POST['_lp_pmpro_levels'] as $c_level ) {
				add_post_meta( $course_id, '_lp_pmpro_levels', $c_level );
			}
		}
	}

	if ( e_is_active_bbpress() && ! empty( $_POST['nonce_course_bbpress'] ) ) {
		$props = array_merge( $props, array(
				'_lp_bbpress_forum_enable',
				'_lp_course_forum',
				'_lp_bbpress_forum_enrolled_user'
			)
		);
	}

	//Coming Soon
	if ( e_is_active_coming_soon() && ! empty( $_POST['nonce_course_coming_soon'] ) ) {
		$props = array_merge( $props, array(
				'_lp_coming_soon',
				'_lp_coming_soon_msg',
				'_lp_coming_soon_end_time',
				'_lp_coming_soon_countdown',
				'_lp_coming_soon_showtext',
				'_lp_coming_soon_metadata',
				'_lp_coming_soon_details',
			)
		);
	}

	// Certificates => Done

	// Gradebook

	// Random quiz

	//print_r( $_REQUEST );

	return $props;
}

add_filter( 'e-update-course-meta-data-props', 'e_update_course_meta_data_props' );

function _e_filter_course_meta_data( $value, $key, $post_id ) {

	if ( $key === '_lp_course_forum' ) {
		$value = array( 'id' => $value, 'name' => get_the_title( $post_id ) );
	}

	return $value;
}

add_filter( 'e-course-meta-data', '_e_filter_course_meta_data', 10, 3 );

/**
 * Content of settings tab for certificate displays in course.
 *
 * @since 3.0.0
 *
 * @return bool
 */
function e_tab_content_certificates() {

	if ( ! isset( frontend_editor()->post_manage ) ) {
		return false;
	}

	$post = frontend_editor()->post_manage->get_post();

	$course_cert  = LP_Certificate::get_course_certificate( $post->ID );
	$certificates = LP_Certificate::get_certificates( $course_cert );
	$user_id      = learn_press_get_current_user_id();
	?>
    <script>
        window.certificateData = window.certificates || [];
    </script>
    <fe-certificates inline-template>
        <div id="certificate-browser" class="theme-browser">
            <input type="hidden" name="course-certificate" value="<?php echo $course_cert; ?>">
            <div class="themes wp-clearfix">

				<?php if ( $certificates ) { ?>

					<?php foreach ( $certificates as $id ) {

						$certificate      = new LP_Certificate( $id );
						$certificate_data = new LP_User_Certificate( $user_id, $post->ID, $id );
						$template_id      = uniqid( $certificate->get_uni_id() );
						?>
                        <div id="certificate-<?php echo $id; ?>"
                             class="attachment theme"
                             :class="[isActive(<?php echo $certificate->get_id(); ?>) ? 'active' : '']"
                             data-active="<?php echo $id == $course_cert ? '1' : ''; ?>"
                             data-id="<?php echo $id; ?>">

                            <div class="theme-screenshot">
                                <div class="thumbnail">

                                    <div class="centered">
                                        <div id="<?php echo $template_id; ?>" class="certificate-preview">
                                            <div class="certificate-preview-inner">
                                                <img class="cert-template"
                                                     src="<?php echo $certificate->get_template(); ?>">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <h3 class="theme-name">
                                    <span>Active: </span><?php echo $certificate->get_title(); ?>
                                </h3>
                            </div>

                            <div class="theme-id-container">

                                <div class="theme-actions">
                                    <button v-if="isActive(<?php echo $certificate->get_id(); ?>)" type="button"
                                            class="e-button"
                                            @click="_remove($event, <?php echo $post->ID; ?>, <?php echo $certificate->get_id(); ?>)">
										<?php esc_html_e( 'Remove', 'learnpress-frontend-editor' ); ?>
                                    </button>
                                    <button v-if="!isActive(<?php echo $certificate->get_id(); ?>)" type="button"
                                            class="e-button"
                                            @click="_assign($event, <?php echo $post->ID; ?>, <?php echo $certificate->get_id(); ?>)">
										<?php esc_html_e( 'Assign', 'learnpress-frontend-editor' ); ?>
                                    </button>

									<?php
									/**
									 * Only show edit button if user is admin
									 */
									if ( current_user_can( 'manage_options' ) ) { ?>
                                        <button class="e-button"
                                                @click="_openEdit($event)"
                                                data-href="<?php echo esc_url( admin_url( 'post.php?post=' . $certificate->get_id() . '&action=edit' ) ); ?>">
											<?php esc_html_e( 'Edit', 'learnpress-frontend-editor' ); ?>
                                        </button>
									<?php } ?>
                                </div>
                            </div>

                            <script type="text/javascript">
                                window.certificateData.push(['#<?php echo $template_id;?>',<?php echo $certificate_data;?>]);
                            </script>

                        </div>
					<?php } ?>
				<?php } ?>

				<?php
				/**
				 * Only show edit button if user is admin
				 */
				if ( current_user_can( 'manage_options' ) ) { ?>

                <div class="attachment add-new-theme theme">

                    <div class="theme-screenshot">
                        <a target="_blank" class="thumbnail"
                           href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . LP_ADDON_CERTIFICATES_CERT_CPT ) ); ?>">
                            <span><?php esc_html_e( 'Add new Certificate', 'learnpress-certificates' ); ?></span>
                        </a>
                    </div>
					<?php } ?>
                </div>
            </div>

            <script>
                jQuery(document).on('FE.editor-rendered', function () {
                    var $ = jQuery,
                        $tab = $('#learn-press-admin-editor-metabox-settings').on('click', '.tabs-nav > .course-certificates', function ($) {
                            FE_Helpers.debounce(show, 300)();
                        }),
                        i, n, d,
                        show = function () {
                            n = window.certificateData.length;

                            for (i = n - 1; i >= 0; i--) {
                                d = window.certificateData[i];
                                try {
                                    // Force to create certificate on hidden element maybe trigger an error!
                                    if (!$(d[0]).is(':visible')) {
                                        continue;
                                    }
                                    new LP_Certificate(d[0], d[1]);
                                } catch (e) {
                                    console.log(e);
                                }
                            }
                        };
                    setTimeout(function () {
                        $tab.find('.tabs-nav > .course-certificates.active').trigger('click');
                    }, 2000)
                });

            </script>
        </div>
    </fe-certificates>
	<?php
	return true;
}

/**
 * Overwrite callback for certificates content in course settings.
 *
 * @since 3.0.0
 *
 * @param array $tabs
 *
 * @return mixed
 */
function e_tab_certificates( $tabs ) {
	if ( ! isset( frontend_editor()->post_manage ) ) {
		return $tabs;
	}

	if ( isset( $tabs['certificates'] ) ) {
		$tabs['certificates']['callback'] = 'e_tab_content_certificates';
	}

	return $tabs;
}

add_filter( 'learn-press/' . LP_COURSE_CPT . '/tabs', 'e_tab_certificates', 10.1 );


/// Content Drip
function e_filter_content_drip_meta_box( $meta_box ) {
	if ( empty( frontend_editor()->post_manage ) ) {
		return $meta_box;
	}

	$post = frontend_editor()->post_manage->get_post();

	foreach ( $meta_box['fields'] as $k => $v ) {
		if ( isset( $v['id'] ) && $v['id'] === '_lp_content_drip_drip_type' ) {
			$meta_box['fields'][ $k ]['desc'] = sprintf( '<span>%s</span>', join(
				'</span><span>',
				array(
					__( '<strong>Drip type:</strong>', 'learnpress-content-drip' ),
					__( '1. Open course item after enrolled course specific time.', 'learnpress-content-drip' ),
					__( '2. Open lesson #2 after completed lesson #1, open lesson #3 after completed lesson #2, and so on...', 'learnpress-content-drip' ),
					__( '3. Open course item after completed prerequisite items.', 'learnpress-content-drip' ),
					sprintf( '<a href="%s" target="_blank">%s</a>', add_query_arg( 'course-id', $post->ID ), __( 'Settings', 'learnpress-content-drip' ) )
				)
			) );
			$meta_box['fields'][]             = array(
				'name' => __( 'Drip Items', 'learnpress-frontend-editor' ),
				'type' => 'html',
				'html' => include LP_ADDON_FRONTEND_EDITOR_PATH . '/inc/integration/content-drip-items.php'
			);
			break;
		}
	}

	return $meta_box;
}

add_filter( 'learn-press/content-drip/settings-meta-box-args', 'e_filter_content_drip_meta_box' );

/**
 * Ajax callback to update content drip settings in course.
 *
 * @since 3.0.0
 */
function e_update_content_drip_settings() {
	$course_id = LP_Request::get_int( 'courseId' );
	$drip_type = get_post_meta( $course_id, '_lp_content_drip_drip_type', true );

// present drip item meta
	$drip_meta = get_post_meta( $course_id, '_lp_drip_items', true );

	if ( $drip_items = LP_Request::get_array( 'item-delay' ) ) {
		foreach ( $drip_items as $id => $item ) {

			if ( $drip_type == 'prerequisite' ) {
				$drip_items[ $id ]['prerequisite'] = isset( $item['prerequisite'] ) ? $item['prerequisite'] : 0;
			} else {
				$drip_items[ $id ]['prerequisite'] = isset( $drip_meta[ $id ]['prerequisite'] ) ? $drip_meta[ $id ]['prerequisite'] : 0;
			}

			if ( ( $item['type'] == 'interval' && ! $item['interval'][0] ) || ( $item['type'] == 'specific' && ! $item['date'] ) ) {
				$drip_items[ $id ]['type'] = 'immediately';
			}

			switch ( $item['type'] ) {
				case 'interval':
					$drip_items[ $id ]['interval'][2] = strtotime( '+' . learn_press_number_to_string_time( $item['interval'][0] . ' ' . $item['interval'][1] ), 0 );
					break;
				case 'specific':
					$drip_items[ $id ]['date'] = strtotime( $item['date'] );
					break;
				default:
					break;
			}
		}
	}

	update_post_meta( $course_id, '_lp_drip_items', $drip_items );
	die();
}

add_action( 'wp_ajax_e_update_content_drip_settings', 'e_update_content_drip_settings' );