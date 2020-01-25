<?php

/**
 * Class FE_Course_Editor
 *
 * @since 3.0.0
 */
class FE_Course_Editor extends FE_Editor {

	/**
	 * Post meta keys (depending on post type)
	 *
	 * @var array
	 */
	protected $item_meta_keys = array();

	/**
	 * Metabox fields
	 *
	 * @var array
	 */
	protected $item_meta_fields = array();

	/**
	 * @var LP_Course_CURD
	 */
	protected $course_curd = null;

	/**
	 * @var LP_Course_Section
	 */
	protected $section_curd = null;

	/**
	 * @var LP_Quiz_CURD
	 */
	protected $quiz_curd = null;

	/**
	 * @since 3.0.0
	 *
	 * @var LP_Question_CURD
	 */
	protected $question_curd = null;

	/**
	 * FE_Course_Editor constructor.
	 */
	protected function __construct() {
		parent::__construct();
		add_filter( 'learn_press_lesson_meta_box_args', array( $this, 'xxx' ), 10000 );
		add_filter( 'learn_press_quiz_general_meta_box', array( $this, 'xxx' ), 10000 );
		add_action( 'save_post_lp_course', array( $this, 'save_course' ) );

	}

	public function update_section_title() {
		$section_ID = LP_Request::get_int( 'section_ID' );
		$title      = LP_Request::get_string( 'title' );

		global $wpdb;

		$wpdb->update( $wpdb->learnpress_sections, array( 'section_name' => $title ), array( 'section_id' => $section_ID ), array( '%s' ), array( '%d' ) );
	}

	/**
	 * Update course meta from request when save post lp_course.
	 *
	 * @since 3.0.0
	 *
	 * @param int $post_id
	 */
	public function save_course( $post_id ) {
		if ( empty( $_POST['_e_post_nonce'] ) ) {
			return;
		}

		$course    = learn_press_get_course( $post_id );
		$meta_keys = apply_filters( 'e-update-course-meta-data-props', array(
			'_lp_duration',
			'_lp_max_students',
			'_lp_students',
			'_lp_retake_count',
			'_lp_featured',
			'_lp_block_lesson_content',
			'_lp_external_link_buy_course',
			'_lp_submission',
			'_lp_course_result',
			'_lp_course_result_final_quiz_passing_condition',
			'_lp_passing_condition',
			'_lp_price',
			'_lp_sale_price',
			'_lp_sale_start',
			'_lp_sale_end',
			'_lp_required_enroll',
			'_lp_course_author'
		), $post_id
		);

		foreach ( $meta_keys as $meta_key ) {
			$meta_value = isset( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : '';

			switch ( $meta_key ) {
				case '_lp_duration':
					$meta_value = join( ' ', $meta_value );
					break;
				case '_lp_course_result':
					if ( 'evaluate_final_quiz' === $meta_value ) {

						$api = LP_Repair_Database::instance();
						$api->sync_course_final_quiz( $course->get_id() );

						$passing_grade = LP_Request::get_string( '_lp_course_result_final_quiz_passing_condition' );
						$quiz_id       = $course->get_final_quiz();

						update_post_meta( $quiz_id, '_lp_passing_grade', $passing_grade );
					}
			}

			$meta_value = apply_filters( 'e-update-course-meta-value', $meta_value, $post_id );
			update_post_meta( $post_id, $meta_key, $meta_value );
		}

	}

	/**
	 * Store current tab user selected
	 */
	public function active_tab() {
		$post_id = LP_Request::get_int( 'course_ID' );
		$tab     = LP_Request::get_string( 'tab' );
		update_post_meta( $post_id, 'e_active_tab', $tab );
	}

	/**
	 * Read settings of an item and send to browser as JSON
	 */
	public function load_item_settings() {

		$item_id   = LP_Request::get( 'item_ID' );
		$item_type = LP_Request::get( 'item_type' );
		$is_new    = ! $item_id || ! is_numeric( $item_id );

		if ( ! $is_new ) {
			$item_type = get_post_type( $item_id );
		} else {
			$item_id = intval( $item_id );
		}

		switch ( $item_type ) {
			case LP_LESSON_CPT:
				$pt = LP_Lesson_Post_Type::instance();
				$pt->add_meta_boxes();
				break;
			case LP_QUIZ_CPT:
				$pt = LP_Quiz_Post_Type::instance();
				$pt->add_meta_boxes();
		}

		$settings = array();
		if ( $this->item_meta_fields ) {
			foreach ( $this->item_meta_fields as $field ) {
				if ( metadata_exists( 'post', $item_id, $field['id'] ) ) {
					$settings[ $field['id'] ] = get_post_meta( $item_id, $field['id'], true );
				} else {
					$settings[ $field['id'] ] = array_key_exists( 'std', $field ) ? $field['std'] : '';
				}
			}
		}
		//$settings['__FIELDS__']  = $this->item_meta_fields;
		$settings['__CONTENT__'] = $is_new ? '' : get_post_field( 'post_content', $item_id );
		$settings                = apply_filters( 'fe/ajax-load-item-settings', $settings, $item_id );

		learn_press_send_json( $settings );
	}

	/**
	 * Update item settings and post type data
	 */
	public function update_item_settings() {
		global $wpdb;
		$item_ID     = LP_Request::get( 'item_ID' );
		$settings    = LP_Request::get( 'settings' );
		$is_new      = ! is_numeric( $item_ID );
		$post_fields = array(
			'__title'   => 'post_title',
			'__content' => 'post_content',
			'__type'    => 'post_type'
		);

		$response = array(
			'id' => $item_ID
		);

		if ( $settings ) {
			$post_data = array(
				'post_status' => 'publish'
			);
			foreach ( $post_fields as $k => $post_field ) {
				if ( array_key_exists( $k, $settings ) ) {
					$post_data[ $post_field ] = $settings[ $k ];
					unset( $settings[ $k ] );
				}
			}
			//LP_Debug::startTransaction();
			if ( $post_data ) {
				if ( $is_new ) {
					$r = wp_insert_post( $post_data, true );
				} else {
					$post_data['ID'] = $item_ID;
					$r               = wp_update_post( $post_data, true );
				}

				if ( is_wp_error( $r ) ) {
					$response['result']  = 'error';
					$response['message'] = $r->get_error_message();
				} else {

					if ( $course_id = LP_Request::get( 'course_ID' ) ) {
						if ( $course = learn_press_get_course( $course_id ) ) {
							$section_id = LP_Request::get( 'section_ID' );

							if ( $is_new ) {
								$order = LP_Request::get( 'position' );
								$wpdb->insert(
									$wpdb->learnpress_section_items,
									array(
										'section_id' => $section_id,
										'item_id'    => $r,
										'item_type'  => get_post_type( $r ),
										'item_order' => absint( $order )
									),
									array( '%d', '%d', '%s', '%d' )
								);

								$this->reorder_items( $section_id, $r, $order );
							}
						}
					}

					$response['item'] = array(
						'id' => $r
					);
				}
			}
			//LP_Debug::rollbackTransaction();
			if ( $settings ) {
				foreach ( $settings as $meta_key => $meta_value ) {
					update_post_meta( $item_ID, $meta_key, $meta_value );
				}
			}
		}

		learn_press_send_json( $response );
	}

	public function update_course_section() {
		$section_ID = LP_Request::get( 'section_ID' );

		global $wpdb;
		$columns            = array(
			'section_name'        => '%s',
			'section_order'       => '%d',
			'section_description' => '%s'
		);
		$update_data        = array();
		$update_data_format = array();

		foreach ( $columns as $field => $format ) {
			if ( array_key_exists( $field, $_REQUEST ) ) {
				$update_data[ $field ] = $_REQUEST[ $field ];
				$update_data_format[]  = $format;
			}
		}

		$wpdb->update(
			$wpdb->learnpress_sections,
			$update_data,
			array( 'section_id' => $section_ID ),
			$update_data_format,
			array( '%d' )
		);

		die();
	}

	public function update_items_order() {
		$section_ID = LP_Request::get( 'section_ID' );
		$items      = LP_Request::get( 'items' );

		global $wpdb;

		if ( $items ) {

			foreach ( $items as $i => $id ) {
				$wpdb->update(
					$wpdb->learnpress_section_items,
					array( 'item_order' => $i + 1 ),
					array( 'section_id' => $section_ID, 'item_id' => $id ),
					array( '%d' ),
					array( '%d' )
				);
			}
		}

		$new_items = $wpdb->get_col( $wpdb->prepare( "SELECT item_id FROM {$wpdb->learnpress_section_items} WHERE section_id=%d ORDER BY item_order ASC", $section_ID ) );

		learn_press_send_json( array(
			'section_ID'    => $section_ID,
			'items'         => $items,
			'updated_items' => $new_items
		) );
	}

	public function update_section() {
		$context   = LP_Request::get( 'context' );
		$course_ID = LP_Request::get( 'course_ID' );
		$section   = LP_Request::get( 'section' );
		$result    = false;
		//LP_Debug::startTransaction();
		switch ( $context ) {
			case 'title':

				$section_data = array(
					'section_course_id'   => $course_ID,
					'section_description' => '',
					'section_name'        => ! empty( $section['title'] ) ? $section['title'] : '',
					'items'               => array(),
				);

				if ( isset( $section['id'] ) && is_numeric( $section['id'] ) ) {
					$section_data['section_id'] = $section['id'];
					$result                     = $this->section_curd( $course_ID )->update( $section_data );
				} else {
					$result = $this->section_curd( $course_ID )->create( $section_data );
				}

		}

		//LP_Debug::rollbackTransaction();
		learn_press_send_json( $result );

		die();
	}

	public function delete_course_item() {
		$course_ID  = LP_Request::get( 'course_ID' );
		$item_ids   = LP_Request::get( 'item_ID' );
		$section_id = LP_Request::get( 'section_ID' );
		$trash_item = LP_Request::get( 'trash' ) === 'yes';

		global $wpdb;
		settype( $item_ids, 'array' );
		$format_items = array_fill( 0, sizeof( $item_ids ), '%d' );
		$query_args   = array_merge( array( $section_id ), $item_ids );

		LP_Debug::startTransaction();

		$curd = new LP_Course_CURD();

		foreach ( $item_ids as $item_id ) {

			if ( $trash_item ) {
				wp_trash_post( $item_id );
			} else {
				$curd->remove_item( $item_id, $course_ID );
			}
		}

		$query          = $wpdb->prepare( "SELECT item_id FROM {$wpdb->learnpress_section_items} WHERE section_id = %d AND item_id IN(" . join( ',', $format_items ) . ")", $query_args );
		$un_deleted_ids = $wpdb->get_col( $query );

		if ( $un_deleted_ids ) {
			$deleted_items = array_diff( $item_ids, $un_deleted_ids );
		} else {
			$deleted_items = $item_ids;
		}

		$item_ids      = array_map( 'absint', $item_ids );
		$deleted_items = array_map( 'absint', $deleted_items );

		LP_Debug::rollbackTransaction();

		learn_press_send_json(
			array(
				'item_ID'       => $item_ids,
				'section_ID'    => $section_id,
				'deleted_items' => $deleted_items
			)
		);
	}

	public function add_course_item() {
		$course_ID  = LP_Request::get( 'course_ID' );
		$section_ID = LP_Request::get( 'section_ID' );
		$item       = LP_Request::get( 'item' );
		$result     = false;

		$result = $this->_add_course_item( $item, $section_ID, $course_ID );

		learn_press_send_json( $result );
		die();
	}

	public function add_course_items() {
		$course_ID  = LP_Request::get( 'course_ID' );
		$section_ID = LP_Request::get( 'section_ID' );
		$items      = LP_Request::get( 'items' );
		$position   = LP_Request::get( 'position' );
		$result     = false;
		$items      = array_reverse( $items );

		foreach ( $items as $item ) {
			$item_type = get_post_type( $item );

			$this->section_curd( $course_ID )->add_items_section( $section_ID, array(
				array(
					'id'   => $item,
					'type' => $item_type
				)
			) );

			$this->reorder_items( $section_ID, $item, absint( $position ) );
			$position ++;
//			$result = $this->_add_course_item(
//				array(
//					'id' => $item,
//					'position' => $position
//				),
//				$section_ID,
//				$course_ID
//			);
		}

		learn_press_send_json( $result );
		die();
	}

	protected function _add_course_item( $item, $section_ID, $course_ID ) {
		$itemData = wp_parse_args( $item, array( 'id' => 0, 'post_status' => 'publish' ) );
		$result   = array();

		$isNew = ! ( $itemData['id'] && is_numeric( $itemData['id'] ) );

		if ( $isNew ) {
			$itemData['temp_id'] = $itemData['id'];
			unset( $itemData['id'] );
			if ( $new_items = $this->section_curd( $course_ID )->new_item( $section_ID, $itemData ) ) {

				foreach ( $new_items as $new_item ) {
					if ( ! isset( $new_item['temp_id'] ) ) {
						continue;
					}

					if ( $new_item['temp_id'] === $itemData['temp_id'] ) {
						$result['new_item'] = $new_item;
						break;
					}
				}

				update_post_meta( $result['new_item']['id'], '_lp_duration', '30 minute' );

				if ( array_key_exists( 'position', $itemData ) && ! empty( $result['new_item'] ) ) {
					$this->reorder_items( $section_ID, $result['new_item']['id'], absint( $itemData['position'] ) );
				}

				//$result = $new_items;
			}
		} else {

		}

		return $result;
	}

	/**
	 * Reorder item in a section.
	 *
	 * @param int $section_id
	 * @param int $item_id
	 * @param int $order
	 */
	public function reorder_items( $section_id, $item_id, $order ) {
		global $wpdb;

		$query = $wpdb->prepare( "
			SELECT section_item_id, item_id 
			FROM {$wpdb->learnpress_section_items} si 
			INNER JOIN {$wpdb->posts} p ON p.ID = si.item_id
			WHERE section_id = %d
			AND p.post_status = %s
			AND item_id <> %d
			ORDER BY item_order, section_item_id ASC
		", $section_id, 'publish', $item_id );
		$items = $wpdb->get_results( $query );

		if ( $items ) {
			$_order = 1;
			foreach ( $items as $i => $item ) {

				if ( $order == $_order ) {
					$wpdb->update(
						$wpdb->learnpress_section_items,
						array( 'item_order' => $_order ),
						array( 'section_id' => $section_id, 'item_id' => $item_id ),
						array( '%d' ),
						array( '%d', '%d' )
					);
					$_order ++;
				}

				$wpdb->update(
					$wpdb->learnpress_section_items,
					array( 'item_order' => $_order ),
					array( 'section_item_id' => $item->section_item_id ),
					array( '%d' ),
					array( '%d' )
				);

				$_order ++;
			}
		}

	}

	/**
	 * Get course curd.
	 *
	 * @since 3.0.0
	 *
	 * @return LP_Course_CURD
	 */
	public function course_curd() {
		if ( ! $this->course_curd ) {
			$this->course_curd = new LP_Course_CURD();
		}

		return $this->course_curd;
	}

	/**
	 * Get course section curd.
	 *
	 * @since 3.0.0
	 *
	 * @param int $course_id
	 *
	 * @return LP_Section_CURD
	 */
	public function section_curd( $course_id ) {
		if ( ! $this->section_curd ) {
			$this->course_curd = array();
		}

		if ( empty( $this->course_curd[ $course_id ] ) ) {
			$this->section_curd[ $course_id ] = new LP_Section_CURD( $course_id );
		}

		return $this->section_curd[ $course_id ];
	}

	/**
	 * @return LP_Quiz_CURD|null
	 */
	public function quiz_curd() {
		if ( ! $this->quiz_curd ) {
			$this->quiz_curd = new LP_Quiz_CURD();
		}

		return $this->quiz_curd;
	}

	public function question_curd() {
		if ( ! $this->question_curd ) {
			$this->question_curd = new LP_Question_CURD();
		}

		return $this->question_curd;
	}

	/**
	 * Re-ordering sections
	 *
	 * @since 3.0.0
	 */
	public function update_sections_order() {
		$course_ID  = LP_Request::get( 'course_ID' );
		$section_ID = LP_Request::get( 'section_ID' );
		$sections   = LP_Request::get( 'sections' );

		global $wpdb;

		if ( $sections ) {
			foreach ( $sections as $i => $id ) {
				$wpdb->update(
					$wpdb->learnpress_sections,
					array( 'section_order' => $i + 1 ),
					array( 'section_course_id' => $course_ID, 'section_id' => $id ),
					array( '%d' ),
					array( '%d' )
				);
			}
		}

		$new_sections = $wpdb->get_col( $wpdb->prepare( "SELECT section_id FROM {$wpdb->learnpress_sections} WHERE section_course_id = %d ORDER BY section_order ASC", $course_ID ) );

		learn_press_send_json( array(
			'course_ID'        => $course_ID,
			'sections'         => $sections,
			'updated_sections' => $new_sections
		) );
	}

	public function update_hidden_sections() {
		$course_ID = LP_Request::get( 'course_ID' );
		$sections  = LP_Request::get( 'sections' );

		update_post_meta( $course_ID, '_admin_hidden_sections', $sections );
	}

	public function modal_query_items() {
		$term       = (string) ( stripslashes( learn_press_get_request( 'term' ) ) );
		$type       = (string) ( stripslashes( learn_press_get_request( 'type' ) ) );
		$context    = (string) ( stripslashes( learn_press_get_request( 'context' ) ) );
		$context_id = (string) ( stripslashes( learn_press_get_request( 'context_id' ) ) );
		$paged      = (string) ( stripslashes( learn_press_get_request( 'paged' ) ) );
		$filters    = LP_Request::get( 'filters' );
		$exclude    = LP_Request::get( 'exclude' );

		if ( ! $type ) {
			$type = LP_LESSON_CPT;
		}

		if ( ! class_exists( 'LP_Modal_Search_Items' ) ) {
			include_once LP_PLUGIN_PATH . 'inc/admin/class-lp-modal-search-items.php';
		}

		if ( $filters['filterBy'] ) {
			LP()->session->set( 'modal_search_items_filters', $filters );
			add_filter( 'learn-press/modal-search-items/args', array( $this, '_filter_query_args' ) );
		}

		$limit = $filters['itemsPerPage'];

		$search   = new LP_Modal_Search_Items( compact( 'term', 'type', 'context', 'context_id', 'paged', 'exclude', 'limit' ) );
		$id_items = $search->get_items();

		$items = array();
		foreach ( $id_items as $id ) {
			$post = get_post( $id );

			$items[] = array(
				'id'    => $post->ID,
				'title' => $post->post_title,
				'type'  => $post->post_type,
			);
		}

		learn_press_send_json( array(
			'items'      => $items,
			'pagination' => $search->get_pagination( false )
		) );
	}

	public function add_questions() {
		$quiz_ID   = LP_Request::get( 'quiz_ID' );
		$questions = LP_Request::get( 'questions' );
		$results   = array(
			'questions' => array()
		);

		foreach ( $questions as $question_ID ) {
			$this->quiz_curd()->add_question( $quiz_ID, $question_ID );
			$question                             = LP_Question::get_question( $question_ID );
			$results['questions'][ $question_ID ] = array(
				'answers' => $question->get_answers()->to_array(),
				'type'    => $question->get_type()
			);
		}

		learn_press_send_json( $results );
	}

	public function update_section_items() {
		$section_ID = LP_Request::get( 'section_ID' );
		$items      = LP_Request::get( 'items' );
	}

	public function update_question() {
		///LP_Debug::startTransaction();
		$quiz_ID       = LP_Request::get( 'quiz_ID' );
		$question_data = LP_Request::get( 'question' );
		$context       = LP_Request::get( 'context' );
		$is_new        = empty( $question_data['id'] ) || ! is_numeric( $question_data['id'] );
		$result        = array( 'result' => 'success' );
		try {
			if ( $is_new ) {
				$post_data = array(
					'post_title'  => $question_data['title'],
					'post_type'   => LP_QUESTION_CPT,
					'post_status' => 'publish'
				);

				$question_ID = wp_insert_post( $post_data, true );

				if ( is_wp_error( $question_ID ) ) {
					throw new Exception( $question_ID->get_error_message(), $question_ID->get_error_code() );
				}

				$result['temp_id'] = $question_data['id'];

			} else {
				$question_ID = $question_data['id'];
				if ( get_post_type( $question_ID ) !== LP_QUESTION_CPT ) {
					throw new Exception( __( 'Question invalid', 'learnpress-frontend-editor' ) );
				}
			}

			if ( ! empty( $question_data['type'] ) ) {
				update_post_meta( $question_ID, '_lp_type', $question_data['type'] );
			}

			if ( $quiz_ID ) {
				$this->quiz_curd()->add_question( $quiz_ID, $question_ID );
			}

			switch ( $context ) {
				case 'title':
				case 'content':
					$postarr = array(
						'ID'              => $question_ID,
						"post_{$context}" => $question_data[ $context ]
					);

					if ( ! $is_new ) {
						wp_update_post( $postarr );
					} else {
					}
					break;
				case 'change-type':
					$question = learn_press_get_question( $question_ID );
					if ( $newQuestion = $this->question_curd()->change_question_type( $question, $question_data['newType'], $question->get_data( 'answer_options' ) ) ) {
						$result['answers'] = e_get_question_answers_array( $newQuestion );
					}
					break;
				case 'update-answers':
					$question_answer_ids           = $this->update_question_answers( $question_ID, $question_data['answers'] );
					$result['question_answer_ids'] = $question_answer_ids;
					break;
				case 'settings':
				default:
					if ( ! empty( $question_data['settings'] ) ) {
						foreach ( $question_data['settings'] as $field => $stt ) {
							update_post_meta( $question_ID, $field, $stt );
						}
					}
					break;
			}

			$result['id'] = $question_ID;

			if ( $is_new ) {
				$question          = LP_Question::get_question( $question_ID );
				$defaultAnswers    = $question->get_default_answers();
				$result['answers'] = array();

				global $wpdb;
				foreach ( $defaultAnswers as $k => $answer ) {
					unset( $answer['question_answer_id'] );
					$wpdb->insert(
						$wpdb->learnpress_question_answers,
						array(
							'question_id'  => $question_ID,
							'answer_data'  => serialize( $answer ),
							'answer_order' => $k + 1
						)
					);
				}

				$result['answers'] = e_get_question_answers_array( $question );// $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->learnpress_question_answers} WHERE question_id = %d", $question_ID ) );
			}
		}
		catch ( Exception $ex ) {
			$result['result']  = 'error';
			$result['message'] = $ex->getMessage();
		}

		//LP_Debug::rollbackTransaction();
		learn_press_send_json( $result );
	}

	public function update_question_answers( $question_ID, $answers ) {
		global $wpdb;

		if ( ! $answers || sizeof( $answers ) === 0 ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->learnpress_question_answers} WHERE question_id = %d", $question_ID ) );
		} else {

			$old_answer_ids = $wpdb->get_col( $wpdb->prepare( "SELECT question_answer_id FROM {$wpdb->learnpress_question_answers} WHERE question_id = %d", $question_ID ) );
			$new_answer_ids = @wp_list_pluck( $answers, 'question_answer_id' );


			$update_answer_ids  = array_intersect( $old_answer_ids, $new_answer_ids );
			$delete_answer_ids  = array_diff( $old_answer_ids, $new_answer_ids );
			$updated_answer_ids = array();
			$order              = 1;

			foreach ( $answers as $k => $answer ) {
				$new_id      = ! empty( $answer['question_answer_id'] ) ? $answer['question_answer_id'] : 0;
				$update_id   = $new_id;
				$answer_data = array(
					'text'    => $answer['text'],
					'value'   => $answer['value'],
					'is_true' => $answer['is_true']
				);

				if ( ! in_array( $new_id, $update_answer_ids ) ) {
					if ( $delete_answer_ids ) {
						$update_id = array_shift( $delete_answer_ids );
						$wpdb->update(
							$wpdb->learnpress_question_answers,
							array(
								'question_id'  => $question_ID,
								'answer_data'  => serialize( $answer_data ),
								'answer_order' => $order ++
							),
							array( 'question_answer_id' => $update_id ),
							array( '%d', '%s', '%d' ),
							array( '%d' )
						);
					} else {
						$wpdb->insert(
							$wpdb->learnpress_question_answers,
							array(
								'question_id'  => $question_ID,
								'answer_data'  => serialize( $answer_data ),
								'answer_order' => $order ++
							),
							array( '%d', '%s', '%d' )
						);
						$update_id = $wpdb->insert_id;
					}
				} else {
					$wpdb->update(
						$wpdb->learnpress_question_answers,
						array(
							'question_id'  => $question_ID,
							'answer_data'  => serialize( $answer_data ),
							'answer_order' => $order ++
						),
						array( 'question_answer_id' => $new_id ),
						array( '%d', '%s', '%d' ),
						array( '%d' )
					);
				}
				$updated_answer_ids[ $new_id ] = $update_id;
			}

			if ( $delete_answer_ids = array_diff( $old_answer_ids, $updated_answer_ids ) ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->learnpress_question_answers} WHERE question_answer_id IN(" . join( ',', $delete_answer_ids ) . ") AND question_id = %d", $question_ID ) );
			}

			return $updated_answer_ids;
		}

		return array();
	}

	public function update_questions_order() {
		global $wpdb;
		$quiz_ID   = LP_Request::get( 'quiz_ID' );
		$questions = LP_Request::get( 'questions' );

		$query = "UPDATE {$wpdb->learnpress_quiz_questions} SET question_order = %d WHERE quiz_id = %d AND question_id = %d";

		foreach ( $questions as $order => $question_ID ) {
			echo $wpdb->prepare( $query, $order + 1, $quiz_ID, $question_ID );
			$wpdb->query( $wpdb->prepare( $query, $order + 1, $quiz_ID, $question_ID ) );
		}

		die();
	}

	/**
	 * Remove course section and move all items in
	 * that section to trash
	 */
	public function remove_section() {

		$course_ID   = LP_Request::get( 'course_ID' );
		$section_ID  = LP_Request::get( 'section_ID' );
		$trash_items = LP_Request::get_bool( 'trash_items' );
		$item_ids    = array();

		$curd = $this->section_curd( $course_ID );

		// If enable trash items
		if ( $trash_items ) {
			$item_ids = $curd->read_items( $section_ID );
		}

		$curd->delete( $section_ID );

		// Have items in sections?
		if ( $item_ids ) {
			foreach ( $item_ids as $item_id ) {
				wp_trash_post( $item_id );
			}
		}

		learn_press_send_json( $_REQUEST );
		die();
	}

	/**
	 * Filter modal search items query args
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function _filter_query_args( $args ) {
		$filters = LP()->session->get( 'modal_search_items_filters' );

		if ( $filters['filterBy'] === 'name' ) {
			$args['orderby'] = 'parent title';
		} else {
			$args['orderby'] = 'date';
		}

		$args['order'] = $filters['filterOrder'];

		return $args;
	}

	/**
	 * Turn item is preview on or off
	 */
	public function toggle_item_preview() {
		$item_ID    = LP_Request::get( 'item_ID' );
		$is_preview = LP_Request::get( 'isPreview' );

		update_post_meta( $item_ID, '_lp_preview', $is_preview );
		die();
	}

	public function update_questionx() {
		print_r( $_REQUEST );
		die();
	}

	public function xxx( $meta_box ) {
		$this->item_meta_keys   = wp_list_pluck( $meta_box['fields'], 'id' );
		$this->item_meta_fields = $meta_box['fields'];

		return $meta_box;
	}


	public static function trash_course() {
		$course_ID   = LP_Request::get_int( 'course_ID' );
		$permanently = LP_Request::get_bool( 'permanently' );
		$deleteItems = LP_Request::get( 'deleteItems' );
		//LP_Debug::startTransaction();

		if ( $deleteItems ) {
			$curd = new LP_Course_CURD();
			if ( $items = $curd->read_course_items( $course_ID, false ) ) {
				foreach ( $items as $item ) {
					if ( $deleteItems === 'trash' ) {
						$i = wp_trash_post( $item->id );
					} elseif ( $deleteItems === 'permanently' ) {
						$i = wp_delete_post( $item->id );
					}
				}
			}
		}

		if ( $permanently ) {
			$deleted = wp_delete_post( $course_ID );
		} else {
			$deleted = wp_trash_post( $course_ID );
		}

		//LP_Debug::rollbackTransaction();
		learn_press_send_json(
			array(
				'delete' => $deleted
			)
		);
		die();
	}

	public static function update_course_categories() {
		$courseID   = LP_Request::get_int( 'course_ID' );
		$categories = LP_Request::get( 'categories' );

		foreach ( $categories as $category ) {
			$category = absint( $category );

			if ( ! term_exists( $category, 'course_category' ) ) {
				echo "X=" . $category;
				continue;
			}

			wp_add_object_terms( $courseID, $category, 'course_category' );
		}

		// Remove terms
		$exists_terms = wp_get_object_terms( $courseID, 'course_category', array( 'fields' => 'ids' ) );
		if ( $remove_terms = array_diff( $exists_terms, $categories ) ) {
			foreach ( $remove_terms as $term ) {
				wp_remove_object_terms( $courseID, $term, 'course_category' );
			}
		}

		die();
	}

	public static function add_new_category() {
		$courseId = LP_Request::get_int( 'course_ID' );
		$category = LP_Request::get( 'category' );


		$added_cat = wp_insert_term(
			$category['name'],
			'course_category',
			array(
				'parent' => $category['parent']
			)
		);

		if ( ! is_wp_error( $added_cat ) ) {
			wp_add_object_terms( $courseId, $added_cat['term_id'], 'course_category' );
			$term = get_term( $added_cat['term_id'] );
			learn_press_send_json(
				array(
					'cat'  => array(
						'id'     => $term->term_id,
						'name'   => $term->name,
						'parent' => $term->parent

					),
					'cats' => e_get_all_category( array(
						'group'    => true,
						'selected' => wp_get_object_terms( $courseId, 'course_category', array( 'fields' => 'ids' ) )
					) )
				)
			);
		}

		die( 0 );
	}

	/**
	 * Handles request to remove a question outside quiz.
	 *
	 * @since 3.0.0
	 */
	public static function remove_question() {
		$quiz_ID     = LP_Request::get_int( 'quiz_ID' );
		$question_ID = LP_Request::get_int( 'question_ID' );
		$trash       = LP_Request::get_bool( 'trash' );
		LP_Debug::startTransaction();

		$response = array();
		$curd     = new self();

		$deleted = $curd->quiz_curd()->remove_questions( $quiz_ID, $question_ID );

		if ( $deleted && $trash ) {
			wp_trash_post( $question_ID );
		}

		if ( $deleted ) {
			$response['result']      = 'success';
			$response['question_ID'] = $question_ID;
			$response['quiz_ID']     = $quiz_ID;
		}

		LP_Debug::rollbackTransaction();

		learn_press_send_json( $response );
		die();
	}

	/**
	 * Handles request to update settings of a question.
	 *
	 * @since 3.0.0
	 */
	public static function update_question_settings() {
		$question_ID = LP_Request::get( 'question_ID' );
		$settings    = LP_Request::get( 'settings' );

		foreach ( $settings as $k => $v ) {
			update_post_meta( $question_ID, $k, $v );
		}
	}

	/**
	 * Restore a course from trash
	 *
	 * @since 3.0.0
	 */
	public static function restore_course() {
		$course_ID = LP_Request::get_int( 'course_ID' );

		if ( ! current_user_can( 'delete_post', $course_ID ) ) {
			learn_press_send_json( array(
				'result'  => 'error',
				'message' => __( 'Forbidden access', 'learnpress-frontend-editor' )
			) );
		}

		$post = wp_untrash_post( $course_ID );

		if ( $post ) {
			learn_press_send_json( array(
				'result'    => 'success',
				'course_ID' => $course_ID
			) );
		}

		learn_press_send_json( array(
			'result' => 'error'
		) );
	}

	public static function update_post_meta() {
		$post_ID    = LP_Request::get_int( 'post_ID' );
		$post_metas = LP_Request::get( 'postMeta' );

		if ( ! $post_ID || ! self::user_can( $post_ID, 'edit_posts' ) ) {
			die( '-1' );
		}

		if ( ! $post_metas ) {
			die( '-2' );
		}

		foreach ( $post_metas as $key => $value ) {
			update_post_meta( $post_ID, $key, $value );
		}

		die();
	}

	public static function user_can( $postId, $perms ) {
		if ( is_array( $perms ) ) {
			foreach ( $perms as $perm ) {
				if ( ! self::user_can( $postId, $perm ) ) {
					return false;
				}
			}

			return true;
		}

		return current_user_can( $perms, $postId );
	}

	/**
	 * Update post field with name and value from request.
	 *
	 * @since 3.0.0
	 */
	public static function update_post() {
		$post_ID     = LP_Request::get_int( 'post_ID' );
		$prop        = LP_Request::get( 'prop' );
		$propContent = LP_Request::get( 'propContent' );
		$mapFields   = array(
			'title'   => 'post_title',
			'content' => 'post_content'
		);

		if ( ! empty( $mapFields[ $prop ] ) ) {
			$prop = $mapFields[ $prop ];
		}

		$postArr = array(
			'ID'  => $post_ID,
			$prop => $propContent
		);

		$return   = wp_update_post( $postArr, true );
		$response = array();

		if ( is_wp_error( $return ) ) {
			$response['result']  = 'error';
			$response['message'] = $return->get_error_message();
		} else {
			$response['result']  = 'success';
			$response['message'] = __( 'Updated', 'learnpress-frontend-editor' );
		}

		self::send_response_activity( $response );
	}

	public static function update_course() {
		if ( ! $nonce = LP_Request::get( '_e_post_nonce' ) ) {
			die( "Invalid nonce!" );
		}

		if ( ! wp_verify_nonce( $nonce, 'e_save_post' ) ) {
			die( "Invalid nonce!" );
		}

		$post_id      = LP_Request::get_int( 'post_ID' );
		$post_status  = LP_Request::get( 'post_status' );
		$post_title   = LP_Request::get( 'post_title' );
		$post_content = LP_Request::get( 'post_content' );
		$post_name    = LP_Request::get( 'post_name' );

		$post_data = array(
			'ID'           => $post_id,
			'post_status'  => ! in_array( $post_status, array( 'publish', 'pending' ) ) ? 'draft' : $post_status,
			'post_title'   => $post_title,
			'post_content' => $post_content,
			'post_name'    => $post_name
		);

		$post_id = wp_update_post( $post_data, true );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			if ( ( $thumbnail_id = LP_Request::get( '_thumbnail_id' ) ) > 0 ) {
				update_post_meta( $post_id, '_thumbnail_id', $thumbnail_id );
			} else {
				delete_post_thumbnail( $post_id );
			}
		}

		global $wpdb;
		$json = array( 'meta' => array() );
		if ( $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->postmeta} WHERE post_id = %d", $post_id ) ) ) {
			foreach ( $rows as $row ) {
				$json['meta'][ $row->meta_key ] = apply_filters( 'e-course-meta-data', get_post_meta( $post_id, $row->meta_key, true ), $row->meta_key, $post_id );
			}
		}

		learn_press_send_json( $json );

		die();
	}

	public static function send_response_activity( $response ) {

		if ( isset( $response['result'] ) && isset( $response['message'] ) ) {
			$response['__activity'] = array(
				'message' => $response['message'],
				'type'    => $response['result']
			);
		} elseif ( isset( $response['result'] ) ) {
			$response['__activity'] = true;
		} elseif ( isset( $response['message'] ) ) {
			$response['__activity'] = $response['message'];
		}

		learn_press_send_json( $response );
	}
}