<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://t.me/NSA007
 * @since      1.0.0
 *
 * @package    Webinars
 * @subpackage Webinars/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Webinars
 * @subpackage Webinars/admin
 * @author     Dennis Mwaniki <theguy3474@gmail.com>
 */
class Webinars_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->init();

	}

	protected function init(){
		add_filter( 'learn-press/course-settings-fields/archive', array($this, 'settingsFilters') );

		/* Change course title */
		add_filter( 'display_post_states', array($this, 'ecs_add_post_state'), 10, 2 );
		add_filter( 'manage_lp_course_posts_columns', array( $this, 'manage_course_posts_columns' ) );
		add_filter( 'wp_insert_post_empty_content', array($this, 'allow_empty_post'), 10, 2 );
		add_action( 'admin_menu', array( $this, 'notify_new_course' ) );
	}


	/*
	 * Notify an administrator with pending courses
	 */
	public function notify_new_course() {
		global $menu;
		global $submenu;


		$current_user = wp_get_current_user();
		if ( ! in_array( 'administrator', $current_user->roles ) ) {
			return;
		}
		$count_courses   = wp_count_posts( LP_COURSE_CPT );
		$awaiting_mod    = $count_courses->pending;
		$submenu['learn_press'][0][0] .= " <span class='custom awaiting-mod count-$awaiting_mod'><span class='pending-count'>" . number_format_i18n( $awaiting_mod ) . "</span></span>";
	}


	/*
	* Allow empty post to delete
	*/
	public function allow_empty_post( $maybe_empty, $postarr ) {
        if ( ! $maybe_empty ) {
                return $maybe_empty;
        }

        if ( 'lp_course' === $postarr['post_type']
             && in_array( $postarr['post_status'], array( 'inherit', 'draft', 'trash', 'auto-draft' ) )
        ) {
                $maybe_empty = false;
        }

        return $maybe_empty;
	}

	/**
	 * Add grade book column to course page in admin.
	 *
	 * @param  array $column
	 *
	 * @return array
	 */
	function manage_course_posts_columns( $column ) {
		unset($column['taxonomy-course_category']);
		unset($column['taxonomy-webinar_categories']);
			
		return $column;
	}


	/*
	* Course column customization 
	*/	
	public function ecs_add_post_state( $post_states, $post ) {
		$post_status = get_post_status($post);
			if($post_status == 'publish'){
				$post_states[] = ucfirst($post_status);
			}
		return $post_states;
	}

	/*
	* Set webner page to learnpress settings
	*/
	public function settingsFilters($settings){
		
		$new = array(
			'title'   => __( 'Webinars page', 'learnpress' ),
			'id'      => 'webinar_page_id',
			'default' => '',
			'type'    => 'pages-dropdown'
		);


		$new1 = array(
			'title'   => __( 'Webinars per page', 'learnpress' ),
			'id'      => 'wcount_page_id',
			'default' => 8,
			'type'    => 'number'
		);


		array_push($settings, $new);
		array_push($settings, $new1);
		return $settings;


	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Webinars_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Webinars_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, get_stylesheet_directory_uri() . '/inc/webinars/admin/css/webinars-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Webinars_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Webinars_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, get_stylesheet_directory_uri() . '/inc/webinars/admin/js/webinars-admin.js', array( 'jquery' ), $this->version, false );

	}
	function add_custom_taxonomies() {
		register_taxonomy('webinar_categories', 'lp_course', array(
		  // Hierarchical taxonomy (like categories)
		  'hierarchical' => true,
		  'query_var'         => true,
			'public'            => true,
					
			'show_ui'           => true,
			'show_in_menu'      => 'learn_press',
			'show_admin_column' => true,
			'show_in_admin_bar' => true,
			'show_in_nav_menus' => true,
		  // This array of options controls the labels displayed in the WordPress Admin UI
		  'labels' => array(
			'name' => _x( 'Webinar Categories', 'taxonomy general name' ),
			'singular_name' => _x( 'Webinar category', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Webinar categories' ),
			'all_items' => __( 'All Webinar categories' ),
			'parent_item' => __( 'Parent Webinar categories' ),
			'parent_item_colon' => __( 'Parent Webinar categories:' ),
			'edit_item' => __( 'Edit Webinar categories' ),
			'update_item' => __( 'Update Webinar categories' ),
			'add_new_item' => __( 'Add New Webinar categories' ),
			'new_item_name' => __( 'New Webinar category Name' ),
			'menu_name' => __( 'Webinar categories' ),
		  ),
		  // Control the slugs used for this taxonomy
		  'rewrite' => array(
			'slug' => 'webinars', 
			'with_front' => true, 
			'hierarchical' => true 
		  ),
		));
		register_taxonomy( 
            'webinar_tag', 
            'lp_course', 
            array( 
                'hierarchical'  => false, 
                'label'         => __( 'Webinar Tags'), 
                'singular_name' => __( 'Webinar Tag'), 
                'rewrite'       => true, 
                'query_var'     => true 
            )  
		);
	  }


	  function action_edit_post(){
		$screen = get_current_screen();
		$post_type = $screen->post_type;
		$post_id = (isset($_GET['post'])) ? $_GET['post'] : '';
		$courseType = get_post_meta( $post_id, '_course_type', true );
		if($courseType == 'webinar' && $post_type == 'lp_course'){
			echo '<script>
				jQuery(window).load(function(){
					jQuery("div#course_categorydiv, div#tagsdiv-course_tag").remove();
				});
			</script>';
		}else{
			echo '<script>
				jQuery(window).load(function(){
					jQuery("div#webinar_categoriesdiv, div#tagsdiv-webinar_tag").remove();
				});
				
			</script>';
		}
	  }
}

/*
* Webinars Category
*/
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/webinars-category.php';

/*
* Webinars Latest Webinars
*/
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/latest-webinars.php';