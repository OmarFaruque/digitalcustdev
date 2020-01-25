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

	public function query_own_courses( $user_id, $args = '' ) {
		echo 'testOmar';
		global $wpdb, $wp;
		$paged = 1;

		if ( ! empty( $wp->query_vars['view_id'] ) ) {
			$paged = absint( $wp->query_vars['view_id'] );
		}

		$paged = max( $paged, 1 );
		$args  = wp_parse_args( $args, array(
			'paged'  => $paged,
			'limit'  => 5,
			'status' => ''
		) );

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$cache_key = sprintf( 'own-courses-%d-%s', $user_id, md5( build_query( $args ) ) );

		if ( false === ( $courses = LP_Object_Cache::get( $cache_key, 'learn-press/user-courses' ) ) ) {

			$courses = array(
				'total' => 0,
				'paged' => $args['paged'],
				'limit' => $args['limit'],
				'pages' => 0,
				'items' => array()
			);

			echo '<pre>';
			print_r($args);
			echo '</pre>';

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

				if ( ! empty( $args['status'] ) ) {
					if ( is_array( $args['status'] ) ) {
						$a     = array_fill( 0, sizeof( $where ), '%d' );
						$where .= $wpdb->prepare( " AND post_status IN(" . join( ',', $where ) . ")", $a );
					} else {
						if ( 'pending' === $args['status'] ) {
							$where .= $wpdb->prepare( " AND post_status IN( %s, %s )", array( 'draft', 'pending' ) );
						} elseif ( $args['status'] !== '*' ) {
							$where .= $wpdb->prepare( " AND post_status = %s", $args['status'] );
						}
					}
				} else {
					$where .= $wpdb->prepare( " AND post_status NOT IN (%s, %s)", array( 'trash', 'auto-draft' ) );
				}

				$where .= $wpdb->prepare( " AND meta_key = %s", '_course_type' );

				$where = $where . $wpdb->prepare( " AND post_type = %s AND post_author = %d", LP_COURSE_CPT, $user_id );

				$where = $where . " AND c.ID = d.post_id";
				$sql   = "
					SELECT SQL_CALC_FOUND_ROWS ID
					FROM {$wpdb->posts} c, {$wpdb->postmeta} d
					{$where} 
					LIMIT {$offset}, {$limit}
				";

				$items = $wpdb->get_results( $sql );

				if ( $items ) {
					$count      = $wpdb->get_var( "SELECT FOUND_ROWS()" );
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

			LP_Object_Cache::set( $cache_key, $courses, 'learn-press/user-courses' );
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
		wp_enqueue_style( 'digitalcustdev-datable' );
		wp_enqueue_script( 'digitalcustdev-datable-js' );
		wp_enqueue_script( 'digitalcustdev-core' );
		if ( current_user_can( 'lp_teacher' ) || current_user_can( 'administrator' ) ) {
			?>
            <div class="webinar-wrapper omar1">
				<?php
				dcd_core_get_notification( __( 'Webinar course updated.', 'digitalcustdev-core' ) );
				require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tpl-list.php';
				?>
            </div>
			<?php
		} else {
			?>
            <div class="webinar-wrapper omar2">
				<?php
				require_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/webinars/tpl-list-purchased.php';
				?>
            </div>
			<?php
		}
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

	public function get_purchased_webinars( $user ) {
		$profile = learn_press_get_profile();
		$query   = $profile->query_courses( 'purchased', array( 'limit' => 999999 ) );

		return $query;
	}


	public function testFunction(){
		echo 'teest omar';
	}
}

function dcd_webinars() {
	return new DigitalCustDev_CPT_Functions();
}

dcd_webinars();