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
		$post_id 		= $wp_query->get( 'post-id' );
		$type    		= get_post_meta( $post_id, '_course_type', true );
		$timezone 		= 'Europe/Moscow';
		$when_webinar 	= date( 'd/m/Y H:i' );

		$input_type = 'lesson_duration';

		foreach ( $fields['fields'] as $k => $field ) {
			if ( $field['id'] === "_lp_duration" ) {
				unset( $fields['fields'][ $k ] );
			}
		}

		$fields['fields'][] = array(
			'name'         => __( 'Duration', 'learnpress' ),
			'id'           => '_lp_duration',
			'type'         => $input_type,
			'default_time' => 'minute',
			'desc'         => '',
			'std'          => '45 minute'
		);

		if ( $type === "webinar" ) {
			$ip     = $_SERVER['REMOTE_ADDR']; // means we got user's IP address 
			$json   = file_get_contents( 'http://ip-api.com/json/' . $ip); // this one service we gonna use to obtain timezone by IP
			// maybe it's good to add some checks (if/else you've got an answer and if json could be decoded, etc.)
			$ipData = json_decode( $json, true);
			if ($ipData['timezone']){
				$tz = new DateTimeZone( $ipData['timezone']);
				$now = new DateTime( 'now', $tz);
				$timezone = $ipData['timezone'];
				$when_webinar =  $now->format('Y-m-d H:i');
				$mint = date('i', strtotime($when_webinar));
				
				$extra = $mint % 5;
				$addminit = 5 - $extra;
				$addminit = ($addminit < 5) ? $addminit : 0;
				$when_webinar = date('d/m/Y H:i', strtotime( '+'.$addminit.' minutes', strtotime($when_webinar)));
			}

			foreach ( $fields['fields'] as $k => $field ) {
				if ( $field['id'] === "_lp_preview" || $field['id'] === "_lp_lesson_video_intro" ) {
					unset( $fields['fields'][ $k ] );
				}
			}

			$fields['fields'][] = array(
				'name' => __( 'Timezone', 'learnpress' ),
				'id'   => '_lp_timezone',
				'type' => 'timezone',
				'std'  => $timezone
			);

			array_unshift( $fields['fields'], array(
				'name' => __( 'When', 'learnpress' ),
				'id'   => '_lp_webinar_when',
				'type' => 'when_webinar',
				'std'  => $when_webinar
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