<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://t.me/NSA007
 * @since      1.0.0
 *
 * @package    Webinars
 * @subpackage Webinars/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Webinars
 * @subpackage Webinars/public
 * @author     Dennis Mwaniki <theguy3474@gmail.com>
 */
// use LP_Lesson;
class Webinars_Public {

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

	/*
	* Couse ID
	*/
	private $course_id;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->register_shortcodes();
		$this->init();
	}

	public function init(){
		// add_action( 'thim_courses_loop_item_thumb', array($this, 'thim_courses_loop_item_thumbnail_webiner'), 20, 5 );
		// do_action('wp_head', array($this, 'testfunction'));
		// add_action( 'learn-press/section-summary', $section );
		// add_action( 'learn-press/section-summary', array($this, 'sectionSummerychangetimestatus'), 10, 2 );
	}

	public function testfunction($content){
		global $wpdb;
		$alldata = $wpdb->get_results('SELECT * FROM `sours8nl_learnpress_sections`', OBJECT);
		$alldata21 = $wpdb->get_results('SELECT * FROM `sours8nl_learnpress_section_items`', OBJECT);

	
		echo 'All data: <br/><pre>';
		print_r($alldata);
		echo '</pre>';
		echo 'All data2: <br/><pre>';
		print_r($alldata21);
		echo '</pre>';
	}

	/**
 * Display thumbnail course
 */

	public function is_rebon( $status ){
		$page = get_queried_object_id();
		$course_page_id = learn_press_get_page_id( 'webinar' );

		if($course_page_id == $page && $status !='') echo('<div class="ribbon ribbon-top-left"><span>'.$status.'</span></div>');
	}
	public function thim_courses_loop_item_thumbnail_webiner( $course = null ) {
		
		$course = LP_Global::course();
		
		global $frontend_editor;
		global $wpdb;
		$post_manage = $frontend_editor->post_manage;
		$course = LP_Global::course();
		if($course->get_post_status() !== "Draft"){
			$url = $post_manage->get_edit_post_link( get_post_type(), get_the_id() );
		}
		else {
			$url = get_the_permalink( $course->get_id() );
		}
		
		
		
		$course_id = $course->get_id();
		$lessons = $this->get_course_lessons($course_id);
		// echo 'lesson <pre><br/>';
		// print_r($lessons);

		// echo '</pre>';
		// $lessons = learn_press_get_lessons($course->get_id());
		$currentdate = date('Y-m-d h:i:s');
		$past = false;
		$future = false;
		foreach($lessons as $lesson){
			$lessonDate = get_post_meta( $lesson, '_lp_webinar_when', true );
			$lessonDate = str_replace('/', '-', $lessonDate);
			$lessonDate = ($lessonDate) ? date('Y-m-d h:i:s', strtotime($lessonDate)) : '';
			if($lessonDate !='' && $lessonDate <= $currentdate) $past = true;
			if($lessonDate !='' && $lessonDate >= $currentdate) $future = true;
		}

		
		$status = '';
		if($past == true && $future == true) $status = 'Progress';
		if($past == true && $future == false) $status = 'Passed';

		echo '<div class="course-thumbnail">';
		echo 'test o';
		echo '<a class="thumb" href="' . esc_url( $url ) . '" >';
		echo thim_get_feature_image( get_post_thumbnail_id( $course->get_id() ), 'full', apply_filters( 'thim_course_thumbnail_width', 400 ), apply_filters( 'thim_course_thumbnail_height', 320 ), $course->get_title() );
		echo ($this->is_rebon($status));
		echo '</a>';
		do_action( 'thim_inner_thumbnail_course' );
		echo '<a class="course-readmore" href="' . esc_url( get_the_permalink( $course->get_id() ) ) . '">' . esc_html__( 'Read More', 'eduma' ) . '</a>';
		echo '</div>';
	}



	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( 'webinarspubliccss', get_stylesheet_directory_uri() . '/inc/webinars/public/css/webinars-public.css', array(), time(), 'all' );

	}



	function customize_root_slug(){
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
	 * Register the JavaScript for the public-facing side of the site.
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
		// echo '<pre>';
		// print_r(get_user_meta( get_current_user_id() ));
		// echo '</pre>';
		
		wp_enqueue_script( 'webinarsPublicJS', get_stylesheet_directory_uri() . '/inc/webinars/public/js/webinars-public.js', array( 'jquery' ), time(), false );
		// 
		wp_localize_script( 'webinarsPublicJS', 'lp_webinars', array(
			'instructor' => $this->customize_root_slug(),
			'instructor_url' => get_home_url( '/') . '/' .  $this->customize_root_slug()
		) );

	}
	/**
* Returns a post object of random quotes
*
* @param array $params An array of optional parameters
* quantity Number of quote posts to return
*
* @return object A post object
*/

public function get_webinars() {
	$return = '';
	$args = array(
		'post_type' => 'lp_course', 
		'meta_value' => 'webinar',
		'posts_per_page' => -1,
		'orderby' => 'rand'
	);

	$query = new WP_Query( $args );

	if ( is_wp_error( $query ) ) {
		$return = 'Oops!...No webinars for you!';
	} else {
		$return = $query->posts;
	}

	return $return;
} // get_webinars()


public function get_course_lessons($course_id){
		$course = learn_press_get_course( $course_id );
		$sections = $course->get_curriculum_raw();
		$lessons = array();
		if ( ! empty( $sections ) ) {
			foreach ( $sections as $section ) {
					if ( isset( $section['items'] ) && is_array( $section['items'] ) ) {
						foreach($section['items'] as $singleitem){
							if($singleitem['type'] == 'lp_lesson') array_push($lessons, $singleitem['id']);
						}
					}
			}
		}
		return $lessons;
}


public function register_webiner_sidebar(){
	register_sidebar( array(
        'name' => __( 'Webinars Sidebar', 'webinar' ),
        'id' => 'webinars-sidebar',
        'description' => __( 'Sidebar for Webinars page.', 'webinar' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
    ) );
}

public function register_shortcodes() {
	add_action( 'vc_before_init', array($this, 'listwebinarsshortMap' ));
	add_shortcode( 'webinarsrr', array( $this, 'webinarsCallback' ) );
}


function listwebinarsshortMap() {
// 	vc_map( 
// 		array(
// 		   "name" => __( "Thim: Webinars", "webinar" ),
// 		   "base" => "webinarsrr",
// 		   "class" => "",
// 		   "category" => __( "Thim Shortcodes", "webinar"),
// 		   "icon" => 'vc_general vc_element-icon thim-widget-icon thim-widget-icon-courses'
// 	   ) 
//    );
   vc_map( array(
	'name'        => esc_html__( 'Thim: Webinars', 'eduma' ),
	// 'base'        => 'thim-courses',
	'base'        => 'webinarsrr',
	'category'    => esc_html__( 'Thim Shortcodes', 'eduma' ),
	'description' => esc_html__( 'Display Webinars.', 'eduma' ),
	'icon'        => 'thim-widget-icon thim-widget-icon-courses',
	'params'      => array(
		array(
			'type'        => 'textfield',
			'admin_label' => true,
			'heading'     => esc_html__( 'Heading text', 'eduma' ),
			'param_name'  => 'title',
			'value'       => '',
		),

		array(
			'type'        => 'number',
			'admin_label' => true,
			'heading'     => esc_html__( 'Limit', 'eduma' ),
			'param_name'  => 'limit',
			'min'         => 1,
			'max'         => 20,
			'std'         => '8',
			'description' => esc_html__( 'Limit number courses.', 'eduma' )
		),

		array(
			'type'        => 'checkbox',
			'admin_label' => true,
			'heading'     => esc_html__( 'Featured', 'eduma' ),
			'param_name'  => 'featured',
			'description' => esc_html__( 'Only display featured courses', 'eduma' ),
			'std'         => false,
		),

		array(
			'type'        => 'dropdown',
			'admin_label' => true,
			'heading'     => esc_html__( 'Order By', 'eduma' ),
			'param_name'  => 'order',
			'value'       => array(
				esc_html__( 'Select', 'eduma' )   => '',
				esc_html__( 'Popular', 'eduma' )  => 'popular',
				esc_html__( 'Latest', 'eduma' )   => 'latest',
				esc_html__( 'Webinar Category', 'eduma' ) => 'category',
			),
			'description' => esc_html__( 'Select order by.', 'eduma' ),
		),

		array(
			'type'       => 'dropdown',
			'heading'    => esc_html__( 'Select Webinar Category', 'eduma' ),
			'param_name' => 'cat_id',
			'value'      => thim_sc_get_wibner_categories( array( 'All' => esc_html__( 'all', 'eduma' ) ) ),
			'dependency' => array(
				'element' => 'order',
				'value'   => 'category',
			),
		),

		array(
			'type'        => 'dropdown',
			'admin_label' => true,
			'heading'     => esc_html__( 'Layout', 'eduma' ),
			'param_name'  => 'layout',
			'value'       => array(
				esc_html__( 'Select', 'eduma' )        => '',
				esc_html__( 'Slider', 'eduma' )        => 'slider',
				esc_html__( 'Grid', 'eduma' )          => 'grid',
                esc_html__( 'Grid 1', 'eduma' )  => 'grid1',
				esc_html__( 'Category Tabs', 'eduma' ) => 'tabs',
				esc_html__( 'Mega Menu', 'eduma' )     => 'megamenu',
				esc_html__( 'List Sidebar', 'eduma' )  => 'list-sidebar',
                esc_html__( 'Category Tabs Slider', 'eduma' )  => 'tabs-slider',
                esc_html__( 'Slider - Home Instructor', 'eduma' )  => 'slider-instructor',
                esc_html__( 'Grid - Home Instructor', 'eduma' )  => 'grid-instructor',
			),
		),

        array(
            'type'        => 'number',
            'admin_label' => true,
            'heading'     => esc_html__( 'Thumbnail Width', 'eduma' ),
            'param_name'  => 'thumbnail_width',
            'min'         => 100,
            'max'         => 800,
            'std'         => 400,
            'description' => esc_html__( 'Set width for thumbnail course.', 'eduma' ),
            'dependency'  => array(
                'element' => 'layout',
                'value'   => array( 'slider', 'grid', 'grid1', 'tabs', 'tabs-slider','slider-instructor','grid-instructor' ),
            ),
        ),

        array(
            'type'        => 'number',
            'admin_label' => true,
            'heading'     => esc_html__( 'Thumbnail Height', 'eduma' ),
            'param_name'  => 'thumbnail_height',
            'min'         => 100,
            'max'         => 800,
            'std'         => 300,
            'description' => esc_html__( 'Set height for thumbnail course.', 'eduma' ),
            'dependency'  => array(
                'element' => 'layout',
                'value'   => array( 'slider', 'grid', 'grid1', 'tabs', 'tabs-slider','slider-instructor','grid-instructor' ),
            ),
        ),

		//Slider Options
		array(
			'type'        => 'number',
			'admin_label' => true,
			'heading'     => esc_html__( 'Items Visible', 'eduma' ),
			'param_name'  => 'slider_item_visible',
			'min'         => 1,
			'max'         => 20,
			'std'         => '3',
			'dependency'  => array(
				'element' => 'layout',
				'value'   => array('slider','slider-instructor'),
			),
			'group'       => esc_html__( 'Slider Settings', 'eduma' ),
		),

		array(
			'type'        => 'textfield',
			'admin_label' => true,
			'heading'     => esc_html__( 'Auto Play Speed (in ms)', 'eduma' ),
			'param_name'  => 'slider_auto_play',
			'std'         => '0',
			'description' => esc_html__( 'Set 0 to disable auto play.', 'eduma' ),
			'dependency'  => array(
				'element' => 'layout',
				'value'   => array('slider','slider-instructor'),
			),
			'group'       => esc_html__( 'Slider Settings', 'eduma' ),
		),

		array(
			'type'        => 'checkbox',
			'admin_label' => true,
			'heading'     => esc_html__( 'Show Pagination', 'eduma' ),
			'param_name'  => 'slider_pagination',
			'std'         => false,
			'dependency'  => array(
				'element' => 'layout',
				'value'   => array('slider','slider-instructor'),
			),
			'group'       => esc_html__( 'Slider Settings', 'eduma' ),
		),

		array(
			'type'        => 'checkbox',
			'admin_label' => true,
			'heading'     => esc_html__( 'Show Navigation', 'eduma' ),
			'param_name'  => 'slider_navigation',
			'std'         => true,
			'dependency'  => array(
				'element' => 'layout',
				'value'   => array('slider','slider-instructor'),
			),
			'group'       => esc_html__( 'Slider Settings', 'eduma' ),
		),

		//Grid options
		array(
			'type'        => 'number',
			'admin_label' => true,
			'heading'     => esc_html__( 'Grid Columns', 'eduma' ),
			'param_name'  => 'grid_columns',
			'min'         => 1,
			'max'         => 20,
			'std'         => '3',
			'dependency'  => array(
				'element' => 'layout',
				'value'   => array('grid', 'grid1','grid-instructor'),
			),
			'group'       => esc_html__( 'Grid Settings', 'eduma' ),
		),

		array(
			'type'        => 'textfield',
			'admin_label' => true,
			'heading'     => esc_html__( 'Text View All Courses', 'eduma' ),
			'param_name'  => 'view_all_courses',
			'dependency'  => array(
				'element' => 'layout',
				'value'   => array('grid', 'grid1', 'tabs-slider','grid-instructor'),
			),
			'group'       => esc_html__( 'Grid Settings', 'eduma' ),
		),

		array(
			'type'        => 'dropdown',
			'admin_label' => true,
			'heading'     => esc_html__( 'View All Position', 'eduma' ),
			'param_name'  => 'view_all_position',
			'value'       => array(
				esc_html__( 'Select', 'eduma' ) => '',
				esc_html__( 'Top', 'eduma' )    => 'top',
				esc_html__( 'Bottom', 'eduma' ) => 'bottom',
			),
			'dependency'  => array(
				'element' => 'layout',
				'value'   => array('grid', 'grid1', 'tabs-slider','grid-instructor'),
			),
			'group'       => esc_html__( 'Grid Settings', 'eduma' ),
		),

		//Tabs options
		array(
			'type'        => 'number',
			'admin_label' => true,
			'heading'     => esc_html__( 'Limit Tab', 'eduma' ),
			'param_name'  => 'limit_tab',
			'min'         => 1,
			'max'         => 20,
			'std'         => '4',
			'dependency'  => array(
				'element' => 'layout',
				'value'   => array('tabs', 'tabs-slider'),
			),
			'group'       => esc_html__( 'Tabs Settings', 'eduma' ),
		),

		array(
			'type'       => 'dropdown_multiple',
			'heading'    => esc_html__( 'Select Category Tabs', 'eduma' ),
			'param_name' => 'cat_id_tab',
			'std'        => 'all',
			'value'      => thim_sc_get_wibner_categories(),
			'dependency' => array(
				'element' => 'layout',
				'value'   => array('tabs', 'tabs-slider'),
			),
			'group'      => esc_html__( 'Tabs Settings', 'eduma' ),
		),

		//Animation
		array(
			'type'        => 'dropdown',
			'heading'     => esc_html__( 'Animation', 'eduma' ),
			'param_name'  => 'css_animation',
			'admin_label' => true,
			'value'       => array(
				esc_html__( 'No', 'eduma' )                 => '',
				esc_html__( 'Top to bottom', 'eduma' )      => 'top-to-bottom',
				esc_html__( 'Bottom to top', 'eduma' )      => 'bottom-to-top',
				esc_html__( 'Left to right', 'eduma' )      => 'left-to-right',
				esc_html__( 'Right to left', 'eduma' )      => 'right-to-left',
				esc_html__( 'Appear from center', 'eduma' ) => 'appear'
			),
			'description' => esc_html__( 'Select type of animation if you want this element to be animated when it enters into the browsers viewport. Note: Works only in modern browsers.', 'eduma' )
		),

        // Extra class
        array(
            'type'        => 'textfield',
            'admin_label' => true,
            'heading'     => esc_html__( 'Extra class', 'eduma' ),
            'param_name'  => 'el_class',
            'value'       => '',
            'description' => esc_html__( 'Add extra class name that will be applied to the icon box, and you can use this class for your customizations.', 'eduma' ),
        ),
	)
) );
}





function thim_shortcode_courses( $atts ) {
	$instance = shortcode_atts( array(
		'title'               => '',
		'limit'               => '8',
		'featured'            => false,
		'layout'              => 'slider',
		'thumbnail_width'     => 400,
		'thumbnail_height'    => 300,
		'order'               => '',
		'cat_id'              => '',
		'slider_pagination'   => '',
		'slider_navigation'   => '',
		'slider_item_visible' => '3',
		'slider_auto_play'    => '0',
		'grid_columns'        => '3',
		'view_all_courses'    => '',
		'view_all_position'   => '',
		'limit_tab'           => '4',
		'cat_id_tab'          => '',
		'css_animation'       => '',
        'el_class'           => '',
	), $atts );

	$instance['slider-options']['show_pagination'] = $instance['slider_pagination'];
	$instance['slider-options']['show_navigation'] = $instance['slider_navigation'];
	$instance['slider-options']['item_visible']    = $instance['slider_item_visible'];
	$instance['slider-options']['auto_play']       = $instance['slider_auto_play'];
	$instance['grid-options']['columns'] = $instance['grid_columns'];
	$instance['tabs-options']['limit_tab']  = $instance['limit_tab'];
	if($instance['cat_id_tab']){
		$instance['tabs-options']['cat_id_tab'] = explode( ',', $instance['cat_id_tab'] );
	}else{
		$instance['tabs-options']['cat_id_tab'] = '';
	}
	$args                 = array();
	$args['before_title'] = '<h3 class="widget-title">';
	$args['after_title']  = '</h3>';

    if ( thim_is_new_learnpress( '3.0' ) ) {
        $layout = $instance['layout'] . '-v3.php';
        $class_layout = ' template-'. $instance['layout'] . '-v3';
    } else if ( thim_is_new_learnpress( '2.0' ) ) {
		$layout = $instance['layout'] . '-v2.php';
	    $class_layout = ' template-'. $instance['layout'] . '-v2';
	} else {
		$layout = $instance['layout'] . '-v1.php';
	    $class_layout = ' template-'. $instance['layout'] . '-v1';
	}

	$widget_template       = THIM_DIR . 'inc/widgets/courses/tpl/' . $layout;
	$child_widget_template = THIM_CHILD_THEME_DIR . 'inc/widgets/courses/' . $layout;
	if ( file_exists( $child_widget_template ) ) {
		$widget_template = $child_widget_template;
	}
	ob_start();
    if($instance['el_class']) echo '<div class="'.$instance['el_class'].'">';
	echo '<div class="thim-widget-courses'. $class_layout .'">';
	include $widget_template;
	echo '</div>';
    if($instance['el_class']) echo '</div>';
	$html = ob_get_contents();
	ob_end_clean();

	return $html;
}

// public function webinarsCallback( $atts ) {

// 	echo('<div class="row">');
// 	ob_start();
// 	echo('<style>
// 	#webinars #lp-archive-courses{
// 		display: block !important;
// 	}
// 	#lp-archive-courses{
// 		display: none;
// 	}
// 	#comments{
// 		display: none;
// 	}
// 	@media only screen and (min-width: 768px) {
// 		.row{
// 			width: 1215px;	
// 		}

// 	}
// 	</style>');
// 	echo('<div id="webinars" class="site-main col-sm-9 alignleft">');


	
// 	$nargs = array(
// 			'post_type'   => 'lp_course',
// 			'post_status' => 'publish',
// 			'meta_value' => 'webinar',
// 	);
	
	



// 	// $lessonmeta = get_post_meta( 11260 );
// 	global $wpdb;
	
// 	$alltables = $wpdb->get_row("SELECT * FROM `sours8nl_learnpress_sections`", OBJECT);
// 	$alldata21 = $wpdb->get_results('SELECT * FROM `sours8nl_learnpress_section_items`', OBJECT);

// 	$posts_ids = array();
// 	// $posts = get_posts($nargs);


// 	// Sidebar Filter Start
// 	if(isset($_REQUEST["date-filter"])){
// 		$dateperiod = $_REQUEST ["date-filter"];
// 		switch($dateperiod){
// 			case 'future':
// 				$posts_ids = $this->allcourseidsbytime($nargs, $dateperiod);
// 			break;
// 			case 'inprogress':
// 				$posts_ids = $this->allcourseidsbytime($nargs, $dateperiod);
// 			break;
// 			case 'passed':
// 				$posts_ids = $this->allcourseidsbytime($nargs, $dateperiod);
// 			break;
// 		}
// 		$nargs['post__in'] = $posts_ids;
// 	}
	
// 	if(isset($_REQUEST["course-price-filter"])){
// 		$price = $_REQUEST["course-price-filter"];
// 		switch($price){
// 			case 'paid':
// 				$nargs['meta_query'] = array(
// 					array(
// 						'key' => '_lp_price',
// 						 'value' => 0,
// 						 'type' => 'numaric',
// 						 'compare' => '>'
// 					)
// 				);
// 			break;
// 			case 'free':
// 				$nargs['meta_query'] = array(
// 					'relation' => 'OR',
// 					array(
// 						'key' => '_lp_price',
// 						'value' => 0,
// 						'type' => 'numaric',
// 						'compare' => '<='
// 					),
// 					array(
// 						'key' => '_lp_price',
// 						'compare' => 'NOT EXISTS'
// 					)
// 				);
// 			break;
// 			default:
// 			$nargs = $nargs;

// 		}

// 	} // End if(isset($_REQUEST["course-price-filter"]))

// 	// Webinar Category filter 
// 	if(isset($_REQUEST['webinar-cate-filter'])){
// 		$cats = $_REQUEST['webinar-cate-filter'];
// 		if(count($cats) > 0){
// 			$nargs['tax_query'] = array(
// 				array(
// 					'taxonomy'  => 'webinar_categories',
// 					'field'     => 'term_id',
// 					'terms'     => $cats,
// 				)
// 			);
// 		}
// 	}
// 	// Sidebar Filter End


//     if(isset($_REQUEST["s"])){
// 		$title = $_REQUEST["s"];
// 		$nargs['s'] = $_REQUEST["s"];
// 	} //  End Search

// 	$args = shortcode_atts( array(
// 		'num-quotes' => 20,
// 		'quotes-title' => 'Webinars',),
// 		$atts
// 	);
// 	$testimonials = new WP_Query( $nargs );
	
// 	$_COOKIE['testimonials'] = $testimonials;
// 	//$items = $this->get_webinars();
// 	do_action( 'learn_press_before_main_content' );

// 	/**
// 	 * @since 3.0.0
// 	 */
// 	do_action( 'learn-press/before-main-content' );

// 	/**
// 	 * @deprecated
// 	 */
// 	do_action( 'learn_press_archive_description' );

// 	/**
// 	 * @since 3.0.0
// 	 */
// 	include('search.php');
// 	do_action( 'learn-press/archive-description' );
// 	do_action( 'learn_press_before_courses_loop' );

// 	//echo('<h4>' . $args['quotes-title'] . '</h4>');
// 	// echo 'templatepart: ' . learn_press_get_template_part() . '<br/>';
// 	do_action( 'learn-press/before-courses-loop' );
// 	learn_press_begin_courses_loop();
//     echo('<div id="thim-course-archive" class="thim-course-grid" data-cookie="grid-layout">');
// 	while ( $testimonials->have_posts() ) : $testimonials->the_post();
// 		learn_press_get_template_part( 'content', 'webinar' );
// 	endwhile;
//     echo('</div>');
// 	learn_press_end_courses_loop();
// 	do_action( 'learn_press_after_courses_loop' );
// 	do_action( 'learn-press/after-courses-loop' );
// 	do_action( 'learn-press/after-main-content' );

// 	/**
// 	 * @deprecated
// 	 */
// 	do_action( 'learn_press_after_main_content' );
// 	echo('</div>');
// 	echo('</div>');
// 	include('sidebar.php');
// 	echo('</div>');
// 	} 





public function webinarsCallback( $atts ) {

		
		ob_start();
		
		$instance = shortcode_atts( array(
			'title'               => '',
			'limit'               => '8',
			'featured'            => false,
			'layout'              => 'slider',
			'thumbnail_width'     => 400,
			'thumbnail_height'    => 300,
			'order'               => '',
			'cat_id'              => '',
			'slider_pagination'   => '',
			'slider_navigation'   => '',
			'slider_item_visible' => '3',
			'slider_auto_play'    => '0',
			'grid_columns'        => '3',
			'view_all_courses'    => '',
			'view_all_position'   => '',
			'limit_tab'           => '4',
			'cat_id_tab'          => '',
			'css_animation'       => '',
			'el_class'           => '',
		), $atts );
	
		$instance['slider-options']['show_pagination'] = $instance['slider_pagination'];
		$instance['slider-options']['show_navigation'] = $instance['slider_navigation'];
		$instance['slider-options']['item_visible']    = $instance['slider_item_visible'];
		$instance['slider-options']['auto_play']       = $instance['slider_auto_play'];
		$instance['grid-options']['columns'] = $instance['grid_columns'];
		$instance['tabs-options']['limit_tab']  = $instance['limit_tab'];
		if($instance['cat_id_tab']){
			$instance['tabs-options']['cat_id_tab'] = explode( ',', $instance['cat_id_tab'] );
		}else{
			$instance['tabs-options']['cat_id_tab'] = '';
		}
		$args                 = array();
		$args['before_title'] = '<h3 class="widget-title">';
		$args['after_title']  = '</h3>';
	
		if ( thim_is_new_learnpress( '3.0' ) ) {
			$layout = $instance['layout'] . '-v3.php';
			$class_layout = ' template-'. $instance['layout'] . '-v3';
		} else if ( thim_is_new_learnpress( '2.0' ) ) {
			$layout = $instance['layout'] . '-v2.php';
			$class_layout = ' template-'. $instance['layout'] . '-v2';
		} else {
			$layout = $instance['layout'] . '-v1.php';
			$class_layout = ' template-'. $instance['layout'] . '-v1';
		}
	

			// echo 'layout: ' . $layout . '<br/>';
			// echo 'class: ' . $instance['el_class'] . '<br/>';
		$widget_template       = THIM_DIR . 'inc/widgets/courses/tpl/' . $layout;
		$child_widget_template = THIM_CHILD_THEME_DIR . 'inc/widgets/courses/' . $layout;
		$wibinar_template = THIM_CHILD_THEME_DIR . 'inc/webinars/public/template/' . $layout;
		if ( file_exists( $child_widget_template ) ) {
			$widget_template = $child_widget_template;
		}
		if ( file_exists( $wibinar_template ) ) {
			$widget_template = $wibinar_template;
		}

		if($instance['el_class']) echo '<div class="'.$instance['el_class'].'">';
		echo '<div class="thim-widget-courses'. $class_layout .'">';
		include $widget_template;
		echo '</div>';
		if($instance['el_class']) echo '</div>';

		$html = ob_get_contents();
		ob_end_clean();
	
		return $html;
		} 
}


