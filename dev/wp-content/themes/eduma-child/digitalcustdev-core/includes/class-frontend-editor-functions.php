<?php

class DigitalCustDev_FrontendEditor {

	public function __construct() {
		add_action( 'wp_ajax_lp_duplicate_main_section', array( $this, 'duplicate_section' ) );
		add_action( 'wp_ajax_nopriv_lp_duplicate_main_section', array( $this, 'duplicate_section' ) );

		add_filter( 'wp_handle_upload_prefilter', array( $this, 'modify_uploaded_file_names' ), 1 );

		add_filter( 'upload_dir', array( $this, 'change_directory_format' ) );
		add_action( 'before_delete_post', array( $this, 'delete_attachments' ), 20 );

		add_action( 'wp_ajax_check_foldersize', array( $this, 'check_foldersize' ) );
		add_action( 'wp_ajax_nopriv_check_foldersize', array( $this, 'check_foldersize' ) );

		/* Custom Auto Save */ 
		add_action( 'wp_ajax_step_two_custom_autosave', array( $this, 'step_two_custom_autosave' ) );
		add_action( 'wp_ajax_nopriv_step_two_custom_autosave', array( $this, 'step_two_custom_autosave' ) );

		// add_action('wp_head', array($this, 'testf'));
	}



	/*
	* Custom AutoSave
	*/
	public function step_two_custom_autosave(){
		$posts = $_REQUEST;
		$post_type = get_post_type( $posts['item_id'] );
		$update = update_post_meta( $posts['item_id'], $posts['field_name'], $posts['field_value'] );
		$msg = ($update) ? 'success' : 'fail';

		switch($posts['field_name']){
			case '_lp_lesson_video_intro':
				$item_details = get_post($posts['item_id']);

			break;
		}


		wp_send_json( 
			array(
				'msg' => $msg,
				'item_details' => $item_details
			)
		 );
	}


	public function testf(){
		$path = '/var/www/u0501458/data/www/digitalcustdev.ru/dev/wp-content/uploads/user9859113/13136';			
		$totalSize = 0;
		foreach (new DirectoryIterator($path) as $file) {
			if ($file->isFile()) {
				$totalSize += $file->getSize();
			}
		}
		$totalSizemb = number_format($totalSize / 1073741824, 2);

	}


	/** 
	* Check user upload folder size
	* function source ajax from post.js check_foldersize
	* @return foldersize
	*/
	public function check_foldersize(){
		$param = wp_get_upload_dir();
		$user_id = $_REQUEST['user_id'];
		$post_id = $_REQUEST['post_id'];
		$user = get_user_by('id', $user_id);
		if($post_id){
			$path = $param['basedir'] .'/'. $user->data->user_nicename . '/' . $post_id;
		}

		$totalSize = 0;
		foreach (new DirectoryIterator($path) as $file) {
			if ($file->isFile()) {
				$totalSize += $file->getSize();
			}
		}
		// $totalSizeMb = number_format($totalSize / 1073741824, 2); // GB
		// $totalSizeMb = number_format($totalSize / 1048576, 2); // MB

		echo json_encode(
			array(
				'msg' => 'success', 
				'titalsize' => $totalSize,
				'display_msg' => __('You can\'t upload any new content as you exceeded 2GB. Please, remove old content to upload new one.', 'webinar')
			)
		);

		wp_die();
	}




	/**
	 * Duplicate section ajax call
	 * @return WP_Error
	 */
	function duplicate_section() {
		$section = $_POST['section'];

		if ( empty( $section['course_id'] ) ) {
			learn_press_send_json_error( __( 'Ops! Course ID not found', 'learnpress' ) );
		}

		if ( ! empty( $section['course_id'] ) ) {
			// new course section curd
			$new_course_section_curd = new LP_Section_CURD( $section['course_id'] );

			$data = array(
				'section_name'        => $section['title'],
				'section_course_id'   => $section['course_id'],
				'section_order'       => $section['order'] + 1,
				'section_description' => $section['description']
			);

			// clone sections to new course
			$new_section = $new_course_section_curd->create( $data );

			// get section items of original course
			$items = $section['items'];

			$new_items = array();

			// quiz curd
			$quiz_curd = new LP_Quiz_CURD();
			$curd      = new LP_Question_CURD();

			// duplicate items
			if ( is_array( $items ) ) {
				foreach ( $items as $key => $item ) {
					// duplicate quiz
					if ( $item['type'] == LP_QUIZ_CPT ) {
						$new_item_id = $quiz_curd->duplicate( $item['id'], array( 'post_status' => 'publish' ) );
					} else if ( $item['type'] == LP_QUESTION_CPT ) {
						$new_item_id = $curd->duplicate( $item['id'], array( 'post_status' => 'publish' ) );
					} else {
						// clone lesson
						$new_item_id = learn_press_duplicate_post( $item['id'], array( 'post_status' => 'publish' ) );
					}

					// get new items data to add to section
					$new_items[ $key ] = array( 'id' => $new_item_id, 'type' => $item['type'] );
				}

				// add new clone items to section
				$result = $new_course_section_curd->add_items_section( $new_section['section_id'], $new_items );
			}
		}

		if ( is_wp_error( $result ) ) {
			learn_press_send_json_error( __( 'Duplicate post fail, please try again', 'learnpress' ) );
		} else {
			learn_press_send_json_success( 'Success !' );
		}

		wp_die();
	}

	function modify_uploaded_file_names( $file ) {
		$info = pathinfo( $file['name'] );
		$ext  = empty( $info['extension'] ) ? '' : '.' . $info['extension'];

		$uploads_dir        = wp_get_upload_dir();
		$uplods_folder_size = dcd_core_folderSize( $uploads_dir['path'] );

		$file['name'] = uniqid( 'file_' ) . $ext; // base64 method

		$image_type       = wp_check_filetype( $file['name'] );
		$image_conditions = $image_type['type'] === "image/svg+xml" || $image_type['type'] === "image/jpeg" || $image_type['type'] === "image/png" || $image_type['type'] === "image/gif";

		//3 mb check for images
		if ( $image_conditions && $file['size'] > 3145728 ) {
			$file['error'] = "Image size cannot exceed more than 3mb.";
		} else if ( $file['size'] > 209715200 ) {
			$file['error'] = "Video file size cannot exceed more than 200mb.";
		}

		if ( $image_conditions && ( isset( $_POST['post_id'] ) && get_post_type( $_POST['post_id'] ) === "lp_course" ) ) {
			$img     = getimagesize( $file['tmp_name'] );
			$minimum = array( 'width' => '870', 'height' => '500' );
			$width   = $img[0];
			$height  = $img[1];

			if ( $width < $minimum['width'] ) {
				$file['error'] = "Image dimensions are too small. Minimum width is {$minimum['width']}px. Uploaded image width is $width px";
			} elseif ( $height < $minimum['height'] ) {
				$file['error'] = "Image dimensions are too small. Minimum height is {$minimum['height']}px. Uploaded image height is $height px";
			}
		}

		//2 GB check
		if ( ! is_admin() || ! is_super_admin() ) {
			if ( $uplods_folder_size > 314572800 ) { //300 mb
				#if ( $uplods_folder_size > 2147483648 ) {
				$file['error'] = "Your upload size exceeded 2 GB limit. Please delete some files in order to continue.";
			}
		}

		return $file;
	}

	/**
	 * Change Directory format from here
	 *
	 * @param $param
	 *
	 * @return mixed
	 */
	function change_directory_format( $param ) {
		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			$post_id = get_transient( get_current_user_id() . 'e_post_id' );
			if($post_id){
				$param['path'] = $param['basedir'] .'/'. $user->data->user_nicename . '/' . $post_id;
				$param['url']  = $param['baseurl'] .'/'. $user->data->user_nicename . '/' .  $post_id;
			}
		}
		return $param;
	}

	function delete_attachments( $postid ) {
		$attachments = get_attached_media( '', $postid );

		foreach ( $attachments as $attachment ) {
			wp_delete_attachment( $attachment->ID, 'true' );
		}
	}
}

new DigitalCustDev_FrontendEditor();
