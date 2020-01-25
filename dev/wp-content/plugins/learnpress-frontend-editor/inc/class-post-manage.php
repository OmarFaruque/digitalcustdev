<?php

/**
 * Class LP_Addon_Frontend_Editor_Post_Manage
 *
 * @author  ThimPress
 * @package LearnPress/Frontend_Editor/Classes
 * @version 3.0.0
 */
class LP_Addon_Frontend_Editor_Post_Manage {
	/**
	 * Current post type
	 *
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * @var int
	 */
	protected $post_id = 0;

	/**
	 * @var string
	 */
	protected $template = '';

	/**
	 * LP_Addon_Frontend_Editor_Post_Manage constructor.
	 *
	 * @param     $post_type
	 * @param int $post_id
	 */
	public function __construct( $post_type, $post_id = 0 ) {
		$this->post_type = $post_type;
		$this->post_id   = $post_id;
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_item' ), 99 );
		////add_action( 'template_include', array( $this, 'update_post' ), 99 );
		//add_action( 'template_include', array( $this, 'restore_template' ), 99.0001 );
		add_action( 'learn-press/after-single-course', array( $this, 'single_edit_button' ) );

	}

	public function single_edit_button() {
		if ( LP_COURSE_CPT !== $this->get_post_type() ) {
			return;
		}

		$user = learn_press_get_current_user();
		if ( $user->is_admin() || $user->is_author_of( $this->post_id ) ) {
			frontend_editor()->get_template( 'global/edit-button' );
		}
	}

	/**
	 * @return string
	 */
	public function restore_template() {

		//learn_press_debug($wp_query);
		return $this->template;
	}

	/**
	 * @param string $template
	 *
	 * @return bool
	 */
	public function update_post( $template ) {
		$this->template = $template;

		if ( ! $nonce = LP_Request::get( '_e_post_nonce' ) ) {
			return false;
		}

		if ( ! wp_verify_nonce( $nonce, 'e_save_post' ) ) {
			return false;
		}

		$post_id      = LP_Request::get_int( 'post_ID' );
		$post_status  = LP_Request::get( 'post_status' );
		$post_title   = LP_Request::get( 'post_title' );
		$post_content = LP_Request::get( 'post_content' );
		$post_name    = LP_Request::get( 'post_name' );

		$post_data = array(
			'ID'           => $post_id,
			'post_status'  => $post_status === 'auto-draft' ? 'draft' : $post_status,
			'post_title'   => $post_title,
			'post_content' => $post_content,
			'post_name'    => $post_name
		);

		$post_id = wp_update_post( $post_data );

		$redirect = $this->get_edit_post_link( $this->get_post_type(), $post_id );
		if ( $post_id ) {
			if ( ( $thumbnail_id = LP_Request::get( '_thumbnail_id' ) ) > 0 ) {
				update_post_meta( $post_id, '_thumbnail_id', $thumbnail_id );
			} else {
				delete_post_thumbnail( $post_id );
			}

			$redirect = add_query_arg( 'updated', 1, $redirect );
		}

		wp_redirect( $redirect );
		exit;
	}

	/**
	 * Add new item to wp-admin-bar
	 *
	 * @since 3.0.0
	 *
	 * @param WP_Admin_Bar $admin_bar
	 */
	public function admin_bar_item( $admin_bar ) {

		if ( ! $this->post_id ) {
			return;
		}

		if ( $post_type_object = $this->get_post_type_object() ) {
			$admin_bar->add_menu(
				array(
					'id'    => 'edit-' . $this->get_post_type(),
					'title' => $post_type_object->labels->edit_item,
					'href'  => get_edit_post_link( $this->post_id )
				)
			);
		}

//		if ( $edit_course = $admin_bar->get_node( 'edit-lp_course' ) ) {
//			$edit_course->href = $this->get_edit_post_link();
//
//			$admin_bar->remove_node( 'edit-lp_course' );
//			$admin_bar->add_node( (array) $edit_course );
//		}

	}

	/**
	 * Get post type object
	 */
	public function get_post_type_object() {
		return get_post_type_object( $this->get_post_type() );
	}

	/**
	 * @return WP_Query
	 */
	public function get_posts() {
		$args = array(
			'post_type' => $this->post_type
		);

		return new WP_Query( $args );
	}

	/**
	 * @param bool $default
	 *
	 * @return WP_Post
	 */
	public function get_post( $default = false ) {
		if ( ! function_exists( 'get_default_post_to_edit' ) ) {
			include_once ABSPATH . '/wp-admin/includes/post.php';
		}
		global $post;

		if ( $this->post_id ) {
			$post = get_post( $this->post_id );
		} else if ( $post ) {
			$this->post_id = $post->ID;
		} else if ( $default ) {
			$post          = get_default_post_to_edit( $this->post_type, true );
			$this->post_id = $post->ID;
		}

		return $post;
	}

	/**
	 * Get name of current post type.
	 *
	 * @param bool $singular
	 *
	 * @return string
	 */
	public function get_name( $singular = false ) {
		$post_type_object = get_post_type_object( $this->post_type );

		return $singular ? $post_type_object->labels->singular_name : $post_type_object->labels->name;
	}

	/**
	 * Get link of post type.
	 *
	 * @param string $post_type
	 * @param string $screen
	 *
	 * @return string
	 */
	public function get_post_type_link( $post_type = '', $screen = 'list-post' ) {
		global $frontend_editor;

		$name = '';

		if ( ! $post_type ) {
			$post_type = $this->get_post_type();
		}

		if ( $post_type_object = get_post_type_object( $post_type ) ) {
			$map_post_types = self::get_post_type_map();

			switch ( $screen ) {
				case 'edit':
					$name = $post_type_object->labels->singular_name;
					break;
				default:
					$name = $post_type_object->labels->name;
			}
			$name = sanitize_title( $name );

			if ( ! empty( $map_post_types[ $name ] ) ) {
				$name = $map_post_types[ $name ];
			}
		}

		return trailingslashit( $frontend_editor->get_url( $screen . '/' . $name ) );
	}

	/**
	 * Get edit link of a post.
	 *
	 * @param string $post_type
	 * @param int    $post_id
	 *
	 * @return string
	 */
	public function get_edit_post_link( $post_type = '', $post_id = 0 ) {
		global $frontend_editor;

		if ( ! $post_id && $post = $this->get_post() ) {
			$post_id = $post->ID;
		}

		return $frontend_editor->get_url( 'edit-post' . '/' . $post_id );
	}

	/**
	 * @param string $post_type
	 *
	 * @return string
	 */
	public function get_new_post_link( $post_type = '' ) {
		global $frontend_editor;

		if ( ! $post_type ) {
			$post_type = $this->get_post_type();
		}

		return add_query_arg( 'post-type', $post_type, $frontend_editor->get_url( 'edit-post' ) );
	}

	/**
	 * @return array
	 */
	public static function get_post_type_map() {
		$map_post_types = array(
			'question-bank' => 'questions'
		);

		return apply_filters( 'learn-press/frontend-editor/map-post-types', $map_post_types );
	}

	/**
	 * @return array
	 */
	public function get_post_type_list() {
		return apply_filters(
			'e-post-type-list',
			array(
				LP_COURSE_CPT   => array( 'name' => _x( 'Courses', 'courses-post-type-name', 'learnpress-frontend-editor' ) ),
				LP_LESSON_CPT   => array( 'name' => _x( 'Lessons', 'lessons-post-type-name', 'learnpress-frontend-editor' ) ),
				LP_QUIZ_CPT     => array( 'name' => _x( 'Quizzes', 'quizzes-post-type-name', 'learnpress-frontend-editor' ) ),
				LP_QUESTION_CPT => array( 'name' => _x( 'Questions', 'questions-post-type-name', 'learnpress-frontend-editor' ) )
			) );
	}

	public function get_filter_status() {
		return LP_Request::get( 'filter-status' );
	}

	/**
	 * @param string $filter_status
	 *
	 * @return array
	 */
	public function post_counts( $filter_status = '' ) {
		$filter_status = $filter_status ? $filter_status : LP_Request::get( 'filter-status' );
		$counts        = e_count_user_posts( $this->post_type );

		$labels         = array();
		$count_statuses = array(
			'all'     => __( 'All %s', 'learnpress-frontend-editor' ),
			'publish' => __( 'Publish %s', 'learnpress-frontend-editor' ),
			//'draft'   => __( 'Drafts %s', 'learnpress-frontend-editor' ),
			'pending' => __( 'Pending %s', 'learnpress-frontend-editor' ),
			'trash'   => __( 'Trash %s', 'learnpress-frontend-editor' ),
		);

		$all = 0;
		foreach ( array( 'publish', 'draft', 'pending' ) as $status ) {
			if ( isset( $counts->{$status} ) ) {
				$all += $counts->{$status};
			}
		}

		$labels[] = sprintf( '<a href="%s"%s>%s</a>', remove_query_arg( 'filter-status', $this->get_post_type_link() ), ! $filter_status ? ' class="current"' : '', sprintf( $count_statuses['all'], '<span class="count">(' . $all . ')</span>' ) );

		foreach ( $count_statuses as $status => $label ) {
			if ( isset( $counts->{$status} ) && $counts->{$status} ) {
				$labels[] = sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'filter-status', $status, $this->get_post_type_link() ), $filter_status === $status ? ' class="current"' : '', sprintf( $label, '<span class="count">(' . $counts->{$status} . ')</span>' ) );
			}
		}

		return $labels;
	}

	/**
	 * @return string
	 */
	public function get_post_type() {
		return $this->post_type;
	}
}