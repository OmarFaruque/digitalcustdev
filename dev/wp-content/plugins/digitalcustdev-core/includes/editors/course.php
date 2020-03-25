<?php

function clone_question() {
	$course_ID = LP_Request::get( 'course_ID' );
	$quiz_ID   = LP_Request::get( 'quiz_ID' );
	$question  = LP_Request::get( 'question' );

	$postData = array(
		'id'       => $course_ID,
		'type'     => 'clone-question',
		'quiz_id'  => $quiz_ID,
		'question' => $question
	);

	$cloner = new DCD_FE_Cloner();
	$result = $cloner->clone_question( $postData );
	learn_press_send_json( $result );
	wp_die();
}

class DCD_FE_Cloner {

	public function __construct() {
	}

	public function clone_question( $args ) {
		$question = ! empty( $args['question'] ) ? $args['question'] : false;
		$result   = array( 'result' => 'success' );
		if ( ! $question ) {
			$result['result'] = 'error';

			return $result;
		}

		$result['temp_id'] = $question['id'];
		$duplicated        = $this->duplicate_question( $question['id'], $args['quiz_id'], false );
		if ( $duplicated ) {
			$result['id']      = $duplicated;
			$question          = LP_Question::get_question( $duplicated );
			$result['answers'] = e_get_question_answers_array( $question );
		} else {
			$result['result']  = 'error';
			$result['message'] = 'Some error occured';
		}

		return $result;
	}

	private function duplicate_question( $question_id = null, $quiz_id = null, $args = array() ) {
		if ( ! $question_id || ! get_post( $question_id ) ) {
			return new WP_Error( sprintf( __( 'Question id %s does not exist.', 'learnpress' ), $question_id ) );
		}
		if ( $quiz_id && ! get_post( $quiz_id ) ) {
			return new WP_Error( sprintf( __( 'Quiz id %s does not exist.', 'learnpress' ), $quiz_id ) );
		}

		global $wpdb;
		$new_question_id = $this->duplicate_post( $question_id, $args );
		if ( $quiz_id ) {
			// learnpress_quiz_questions
			$sql                = $wpdb->prepare( "
                        SELECT * FROM $wpdb->learnpress_quiz_questions WHERE quiz_id = %d AND question_id = %d
                    ", $quiz_id, $question_id );
			$quiz_question_data = $wpdb->get_row( $sql );
			$max_order          = $wpdb->get_var( $wpdb->prepare( "SELECT max(question_order) FROM {$wpdb->prefix}learnpress_quiz_questions WHERE quiz_id = %d", $quiz_id ) );

			if ( $quiz_question_data ) {
				$wpdb->insert( $wpdb->learnpress_quiz_questions, array(
					'quiz_id'        => $quiz_id,
					'question_id'    => $new_question_id,
					'question_order' => $max_order + 1,
					'params'         => $quiz_question_data->params
				), array(
					'%d',
					'%d',
					'%d',
					'%s'
				) );
			}
		}
		// learnpress_question_answers
		$sql              = $wpdb->prepare( "
                    SELECT * FROM $wpdb->learnpress_question_answers WHERE question_id = %d
                ", $question_id );
		$question_answers = $wpdb->get_results( $sql );
		if ( $question_answers ) {
			foreach ( $question_answers as $q_a ) {
				$wpdb->insert( $wpdb->learnpress_question_answers, array(
					'question_id'  => $new_question_id,
					'answer_data'  => $q_a->answer_data,
					'answer_order' => $q_a->answer_order
				), array(
					'%d',
					'%s',
					'%s'
				) );
			}
		}

		return $new_question_id;
	}

	/**
	 * Duplicate post.
	 *
	 * @since 3.0.0
	 *
	 * @param null $post_id
	 * @param array $args
	 * @param bool $meta
	 *
	 * @return bool|mixed
	 */
	private function duplicate_post( $post_id = null, $args = array(), $meta = true ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return false;
		}
		$default = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => get_current_user_id(),
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title . __( ' Copy', 'learnpress' ),
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order
		);
		$args    = wp_parse_args( $args, $default );

		$new_post_id = wp_insert_post( $args );

		if ( ! is_wp_error( $new_post_id ) && $meta ) {
			$this->duplicate_post_meta( $post_id, $new_post_id );
			// assign related tags/categories to new course
			$taxonomies = get_object_taxonomies( $post->post_type );
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
				wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
			}
		}

		return apply_filters( 'learn_press_duplicate_post', $new_post_id, $post_id );
	}

	/**
	 * duplicate all post meta just in two SQL queries
	 */
	private function duplicate_post_meta( $old_post_id, $new_post_id, $excerpt = array() ) {
		global $wpdb;
		$post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$old_post_id" );
		if ( count( $post_meta_infos ) != 0 ) {
			$excerpt       = array_merge( array( '_edit_lock', '_edit_last' ), $excerpt );
			$excerpt       = apply_filters( 'learn_press_excerpt_duplicate_post_meta', $excerpt, $old_post_id, $new_post_id );
			$sql_query     = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			$sql_query_sel = array();
			foreach ( $post_meta_infos as $meta ) {
				if ( in_array( $meta->meta_key, $excerpt ) ) {
					continue;
				}
				if ( $meta->meta_key === '_lp_course_author' ) {
					$meta->meta_value = get_current_user_id();
				}
				$meta_key        = $meta->meta_key;
				$meta_value      = addslashes( $meta->meta_value );
				$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
			}
			$sql_query .= implode( " UNION ALL ", $sql_query_sel );
			$wpdb->query( $sql_query );
		}
	}
}