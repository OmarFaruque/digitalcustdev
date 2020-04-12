<?php
if ( class_exists( 'ACF' ) ) {
	if ( function_exists( 'acf_add_local_field_group' ) ):
		acf_add_local_field_group( array(
			'key'                   => 'group_5d526229700d0',
			'title'                 => 'Intro Video Upload',
			'fields'                => array(
				array(
					'key'               => 'field_5d52623d7778a',
					'label'             => 'Upload Intro Video',
					'name'              => 'upload_intro_video',
					'type'              => 'file',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'return_format'     => 'url',
					'library'           => 'uploadedTo',
					'min_size'          => '',
					'max_size'          => 200,
					'mime_types'        => 'mp4',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'lp_course',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'side',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		) );





		// For Lesson
		// acf_add_local_field_group( array(
		// 	'key'                   => 'group_for_lesson',
		// 	'title'                 => 'Intro Video lesson Upload',
		// 	'fields'                => array(
		// 		array(
		// 			'key'               => 'field_lesson_video',
		// 			'label'             => 'Upload Intro Video',
		// 			'name'              => '_lp_lesson_video_intro_internal',
		// 			'type'              => 'file',
		// 			'instructions'      => '',
		// 			'required'          => 0,
		// 			'conditional_logic' => 0,
		// 			'wrapper'           => array(
		// 				'width' => '',
		// 				'class' => '',
		// 				'id'    => '',
		// 			),
		// 			'return_format'     => 'url',
		// 			'library'           => 'uploadedTo',
		// 			'min_size'          => '',
		// 			'max_size'          => 200,
		// 			'mime_types'        => 'mp4',
		// 		),
		// 	),
		// 	'location'              => array(
		// 		array(
		// 			array(
		// 				'param'    => 'post_type',
		// 				'operator' => '==',
		// 				'value'    => 'lp_lesson',
		// 			),
		// 		),
		// 	),
		// 	'menu_order'            => 0,
		// 	'position'              => 'side',
		// 	'style'                 => 'default',
		// 	'label_placement'       => 'top',
		// 	'instruction_placement' => 'label',
		// 	'hide_on_screen'        => '',
		// 	'active'                => true,
		// 	'description'           => '',
		// ) );

	endif;
}
