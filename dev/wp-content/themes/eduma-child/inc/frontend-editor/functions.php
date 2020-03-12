<?php
/**
 * @author Deepen.
 * @created_on 7/2/19
 */

function dcd_convertYoutube($string) {
	return preg_replace(
		"/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
		"<iframe width=\"100%\" height=\"500\" src=\"//www.youtube.com/embed/$2\" allowfullscreen></iframe>",
		$string
	);
}

add_filter( 'e-course-editor-tabs', 'dcd_e_get_course_editor_tabs' );
function dcd_e_get_course_editor_tabs( $tabs ) {
	$tabs[0]['name']     = "Step 1: Create Overview";
	$tabs[1]['name']     = "Step 2: Create Curriculum";
	$tabs[2]['name']     = "Step 3: Price & Grade";
	$tabs[2]['callback'] = "dcd_e_course_editor_tab_settings";

	return $tabs;
}

function dcd_e_course_editor_tab_settings() {
	global $frontend_editor, $wp_meta_boxes, $post;

	$post_manage = $frontend_editor->post_manage;
	$load_screen = false;

	$post = $post_manage->get_post();
	setup_postdata( $post );

	if ( ! isset( $GLOBALS['current_screen'] ) ) {
		$GLOBALS['current_screen'] = WP_Screen::get( $post_manage->get_post_type() );
		$load_screen               = true;
	}

	/////////////
	// Tell wp load metaboxes for post type.
	do_action( 'load-post.php' );
	do_action( 'add_meta_boxes', $post_manage->get_post_type(), $post );

	// Show metaboxes
	//do_meta_boxes( $post_manage->get_post_type(), 'normal', '' );
	//do_meta_boxes( $post_manage->get_post_type(), 'advanced', '' );

	$default_tabs = array(
		'payment'    => new RW_Meta_Box( LP_Course_Post_Type::payment_meta_box() ),
		'assessment' => new RW_Meta_Box( LP_Course_Post_Type::assessment_meta_box() )
	);

	$tabs = apply_filters( 'learn-press/admin-course-tabs', $default_tabs );
	$tabs = apply_filters( 'learn-press/lp_course/tabs', $tabs );

	unset( $tabs['woo-payment-tax'] );

	/**
	 * Don't know why the global $post has been reset to main post
	 * so, we need to setup it to current course again.
	 */
	$post = $post_manage->get_post();
	setup_postdata( $post );

	if ( ! $tabs ) {
		return;
	}

	$current_tab = ! empty( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : '';
	?>
    <div id="learn-press-admin-editor-metabox-settings" class="learn-press-tabs vertical initialize">
		<?php
		$remove_meta_boxes = array();
		foreach ( $tabs as $k => $tab ) {
			if ( is_array( $tab ) ) {
				$tab = wp_parse_args( $tab, array(
					'title'    => '',
					'id'       => '',
					'callback' => '',
					'meta_box' => '',
					'icon'     => ''
				) );
				if ( $tab['meta_box'] ) {
					call_user_func( $tab['callback'] );

					$page     = get_post_type();
					$contexts = array( 'normal', 'advanced' );
					foreach ( $contexts as $context ) {
						if ( isset( $wp_meta_boxes[ $page ][ $context ] ) ) {
							foreach ( array( 'high', 'sorted', 'core', 'default', 'low' ) as $priority ) {
								if ( isset( $wp_meta_boxes[ $page ][ $context ][ $priority ] ) ) {
									foreach ( (array) $wp_meta_boxes[ $page ][ $context ][ $priority ] as $box ) {
										if ( false == $box || ! $box['title'] || $box['id'] != ( $tab['meta_box'] ) ) {
											continue;
										}
										ob_start();
										call_user_func( $box['callback'], $post, $box );
										$tab['content'] = ob_get_clean();
										$tab['title']   = $box['title'];
										$tab['id']      = $box['id'];
										//$tab['icon']    = ! empty( $box['icon'] ) ? $box['icon'] : '';
										unset( $wp_meta_boxes[ $page ][ $context ][ $priority ] );
										break 3;
									}
								}
							}
						}
					}
				}
			} elseif ( $tab instanceof RW_Meta_Box ) {
				$metabox = $tab;
				$tab->set_object_id( $post->ID );
				$tab = array(
					'title'    => $metabox->meta_box['title'],
					'id'       => $metabox->meta_box['id'],
					'icon'     => ! empty( $metabox->meta_box['icon'] ) ? $metabox->meta_box['icon'] : '',
					'callback' => array( $tab, 'show' )
				);

				$remove_meta_boxes[] = $metabox;
			}
			if ( empty( $tab['title'] ) ) {
				continue;
			}
			if ( empty( $tab['id'] ) ) {
				$tab['id'] = sanitize_title( $tab['title'] );
			}
			if ( empty( $current_tab ) || ( $current_tab == $tab['id'] ) ) {
				$current_tab = $tab;
			}
			$classes = array( $tab['id'], 'course-tab' );
			if ( get_post_meta( $post->ID, '_fe_current_settings_tab', true ) == $tab['id'] ) {
				$classes[] = 'active';
			}
			if ( ! empty( $tab['icon'] ) ) {
				$classes[] = 'tab-icon';
			}

			$tabs[ $k ] = $tab;
		}

		foreach ( $tabs as $tab ) {
			if ( empty( $tab['title'] ) ) {
				continue;
			}

			echo '<li id="meta-box-tab-' . $tab['id'] . '" class="' . $tab['id'] . ( get_post_meta( $post->ID, '_fe_current_settings_tab', true ) == $tab['id'] ? ' active' : '' ) . '">';
			if ( ! empty( $tab['content'] ) ) {
				echo $tab['content'];
			} elseif ( ! empty( $tab['callback'] ) && is_callable( $tab['callback'] ) ) {
				call_user_func( $tab['callback'] );
				
			} else {
				do_action( 'learn_press_meta_box_tab_content', $tab );
			}
			echo '</li>';
		}

		if ( ! empty( $remove_meta_boxes ) ) {
			$contexts = array( 'normal', 'side', 'advanced' );
			foreach ( $remove_meta_boxes as $meta_box ) {
				if ( $meta_box instanceof RW_Meta_Box ) {
					$mbox = $meta_box->meta_box;
					foreach ( $mbox['post_types'] as $page ) {
						foreach ( $contexts as $context ) {
							remove_meta_box( $mbox['id'], $page, $context );
							if ( ! empty( $wp_meta_boxes[ $page ][ $context ]['sorted'][ $mbox['id'] ] ) ) {
								$wp_meta_boxes[ $page ][ $context ]['sorted'][ $mbox['id'] ] = false;
							}
						}
					}
				}
			}
		}

		if ( is_array( $current_tab ) ) {
			echo '<input type="hidden" name="learn-press-meta-box-tab" value="' . $current_tab['id'] . '" />';
		}
		?>
    </div>
	<?php

	// Do nothing
	add_filter( 'frontend-editor/add-meta-boxes', '__return_false' );

	if ( $load_screen ) {
		unset( $GLOBALS['current_screen'] );
	}

	wp_reset_postdata();
}

/**
 * Display course info
 */
function thim_course_info() {
	$course    = LP()->global['course'];
	$course_id = get_the_ID();
	$user      = LP_Global::user();

	$course_skill_level = get_post_meta( $course_id, 'thim_course_skill_level', true );
	$course_language    = get_post_meta( $course_id, 'thim_course_language', true );
	$course_duration    = get_post_meta( $course_id, 'thim_course_duration', true );
	$course_type        = get_post_meta( $course_id, '_course_type', true );
	$webinar_id         = get_post_meta( $course_id, '_webinar_ID', true );
	$webinar            = get_post_meta( $course_id, '_webinar_details', true );
	?>
    <div class="thim-course-info">
        <h3 class="title"><?php esc_html_e( 'Course Features', 'eduma' ); ?></h3>
        <ul>
			<?php if ( $user && $user->has_enrolled_course( $course_id ) && ! empty( $webinar ) && ! empty( $webinar_id ) && ! empty( $course_type ) && $course_type === "webinar" ) { ?>
                <li class="lectures-webinar">
                    <i class="fa fa-play"></i>
                    <span class="label"><?php esc_html_e( 'Webinar', 'eduma' ); ?></span>
                    <span class="value"><a href="<?php echo $webinar->join_url; ?>">Join Webinar</a></span>
                </li>
			<?php } ?>
			<?php if ( ! empty( $course_type ) && $course_type !== "webinar" ) { ?>
                <li class="lectures-feature">
                    <i class="fa fa-files-o"></i>
                    <span class="label"><?php esc_html_e( 'Lectures', 'eduma' ); ?></span>
                    <span class="value"><?php echo $course->get_curriculum_items( 'lp_lesson' ) ? count( $course->get_curriculum_items( 'lp_lesson' ) ) : 0; ?></span>
                </li>
                <li class="quizzes-feature">
                    <i class="fa fa-puzzle-piece"></i>
                    <span class="label"><?php esc_html_e( 'Quizzes', 'eduma' ); ?></span>
                    <span class="value"><?php echo $course->get_curriculum_items( 'lp_quiz' ) ? count( $course->get_curriculum_items( 'lp_quiz' ) ) : 0; ?></span>
                </li>
			<?php } ?>

			<?php if ( ! empty( $course_duration ) ): ?>
                <li class="duration-feature">
                    <i class="fa fa-clock-o"></i>
                    <span class="label"><?php esc_html_e( 'Duration', 'eduma' ); ?></span>
                    <span class="value"><?php echo $course_duration; ?></span>
                </li>
			<?php endif; ?>
			<?php if ( ! empty( $course_skill_level ) ): ?>
                <li class="skill-feature">
                    <i class="fa fa-level-up"></i>
                    <span class="label"><?php esc_html_e( 'Skill level', 'eduma' ); ?></span>
                    <span class="value"><?php echo esc_html( $course_skill_level ); ?></span>
                </li>
			<?php endif; ?>
			<?php if ( ! empty( $course_language ) ): ?>
                <li class="language-feature">
                    <i class="fa fa-language"></i>
                    <span class="label"><?php esc_html_e( 'Language', 'eduma' ); ?></span>
                    <span class="value"><?php echo esc_html( $course_language ); ?></span>
                </li>
			<?php endif; ?>
            <li class="students-feature">
                <i class="fa fa-users"></i>
                <span class="label"><?php esc_html_e( 'Students', 'eduma' ); ?></span>
				<?php $user_count = $course->get_users_enrolled() ? $course->get_users_enrolled() : 0; ?>
                <span class="value"><?php echo esc_html( $user_count ); ?></span>
            </li>
			<?php if ( ! empty( $course_type ) && $course_type !== "webinar" ) { ?>
				<?php thim_course_certificate( $course_id ); ?>
                <li class="assessments-feature">
                    <i class="fa fa-check-square-o"></i>
                    <span class="label"><?php esc_html_e( 'Assessments', 'eduma' ); ?></span>
                    <span class="value"><?php echo ( get_post_meta( $course_id, '_lp_course_result', true ) == 'evaluate_lesson' ) ? esc_html__( 'Yes', 'eduma' ) : esc_html__( 'Self', 'eduma' ); ?></span>
                </li>
			<?php } ?>
        </ul>
		<?php do_action( 'thim_after_course_info' ); ?>
    </div>
	<?php
}
