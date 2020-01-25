<?php
/**
 * @author Deepen.
 * @created_on 6/21/19
 */

class DigitalCustDev_LearpressMetaboxFields {

	public function __construct() {
		add_filter( 'learn_press_lesson_meta_box_args', array( $this, 'lesson_metabox' ), 99 );
		add_filter( 'learn_press_quiz_general_meta_box', array( $this, 'quiz_setting_metabox' ), 99 );
		add_filter( 'learn_press_question_meta_box_args', array( $this, 'quiz_question_metabox' ), 99 );
		add_filter( 'learn_press_assignment_general_settings_meta_box', array( $this, 'assignment_metabox' ), 199 );
		add_filter( 'learn_press_assignment_attachments_meta_box', array( $this, 'attachment_metabox' ) );
	}

	function lesson_metabox( $fields ) {
		global $wp_query;
		$post_id = $wp_query->get( 'post-id' );
		$type    = get_post_meta( $post_id, '_course_type', true );

		foreach ( $fields['fields'] as $k => $field ) {
			if ( $field['id'] === "_lp_duration" ) {
				unset( $fields['fields'][ $k ] );
			}
		}

		$fields['fields'][] = array(
			'name'         => __( 'Duration', 'learnpress' ),
			'id'           => '_lp_duration',
			'type'         => 'lesson_duration',
			'default_time' => 'minute',
			'desc'         => '',
			'std'          => '45 minute'
		);

		if ( $type === "webinar" ) {
			foreach ( $fields['fields'] as $k => $field ) {
				if ( $field['id'] === "_lp_preview" || $field['id'] === "_lp_lesson_video_intro" ) {
					unset( $fields['fields'][ $k ] );
				}
			}

			$fields['fields'][] = array(
				'name' => __( 'Timezone', 'learnpress' ),
				'id'   => '_lp_timezone',
				'type' => 'timezone',
				'std'  => 'Europe/Moscow'
			);

			array_unshift( $fields['fields'], array(
				'name' => __( 'When', 'learnpress' ),
				'id'   => '_lp_webinar_when',
				'type' => 'when_webinar',
				'std'  => date( 'd/m/Y H:i' )
			) );
		}

		return $fields;
	}

	function quiz_setting_metabox( $fields ) {
		foreach ( $fields['fields'] as $k => $field ) {
			if ( $field['id'] === '_lp_passing_grade' ) {
				$fields['fields'][ $k ]['max'] = 90;
			}

			if ( $field['id'] != '_lp_passing_grade' ) {
				unset( $fields['fields'][ $k ] );
			}
		}

		return $fields;
	}

	function quiz_question_metabox( $fields ) {
		return $fields;
	}

	function assignment_metabox( $fields ) {
		foreach ( $fields['fields'] as $k => $field ) {
			if ( $field['id'] != '_lp_duration' ) {
				unset( $fields['fields'][ $k ] );
			}

			if ( $field['id'] === '_lp_duration' ) {
				$fields['fields'][ $k ]['default_time'] = 'day';
				$fields['fields'][ $k ]['std']          = '7 day';
				unset( $fields['fields'][ $k ]['min'] );
			}
		}

		return $fields;
	}

	function attachment_metabox( $fields ) {
		/*foreach ( $fields['fields'] as $k => $field ) {
			if ( $field['id'] === '_lp_introduction' ) {
				unset( $fields['fields'][ $k ] );
			}
		}*/

		return $fields;
	}
}

new DigitalCustDev_LearpressMetaboxFields();