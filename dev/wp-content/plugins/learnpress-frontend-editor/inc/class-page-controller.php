<?php

/**
 * Class LP_Addon_Frontend_Editor_Page_Controller
 */
class LP_Addon_Frontend_Editor_Page_Controller {

	/**
	 * @var LP_Addon_Frontend_Editor
	 */
	public static $app = null;

	/**
	 * @var string
	 */
	public static $sub_page = '';

	/**
	 * @var string
	 */
	public static $post_type = '';

	/**
	 * @var int
	 */
	public static $post_id = 0;

	/**
	 * Init
	 */
	public static function init() {
		self::$app = LP_Addon_Frontend_Editor::instance();

		add_action( 'learn-press/frontend-editor/dashboard', array( __CLASS__, 'display_sub_page' ) );
		add_action( 'pre_get_posts', array(
			__CLASS__,
			'load'
		), 99 ); // 99 instead of 10 because 10 will be conflict with Membership 2 plugin
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		add_filter( 'wp_enqueue_scripts', array( __CLASS__, 'main_scripts' ) );
		add_filter( 'template_include', array( __CLASS__, 'template_include' ), 1 );
		add_filter( 'wp_footer', array( __CLASS__, 'footer_scripts' ) );
		add_filter( 'wp_print_footer_scripts', array( __CLASS__, 'print_footer_scripts' ) );
	}

	public static function footer_scripts() {
		?>
        <script type="text/javascript">
            var lpAdminCourseEditorSettings = {
                i18n: {
                    notice_invalid_date: '<?php _e( 'Invalid date', 'learnpress-frontend-editor' );?>',
                    notice_sale_start_date: '<?php _e( 'Sale start date must before sale end date', 'learnpress-frontend-editor' );?>',
                    notice_sale_end_date: '<?php _e( 'Sale end date must after sale start date', 'learnpress-frontend-editor' );?>',
                }
            }
        </script>
		<?php
	}

	public static function template_include( $template ) {
		/**
		 * @var WP_Query $wp_query
		 */
		global $e_wp_query, $wp;

		if ( ! empty( $wp->query_vars['frontend-editor'] ) && ! empty( $wp->query_vars['post-type'] ) ) {

			$url = remove_query_arg( 'paged', learn_press_get_current_url() );

			if ( isset( $_REQUEST['paged'] ) ) {
				$_paged = $_REQUEST['paged'];
			} else {
				$_paged = $e_wp_query->query_vars['paged'];
			}

			$paged = $_paged;

			if ( $_paged < 1 ) {
				$paged = 1;
			} elseif ( $_paged > $e_wp_query->max_num_pages ) {
				$paged = $e_wp_query->max_num_pages;
			}

			if ( $paged !== $_paged || isset( $_REQUEST['paged'] ) ) {
				if ( preg_match( '~/page/([0-9]+)?~', $url ) ) {
					$url = preg_replace( '~/page/([0-9]+)?~', $paged > 1 ? '/page/' . $paged : '', $url );
				} elseif ( $paged > 1 ) {
					$x   = explode( '?', $url );
					$url = trailingslashit( $x[0] ) . 'page/' . $paged . ( isset( $x[1] ) ? '?' . $x[1] : '' );
				}

				if ( ! learn_press_is_current_url( $url ) ) {
					wp_redirect( $url );
					exit();
				}
			}
		}

		return $template;
	}

	/**
	 * @param array $classes
	 *
	 * @return array
	 */
	public static function body_class( $classes ) {
		/**
		 * @var WP_Query $wp_query
		 */
		global $wp_query;

		$classes[] = get_stylesheet();

		if ( $wp_query->get( 'frontend-editor' ) ) {
			$classes[] = 'page-frontend-editor';
		}

		return $classes;
	}

	/**
	 * Load
	 *
	 * @param WP_Query $wp_query
	 */
	public static function load( $wp_query ) {
		remove_action( 'pre_get_posts', array( __CLASS__, 'load' ) );
		/**
		 * @var WP         $wp
		 * @var WP_Query   $e_wp_query
		 * @var WP_Rewrite $wp_rewrite
		 * @var WP_Post    $post
		 */

		global $wp, $wp_rewrite, $post;
		$args = array();//$wp_query->query_vars;


		if ( is_admin() || ! $wp_query->is_main_query() ) {
			return;
		}

		if ( isset( $wp->query_vars ) && array_key_exists( 'frontend-editor', $wp->query_vars ) ) {
			if ( empty( $wp->query_vars['frontend-editor'] ) ) {
				$wp_query->query_vars['frontend-editor'] = 'dashboard';
				$wp_query->query['frontend-editor']      = 'dashboard';
			} else {
				$wp_query->query_vars['frontend-editor'] = $wp->query_vars['frontend-editor'];
				$wp_query->query['frontend-editor']      = $wp->query_vars['frontend-editor'];
			}
		}
//		elseif ( ( $page_id = get_option( 'learn_press_frontend_editor_page_id' ) ) && is_page( $page_id ) ) {
//
//		}

		$post_id   = 0;
		$post_type = '';

		if ( $root = $wp_query->get( 'frontend-editor' ) ) {
			switch ( $root ) {
				case 'edit-post':
				case 'list-post':
				case 'dashboard':
					$paged = 1;
					if ( $root == 'edit-post' ) {
						if ( $post_id = $wp_query->get( 'post-id' ) ) {
							$post_type      = get_post_type( $post_id );
							self::$sub_page = 'post-edit';
						} elseif ( $post_type = LP_Request::get( 'post-type' ) ) {
							self::$sub_page = 'post-edit';
						}

						if ( get_current_user_id() != get_post_field( 'post_author', $post_id ) ) {
							wp_die( __( 'You don\'t have permission to edit this course.', 'learnpress-frontend-editor' ) );
						}

					} else {
						$post_type_name = $wp_query->get( 'post-type' );

						// Pagination!!! Have .../post-type-name/page/123 at the end of URL
						if ( preg_match( '~(.*)/page/([0-9]+)~', $post_type_name, $m ) ) {
							$post_type_name = $m[1];
							$paged          = $m[2];
						}

						$post_type      = self::get_post_type_by_name( $post_type_name );
						self::$sub_page = 'post-list';
					}

					if ( $root === 'dashboard' && ! $post_type ) {
						$post_type = LP_COURSE_CPT;
					}

					if ( ! post_type_exists( $post_type ) ) {
						wp_die( sprintf( __( 'Frontend Editor: The post type  &quot;%s&quot; does not exist.', 'learnpress-frontend-editor' ), $post_type ) );
					}

					$args['post_type'] = $post_type;
					$args['paged']     = $paged;

			}
			if ( ! class_exists( 'WP_List_Table' ) || ! class_exists( 'WP_Posts_List_Table' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
				require_once( ABSPATH . 'wp-admin/includes/class-wp-posts-list-table.php' );
				require_once( ABSPATH . 'wp-admin/includes/comment.php' );
				require_once( ABSPATH . 'wp-admin/includes/post.php' );
				require_once( ABSPATH . 'wp-admin/includes/taxonomy.php' );
				require_once( ABSPATH . 'wp-admin/includes/screen.php' );
				require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
				require_once( ABSPATH . 'wp-admin/includes/template.php' );
				require_once( LP_PLUGIN_PATH . '/inc/admin/class-lp-admin-assets.php' );

				$GLOBALS['hook_suffix'] = '';
			}
			include_once LP_ADDON_FRONTEND_EDITOR_PATH . '/inc/class-post-list-table.php';

			add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		}


		self::$post_type = $post_type;
		self::$post_id   = $post_id;

		if ( $post_type ) {
			//$e_wp_query->set( 'post_type', $post_type );
			$args['post_type'] = $post_type;
		}

		if ( $wp_query->get( 'search' ) ) {
			$args['s'] = $wp_query->get( 'search' );
		}

		if ( $wp_query->get( 'sort' ) ) {
			$args['order'] = $wp_query->get( 'sort' );
		}

		if ( $wp_query->get( 'sortby' ) ) {
			$args['orderby'] = $wp_query->get( 'sortby' );
		}

		if ( isset( $_REQUEST['paged'] ) ) {
			$args['paged'] = 1;
		}
		$args['posts_per_page'] = 5;

		global $frontend_editor;
		$frontend_editor->post_manage = new LP_Addon_Frontend_Editor_Post_Manage( self::$post_type, self::$post_id );

		if ( $root ) {
			include_once LP_PLUGIN_PATH . '/inc/admin/lp-admin-functions.php';
			include_once LP_PLUGIN_PATH . '/inc/admin/class-lp-meta-box-tabs.php';

			// This code make metabox works
			if ( class_exists( 'WP_List_Table' ) ) {
				global $current_screen, $post;
				//$current_screen = WP_Screen::get( self::$post_type );
			}

			if ( ! class_exists( '_WP_Editors' ) ) {
				require_once( ABSPATH . '/wp-includes/class-wp-editor.php' );
			}

			add_action( 'wp_print_footer_scripts', array( '_WP_Editors', 'editor_js' ), 50 );
			add_action( 'wp_print_footer_scripts', array( '_WP_Editors', 'enqueue_scripts' ), 1 );

			if ( self::$post_id ) {

				if ( ! current_user_can( 'edit_post', self::$post_id ) ) {
					wp_die( sprintf( __( "You don't have permission to edit this post. Please <a href=\"%s\">%s</a> to continue!", 'learnpress-frontend-editor' ), wp_login_url( learn_press_get_current_url() ), __( 'login', 'learnpress-frontend-editor' ) ) );
				}

				$post = get_post( self::$post_id );
				setup_postdata( $post );
				//$e_wp_query->set( 'name', $post->post_name );
				$args['name'] = $post->post_name;

				do_action( 'load-post.php' );
			} else {
				do_action( 'load-post-new.php' );
			}

			if ( isset( $_REQUEST['filter-status'] ) ) {
				$args['post_status'] = $_REQUEST['filter-status'];
			} else {
				$args['post_status'] = array( 'publish', 'draft', 'pending' );
			}
		}

		// Currently, only fetch courses of current user
		$args['author'] = get_current_user_id();

		$GLOBALS['e_wp_query'] = new WP_Query( $args );

		//add_action( 'pre_get_posts', array( __CLASS__, 'load' ) );
	}

	public static function __callStatic( $name, $arguments ) {
		echo '<h1>' . $name . '</h1>';
	}

	public static function list_courses() {

	}

	/**
	 * Scripts
	 */
	public static function main_scripts() {

		if ( ! e_is_frontend_editor_page() ) {
			return;
		}

		RWMB_Datetime_Field::admin_register_scripts();

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'jquery-ui-timepicker' );
		//wp_enqueue_script( 'rwmb-datetime' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-timepicker-i18n' );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		wp_enqueue_style( 'frontend-editor', plugins_url( '/assets/css/editor.css', LP_ADDON_FRONTEND_EDITOR_FILE ), 'dashicons' );
		wp_enqueue_script( 'frontend-editor', plugins_url( '/assets/js/frontend-editor.js', LP_ADDON_FRONTEND_EDITOR_FILE ) );
		wp_enqueue_script( 'frontend-course-editor', plugins_url( '/assets/js/course-editor.php', LP_ADDON_FRONTEND_EDITOR_FILE ), array(
			/*'lp-vue',
			'lp-vuex',
			'lp-vue-resource',*/
			'jquery-ui-draggable',
			'jquery-ui-droppable',
		) );
		do_action( 'learn-press/frontend-editor/enqueue' );
		global $post;

		$settings = LP()->settings();


//		learn_press_assets()->add_script_data(
//			'frontend-course-editor',
//			array(
//				'Course_Store_Data' => e_get_course_store_data(),
//				'i18n'              => e_get_localize(),
//				'rootURL'           => get_home_url()
//			)
//		);

		RWMB_Select_Advanced_Field::admin_enqueue_scripts();
	}

	public static function print_footer_scripts() {
		if ( ! e_is_frontend_editor_page() ) {
			return;
		}

		$data = array(
			'Course_Store_Data' => e_get_course_store_data(),
			'i18n'              => e_get_localize(),
			'rootURL'           => get_home_url()
		);
		?>
        <script type='text/javascript'>
            /* <![CDATA[ */
            var lpFrontendCourseEditorSettings = <?php echo wp_json_encode($data);?>
            /* ]]> */
        </script>
		<?php
	}

	/**
	 * @param string $template
	 */
	public static function show_page( &$template ) {
		/**
		 * @var WP         $wp
		 * @var WP_Rewrite $wp_rewrite
		 * @var WP_Query   $wp_query
		 */
		global $wp, $wp_rewrite, $wp_query;
		$template = self::$app->locate_template( 'dashboard' );
	}

	/**
	 * @param string $name
	 *
	 * @return bool|mixed
	 */
	public static function get_post_type_by_name( $name ) {
		static $post_types = array();

		if ( ! $post_types ) {
			$map_post_types = LP_Addon_Frontend_Editor_Post_Manage::get_post_type_map();

			if ( $all_post_types = get_post_types( '', false ) ) {
				foreach ( $all_post_types as $_post_type_slug => $_post_type ) {
					$sanitize_name = sanitize_title( $_post_type->label );

					if ( ! empty( $map_post_types[ $sanitize_name ] ) ) {
						$sanitize_name = $map_post_types[ $sanitize_name ];
					}

					$post_types[ $sanitize_name ] = $_post_type_slug;
				}
			}
		}

		if ( ! empty( $post_types[ $name ] ) ) {
			return $post_types[ $name ];
		}

		return false;
	}

	/**
	 * Display sub-page
	 */
	public static function display_sub_page() {
		if ( self::$sub_page ) {
			self::$app->get_template( self::$sub_page );
		} else {
			self::$app->get_template( 'list/list-table-lp_course' );
		}
	}
}

add_action( 'init', array( 'LP_Addon_Frontend_Editor_Page_Controller', 'init' ) );