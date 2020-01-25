<?php

/**
 * class Frontend_Editor_Ajax
 *
 * @since 3.0.0
 */
class Frontend_Editor_Ajax {

	/**
	 * Init
	 */
	public static function init() {

		add_action( 'wp_ajax_update_current_course_settings_tab', array(
			__CLASS__,
			'update_current_course_settings_tab'
		) );

		LP_Request::register_ajax( 'create-new-post', array( __CLASS__, 'create_new_post' ) );

		if ( ! $action = LP_Request::get_string( 'lp-ajax' ) ) {
			return;
		}

		if ( ! preg_match( '~^fe/~', $action ) ) {
			return;
		}

		if ( ! $namespace = LP_Request::get_string( 'namespace' ) ) {
			return;
		}

		$editor = false;

		foreach ( array( 'course', 'quiz', 'question' ) as $ed ) {
			if ( preg_match( '~^(' . $ed . '-editor-)~', $namespace ) ) {
				$editor = $ed;
				break;
			}
		}

		if ( ! $editor ) {
			return;
		}

		$nonce = str_replace( "{$editor}-editor-", '', $namespace );

		if ( ! wp_verify_nonce( $nonce, "{$editor}-editor" ) ) {
			wp_die( "Something went wrong (#1)!" );
		}

		if ( ! $editor = FE_Editor::get( $editor ) ) {
			wp_die( "Something went wrong (#2)!" );
		}

		$result = $editor->dispatch( $action );

	}

	/**
	 * Update current tab of settings when user click on a tab
	 */
	public static function update_current_course_settings_tab() {
		$postId = LP_Request::get_int( 'courseID' );
		$tab    = LP_Request::get( 'tab' );
		update_post_meta( $postId, '_fe_current_settings_tab', $tab );
	}

	/**
	 * Create a new post type.
	 */
	public static function create_new_post() {
		$post_type = LP_Request::get_string( 'type' );
		$nonce     = LP_Request::get_string( 'nonce' );

		if ( ! wp_verify_nonce( $nonce, 'e-new-post' ) ) {
			wp_die( 'Opp' );
		}

		if ( ! function_exists( 'get_default_post_to_edit' ) ) {
			require_once ABSPATH . '/wp-admin/includes/post.php';
		}

		$post      = get_default_post_to_edit( $post_type, true );
		$post_data = array(
			'ID'          => $post->ID,
			'post_status' => 'draft',
			'post_title'  => $post->ID
		);
		$id        = wp_update_post( $post_data, true );

		// Remove default title as it is the ID of post
		if ( ! is_wp_error( $id ) ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_title = '' WHERE ID = %d", $id ) );
		}

		learn_press_send_json(
			array(
				'redirect' => frontend_editor()->post_manage->get_edit_post_link( $post_type, $post->ID )
			)
		);
		die();
	}

}

add_action( 'init', array( 'Frontend_Editor_Ajax', 'init' ), 1000 );

