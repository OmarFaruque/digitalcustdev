<?php
/**
 * @author Deepen.
 * @created_on 6/24/19
 */


class DigitalCustDev_CPT_Functions {

	public static $_instance;

	public static function instance() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {
		add_action( 'wp_ajax_create_new_webinar', array( $this, 'create_new_webinar' ) );
		add_action( 'save_post_lp_course', array( $this, 'save_webinar' ), 99 );

		//Save MetaData
		add_action( 'e-update-course-meta-data-props', array( $this, 'save_meta_keys' ), 10, 2 );
		add_action( 'e-update-course-meta-value', array( $this, 'save_meta_values' ), 10, 2 );
	}

	public function query_own_courses_webinars( $user_id, $args = '' ) {
		global $wpdb, $wp;
		$paged = 1;

		if ( ! empty( $wp->query_vars['view_id'] ) ) {
			$paged = absint( $wp->query_vars['view_id'] );
		}

		$paged = max( $paged, 1 );
		$args  = wp_parse_args( $args, array(
			'paged'  => $paged,
			'limit'  => 10,
			'status' => ''
		) );

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		
		$cache_key = sprintf( 'own-courses-%d-%s', $user_id, md5( build_query( $args ) ) );

		$courses = LP_Object_Cache::get( $cache_key, 'learn-press/user-webinars' );

		if (get_current_user_id() != $user_id || false === ( $courses = LP_Object_Cache::get( $cache_key, 'learn-press/user-webinars' ) ) ) {
			
			$courses = array(
				'total' => 0,
				'paged' => $args['paged'],
				'limit' => $args['limit'],
				'pages' => 0,
				'items' => array()
			);

			try {

				//$orders = $this->get_orders( $user_id );
				$query = array( 'total' => 0, 'pages' => 0, 'items' => false );

				//				//if ( ! $orders ) {
				//				//	throw new Exception( "Error", 0 );
				//				//}
				//
				//				//$course_ids   = array_keys( $orders );
				//				//$query_args   = $course_ids;
				//				$query_args[] = $user_id;
				$limit  = $args['limit'];
				$where  = "WHERE 1";
				$offset = ( $args['paged'] - 1 ) * $limit;

				if(isset($args['s']) && !empty($args['s'])){
					$where .= " AND post_title LIKE '%".$args['s']."%'";
				}
				if ( ! empty( $args['status'] ) ) {
					if ( is_array( $args['status'] ) ) {
						$a     = array_fill( 0, sizeof( $where ), '%d' );
						$where .= $wpdb->prepare( " AND post_status IN(" . join( ',', $where ) . ")", $a );
					} else {
						if ( 'pending' === $args['status'] ) {
							$where .= $wpdb->prepare( " AND post_status IN( %s )", array( 'pending' ) );
						} elseif ('closest-webinar-lesson' != $args['status'] && $args['status'] !== '*' ) {
							$where .= $wpdb->prepare( " AND post_status = %s", $args['status'] );
						}
						
					}
				} else {
					$where .= $wpdb->prepare( " AND post_status NOT IN (%s, %s)", array( 'trash', 'auto-draft' ) );
				}

				$where .= $wpdb->prepare( " AND d.`meta_key` = %s", '_course_type' );
				$where .= $wpdb->prepare( " AND d.`meta_value` = %s", 'webinar' );
				// $where .= $wpdb->prepare( " AND d2.`meta_key` = %s", '_lp_webinar_when' );

				$where = $where . $wpdb->prepare( " AND post_type = %s AND post_author = %d", LP_COURSE_CPT, $user_id );

				$where = $where . " AND c.`ID` = d.post_id";
			
				$where = $where . " GROUP BY c.`ID`";
				
				
				if(isset($args['orderby']) && isset($args['order'])){
					$where .= $wpdb->prepare( " ORDER BY post_status ASC");
					$where .= ", STR_TO_DATE(d2.`meta_value`, '%m/%d/%Y') DESC";
					$where .= $wpdb->prepare( ", post_date DESC");
				}

				$leftjoin = " LEFT JOIN {$wpdb->postmeta} d ON d.`post_id`= c.`ID`";
				$leftjoin .= " LEFT JOIN {$wpdb->learnpress_sections} ls ON ls.`section_course_id`= c.`ID`";
				$leftjoin .= " LEFT JOIN {$wpdb->learnpress_section_items} lsi ON lsi.`section_id`= ls.`section_id`";
				$leftjoin .= " LEFT JOIN {$wpdb->postmeta} d2 ON lsi.`item_id`= d2.`post_id`";

				
				$sql = "
					SELECT SQL_CALC_FOUND_ROWS ID
					FROM {$wpdb->posts} c
					{$leftjoin} 
					{$where} 
					LIMIT {$offset}, {$limit} 
				";

				// {$wpdb->postmeta} d, 
				// 	{$wpdb->learnpress_sections} ls, 
				// 	{$wpdb->learnpress_section_items} lsi 
				// echo 'sql: ' . $sql . '<br/>';
				$items = $wpdb->get_results( $sql );

				// echo '<pre>';
				// print_r($items);
				// echo '</pre>';
				

				if(isset($args['status']) && $args['status'] == 'closest-webinar-lesson'){
					
					$new_items = array();
					foreach($items as $item){
						/*
						* Get Webinar lessons
						*/
						$lessons = get_course_lessons($item->ID);

						$nextdate = '';	
						$previousd = '';	
						
						
						
						foreach($lessons as $l => $sl){
							$webinar_details = get_post_meta( $sl, '_webinar_details', true );
							$webinar_when = get_post_meta( $sl, '_lp_webinar_when', true );
							$webinar_when = str_replace('/', '-', $webinar_when);
							
							$timezone = (isset($webinar_details->timezone)) ? $webinar_details->timezone : $timezone;
							
							$currentdate = strtotime("now");
							if(strtotime($webinar_when) > strtotime("now")){
								// echo 'big <br/>';
								$nextdate = ($previousd != '' && strtotime($previousd) > strtotime($webinar_when)) ? $webinar_when : $previousd; 

								$previousd = $webinar_when;
				
								
								// break;
							}
						// $start_time = $webinar_details->start_time;
						}
						// if(!empty($nextdate)) $new_items[strtotime($nextdate)] = $item;
						if(!empty($nextdate)) $new_items[strtotime($nextdate)] = $item;
					} // foreach($items as $item)
					
					$new_new = array();
					foreach($new_items as $k => $ni){
						$new_min = min(array_keys($new_items));
					
						array_push($new_new, $new_items[$new_min]);
						unset($new_items[$new_min]);
					}
					
					$items = $new_new;
				
				}



				

				if ( $items ) {
					$count      = (isset($args['status']) && $args['status'] == 'closest-webinar-lesson') ? count($items) : $wpdb->get_var( "SELECT FOUND_ROWS()" );
					$course_ids = wp_list_pluck( $items, 'ID' );
					LP_Helper::cache_posts( $course_ids );

					$courses['total'] = $count;
					$courses['pages'] = ceil( $count / $args['limit'] );
					foreach ( $items as $item ) {
						$courses['items'][] = $item->ID;
					}
				}


			} catch ( Exception $ex ) {
				learn_press_add_message( $ex->getMessage() );
			}

			LP_Object_Cache::set( $cache_key, $courses, 'learn-press/user-webinars' );
		}

		$courses['single'] = __( 'course', 'learnpress' );
		$courses['plural'] = __( 'courses', 'learnpress' );

		return new LP_Query_List_Table( $courses );
	}

	public function get_webinars( $post_id = false ) {
		if ( empty( $post_id ) ) {
			$webinars_args = array(
				'post_type'      => 'lp_course',
				'posts_per_page' => - 1,
				'post_status'    => array( 'publish', 'draft', 'pending' ),
				'author'         => get_current_user_id(),
				'meta_key'       => '_course_type',
				'meta_value'     => 'webinar'
			);

			$webinars = get_posts( $webinars_args );
		} else {
			$webinars = get_post( $post_id, OBJECT );
		}

		return $webinars;
	}

	function list_webinars() {
		$profile = LP_Profile::instance();
		$profileid = $profile->get_user_data( 'id' );
		$tpllist = false;
		if($profile->get_user_data( 'id' ) != get_current_user_id()) $tpllist = true;

		wp_enqueue_style( 'digitalcustdev-datable' );
		wp_enqueue_script( 'digitalcustdev-datable-js' );
		wp_enqueue_script( 'digitalcustdev-core' );
		if ( current_user_can( 'lp_teacher' ) || current_user_can( 'administrator')) {
			?>
            <div class="webinar-wrapper omar3 5">
				<?php
				if(!$tpllist && get_user_meta( get_current_user_id(), 'lp_switch_view', true) && get_user_meta( get_current_user_id(), 'lp_switch_view', true) == 'student'){
					require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tpl-list-purchased.php';	
				}else{
					if(!$tpllist){
						dcd_core_get_notification( __( 'Webinar course updated.', 'digitalcustdev-core' ) );
						require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tpl-list.php';
					}else{
						require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tpl-unautorized-webinar.php';
					}
					
				}
				?>
            </div>
			<?php
		} else {
			?>
            <div class="webinar-wrapper omar4">
				<?php
				if(!$tpllist){
					
					require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tpl-list-purchased.php';
				}else{
					
					require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tpl-unautorized-webinar.php';
				}
				?>
            </div>
			<?php
		}
	}

	function list_instructor_assignment(){
		// echo 'tabs: ' . $_REQUEST['tab'] . '<br/>';
		// require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/instructor/list-assignment.php';
		// learn_press_assignment_get_template( 'compatible/learnpress-buddypress/profile/assignments.php', $args );

		if(!$_REQUEST['cid']){
			learn_press_assignment_get_template( 'profile/tabs/instructor-assignments.php' );
		}else{
			learn_press_assignment_get_template( 'profile/tabs/instructor-single-assignment.php' );
		}
	}

	public function list_salesandcommission(){
		require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/instructor/list-salesandcommission.php';
	}

	public function list_visitor(){
		require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/instructor/list-salesandcommission.php';
	}

	public function tab_events(){
		require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/instructor/list-salesandcommission.php';
	}

	public function list_downloads(){
		require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/instructor/list-salesandcommission.php';
	}

	function create_new_webinar() {
		$post_type = LP_Request::get_string( 'type' );
		$nonce     = LP_Request::get_string( 'nonce' );

		if ( ! wp_verify_nonce( $nonce, 'e-new-post' ) ) {
			wp_die( 'Opp' );
		}

		$post      = get_default_post_to_edit( $post_type, true );
		$post_data = array(
			'ID'          => $post->ID,
			'post_status' => 'draft',
			'post_title'  => $post->ID
		);
		$id        = wp_update_post( $post_data, true );
		update_post_meta( $id, '_course_type', 'webinar' );

		// Remove default title as it is the ID of post
		if ( ! is_wp_error( $id ) ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_title = '' WHERE ID = %d", $id ) );
		}

		$manage = new LP_Addon_Frontend_Editor_Post_Manage( 'lp_course' );
		wp_send_json( array(
			'redirect' => $manage->get_edit_post_link( $post_type, $post->ID )
		) );

		wp_die();
	}

	function save_webinar( $post_id ) {
		if ( empty( $_POST['_e_post_nonce'] ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		//Perform permission checks! For example:
		if ( !current_user_can('edit_post', $post_id) ) {
			return;
		}

		$course_tags     = filter_input( INPUT_POST, 'course_tags', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$course_category = filter_input( INPUT_POST, 'course_category', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$duration_number = sanitize_text_field( filter_input( INPUT_POST, 'webinar_duration_number' ) );
		$duration_text   = sanitize_text_field( filter_input( INPUT_POST, 'webinar_duration_term' ) );

		$postMetaData = array(
			'_lp_duration' => $duration_number . ' ' . $duration_text,
		);

		// Save the fields to the post
		do_action( 'acf/save_post', $post_id );

		foreach ( $postMetaData as $k => $data ) {
			update_post_meta( $post_id, $k, $data );
		}

		wp_set_post_terms( $post_id, $course_tags, 'course_tag' );
		wp_set_post_terms( $post_id, $course_category, 'course_category' );

		if ( isset( $_POST['post_on_review'] ) ) {
			remove_action( 'save_post_lp_course', array( $this, 'save_webinar' ), 99 );
			wp_update_post( array( 'ID' => $post_id, 'post_status' => 'pending' ) );
			add_action( 'save_post_lp_course', array( $this, 'save_webinar' ), 99 );
		}
	}

	function save_meta_keys( $meta_fields, $post_id ) {
		$meta_fields[] = 'thim_course_skill_level';
		$meta_fields[] = 'thim_course_language';
		$meta_fields[] = 'thim_course_media_intro';

		return $meta_fields;
	}

	function save_meta_values( $meta_fields, $post_id ) {
		return $meta_fields;
	}


	/**
	 * Query courses by user
	 *
	 * @param int    $user_id
	 * @param string $args
	 *
	 * @return LP_Query_List_Table
	 */
	public function get_purchased_webinars( $user_id = 0, $args = '' ) {
	
		global $wpdb, $wp;
		$paged = 1;
		$card = new LP_User_CURD();

		if ( ! empty( $wp->query_vars['view_id'] ) ) {
			$paged = absint( $wp->query_vars['view_id'] );
		}

		$args = wp_parse_args(
			$args,
			array(
				'paged'  => $paged,
				'limit'  => 5,
				'status' => $_REQUEST['filter-status']
			)
		);

		$args['paged'] = max( $args['paged'], 1 );

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$cache_key = sprintf( 'purchased-courses-%d-%s', $user_id, md5( build_query( $args ) ) );

		if ( false === ( $courses = LP_Object_Cache::get( $cache_key, 'learn-press/user-courses' ) ) ) {

			$courses = array(
				'total' => 0,
				'paged' => $args['paged'],
				'limit' => $args['limit'],
				'pages' => 0,
				'items' => array()
			);

			
			try {

				/**
				 * Get an array of all orders are completed with keys are id of
				 * courses
				 */
				$orders = $card->get_orders( $user_id, array( 'status' => 'completed' ) );

				// foreach($orders as $so){
				// 	echo '<pre>';
				// 	print_r(get_post_meta( $so[0]));
				// 	echo '</pre>';
					
				// }
				
				

				if ( ! $orders ) {
					throw new Exception( "", 0 );
				}

				$course_ids = array_keys( $orders );



				$query = array( 'total' => 0, 'pages' => 0, 'items' => false );


				if ( ! $course_ids ) {
					throw new Exception( "", 0 );
				}

				$valid_orders = array_keys( $orders );
				$course_ids   = array_keys( $orders );
				$query_args   = $course_ids;
				foreach($course_ids as $l => $swebinar){
					$course_type = get_post_meta($swebinar, '_course_type', 'true');
					if(!$course_type) unset($course_ids[$l]);
				}
				$query_args[] = $user_id;
				$limit        = $args['limit'];
				$offset       = ( $args['paged'] - 1 ) * $limit;

				// SELECT
				$select = "SELECT c.ID, c.post_title, ui.*";

				// FROM
				$from = "FROM {$wpdb->learnpress_user_items} ui";

				// JOIN
				$join = $wpdb->prepare( "INNER JOIN {$wpdb->posts} c ON c.ID = ui.item_id AND c.post_type = %s", LP_COURSE_CPT );

				// WHERE
				$where = $wpdb->prepare( "WHERE ui.user_id = %d", $user_id );
				if(count($course_ids) > 0) $where .= " AND c.ID IN(" . join( ',', $course_ids ) . ")";

				// HAVING
				$having = "HAVING 1";
				// ORDER BY
				$orderby = "ORDER BY start_time DESC";
				// $orderby = "ORDER BY c.post_date DESC";

				$unenrolled_course_ids = array();


				// echo 'status: <br/><pre>';
				// print_r($args);
				// echo '</pre>';


				if(isset($args['s']) && !empty($args['s'])){
					$where .= $wpdb->prepare( " AND c.post_title LIKE %s", '%'.$args['s'].'%' );
				}

				if ( ! empty( $args['status'] ) ) {
					switch ( $args['status'] ) {
						case 'finished':
						case 'passed':
						case 'failed':

							$where .= $wpdb->prepare( " AND ui.status IN( %s )", array(
								'finished'
							) );

							if ( $args['status'] !== 'finished' ) {
								$select .= ", uim.meta_value AS grade";
								$join   .= $wpdb->prepare( "
									LEFT JOIN {$wpdb->learnpress_user_itemmeta} uim ON uim.learnpress_user_item_id = ui.user_item_id AND uim.meta_key = %s
								", 'grade' );

								if ( 'passed' === $args['status'] ) {
									$having .= $wpdb->prepare( " AND grade = %s", 'passed' );
								} else {
									$having .= $wpdb->prepare( " AND ( grade IS NULL OR grade = %s )", 'failed' );
								}
							}

							break;
						
						case 'not-enrolled':
							$where .= $wpdb->prepare( " AND ui.status NOT IN( %s, %s, %s )", array(
								'enrolled',
								'finished',
								'pending'
							) );
					}
				}

				if ( empty( $args['status'] ) || $args['status'] === 'not-enrolled' ) {
					$unenrolled_course_ids = $card->query_courses_by_order( $user_id );
				}

				$where .= $wpdb->prepare( " AND ui.status NOT IN(%s) ", 'pending' );

				$query_parts = apply_filters(
					'learn-press/query/user-purchased-courses',
					compact( 'select', 'from', 'join', 'where', 'having', 'orderby' ),
					$user_id,
					$args
				);

				list( $select, $from, $join, $where, $having, $orderby ) = array_values( $query_parts );

				/**
				 * If there are some courses user has purchased and it's order is already completed
				 * but for some reasons it is not inserted into table user-items.
				 *
				 * In this case we temporary to add it to table user-items (by using a transaction)
				 * and query it back and then restore data by rollback that transaction.
				 */
				if ( $unenrolled_course_ids ) {
					LP_Debug::startTransaction();

					foreach ( $unenrolled_course_ids as $unenrolled_course_id ) {
						$wpdb->insert(
							$wpdb->learnpress_user_items,
							array(
								'user_id'   => $user_id,
								'item_id'   => $unenrolled_course_id,
								'item_type' => LP_COURSE_CPT,
								'status'    => 'purchased'
							),
							array( '%d', '%d', '%s', '%s' )
						);
					}
				}
				//item_id, user_item_id

			
				$sql = "
					SELECT SQL_CALC_FOUND_ROWS *
					FROM
					(	
						{$select}
						{$from}
						{$join}
						{$where}
						{$having}
						ORDER BY item_id, user_item_id, post_date DESC
					) X 
					GROUP BY item_id 
					{$orderby} 
					LIMIT {$offset}, {$limit}
				";


			
				

				
				// GROUP BY item_id
				$items = $wpdb->get_results( $sql );
				



				if(isset($args['status']) && $args['status'] == 'closest-webinar-lesson'){
					$new_items = array();
					foreach($items as $item){
						/*
						* Get Webinar lessons
						*/
						$lessons = get_course_lessons($item->ID);

						$nextdate = '';	
						$previousd = '';				
						
						foreach($lessons as $l => $sl){
							$webinar_details = get_post_meta( $sl, '_webinar_details', true );
							$webinar_when = get_post_meta( $sl, '_lp_webinar_when', true );
							// echo 'webinar when: ' . $webinar_when . '<br/>';
							$webinar_when = str_replace('/', '-', $webinar_when);
							
							$timezone = (isset($webinar_details->timezone)) ? $webinar_details->timezone : $timezone;
							
							$currentdate = strtotime("now");
							if(strtotime($webinar_when) > strtotime("now")){
								// echo 'big <br/>';
								$nextdate = ($previousd != '' && strtotime($previousd) > strtotime($webinar_when)) ? $webinar_when : $previousd; 

								$previousd = $webinar_when;
				
								
								// break;
							}
						// $start_time = $webinar_details->start_time;
						}

						// if(!empty($nextdate)) $new_items[strtotime($nextdate)] = $item;
						if(!empty($nextdate)) $new_items[strtotime($nextdate)] = $item;
					} // foreach($items as $item)

					$new_new = array();
					foreach($new_items as $k => $ni){
						$new_min = min(array_keys($new_items));
						array_push($new_new, $new_items[$new_min]);
						unset($new_items[$new_min]);
					}
					
					$items = $new_new;
				
				}


				if ( $unenrolled_course_ids ) {
					LP_Debug::rollbackTransaction();
				}

				if ( $items ) {
				
					$count      = $wpdb->get_var( "SELECT FOUND_ROWS()" );
					$course_ids = wp_list_pluck( $items, 'item_id' );
					LP_Helper::cache_posts( $course_ids );

					$courses['total'] = $count;
					$courses['pages'] = ceil( $count / $args['limit'] );
					foreach ( $items as $item ) {
						$item               = (array) $item;
						$course_item        = new LP_User_Item_Course( $item );
						$courses['items'][] = $course_item;
					}
				}
			}
			catch ( Exception $ex ) {

			}

			LP_Object_Cache::set( $cache_key, $courses, 'learn-press/user-courses' );
		}

		$courses['single'] = __( 'course', 'learnpress' );
		$courses['plural'] = __( 'courses', 'learnpress' );


		return new LP_Query_List_Table( $courses );
	}





	public function testFunction(){
		echo 'tst OMar function';
	}
}

function dcd_webinars() {
	return new DigitalCustDev_CPT_Functions();
}

dcd_webinars();



	