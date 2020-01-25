<?php
/**
 * Core functions used for Frontend Editor plugin.
 *
 * @author  ThimPress
 * @package LearnPress
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * @param array $meta_box
 *
 * @return mixed
 */
function e_course_item_meta_boxes( $meta_box ) {
	//$this->item_meta_keys   = wp_list_pluck( $meta_box['fields'], 'id' );
	//$this->item_meta_fields = $meta_box['fields'];
	$post_types = array();
	if ( ! empty( $meta_box['post_types'] ) ) {
		$post_types = $meta_box['post_types'];
	} elseif ( ! empty( $meta_box['pages'] ) ) {
		$post_types = $meta_box['pages'];
	}

	settype( $post_types, 'array' );
	foreach ( $post_types as $post_type ) {
		$fields = LP()->session->get( 'fe_' . $post_type . '_meta_box' );
		if ( ! $fields ) {
			$fields = array();
		}

		$field_ids = wp_list_pluck( $fields, 'id' );

		foreach ( $meta_box['fields'] as $field ) {
			if ( in_array( $field['id'], $field_ids ) ) {
				continue;
			}

			$fields[]    = $field;
			$field_ids[] = $field['id'];
		}
		LP()->session->set( 'fe_' . $post_type . '_meta_box', $fields );
	}

	return $meta_box;
}

add_filter( 'learn_press_lesson_meta_box_args', 'e_course_item_meta_boxes', 1000 );
add_filter( 'learn_press_quiz_general_meta_box', 'e_course_item_meta_boxes', 1000 );
add_filter( 'learn_press_question_meta_box_args', 'e_course_item_meta_boxes', 1000 );
add_filter( 'learn_press_assignment_attachments_meta_box', 'e_course_item_meta_boxes', 1000 );
add_filter( 'learn_press_assignment_general_settings_meta_box', 'e_course_item_meta_boxes', 1000 );

/**
 * Shutdown
 */
function e_shutdown() {
	foreach ( learn_press_get_course_item_types() as $post_type ) {
		LP()->session->remove( 'fe_' . $post_type . '_meta_box' );
	}

	LP()->session->remove( 'fe_' . LP_QUESTION_CPT . '_meta_box' );

}

add_action( 'shutdown', 'e_shutdown' );
/**
 * Get js data for course editor store
 *
 * @since 3.0.0
 *
 * @return array
 */
function e_get_course_store_data() {
	$post_manage = frontend_editor()->post_manage;
	$post        = $post_manage->get_post();

	if ( empty( $post ) ) {
		return array();
	}

	/**
	 * @var WP $wp
	 */
	global $wp;
	$settings = LP()->settings();

	$data = array(
		'course_ID'             => $post->ID,
		'coursePermalink'       => $post_manage->get_edit_post_link(),
		'courseCategories'      => '',
		'item_ID'               => ! empty( $post ) ? $post->ID : 0,
		'identify'              => 'course-editor-' . wp_create_nonce( 'course-editor' ),
		'active_tab'            => get_post_meta( $post->ID, 'e_active_tab', true ),
		'course_item_types'     => e_get_course_item_types(),
		'question_types'        => apply_filters( 'e-question-types', array(
				array(
					'type' => 'true_or_false',
					'name' => __( 'True or False', 'learnpress-frontend-editor' )
				),
				array(
					'type'     => 'single_choice',
					'name'     => __( 'Single choice', 'learnpress-frontend-editor' ),
					'supports' => array(
						'add_answer' => 'yes'
					)
				),
				array(
					'type'     => 'multi_choice',
					'name'     => __( 'Multiple choice', 'learnpress-frontend-editor' ),
					'supports' => array(
						'add_answer' => 'yes'
					)
				)
			)
		),
		'categories'            => e_get_all_category(
			array(
				'group'    => true,
				'selected' => wp_get_object_terms( $post->ID, 'course_category', array( 'fields' => 'ids' ) )
			)
		),
		'flattenCategories'     => e_get_all_category(
			array(
				'group'    => false,
				'selected' => wp_get_object_terms( $post->ID, 'course_category', array( 'fields' => 'ids' ) )
			)
		),
		'default_course_item'   => LP_LESSON_CPT,
		'sections'              => e_get_course_editor_sections( $post->ID ),
		'admin_hidden_sections' => get_post_meta( $post->ID, '_admin_hidden_sections', true ),
		'supports'              => array(
			'preview' => array( LP_LESSON_CPT )
		),
		'settings'              => array(
			'reviewCourseBeforePublish' => $settings->get( 'required_review' ),
			'enableEditPublishedCourse' => $settings->get( 'enable_edit_published' )
		),
		'courseStatus'          => get_post_status( $post->ID )
	);

	$post_type_fields = array();
	foreach ( learn_press_get_course_item_types() as $post_type ) {

		switch ( $post_type ) {
			case LP_LESSON_CPT:
				$pt = LP_Lesson_Post_Type::instance();
				$pt->add_meta_boxes();
				break;
			case LP_QUIZ_CPT:
				$pt = LP_Quiz_Post_Type::instance();
				$pt->add_meta_boxes();
				break;
			case LP_ASSIGNMENT_CPT:
				$pt = LP_Assignment_Post_Type::instance();
				$pt->add_meta_boxes();
				break;
		}

		$post_type_fields[ $post_type ] = apply_filters( 'e-post-type-fields', LP()->session->get( 'fe_' . $post_type . '_meta_box' ), $post_type );
	}//echo'<pre>';print_r($post_type_fields);die;

	$pt = LP_Question_Post_Type::instance();
	$pt->add_meta_boxes();
	$post_type_fields[ LP_QUESTION_CPT ] = apply_filters( 'e-post-type-fields', LP()->session->get( 'fe_' . LP_QUESTION_CPT . '_meta_box' ), LP_QUESTION_CPT );

	wp_reset_postdata();

	$data['post_type_fields'] = $post_type_fields;

	return apply_filters( 'e-course-data-store', $data );
}

/**
 * @return array
 */
function e_get_course_item_types() {
	$types        = learn_press_get_course_item_types();
	$icons        = array( LP_LESSON_CPT => 'dashicons dashicons-book', LP_QUIZ_CPT => 'dashicons dashicons-clock' );
	$default_icon = 'dashicons dashicons-book';

	foreach ( $types as $k => $type ) {
		if ( ! $post_type_object = get_post_type_object( $type ) ) {
			continue;
		}

		$types[ $k ] = array(
			'name'        => $post_type_object->labels->singular_name,
			'plural_name' => $post_type_object->label,
			'type'        => $type,
			'icon'        => apply_filters( 'e-course-item-type-icon', isset( $icons[ $type ] ) ? $icons[ $type ] : $default_icon ),
			'placeholder' => sprintf( __( '%s name', 'learnpress-frontend-editor' ), ucfirst( $post_type_object->labels->singular_name ) )
		);
	}

	return apply_filters( 'e-course-item-types', $types );
}

/**
 * Translating text
 *
 * @return array
 */
function e_get_localize() {
	return array(
		'confirm_trash_item'                       => __( 'Are you sure you want to move this item to trash?', 'learnpress-frontend-editor' ),
		'confirm_delete_items'                     => __( 'Are you sure you want to remove those items from course?', 'learnpress-frontend-editor' ),
		'confirm_trash_items'                      => __( 'Are you sure you want to remove those items to trash?', 'learnpress-frontend-editor' ),
		'confirm_delete_checked_answer'            => __( 'This answer was marked is TRUE. Are you sure you want to remove this answer?', 'learnpress-frontend-editor' ),
		'confirm_delete_section'                   => __( 'Do you want to remove this section?', 'learnpress-frontend-editor' ),
		'confirm_trash_items_with_section'         => __( 'Do you want to move items to trash?', 'learnpress-frontend-editor' ),
		'question_require_at_least_checked_answer' => __( 'Question requires at least one answer is checked. Continue?', 'learnpress-frontend-editor' ),
		'question_have_all_answer_checked'         => __( 'Warning! Question have all answers are checked. Continue?', 'learnpress-frontend-editor' ),
		'modal_select_question_button'             => __( 'Add to quiz', 'learnpress-frontend-editor' ),
		'modal_select_question_title'              => __( 'Select questions', 'learnpress-frontend-editor' ),
		'modal_select_button'                      => __( 'Add items', 'learnpress-frontend-editor' ),
		'confirm_remove_course_item'               => __( 'Do you want to remove this item from course?', 'learnpress-frontend-editor' ),
		'confirm_change_question_type'             => __( 'Do you want to change type of this question?', 'learnpress-frontend-editor' ),
		'confirm_trash_question_in_quiz'           => __( 'Do you want to move this question into trash?', 'learnpress-frontend-editor' ),
		'confirm_remove_question_in_quiz'          => __( 'Do you want to remove this question?', 'learnpress-frontend-editor' ),
		'confirm_trash_course'                     => __( 'Do you want to trash this course?', 'learnpress-frontend-editor' ),
	);
}

/**
 * Get course sections for editor
 *
 * @since 3.0.0
 *
 * @param int $course_id
 *
 * @return array
 */
function e_get_course_editor_sections( $course_id = 0 ) {
	if ( ! $course_id ) {
		$course_id = get_the_ID();
	}

	if ( ! $course = learn_press_get_course( $course_id ) ) {
		return array();
	}

	$sections = $course->get_curriculum_raw();

	if ( $sections ) {
		foreach ( $sections as $k => $section ) {

			if ( empty( $section['items'] ) ) {
				continue;
			}

			foreach ( $section['items'] as $v => $item ) {
				$item_settings = array();

				$fields = LP()->session->get( 'fe_' . $item['type'] . '_meta_box' );

				if ( $fields ) {

					foreach ( $fields as $field ) {
						if ( empty( $field['id'] ) ) {
							continue;
						}
						$item_settings[ $field['id'] ] = ! empty( $field['std'] ) ? $field['std'] : '';
						if ( metadata_exists( 'post', $item['id'], $field['id'] ) ) {
							$metaValue = get_post_meta( $item['id'], $field['id'], true );

							if ( in_array( $field['type'], array( 'yes-no', 'yes_no' ) ) ) {
								$metaValue = ( $metaValue === true || $metaValue == 'yes' || $metaValue === 'true' || $metaValue === 'on' || $metaValue == 1 ) ? 'yes' : 'no';
							}

							$item_settings[ $field['id'] ] = apply_filters( 'frontend-editor/item-settings/' . $field['id'], $metaValue, $item['type'], $item['id'] );
						}
					}
				}

				$item['settings'] = apply_filters( 'frontend-editor/item-settings', $item_settings, $item['type'], $item['id'] );
				$item['content']  = get_post_field( 'post_content', $item['id'] );

				if ( $item['type'] === LP_QUIZ_CPT ) {
					$item['questions'] = e_get_quiz_editor_questions( $item['id'] );
					$item['tabs']      = array(
						array( 'id' => 'settings', 'title' => __( 'Settings', 'learnpress-frontend-editor' ) ),
						array( 'id' => 'questions', 'title' => __( 'Questions', 'learnpress-frontend-editor' ) )
					);
				}

				$sections[ $k ]['items'][ $v ] = $item;
			}
		}
	}

	return $sections;
}

function e_get_quiz_editor_questions( $quiz_id ) {
	if ( ! class_exists( 'LP_Admin_Editor' ) ) {
		include_once LP_PLUGIN_PATH . '/inc/admin/editor/class-lp-admin-editor.php';
		include_once LP_PLUGIN_PATH . '/inc/admin/editor/class-lp-admin-editor-quiz.php';
	}

	$id = null;
	if ( array_key_exists( 'id', $_REQUEST ) ) {
		$id = $_REQUEST['id'];
	}
	$_REQUEST['id'] = $quiz_id;
	$editor         = new LP_Admin_Editor_Quiz();

	if ( $id !== null ) {
		$_REQUEST['id'] = $id;
	}

	if ( $questions = e_get_editor_questions( $quiz_id )/*$editor->get_questions()*/ ) {


		foreach ( $questions as $k => $question ) {
			unset( $question['settings'] );

			$question['settings'] = array(
				//'content'     => $post->post_content,
				'_lp_mark'        => get_post_meta( $question['id'], '_lp_mark', true ),
				'_lp_explanation' => get_post_meta( $question['id'], '_lp_explanation', true ),
				'_lp_hint'        => get_post_meta( $question['id'], '_lp_hint', true )
			);
			$question['type']     = $question['type']['key'];
			$questions[ $k ]      = $question;
		}
	}

	return $questions;
}

//add_filter( 'learn-press/quiz-editor/question-data', function ( $data ) {
//	$data['content'] = rand();
//
//	return $data;
//} );

function e_get_editor_questions( $quiz_id ) {


	$quiz = learn_press_get_quiz( $quiz_id );
	// list questions
	$questions = $quiz->get_questions();
	// order questions in quiz
	$question_order = learn_press_quiz_get_questions_order( $questions );
	$result         = array();
	if ( is_array( $questions ) ) {
		foreach ( $questions as $index => $id ) {

			$question = LP_Question::get_question( $id );
			$answers  = e_get_question_answers_array( $question );
			$post     = get_post( $id );
			$result[] = apply_filters( 'learn-press/quiz-editor/question-data', array(
				'id'       => $id,
				'open'     => false,
				'title'    => $post->post_title,
				'content'  => $post->post_content,
				'type'     => array(
					'key'   => $question->get_type(),
					'label' => $question->get_type_label()
				),
				'answers'  => apply_filters( 'learn-press/quiz-editor/question-answers-data', $answers, $id, $quiz->get_id() ),
				'settings' => array(
					'mark'        => get_post_meta( $id, '_lp_mark', true ),
					'explanation' => get_post_meta( $id, '_lp_explanation', true ),
					'hint'        => get_post_meta( $id, '_lp_hint', true )
				),
				'order'    => $question_order[ $index ]
			), $id, $quiz->get_id() );
		}
	}

	return apply_filters( 'learn-press/quiz/quiz_editor_questions', $result, $quiz->get_id() );
}

/**
 * @param LP_Question $question
 *
 * @return array
 */
function e_get_question_answers_array( $question ) {
	/**
	 * @var LP_Question_Answers       $answers
	 * @var LP_Question_Answer_Option $answer
	 */
	$data = array();

	if ( is_callable( array( $question, 'get_answers_to_edit' ) ) ) {
		$answers = $question->get_answers_to_edit();
	} else {
		$answers = $question->get_answers();
	}

	if ( is_callable( array( $answers, 'to_array' ) ) ) {
		$data = $answers->to_array();
	} else {
		if ( $answers ) {
			foreach ( $answers as $answer ) {
				$data[] = array_merge( $answer->get_data(), array( 'classes' => $answer->get_class() ) );
			}
		}
	}

	return $data;
}

/**
 * Get tabs for course editor
 *
 * @since 3.0.0
 *
 * @return array
 */
function e_get_course_editor_tabs() {
	return apply_filters( 'e-course-editor-tabs',
		array(
			array(
				'name'     => __( 'General', 'learnpress-frontend-editor' ),
				'id'       => 'general',
				'callback' => 'e_course_editor_tab_general'
			),
			array(
				'name'     => __( 'Curriculum', 'learnpress-frontend-editor' ),
				'id'       => 'curriculum',
				'callback' => 'e_course_editor_tab_curriculum'
			),
			array(
				'name'     => __( 'Settings', 'learnpress-frontend-editor' ),
				'id'       => 'settings',
				'callback' => 'e_course_editor_tab_settings'
			)
		)
	);
}

function e_list_post_nav() {
	LP_Frontend_Editor_Post_List_Table::instance()->page_nav();
}

/**
 *
 */
function e_course_editor_tab_general() {
	LP_Addon_Frontend_Editor::instance()->get_template( 'edit/form' );
}

function e_course_editor_tab_curriculum() {
	?>
    <e-course-curriculum :count-items="countItems()" :store="$store()"
                         :sections="sections"
                         :item="item"
                         @openItemSettings="openItemSettings"></e-course-curriculum>
	<?php
}

/**
 * Displays metaboxes added to lp-course post type.
 *
 * @since 3.0.0
 */
function e_course_editor_tab_settings() {

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
		'settings'   => new RW_Meta_Box( LP_Course_Post_Type::settings_meta_box() ),
		'assessment' => new RW_Meta_Box( LP_Course_Post_Type::assessment_meta_box() ),
		'payment'    => new RW_Meta_Box( LP_Course_Post_Type::payment_meta_box() )
	);

	if ( is_super_admin() ) {
		$default_tabs['author'] = new RW_Meta_Box( LP_Course_Post_Type::author_meta_box() );
	}

	$tabs = apply_filters( 'learn-press/admin-course-tabs', $default_tabs );
	$tabs = apply_filters( 'learn-press/lp_course/tabs', $tabs );

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
        <div class="tabs-heading">
            <h4><?php esc_html_e( 'Course Settings', 'learnpress-frontend-editor' ); ?></h4>
        </div>
        <div class="tabs-container">
            <ul class="tabs-nav">
				<?php
				$remove_meta_boxes = array();
				foreach ( $tabs as $k => $tab ) {
					if ( is_array( $tab ) ) {
						$tab = wp_parse_args(
							$tab, array(
								'title'    => '',
								'id'       => '',
								'callback' => '',
								'meta_box' => '',
								'icon'     => ''
							)
						);
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
						$tab                 = array(
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

					$classes = apply_filters( 'learn-press/admin/tab-class', $classes, $tab );
					echo sprintf( '<li class="%s" data-tab="%s">', join( ' ', $classes ), $tab['id'] );
					?>
                    <a <?php echo ! empty( $tab['icon'] ) ? 'class="' . $tab['icon'] . '"' : ''; ?>
                            href="<?php echo add_query_arg( 'tab', $tab['id'] ); ?>"><?php echo esc_html( $tab['title'] ); ?></a>
					<?php
					echo '</li>';
					$tabs[ $k ] = $tab;
				}
				?>
            </ul>
            <ul class="tabs-content-container" data-text="<?php esc_attr_e( 'Initializing...', 'learnpress' ); ?>">
				<?php
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
						} else {

						}
					}
				}

				?>
            </ul>
        </div>
		<?php
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
 * Check user permission and admin settings to allow user access to admin or not
 */
function e_prevent_accessing_admin() {
	$user = learn_press_get_current_user();

	// Don't block admin and/or doing ajax request
	if ( $user->is_admin() || defined( 'DOING_AJAX' ) ) {
		return;
	}

	$settings = LP()->settings();

	/**
	 * Accepting for specific user
	 */
	if ( get_user_meta( $user->get_id(), 'frontend_editor_enable_admin', true ) === 'yes' ) {
		return;
	}

	/**
	 * Option is turn off
	 */
	if ( $settings->get( 'frontend_editor_disable_admin' ) !== 'yes' ) {
		return;
	}

	// User is not allowed
	if ( $user->is_instructor() ) {
		wp_die( __( 'Sorry, you are not allowed to access this page.', 'learnpress-frontend-editor' ) );
	}

}

add_action( 'admin_init', 'e_prevent_accessing_admin' );


function e_extra_user_profile_fields( $user ) {
    if ( ! is_admin() ) {
        return;
    }?>
    <h3><?php _e( 'Frontend Editor', 'learnpress_frontend_editor' ); ?></h3>

    <table class="form-table">
        <tr>
            <th><?php _e( 'Enable admin accessing', 'learnpress_frontend_editor' ); ?></th>
            <td>
                <input type="hidden" name="frontend_editor_enable_admin" value="">
                <label><input type="checkbox" name="frontend_editor_enable_admin" id="frontend_editor_enable_admin"
                              value="yes"
						<?php checked( get_the_author_meta( 'frontend_editor_enable_admin', $user->ID ) === 'yes' ); ?>>
					<?php _e( "Allow this user can access to admin for editing courses.", 'learnpress-frontend-editor' ); ?>
                </label>
            </td>
        </tr>
    </table>
<?php }

add_action( 'show_user_profile', 'e_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'e_extra_user_profile_fields' );

function e_save_extra_user_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	update_user_meta( $user_id, 'frontend_editor_enable_admin', $_POST['frontend_editor_enable_admin'] );
}

add_action( 'personal_options_update', 'e_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'e_save_extra_user_profile_fields' );

/**
 * Count number of posts for a post type for an user.
 *
 * @since 3.0.0
 *
 * @param string $type
 * @param int    $user
 *
 * @return object
 */
function e_count_user_posts( $type, $user = 0 ) {
	if ( function_exists( 'learn_press_count_user_posts' ) ) {
		return learn_press_count_user_posts( $type, $user );
	}

	if ( ! $user ) {
		$user = get_current_user_id();
	}

	global $wpdb;

	if ( ! post_type_exists( $type ) ) {
		return new stdClass;
	}

	$cache_key = _count_posts_cache_key( $type, $user );

	$counts = wp_cache_get( $cache_key, 'counts' );

	if ( false !== $counts ) {
		apply_filters( 'learn-press/count_posts', $counts, $type, $user );
	}

	$query   = $wpdb->prepare( "
        SELECT post_status, COUNT( * ) AS num_posts 
        FROM {$wpdb->posts} 
        WHERE post_type = %s AND post_author = %d
        GROUP BY post_status
    ", $type, $user );
	$results = (array) $wpdb->get_results( $query, ARRAY_A );
	$counts  = array_fill_keys( get_post_stati(), 0 );

	foreach ( $results as $row ) {
		$counts[ $row['post_status'] ] = $row['num_posts'];
	}

	$counts = (object) $counts;
	wp_cache_set( $cache_key, $counts, 'counts' );

	return apply_filters( 'learn-press/count_posts', $counts, $type, $user );
}

/**
 * @return LP_Addon_Frontend_Editor
 */
function frontend_editor() {
	global $frontend_editor;

	return $frontend_editor;
}

/**
 * Update post name when ajax request to get post name from 'Permalink' box is calling
 *
 * @param array   $permalink
 * @param int     $post_id
 * @param string  $title
 * @param string  $name
 * @param WP_Post $post
 *
 * @return mixed
 */
function e_get_sample_permalink( $permalink, $post_id, $title, $name, $post ) {
	if ( empty( $_POST['e_post'] ) ) {
		return $permalink;
	}

	wp_update_post( array( 'ID' => $post_id, 'post_name' => $name ), true );

	return $permalink;
}

add_filter( 'get_sample_permalink', 'e_get_sample_permalink', 10, 5 );

function e_get_all_category( $args = array() ) {
	$categories = array();

	$args  = wp_parse_args( $args, array( 'group' => false, 'selected' => array(), 'parent' => 0 ) );
	$group = $args['group'];

	_e_get_all_category( $categories, $args, 1 );

	return $group && $categories ? $categories[0]['nodes'] : $categories;
}

function _e_get_all_category( &$categories, $args = array(), $level = 0 ) {
	$group    = $args['group'];
	$selected = (array) $args['selected'];
	$parent   = $args['parent'];

	$terms = get_terms( 'course_category', array(
		'hide_empty' => false,
		'parent'     => $parent
	) );

	if ( ! $level ) {
		$level = 0;
	}

	$level ++;

	if ( $terms ) {
		foreach ( $terms as $term ) {

			if ( $group && empty( $categories[ $parent ]['nodes'] ) ) {
				$categories[ $parent ]['nodes'] = array();
			}

			$cat = array(
				'id'       => $term->term_id,
				'name'     => $term->name,
				'deep'     => $level - 1,
				'parent'   => $term->parent,
				'selected' => in_array( $term->term_id, $selected )
			);

			if ( $group ) {
				$categories[ $parent ]['nodes'][ $term->term_id ] = $cat;
			} else {
				$categories[ $term->term_id ] = $cat;
			}

			$newArgs = array_merge( $args, array( 'parent' => $term->term_id ) );

			if ( $group ) {
				_e_get_all_category( $categories[ $parent ]['nodes'], $newArgs, $level );
			} else {
				_e_get_all_category( $categories, $newArgs, $level );
			}

		}

	}
}

/**
 * Check if current page is frontend editor.
 *
 * @since 3.0.2.1
 *
 * @return bool
 */
function e_is_frontend_editor() {
	global $wp_query;

	return ! ! $wp_query->get( 'frontend-editor' );
}

/**
 * Add edit link to frontend editor in posts manage.
 *
 * @param array $links
 *
 * @return array
 */
function e_course_row_action_links( $links ) {
	/**
	 * @var LP_Addon_Frontend_Editor             $frontend_editor
	 * @var LP_Addon_Frontend_Editor_Post_Manage $post_manage
	 */
	global $frontend_editor, $post;

	if ( LP_COURSE_CPT === get_post_type( $post ) ) {
		$post_manage = new LP_Addon_Frontend_Editor_Post_Manage( LP_COURSE_CPT, $post->ID );

		if ( get_current_user_id() == get_post_field( 'post_author', $post->ID ) ) {
			$links['editor'] = sprintf( '<a href="%s" target="_blank">%s</a>', $post_manage->get_edit_post_link( '', $post->ID ), __( 'Editor', 'learnpress-frontend-editor' ) );
		}
	}

	return $links;
}

add_filter( 'learn-press/row-action-links', 'e_course_row_action_links' );

function e_shortcode_frontend_editor() {
	ob_start();

	if ( ! is_admin() ) {
		LP_Addon_Frontend_Editor::instance()->get_template( 'dashboard-no-header' );
	}

	return ob_get_clean();
}

add_shortcode( 'frontend_editor', 'e_shortcode_frontend_editor' );

/**
 * Translate post status.
 *
 * @since 3.0.2.4
 *
 * @param int|WP_Post $post
 *
 * @return string
 */
function e_post_status( $post ) {
	$post_status = get_post_status( $post );

	switch ( $post_status ) {
		case 'publish':
			$translated_post_status = _x( 'Publish', 'post status', 'learnpress-frontend-editor' );
			break;
		case 'pending':
			$translated_post_status = _x( 'Pending', 'post status', 'learnpress-frontend-editor' );
			break;
		case 'draft':
			$translated_post_status = _x( 'Draft', 'post status', 'learnpress-frontend-editor' );
			break;
		case 'trash':
			$translated_post_status = _x( 'Trashed', 'post status', 'learnpress-frontend-editor' );
			break;
		default:
			$translated_post_status = $post_status;
	}

	return $translated_post_status;
}

/**
 * Translate post meta duration.
 *
 * @since 3.0.2.4
 *
 * @param int|WP_Post $post
 *
 * @return mixed
 */
function e_post_duration( $post = null ) {
	$post = get_post( $post );

	if ( ! $duration = get_post_meta( $post->ID, '_lp_duration', true ) ) {
		return $duration;
	}

	$t = explode( ' ', $duration );

	switch ( $t[1] ) {
		case 'min':
			$duration = $t[0] > 1 ? sprintf( _x( '%d minutes', 'post duration', 'learnpress-frontend-editor' ), $t[0] ) : sprintf( _x( '%d min', 'post duration', 'learnpress-frontend-editor' ), $t[0] );
			break;
		case 'hour':
			$duration = $t[0] > 1 ? sprintf( _x( '%d hours', 'post duration', 'learnpress-frontend-editor' ), $t[0] ) : sprintf( _x( '%d hour', 'post duration', 'learnpress-frontend-editor' ), $t[0] );
			break;
		case 'day':
			$duration = $t[0] > 1 ? sprintf( _x( '%d days', 'post duration', 'learnpress-frontend-editor' ), $t[0] ) : sprintf( _x( '%d day', 'post duration', 'learnpress-frontend-editor' ), $t[0] );
			break;
		case 'week':
			$duration = $t[0] > 1 ? sprintf( _x( '%d weeks', 'post duration', 'learnpress-frontend-editor' ), $t[0] ) : sprintf( _x( '%d week', 'post duration', 'learnpress-frontend-editor' ), $t[0] );
			break;
	}

	return $duration;
}

function wp_213dasdasda($template) {
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'editor-buttons' );

	return $template;
}

add_filter( 'template_include', 'wp_213dasdasda' );

include_once "integration.php";
include_once "conflict.php";
if ( file_exists( dirname( __FILE__ ) . '/test.php' ) ) {
	include_once 'test.php';
}