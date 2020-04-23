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
		add_action( 'pre_get_posts', array($this, 'course_duration_by_lesson_total_duration') );
		add_action( 'add_meta_boxes', array($this, 'wpdocs_register_meta_boxes') );
		add_filter( 'custom_menu_order', array($this, 'changeAdminSubMenuOrderForLearnPress') );
		add_filter( 'learn_press_admin_tabs_on_pages', array( $this, 'admin_tabs_pages' ), 10, 2 );
		add_filter( 'learn_press_admin_tabs_info', array( $this, 'admin_tab' ), 10, 2 );
		// add_action('restrict_manage_posts', array($this, 'add_post_formats_filter_to_post_administration'));
		// add_action('quick_edit_custom_box',  array($this, 'misha_quick_edit_fields'), 10, 2);
		add_filter('learn-press/payment-settings/general', array($this, 'addFreeEnrollCheckboxToSettingsGeneral'));
		// add_filter('the_content', array($this, 'testcontent'));
	}

	public function testcontent($content){
		// return 'test o';
	}

	public function addFreeEnrollCheckboxToSettingsGeneral($settings){
		// echo 'general Settings<br/><pre>';
		// print_r($settings);
		// echo '</pre>';
		$settings[] = array(
				'title'   => __( 'Enable Enroll button for free Courses/Webinars', 'learnpress' ),
				'id'      => 'free_enroll_allow',
				'default' => 'no',
				'type'    => 'yes-no',
				'desc'    => __( 'Enable user enroll course / webinar as free.', 'learnpress' )
		);
		return $settings;
	}
	public function misha_quick_edit_fields(){
		echo '<div>sfsfsf</div>';
	}





		/**
		 * @param $pages
		 *
		 * @return array
		 */
		public function admin_tabs_pages( $pages ) {
			$allowArray = array('learnpress_page_zoom-webinars', 'edit-webinar_tag', 'edit-webinar_categories');
			if ( in_array(get_current_screen()->id, $allowArray) )
			{
				$pages = $allowArray;
			}	
			return $pages;
		}


		/**
		 * @param $tabs
		 *
		 * @return array
		 */
		public function admin_tab( $tabs ) {
			$allowArray = array('learnpress_page_zoom-webinars', 'edit-webinar_categories', 'edit-webinar_tag');
			$links = array(
				'admin.php?page=zoom-webinars',
				'edit-tags.php?taxonomy=webinar_categories&post_type=lp_course',
				'edit-tags.php?taxonomy=webinar_tag&post_type=lp_course'
			);
			$names = array(
				__( "Webinars", "learnpress" ),
				__( "Webinar Category", "learnpress" ),
				__( "Webinar Tags", "learnpress" )
			);

			if ( in_array(get_current_screen()->id, $allowArray) ){
				$tabs = array();
				foreach($allowArray as $k => $sa){
					$tabs[] = array(
						"link" => $links[$k],
						"name" => $names[$k],
						"id"   => $sa
					);
				}	
			}

			return $tabs;
		}


	/*
	* Change sub-menu order
	*/
	public function changeAdminSubMenuOrderForLearnPress($menu_ord){
		global $submenu;
		$newarray = array();
		$newarray[] = $submenu['learn_press'][0];
		$newarray[] = $submenu['learn_press'][10];
		$newarray[] = $submenu['learn_press'][1];
		$newarray[] = $submenu['learn_press'][2];
		$newarray[] = $submenu['learn_press'][3];
		$newarray[] = $submenu['learn_press'][4];
		$newarray[] = $submenu['learn_press'][5];
		$newarray[] = $submenu['learn_press'][6];
		$newarray[] = $submenu['learn_press'][7];
		$newarray[] = $submenu['learn_press'][8];
		$newarray[] = $submenu['learn_press'][9];
		$submenu['learn_press'] = $newarray;
	}


	/*
	* Lession metabox for lession video intro
	*/
	public function wpdocs_register_meta_boxes() {
		add_meta_box( 'lession-video-data', __( 'Lession Intro Video', 'textdomain' ), array($this, 'lp_lession_metabox'), 'lp_lesson', 'side', 'core' );
	}
	
	public function lp_lession_metabox($post){
		
		$internal_video = get_post_meta( $post->ID, '_lp_lesson_video_intro_internal', true );
		$internal_video = json_decode($internal_video, true);
		
		// echo '<pre>';
		// print_r($internal_video);
		// echo '</pre>';

		echo '<input type="hidden" name="_lp_lesson_video_intro_internal" value="'.$internal_video.'" />';
		
		?>
		<div class="acf-field acf-field-file acf-field-5d52623d7778a" data-name="upload_intro_video" data-type="file" data-key="field_5d52623d7778a">
		<div class="acf-label">
		<label for="acf-field_5d52623d7778a"><?php _e('Upload Intro Video', 'webinar'); ?></label></div>
		
		
		<div class="acf-input">
				<div class="acf-file-uploader <?= ($internal_video) ? 'has-value':''; ?>" data-library="uploadedTo" data-mime_types="mp4" data-uploader="wp">
			
			<?php if($internal_video): ?>
				<div class="show-if-value file-wrap">
				<div class="file-icon">
					<img data-name="icon" src="<?php echo $internal_video['icon']; ?>" alt="">
				</div>
				<div class="file-info">
					<p>
						<strong data-name="title"><?php echo $internal_video['title']; ?></strong>
					</p>
					<p>
						<strong><?php _e('File name', 'webinar') ?>:</strong>
						<a data-name="filename" href="<?php echo $internal_video['url']; ?>" target="_blank"><?php echo $internal_video['filename']; ?></a>
					</p>
					<p>
						<strong><?php _e('File size', 'webinar'); ?>:</strong>
						<span data-name="filesize"><?php echo $internal_video['filesizeHumanReadable']; ?></span>
					</p>
				</div>
				<div class="acf-actions -hover">
					<!-- <a class="acf-icon -pencil dark" data-name="edit" href="#" title="Edit"></a> -->
					<a href="#" data-action="delete_admin_intro" title="Remove" class="acf-icon -cancel remove_lesson_media_attachment dark"></a>
				</div>
			</div>
			<?php endif; ?>
			<div class="hide-if-value">
							
					<p>No file selected 
					<a data-id="intro_video_lesson" class="acf-button button" href="#">Add File</a>
					</p>
			</div>
		</div>
		</div>
		</div>

		<?php
		
	}


	
	/*
	* Change course duration by total lession duration
	*/
	public function course_duration_by_lesson_total_duration($query){
		global $post;
		if(is_admin() && $_REQUEST['action'] == 'edit' && get_post_type( $post ) == 'lp_course' ) {
			$lessons = get_course_lessons($post->ID);
			$course_type = get_post_meta( $post->ID, '_course_type', true );
			
			if($course_type != 'webinar'){
			$_lp_duration = 0;
			foreach($lessons as $sl){
				if(get_post_meta( $sl, '_lp_duration', true )){
					$duration_array = get_post_meta( $sl, '_lp_duration', true );
					$duration_array = preg_split('/(?<=[0-9]) (?=[a-z]+)/i',$duration_array);                                                               
					switch($duration_array[1]){
						case 'minute':
							$_lp_duration += $duration_array[0];
						break;
						case 'hour':
							$mint = $duration_array[0] * 60;
							$_lp_duration += $mint;
						break;
						case 'week':
							$mint = $duration_array[0] * 7;
							$mint = $mint * 24;
							$mint = $mint * 60;
							$_lp_duration += $mint;
						break;
						case 'day':
							$mint = $duration_array[0] * 24;
							$mint = $mint * 60;
							$_lp_duration += $mint;
						break;
					}
				}
			}
			update_post_meta( $post->ID, '_lp_duration', $_lp_duration . ' minute' );
			}
		}
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
		if(isset($_REQUEST['_course_type']) && $_REQUEST['_course_type'] == 'webinar') $courseType = $_REQUEST['_course_type'];
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