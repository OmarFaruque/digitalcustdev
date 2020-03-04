<?php
/**
 * Main class for addon.
 *
 * @author  ThimPress
 * @package LearnPress/Addon
 * @version 3.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_Frontend_Editor' ) ) {
	/**
	 * Class LP_Addon_Frontend_Editor.
	 */
	class LP_Addon_Frontend_Editor extends LP_Addon {

		/**
		 * @var string
		 */
		public $version = LP_ADDON_FRONTEND_EDITOR_VER;

		/**
		 * @var string
		 */
		public $require_version = LP_ADDON_FRONTEND_EDITOR_REQUIRE_VER;

		/**
		 * @var LP_Addon_Frontend_Editor_Post_Manage
		 */
		public $post_manage = null;

		/**
		 * @var null
		 */
		protected $is_used_page = null;

		/**
		 * @var bool
		 */
		protected static $load_header = false;

		/**
		 * LP_Addon_Frontend_Editor constructor.
		 */
		public function __construct() {

			parent::__construct();

			add_action( 'template_include', array( $this, 'template_include' ), 4 );
			add_action( 'admin_bar_menu', array( $this, 'admin_menu' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ), 1000 );
			add_filter( 'learn-press/admin/settings-tabs-array', array( $this, 'admin_settings' ) );
		}

		public function admin_settings( $tabs ) {
			$tabs['frontend-editor'] = include_once "class-fe-admin-settings.php";

			return $tabs;
		}

		public function frontend_scripts() {
			wp_deregister_script( 'learnpress' );
		}

		/**
		 * @param WP_Admin_Bar $wp_admin_bar
		 */
		public function admin_menu( $wp_admin_bar ) {
			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( ! current_user_can( 'edit_' . LP_COURSE_CPT . 's' ) ) {
				return;
			}

			$wp_admin_bar->add_node( array(
				'parent' => 'site-name',
				'id'     => $this->get_root_slug(),
				'title'  => __( 'Frontend Course Editor', 'learnpress-frontend-editor' ),
				'href'   => $this->get_url()
			) );
		}

		/**
		 * Called when WP trigger action 'init'
		 * for initializing something
		 *
		 * @since 3.0.0
		 */
		public function init() {
			parent::init();
			if ( is_admin() ) {
			}
			$this->_init_rewrite_rules();
			$GLOBALS['frontend_editor'] = $this;
		}

		/**
		 * Get root slug of page to manage courses and other items
		 *
		 * @return string
		 */
		public function get_root_slug() {
			$root_slug = 'frontend-editor';

			if ( ( $page_id = get_option( 'learn_press_frontend_editor_page_id' ) ) && $page_id ) {
				if ( get_post( $page_id ) ) {
					$root_slug          = get_post_field( 'post_name', $page_id );
					$this->is_used_page = $page_id;
				}
			}

			return apply_filters( 'learn-press/frontend-editor/root-slug', $root_slug );
		}

		/**
		 * Get url from root slug
		 *
		 * @param string $sub
		 * @param string $slug
		 *
		 * @return string
		 */
		public function get_url( $sub = '', $slug = '' ) {
			$root = get_home_url() . '/' . $this->get_root_slug();

			if ( $sub ) {
				$url = $root . '/' . $sub;
			} else {
				$url = $root;
			}

			return $url;
		}

		/**
		 * Add rewrite rules for front-end pages
		 */
		protected function _init_rewrite_rules() {
			$root_slug = $this->get_root_slug();
			$page_rule = '';
			if ( $this->is_used_page ) {
				$page_rule = '&pagename=' . $root_slug;
			}

			add_rewrite_rule( '^' . $root_slug . '/?$', 'index.php?frontend-editor=$matches[1]&post-type=$matches[2]' . $page_rule, 'top' );
			add_rewrite_rule( '^' . $root_slug . '/(list-post)/?(.*)?', 'index.php?frontend-editor=$matches[1]&post-type=$matches[2]' . $page_rule, 'top' );
			add_rewrite_rule( '^' . $root_slug . '/(edit-post)/([0-9]+)?/?$', 'index.php?frontend-editor=$matches[1]&post-id=$matches[2]' . $page_rule, 'top' );
			add_rewrite_rule( '^' . $root_slug . '/(edit-post)/([0-9]+)/([0-9]+)?/?$', 'index.php?frontend-editor=$matches[1]&post-id=$matches[2]&item-id=$matches[3]' . $page_rule, 'top' );
			add_rewrite_rule( '^' . $root_slug . '/(edit-post)/?(.*)?', 'index.php?frontend-editor=$matches[1]&post-id=0' . $page_rule, 'top' );

			add_rewrite_tag( '%frontend-editor%', '([^&]+)' );
			add_rewrite_tag( '%post-type%', '([^&]+)' );
			add_rewrite_tag( '%post-id%', '([^&]+)' );
			add_rewrite_tag( '%item-id%', '([^&]+)' );
			add_rewrite_tag( '%sort%', '([^&]+)' );
			add_rewrite_tag( '%sortby%', '([^&]+)' );

			flush_rewrite_rules();
		}

		public function _define_constants() {

		}

		/**
		 * Include files
		 */
		public function _includes() {
			include_once LP_ADDON_FRONTEND_EDITOR_PATH . '/inc/class-page-controller.php';
			include_once LP_ADDON_FRONTEND_EDITOR_PATH . '/inc/class-post-manage.php';
			include_once LP_ADDON_FRONTEND_EDITOR_PATH . '/inc/editors/_editor.php';
			include_once LP_ADDON_FRONTEND_EDITOR_PATH . '/inc/editors/course.php';
			include_once LP_ADDON_FRONTEND_EDITOR_PATH . '/inc/class-ajax.php';
			include_once LP_ADDON_FRONTEND_EDITOR_PATH . '/inc/functions.php';
		}

		public function current_user_can_edit_course() {
		}

		/**
		 * Show page depending on request
		 *
		 * @param string $template
		 *
		 * @return string
		 */
		public function template_include( $template ) {
			/**
			 * @var WP $wp
			 */
			global $wp, $wp_query;
			if ( array_key_exists( 'frontend-editor', $wp->query_vars ) ) {

				if ( current_user_can( 'edit_lp_courses' ) ) {
					add_filter( 'the_content', array( $this, 'dashboard_content' ), 9999999 );

					if ( ! $this->is_used_page ) {
						$wp_query->posts_per_page = 1;
						$wp_query->nopaging       = true;
						$wp_query->post_count     = 1;

						$wp_query->found_posts          = 1;
						$wp_query->is_single            = true;
						$wp_query->is_preview           = false;
						$wp_query->is_archive           = false;
						$wp_query->is_date              = false;
						$wp_query->is_year              = false;
						$wp_query->is_month             = false;
						$wp_query->is_day               = false;
						$wp_query->is_time              = false;
						$wp_query->is_author            = false;
						$wp_query->is_category          = false;
						$wp_query->is_tag               = false;
						$wp_query->is_tax               = false;
						$wp_query->is_search            = false;
						$wp_query->is_feed              = false;
						$wp_query->is_comment_feed      = false;
						$wp_query->is_trackback         = false;
						$wp_query->is_home              = false;
						$wp_query->is_404               = false;
						$wp_query->is_comments_popup    = false;
						$wp_query->is_paged             = false;
						$wp_query->is_admin             = false;
						$wp_query->is_attachment        = false;
						$wp_query->is_singular          = false;
						$wp_query->is_posts_page        = false;
						$wp_query->is_post_type_archive = false;

						global $post;
						if ( ! $wp_query->queried_object ) {
							$post = new WP_Post( new stdClass() );

							$wp_query->post              = $post;
							$wp_query->posts             = array( $post );
							$wp_query->queried_object    = $post;
							$wp_query->queried_object_id = 0;
						}

						/**
						 *
						 */
						LP_Addon_Frontend_Editor_Page_Controller::show_page( $template );
					}
				} else {

					if ( is_user_logged_in() ) {
						wp_die( __( 'You don\'t have permission to access this page', 'learnpress-frontend-editor' ) );
					} else {
						if ( $redirect = apply_filters( 'learn-press/frontend-editor/login-redirect', learn_press_get_login_url() ) ) {
							wp_redirect( $redirect );
							die();
						}
					}
				}
			}

			return $template;
		}

		public function dashboard_content( $content ) {
			global $post;

			if ( get_post_type() === 'page' ) {
				remove_filter( 'the_content', array( $this, 'dashboard_content' ), 9999999 );

				ob_start();
				try {
					$this->get_template( 'dashboard' );
				}
				catch ( Exception $ex ) {

				}
				$content = ob_get_clean();
				remove_filter( 'the_content', array( $this, 'dashboard_content' ), 9999999 );
			}

			return $content;
		}

		public function locate_template( $template_name ) {
			$tmpl = parent::locate_template( $template_name );

			return preg_match( '~\.php$~', $tmpl ) ? $tmpl : "$tmpl.php";
		}

		/**
		 * @return LP_Addon_Frontend_Editor
		 */
		public static function instance() {
			return parent::instance();
		}

		/**
		 *
		 */
		public static function get_header() {
			if ( did_action( 'get_header' ) ) {
				return;
			}
			self::$load_header = true;
			get_header();
		}

		public static function get_footer() {
			/**
			 * If header isn't already loaded
			 */
			if ( ! self::$load_header ) {
				return;
			}
			get_footer();
		}
	}
}