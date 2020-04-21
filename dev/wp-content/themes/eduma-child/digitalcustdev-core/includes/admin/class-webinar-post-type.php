<?php

/**
 * Add Metaboxes to EventOn Plugin related to Zoom Meeting ID
 * @author  Deepen
 * @since  1.0.0
 */
class Admin_DigitalCustDev_Webinar {

	/**
	 * WP_List_Table object
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      $webinar_list_table
	 */
	private $webinar_list_table;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );

		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );

		add_action( 'pre_get_posts', array( $this, 'filter_admin_results_courses' ) );

		add_action( 'save_post_lp_lesson', array( $this, 'save' ), 99 );

		add_action( 'admin_enqueue_scripts', array($this, 'add_admin_scripts'), 10, 1 );
	}



	public function add_admin_scripts($hook){
		if($hook == 'learnpress_page_zoom-webinars'){
			wp_enqueue_script( 'inline-edit-post');
		}
	}
	public function menu() {
		$args = array( 'post_type' => LP_COURSE_CPT, 'posts_per_page' => -1, 'post_status' => 'pending', 'meta_key' => '_course_type', 'meta_value' => 'webinar' );
		$query = new WP_Query( $args );
		$awaiting_mod = $query->post_count;
		
		$countrer = "<span class='custom awaiting-mod count-$awaiting_mod'><span class='pending-count'>" . number_format_i18n( $awaiting_mod ) . "</span></span>";
		$page_hook = add_submenu_page( 'learn_press', __( 'Webinars', 'digitalcustdev-core' ), __( 'Webinars' , 'digitalcustdev-core' ) . $countrer, 'manage_options', 'zoom-webinars', array( $this, 'load_webinar_list_table' ) );

		/*
		 * The $page_hook_suffix can be combined with the load-($page_hook) action hook
		 * https://codex.wordpress.org/Plugin_API/Action_Reference/load-(page)
		 *
		 * The callback below will be called when the respective page is loaded
		 *
		 */
		add_action( 'load-' . $page_hook, array( $this, 'load_webinar_list_table_screen_options' ) );


		add_submenu_page( 'zoom-video-conferencing', __( 'Webinars', 'video-conferencing-with-zoom-plank' ), __( 'Webinars', 'video-conferencing-with-zoom-plank' ), 'manage_options', 'zoom-video-conferencing-webinars', array( $this, 'webinars_list' ) );
	}

	function load_webinar_list_table_screen_options() {
		$arguments = array(
			'label'   => __( 'Webinars per page', 'digitalcustdev-core' ),
			'default' => 10,
			'option'  => 'webinars_per_page'
		);

		add_screen_option( 'per_page', $arguments );

		// instantiate the User List Table
		$this->webinar_list_table = new DigitalCustDev_Webinar_List_Table( 'digitalcustdev-core' );
	}

	/*
	 * Display the User List Table
	 *
	 * Callback for the add_users_page() in the add_plugin_admin_menu() method of this class.
	 *
	 * @since	1.0.0
	 */
	public function load_webinar_list_table() {
		// query, filter, and sort the data
		$this->webinar_list_table->prepare_items();
		
		// echo 'omar test<pre>';
		// print_r($this->webinar_list_table->prepare_items());
		// echo '</pre>';
		// echo 'render from : ' . DIGITALCUSTDEV_PLUGIN_PATH . 'views/admin/webinars/tpl-list.php<br/>'; 
		// render the List Table
		include_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/admin/webinars/tpl-list.php';
	}

	function webinars_list() {
		wp_enqueue_script( 'video-conferencing-with-zoom-api-js' );
		wp_enqueue_script( 'video-conferencing-with-zoom-api-select2-js' );
		wp_enqueue_script( 'video-conferencing-with-zoom-api-datable-js' );

		wp_enqueue_style( 'video-conferencing-with-zoom-api' );
		wp_enqueue_style( 'video-conferencing-with-zoom-api-select2' );
		wp_enqueue_style( 'video-conferencing-with-zoom-api-datable' );

		require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/admin/webinars/tpl-list-actual-webinars.php';
	}

	public function register_metabox() {
		add_meta_box( 'dcd_webinar_link', __( 'Zoom Webinar Link', 'digitalcustdev-core' ), array( $this, 'render' ), 'lp_lesson', 'side', 'high' );
	}

	public function render( $post ) {
		$zoom_api_meeting_link = get_post_meta( $post->ID, '_webinar_details', true );
		if ( ! empty( $zoom_api_meeting_link ) && ! empty( $zoom_api_meeting_link->message ) ) {
			echo '<p>' . $zoom_api_meeting_link->message . '</p><p>Please fix and update the course to create webinar for this course.</p>';
		} else {
			if ( ! empty( $zoom_api_meeting_link ) ) {
				?>
                <p><a href="<?php echo $zoom_api_meeting_link->join_url; ?>">Join Meeting</a></p>
				<?php
			} else {
				echo __( 'Create an event to create zoom meeting for this event.', 'digitalcustdev-core' );
			}
		}
	}

	function filter_admin_results_courses( $wp_query ) {
		if ( is_admin() && $wp_query->is_main_query() && $wp_query->get( 'post_type' ) === 'lp_course' ) {
			$wp_query->set( 'meta_query', [
				[
					'key'     => '_course_type',
					'compare' => 'NOT EXISTS',
				]
			] );
		}
	}
}

new Admin_DigitalCustDev_Webinar();