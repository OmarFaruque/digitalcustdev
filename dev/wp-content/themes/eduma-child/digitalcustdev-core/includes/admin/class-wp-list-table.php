<?php
/**
 * @author Deepen.
 * @created_on 8/8/19
 */

if(!class_exists('WP_List_Table')){
	return;
}
class DigitalCustDev_Webinar_List_Table extends WP_List_Table {

	/**
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_text_domain The text domain of this plugin.
	 */
	protected $plugin_text_domain;

	public function __construct( $plugin_text_domain ) {

		$this->plugin_text_domain = $plugin_text_domain;

		parent::__construct( array(
			'plural'   => 'webinars',
			'singular' => 'webinar',
			'ajax'     => true,
		) );
	}

	public function prepare_items() {

		// check if a search was performed.
		$user_search_key = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';

		$this->_column_headers = $this->get_column_info();

		// check and process any actions such as bulk actions.
		$this->handle_table_actions();

		// fetch table data
		$table_data = $this->fetch_table_data();
		// filter the data in case of a search.
		if ( $user_search_key ) {
			$table_data = $this->filter_table_data( $table_data, $user_search_key );
		}

		// required for pagination
		$webinars_per_page = $this->get_items_per_page( 'webinars_per_page' );
		$table_page        = $this->get_pagenum();

		// provide the ordered data to the List Table.
		// we need to manually slice the data based on the current pagination.
		$this->items = array_slice( $table_data, ( ( $table_page - 1 ) * $webinars_per_page ), $webinars_per_page );
		// set the pagination arguments
		$total_webinars = count( $table_data );
		$this->set_pagination_args( array(
			'total_items' => $total_webinars,
			'per_page'    => $webinars_per_page,
			'total_pages' => ceil( $total_webinars / $webinars_per_page )
		) );
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_columns() {

		$table_columns = array(
			'cb'            => '<input type="checkbox" />', // to display the checkbox.\
			'post_title'    => __( 'Title', $this->plugin_text_domain ),
			'post_author'   => __( 'Author', $this->plugin_text_domain ),
			'sections'   => __( 'Content', $this->plugin_text_domain ),
			'linked_students'   => __( 'Students', $this->plugin_text_domain ),
			'meta_price'   => __( 'Price', $this->plugin_text_domain ),
			'post_comments'   => '<span><span class="vers comment-grey-bubble" title="Comments"><span class="screen-reader-text">'.__('Comments', $this->plugin_text_domain).'</span></span></span>',
			'gradebook' => __( 'Gradebook', $this->plugin_text_domain ),
			// 'post_webinars' => __( 'Linked Webinars', $this->plugin_text_domain ),
			'post_date'     => _x( 'Date', 'column name', $this->plugin_text_domain ),
		);

		return $table_columns;

	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {

		/*
		 * actual sorting still needs to be done by prepare_items.
		 * specify which columns should have the sort icon.
		 *
		 * key => value
		 * column name_in_list_table => columnname in the db
		 */
		$sortable_columns = array(
			'post_title' => 'post_title',
			'post_date'  => 'post_date'
		);

		return $sortable_columns;
	}

	/**
	 * Text displayed when no user data is available
	 *
	 * @since   1.0.0
	 *
	 * @return void
	 */
	public function no_items() {
		_e( 'No webinars avaliable.', $this->plugin_text_domain );
	}

	/*
	 * Fetch table data from the WordPress database.
	 *
	 * @since 1.0.0
	 *
	 * @return	Array
	 */

	public function fetch_table_data() {
		global $wpdb;

		$orderby 		= ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'post_date';
		$order   		= ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'ASC';
		$author   		= ( isset( $_GET['author'] ) ) ? esc_sql( $_GET['author'] ) : '';
		$post_status   	= ( isset( $_GET['post_status'] ) ) ? esc_sql( $_GET['post_status'] ) : '';
		$m 				= ( isset( $_GET['m'] ) ) ? esc_sql( $_GET['m'] ) : ''; 
		$year 			= substr($m, 0, 4);
		$month 			= substr($m, 4, 6);


		// echo 'get all array <br/><pre>';
		// print_r($_GET);
		// echo '</pre>';

		// echo 'year: ' . $year . '<br/>';
		// echo 'Month: ' . $month . '<br/>';




		$query = "
		    SELECT p.ID, p.post_title, p.post_date, p.post_status, p.post_type, p.post_author, pm.meta_key, pm.meta_value
		    FROM $wpdb->posts p, $wpdb->postmeta pm
		    WHERE p.ID = pm.post_id 
			AND pm.meta_key = '_course_type' 
			AND pm.meta_value = 'webinar' 
		    AND p.post_type = 'lp_course' 
    	";

		if(!empty($author)){
			$query .= $wpdb->prepare( " AND p.post_author = %d", $author );
		}
		if(!empty($post_status)){
			$query .= $wpdb->prepare( " AND p.post_status = %s", $post_status );
		}

		if(!empty($m)){
			$query .= $wpdb->prepare( " AND YEAR(p.post_date) = %d", $year );
			$query .= $wpdb->prepare( " AND MONTH(p.post_date) = %d", $month );
		}


		$query .= " ORDER BY $orderby $order";
		// query output_type will be an associative array with ARRAY_A.
		$query_results = $wpdb->get_results( $query, ARRAY_A );


		// return result array to prepare_items.
		return $query_results;
	}

	/*
	 * Filter the table data based on the user search key
	 *
	 * @since 1.0.0
	 *
	 * @param array $table_data
	 * @param string $search_key
	 * @returns array
	 */
	public function filter_table_data( $table_data, $search_key ) {
		$filtered_table_data = array_values( array_filter( $table_data, function ( $row ) use ( $search_key ) {
			foreach ( $row as $row_val ) {
				if ( stripos( $row_val, $search_key ) !== false ) {
					return true;
				}
			}
		} ) );

		return $filtered_table_data;

	}

	/**
	 * Render a column when no column specific method exists.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		// echo '<pre>';
		// print_r($item);
		// echo '</pre>';
		switch ( $column_name ) {
			case 'post_title':
			case 'post_date':
			case 'post_webinars':
			case 'post_author':
				return $item[ $column_name ];
			default:
				return $item[ $column_name ];
		}
	}

	/**
	 * Get value for checkbox column.
	 *
	 * The special 'cb' column
	 *
	 * @param object $item A row's data
	 *
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_cb( $item ) {
		
		return sprintf( '<label class="screen-reader-text" for="webinar_' . $item['ID'] . '">' . sprintf( __( 'Select %s' ), $item['ID'] ) . '</label>' . "<input type='checkbox' name='webinars[]' id='webinar_{$item['ID']}' value='{$item['ID']}' />" );
	}

	protected function column_post_author( $item ) {
		$user      = get_userdata( $item['post_author'] );
		
		$user_info = ($user) ? $user->data->display_name : '';

		return $user_info;
	}

	protected function column_post_title( $item ) {
		$status = $item['post_status'] === "draft" ? ' - draft' : false;

		// row actions to view usermeta.
		$view_usermeta_link       = esc_url( get_edit_post_link( $item['ID'] ) );
		$actions['view_usermeta'] = '<a href="' . $view_usermeta_link . '">' . __( 'Edit', $this->plugin_text_domain ) . '</a>';

		$add_usermeta_link       = esc_url( get_permalink( $item['ID'] ) );
		$actions['add_usermeta'] = '<a href="' . $add_usermeta_link . '">' . __( 'View', $this->plugin_text_domain ) . '</a>';

		$row_value = sprintf( '<a href="%s">' . $item['post_title'] . '</a> %s', get_edit_post_link( $item['ID'] ), $status );


		$post = get_post( $item['ID'] );
		if ( LP_COURSE_CPT == $post->post_type ) {
			$duplicate_link = '#';
			$duplicate_link = array(
				array(
					'link'  => $duplicate_link,
					'title' => __( 'Duplicate this course', 'learnpress' ),
					'class' => 'lp-duplicate-post lp-duplicate-course',
					'data'  => $post->ID
				)
			);
			$links          = apply_filters( 'learn_press_row_action_links', $duplicate_link );
			if ( count( $links ) > 1 ) {
				$drop_down = array( '<ul class="lpr-row-action-dropdown">' );
				foreach ( $links as $link ) {
					$drop_down[] = '<li>' . sprintf( '<a href="%s" class="%s" data-post-id="%s">%s</a>', $link['link'], $link['class'], $link['data'], $link['title'] ) . '</li>';
				};
				$drop_down[] = '</ul>';
				$link        = sprintf( '<div class="lpr-row-actions"><a href="%s">%s</a>%s</div>', 'javascript: void(0);', __( 'Course', 'learnpress' ), join( "\n", $drop_down ) );
			} else {
				$link = array_shift( $links );
				$link = sprintf( '<a href="%s" class="%s" data-post-id="%s">%s</a>', $link['link'], $link['class'], $link['data'], $link['title'] );
			}
			$actions['lp-duplicate-row-action'] = $link;
		}


		// Quick edit link

		//Actions to edit
		$post_type_object = get_post_type_object( $post->post_type );
    	$can_edit_post = current_user_can( $post_type_object->cap->edit_post, $post->ID );
		if ( $can_edit_post && 'trash' != $post->post_status ) {
			// $actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '" title="' . esc_attr( __( 'Edit this item' ) ) . '">' . __( 'Edit' ) . '</a>';
			$actions['inline hide-if-no-js'] = sprintf(
				'<button type="button" class="button-link editinline" aria-label="%s" aria-expanded="false">%s</button>',
				/* translators: %s: Post title. */
				esc_attr( sprintf( __( 'Quick edit &#8220;%s&#8221; inline' ), $title ) ),
				__( 'Quick&nbsp;Edit' )
			);
		}

		// $tt = apply_filters( 'learn-press/row-action-links', $actions );
		// return apply_filters( 'post_row_actions', $tt );
		// return $row_value . $this->row_actions( $actions );
		return $row_value;
		// return 'oamr t';
	}




	/**
	 * Generates and displays row action links.
	 *
	 * @since 4.3.0
	 *
	 * @param object $post        Post being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string Row actions output for posts, or an empty string
	 *                if the current column is not the primary column.
	 */
	protected function handle_row_actions( $post, $column_name, $primary ) {
		
		if ( $primary !== $column_name ) {
			return '';
		}

		

		$post_type_object = get_post_type_object( $post->post_type );
		$can_edit_post    = current_user_can( 'edit_post', $post->ID );
		$actions          = array();
		$title            = _draft_or_post_title();

		// echo 'Post array <pre>';
		// print_r($post);
		// echo '</pre>';
		$post = get_post($post['ID']);

		if ( $can_edit_post && 'trash' != $post->post_status ) {
			
			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				get_edit_post_link( $post->ID ),
				/* translators: %s: Post title. */
				esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ),
				__( 'Edit' )
			);

			if ( 'wp_block' !== $post->post_type ) {
				$actions['inline hide-if-no-js'] = sprintf(
					'<button type="button" class="button-link editinline" aria-label="%s" aria-expanded="false">%s</button>',
					/* translators: %s: Post title. */
					esc_attr( sprintf( __( 'Quick edit &#8220;%s&#8221; inline' ), $title ) ),
					__( 'Quick&nbsp;Edit' )
				);
			}
		}

		if ( current_user_can( 'delete_post', $post->ID ) ) {
			if ( 'trash' === $post->post_status ) {
				$actions['untrash'] = sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ),
					/* translators: %s: Post title. */
					esc_attr( sprintf( __( 'Restore &#8220;%s&#8221; from the Trash' ), $title ) ),
					__( 'Restore' )
				);
			} elseif ( EMPTY_TRASH_DAYS ) {
				$actions['trash'] = sprintf(
					'<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
					get_delete_post_link( $post->ID ),
					/* translators: %s: Post title. */
					esc_attr( sprintf( __( 'Move &#8220;%s&#8221; to the Trash' ), $title ) ),
					_x( 'Trash', 'verb' )
				);
			}
			if ( 'trash' === $post->post_status || ! EMPTY_TRASH_DAYS ) {
				$actions['delete'] = sprintf(
					'<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
					get_delete_post_link( $post->ID, '', true ),
					/* translators: %s: Post title. */
					esc_attr( sprintf( __( 'Delete &#8220;%s&#8221; permanently' ), $title ) ),
					__( 'Delete Permanently' )
				);
			}
		}

		if ( is_post_type_viewable( $post_type_object ) ) {
			if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ), true ) ) {
				if ( $can_edit_post ) {
					$preview_link    = get_preview_post_link( $post );
					$actions['view'] = sprintf(
						'<a href="%s" rel="bookmark" aria-label="%s">%s</a>',
						esc_url( $preview_link ),
						/* translators: %s: Post title. */
						esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ),
						__( 'Preview' )
					);
				}
			} elseif ( 'trash' != $post->post_status ) {
				$actions['view'] = sprintf(
					'<a href="%s" rel="bookmark" aria-label="%s">%s</a>',
					get_permalink( $post->ID ),
					/* translators: %s: Post title. */
					esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ),
					__( 'View' )
				);
			}
		}

		if ( 'wp_block' === $post->post_type ) {
			$actions['export'] = sprintf(
				'<button type="button" class="wp-list-reusable-blocks__export button-link" data-id="%s" aria-label="%s">%s</button>',
				$post->ID,
				/* translators: %s: Post title. */
				esc_attr( sprintf( __( 'Export &#8220;%s&#8221; as JSON' ), $title ) ),
				__( 'Export as JSON' )
			);
		}

		if ( is_post_type_hierarchical( $post->post_type ) ) {

			/**
			 * Filters the array of row action links on the Pages list table.
			 *
			 * The filter is evaluated only for hierarchical post types.
			 *
			 * @since 2.8.0
			 *
			 * @param string[] $actions An array of row action links. Defaults are
			 *                          'Edit', 'Quick Edit', 'Restore', 'Trash',
			 *                          'Delete Permanently', 'Preview', and 'View'.
			 * @param WP_Post  $post    The post object.
			 */
			$actions = apply_filters( 'page_row_actions', $actions, $post );
		} else {

			/**
			 * Filters the array of row action links on the Posts list table.
			 *
			 * The filter is evaluated only for non-hierarchical post types.
			 *
			 * @since 2.8.0
			 *
			 * @param string[] $actions An array of row action links. Defaults are
			 *                          'Edit', 'Quick Edit', 'Restore', 'Trash',
			 *                          'Delete Permanently', 'Preview', and 'View'.
			 * @param WP_Post  $post    The post object.
			 */
			$actions = apply_filters( 'post_row_actions', $actions, $post );
		}


		// echo 'all actions <br/><pre>';
		// print_r($actions);
		// echo '</pre>';

		return $this->row_actions( $actions );
	}
	protected function column_gradebook( $item ) {
		printf( '<a href="%s" target="">%s</a>',
					learn_press_gradebook_nonce_url( array( 'course_id' => $item['ID'] ) ),
					__( 'View', 'learnpress-gradebook' )
				);
	}

	protected function column_post_comments( $item ) {
		if(get_comments_number($item['ID']) > 0){
			printf( '<div class="post-com-count-wrapper"><a href="%s" class="post-com-count post-com-count-approved"><span class="comment-count-approved" aria-hidden="true">1</span><span class="screen-reader-text">1 comment</span></a><span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">0</span><span class="screen-reader-text">No pending comments</span></span>		</div>', admin_url( 'edit-comments.php?p='.$item['ID'].'&comment_status=approved' ) );
		}else{
			echo '—';
		}
	}

	protected function column_meta_price( $item ) {
		$course = learn_press_get_course( $item['ID'] );
		$price   = $course->get_price();
					$is_paid = ! $course->is_free();

					$origin_price = '';
					if ( $course->get_origin_price() && $course->has_sale_price() ) {
						$origin_price = sprintf( '<span class="origin-price">%s</span>', $course->get_origin_price_html() );
					}

					if ( $is_paid ) {
						echo sprintf( '<a href="%s" class="price">%s%s</a>', add_query_arg( 'filter_price', $price ), $origin_price, learn_press_format_price( $course->get_price(), true ) );
					} else {
						echo sprintf( '<a href="%s" class="price">%s%s</a>', add_query_arg( 'filter_price', 0 ), $origin_price, __( 'Free', 'learnpress' ) );

						if ( ! $course->is_required_enroll() ) {
							printf( '<p class="description">(%s)</p>', __( 'No requirement enroll', 'learnpress' ) );
						}
					}
	}


	protected function column_linked_students( $item ) {
		$course = learn_press_get_course( $item['ID'] );
		$count = $course->count_completed_orders();
		echo '<span class="lp-label-counter' . ( ! $count ? ' disabled' : '' ) . '">' . $count . '</span>';
	}

	protected function column_sections( $item ) {
		$curd            = new LP_Course_CURD();
		$post_id = $item['ID'];
		$number_sections = $curd->count_sections( $post_id );
		if ( $number_sections ) {
			$output     = sprintf( _n( '<strong>%d</strong> section', '<strong>%d</strong> sections', $number_sections, 'learnpress' ), $number_sections );
			$html_items = array();
			$post_types = get_post_types( null, 'objects' );

			if ( $stats_objects = $curd->count_items( $post_id, 'edit' ) ) {
				foreach ( $stats_objects as $type => $count ) {
					if ( ! $count || ! isset( $post_types[ $type ] ) ) {
						continue;
					}
					$post_type_object = $post_types[ $type ];
					$singular_name    = strtolower( $post_type_object->labels->singular_name );
					$plural_name      = strtolower( $post_type_object->label );
					$html_items[]     = sprintf( _n( '<strong>%d</strong> ' . $singular_name, '<strong>%d</strong> ' . $plural_name, $count, 'learnpress' ), $count );
				}
			}

			if ( $html_items = apply_filters( 'learn-press/course-count-items', $html_items ) ) {
				$output .= ' (' . join( ', ', $html_items ) . ')';
			}

			echo $output;
		} else {
			_e( 'No content', 'learnpress' );
		}
	}

	

	

	protected function column_post_webinars( $item ) {
		$course_id = $item['ID'];
		$course    = learn_press_get_course( $course_id );
		$items     = $course->get_items();
		$found     = false;
		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				$post_type = get_post_type( $item );
				if ( $post_type === "lp_lesson" ) {
					$found   = true;
					$webinar = get_post_meta( $item, '_webinar_details', true );
					?>
                    <li><a href="<?php echo get_edit_post_link( $item ) ?>"><?php echo ($webinar) ? $webinar->topic : ''; ?></a></li>
					<?php
				}
			}
		}

		if ( ! $found ) {
			echo 'N/A';
		}
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @since    1.0.0
	 *
	 * @return array
	 */
	public function get_bulk_actions() {

		$actions = array(
			'bulk-delete' => 'Delete'
		);

		return $actions;
	}

	/**
	 * Process actions triggered by the user
	 *
	 * @since    1.0.0
	 *
	 */
	public function handle_table_actions() {

		/*
		 * Note: Table bulk_actions can be identified by checking $_REQUEST['action'] and $_REQUEST['action2']
		 *
		 * action - is set if checkbox from top-most select-all is set, otherwise returns -1
		 * action2 - is set if checkbox the bottom-most select-all checkbox is set, otherwise returns -1
		 */

		// check for individual row actions
		$the_table_action = $this->current_action();

		if ( 'view_usermeta' === $the_table_action ) {
			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );
			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'view_usermeta_nonce' ) ) {
				$this->invalid_nonce_redirect();
			} else {
				$this->page_view_usermeta( absint( $_REQUEST['user_id'] ) );
				$this->graceful_exit();
			}
		}

		if ( 'add_usermeta' === $the_table_action ) {
			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );
			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'add_usermeta_nonce' ) ) {
				$this->invalid_nonce_redirect();
			} else {
				$this->page_add_usermeta( absint( $_REQUEST['user_id'] ) );
				$this->graceful_exit();
			}
		}

		// check for table bulk actions
		if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'bulk-delete' ) || ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'bulk-delete' ) ) {

			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );
			// verify the nonce.
			/*
			 * Note: the nonce field is set by the parent class
			 * wp_nonce_field( 'bulk-' . $this->_args['plural'] );
			 *
			 */
			if ( ! wp_verify_nonce( $nonce, 'bulk-webinars' ) ) {
				$this->invalid_nonce_redirect();
			} else {
				$this->delete_bulk_webinars( $_REQUEST['webinars'] );
				$this->graceful_exit();
			}
		}

	}

	/**
	 * View a user's meta information.
	 *
	 * @since   1.0.0
	 *
	 * @param int $user_id user's ID
	 */
	public function page_view_usermeta( $user_id ) {

		$user = get_user_by( 'id', $user_id );
		include_once( 'views/partials-wp-list-table-demo-view-usermeta.php' );
	}

	/**
	 * Add a meta information for a user.
	 *
	 * @since   1.0.0
	 *
	 * @param int $user_id user's ID
	 */

	public function page_add_usermeta( $user_id ) {

		$user = get_user_by( 'id', $user_id );
		include_once DIGITALCUSTDEV_PLUGIN_PATH . 'views/admin/webinars/partials/partial-tpl-add-metadata.php';
	}

	/**
	 * Bulk process users.
	 *
	 * @since   1.0.0
	 *
	 * @param array $bulk_user_ids
	 */
	public function delete_bulk_webinars( $webinar_ids ) {
		if ( ! empty( $webinar_ids ) ) {
			foreach ( $webinar_ids as $webinar_id ) {
				wp_delete_post( $webinar_id, true );
			}
		}
	}

	/**
	 * Stop execution and exit
	 *
	 * @since    1.0.0
	 *
	 * @return void
	 */
	public function graceful_exit() {
		exit;
	}

	/**
	 * Die when the nonce check fails.
	 *
	 * @since    1.0.0
	 *
	 * @return void
	 */
	public function invalid_nonce_redirect() {
		wp_die( __( 'Invalid Nonce', $this->plugin_text_domain ), __( 'Error', $this->plugin_text_domain ), array(
			'response'  => 403,
			'back_link' => esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ), admin_url( '?page=zoom-webinars' ) ) ),
		) );
	}










	/* Inline Edit */
	/**
	 * Outputs the hidden row displayed when inline editing
	 *
	 * @since 3.1.0
	 *
	 * @global string $mode List table view mode.
	 */
	public function inline_edit() {
		echo 'tst omar f';
		global $mode;

		$screen = $this->screen;

		$post             = get_default_post_to_edit( $screen->post_type );
		$post_type_object = get_post_type_object( $screen->post_type );

		$taxonomy_names          = get_object_taxonomies( $screen->post_type );
		$hierarchical_taxonomies = array();
		$flat_taxonomies         = array();

		foreach ( $taxonomy_names as $taxonomy_name ) {

			$taxonomy = get_taxonomy( $taxonomy_name );

			$show_in_quick_edit = $taxonomy->show_in_quick_edit;

			/**
			 * Filters whether the current taxonomy should be shown in the Quick Edit panel.
			 *
			 * @since 4.2.0
			 *
			 * @param bool   $show_in_quick_edit Whether to show the current taxonomy in Quick Edit.
			 * @param string $taxonomy_name      Taxonomy name.
			 * @param string $post_type          Post type of current Quick Edit post.
			 */
			if ( ! apply_filters( 'quick_edit_show_taxonomy', $show_in_quick_edit, $taxonomy_name, $screen->post_type ) ) {
				continue;
			}

			if ( $taxonomy->hierarchical ) {
				$hierarchical_taxonomies[] = $taxonomy;
			} else {
				$flat_taxonomies[] = $taxonomy;
			}
		}

		$m            = ( isset( $mode ) && 'excerpt' === $mode ) ? 'excerpt' : 'list';
		$can_publish  = current_user_can( $post_type_object->cap->publish_posts );
		$core_columns = array(
			'cb'         => true,
			'date'       => true,
			'title'      => true,
			'categories' => true,
			'tags'       => true,
			'comments'   => true,
			'author'     => true,
		);

		?>

		<form method="get">
		<table style="display: none"><tbody id="inlineedit">
		<?php
		$hclass              = count( $hierarchical_taxonomies ) ? 'post' : 'page';
		$inline_edit_classes = "inline-edit-row inline-edit-row-$hclass";
		$bulk_edit_classes   = "bulk-edit-row bulk-edit-row-$hclass bulk-edit-{$screen->post_type}";
		$quick_edit_classes  = "quick-edit-row quick-edit-row-$hclass inline-edit-{$screen->post_type}";

		$bulk = 0;
		while ( $bulk < 2 ) :
			$classes  = $inline_edit_classes . ' ';
			$classes .= $bulk ? $bulk_edit_classes : $quick_edit_classes;
			?>
			<tr id="<?php echo $bulk ? 'bulk-edit' : 'inline-edit'; ?>" class="<?php echo $classes; ?>" style="display: none">
			<td colspan="<?php echo $this->get_column_count(); ?>" class="colspanchange">

			<fieldset class="inline-edit-col-left">
				<legend class="inline-edit-legend"><?php echo $bulk ? __( 'Bulk Edit' ) : __( 'Quick Edit' ); ?></legend>
				<div class="inline-edit-col">

				<?php if ( post_type_supports( $screen->post_type, 'title' ) ) : ?>

					<?php if ( $bulk ) : ?>

						<div id="bulk-title-div">
							<div id="bulk-titles"></div>
						</div>

					<?php else : // $bulk ?>

						<label>
							<span class="title"><?php _e( 'Title' ); ?></span>
							<span class="input-text-wrap"><input type="text" name="post_title" class="ptitle" value="" /></span>
						</label>

						<?php if ( is_post_type_viewable( $screen->post_type ) ) : ?>

							<label>
								<span class="title"><?php _e( 'Slug' ); ?></span>
								<span class="input-text-wrap"><input type="text" name="post_name" value="" /></span>
							</label>

						<?php endif; // is_post_type_viewable() ?>

					<?php endif; // $bulk ?>

				<?php endif; // post_type_supports( ... 'title' ) ?>

				<?php if ( ! $bulk ) : ?>
					<fieldset class="inline-edit-date">
						<legend><span class="title"><?php _e( 'Date' ); ?></span></legend>
						<?php touch_time( 1, 1, 0, 1 ); ?>
					</fieldset>
					<br class="clear" />
				<?php endif; // $bulk ?>

				<?php
				if ( post_type_supports( $screen->post_type, 'author' ) ) :
					$authors_dropdown = '';

					if ( current_user_can( $post_type_object->cap->edit_others_posts ) ) :
						$users_opt = array(
							'hide_if_only_one_author' => false,
							'who'                     => 'authors',
							'name'                    => 'post_author',
							'class'                   => 'authors',
							'multi'                   => 1,
							'echo'                    => 0,
							'show'                    => 'display_name_with_login',
						);

						if ( $bulk ) {
							$users_opt['show_option_none'] = __( '&mdash; No Change &mdash;' );
						}

						$authors = wp_dropdown_users( $users_opt );
						if ( $authors ) :
							$authors_dropdown  = '<label class="inline-edit-author">';
							$authors_dropdown .= '<span class="title">' . __( 'Author' ) . '</span>';
							$authors_dropdown .= $authors;
							$authors_dropdown .= '</label>';
						endif;
					endif; // current_user_can( 'edit_others_posts' )
					?>

					<?php
					if ( ! $bulk ) {
						echo $authors_dropdown;
					}
				endif; // post_type_supports( ... 'author' )
				?>

				<?php if ( ! $bulk && $can_publish ) : ?>

					<div class="inline-edit-group wp-clearfix">
						<label class="alignleft">
							<span class="title"><?php _e( 'Password' ); ?></span>
							<span class="input-text-wrap"><input type="text" name="post_password" class="inline-edit-password-input" value="" /></span>
						</label>

						<span class="alignleft inline-edit-or">
							<?php
							/* translators: Between password field and private checkbox on post quick edit interface. */
							_e( '&ndash;OR&ndash;' );
							?>
						</span>
						<label class="alignleft inline-edit-private">
							<input type="checkbox" name="keep_private" value="private" />
							<span class="checkbox-title"><?php _e( 'Private' ); ?></span>
						</label>
					</div>

				<?php endif; ?>

				</div>
			</fieldset>

			<?php if ( count( $hierarchical_taxonomies ) && ! $bulk ) : ?>

				<fieldset class="inline-edit-col-center inline-edit-categories">
					<div class="inline-edit-col">

					<?php foreach ( $hierarchical_taxonomies as $taxonomy ) : ?>

						<span class="title inline-edit-categories-label"><?php echo esc_html( $taxonomy->labels->name ); ?></span>
						<input type="hidden" name="<?php echo ( 'category' === $taxonomy->name ) ? 'post_category[]' : 'tax_input[' . esc_attr( $taxonomy->name ) . '][]'; ?>" value="0" />
						<ul class="cat-checklist <?php echo esc_attr( $taxonomy->name ); ?>-checklist">
							<?php wp_terms_checklist( null, array( 'taxonomy' => $taxonomy->name ) ); ?>
						</ul>

					<?php endforeach; // $hierarchical_taxonomies as $taxonomy ?>

					</div>
				</fieldset>

			<?php endif; // count( $hierarchical_taxonomies ) && ! $bulk ?>

			<fieldset class="inline-edit-col-right">
				<div class="inline-edit-col">

				<?php
				if ( post_type_supports( $screen->post_type, 'author' ) && $bulk ) {
					echo $authors_dropdown;
				}
				?>

				<?php if ( post_type_supports( $screen->post_type, 'page-attributes' ) ) : ?>

					<?php if ( $post_type_object->hierarchical ) : ?>

						<label>
							<span class="title"><?php _e( 'Parent' ); ?></span>
							<?php
							$dropdown_args = array(
								'post_type'         => $post_type_object->name,
								'selected'          => $post->post_parent,
								'name'              => 'post_parent',
								'show_option_none'  => __( 'Main Page (no parent)' ),
								'option_none_value' => 0,
								'sort_column'       => 'menu_order, post_title',
							);

							if ( $bulk ) {
								$dropdown_args['show_option_no_change'] = __( '&mdash; No Change &mdash;' );
							}

							/**
							 * Filters the arguments used to generate the Quick Edit page-parent drop-down.
							 *
							 * @since 2.7.0
							 *
							 * @see wp_dropdown_pages()
							 *
							 * @param array $dropdown_args An array of arguments.
							 */
							$dropdown_args = apply_filters( 'quick_edit_dropdown_pages_args', $dropdown_args );

							wp_dropdown_pages( $dropdown_args );
							?>
						</label>

					<?php endif; // hierarchical ?>

					<?php if ( ! $bulk ) : ?>

						<label>
							<span class="title"><?php _e( 'Order' ); ?></span>
							<span class="input-text-wrap"><input type="text" name="menu_order" class="inline-edit-menu-order-input" value="<?php echo $post->menu_order; ?>" /></span>
						</label>

					<?php endif; // ! $bulk ?>

				<?php endif; // post_type_supports( ... 'page-attributes' ) ?>

				<?php if ( 0 < count( get_page_templates( null, $screen->post_type ) ) ) : ?>

					<label>
						<span class="title"><?php _e( 'Template' ); ?></span>
						<select name="page_template">
							<?php if ( $bulk ) : ?>
							<option value="-1"><?php _e( '&mdash; No Change &mdash;' ); ?></option>
							<?php endif; // $bulk ?>
							<?php
							/** This filter is documented in wp-admin/includes/meta-boxes.php */
							$default_title = apply_filters( 'default_page_template_title', __( 'Default Template' ), 'quick-edit' );
							?>
							<option value="default"><?php echo esc_html( $default_title ); ?></option>
							<?php page_template_dropdown( '', $screen->post_type ); ?>
						</select>
					</label>

				<?php endif; ?>

				<?php if ( count( $flat_taxonomies ) && ! $bulk ) : ?>

					<?php foreach ( $flat_taxonomies as $taxonomy ) : ?>

						<?php if ( current_user_can( $taxonomy->cap->assign_terms ) ) : ?>
							<?php $taxonomy_name = esc_attr( $taxonomy->name ); ?>

							<label class="inline-edit-tags">
								<span class="title"><?php echo esc_html( $taxonomy->labels->name ); ?></span>
								<textarea data-wp-taxonomy="<?php echo $taxonomy_name; ?>" cols="22" rows="1" name="tax_input[<?php echo $taxonomy_name; ?>]" class="tax_input_<?php echo $taxonomy_name; ?>"></textarea>
							</label>

						<?php endif; // current_user_can( 'assign_terms' ) ?>

					<?php endforeach; // $flat_taxonomies as $taxonomy ?>

				<?php endif; // count( $flat_taxonomies ) && ! $bulk ?>

				<?php if ( post_type_supports( $screen->post_type, 'comments' ) || post_type_supports( $screen->post_type, 'trackbacks' ) ) : ?>

					<?php if ( $bulk ) : ?>

						<div class="inline-edit-group wp-clearfix">

						<?php if ( post_type_supports( $screen->post_type, 'comments' ) ) : ?>

							<label class="alignleft">
								<span class="title"><?php _e( 'Comments' ); ?></span>
								<select name="comment_status">
									<option value=""><?php _e( '&mdash; No Change &mdash;' ); ?></option>
									<option value="open"><?php _e( 'Allow' ); ?></option>
									<option value="closed"><?php _e( 'Do not allow' ); ?></option>
								</select>
							</label>

						<?php endif; ?>

						<?php if ( post_type_supports( $screen->post_type, 'trackbacks' ) ) : ?>

							<label class="alignright">
								<span class="title"><?php _e( 'Pings' ); ?></span>
								<select name="ping_status">
									<option value=""><?php _e( '&mdash; No Change &mdash;' ); ?></option>
									<option value="open"><?php _e( 'Allow' ); ?></option>
									<option value="closed"><?php _e( 'Do not allow' ); ?></option>
								</select>
							</label>

						<?php endif; ?>

						</div>

					<?php else : // $bulk ?>

						<div class="inline-edit-group wp-clearfix">

						<?php if ( post_type_supports( $screen->post_type, 'comments' ) ) : ?>

							<label class="alignleft">
								<input type="checkbox" name="comment_status" value="open" />
								<span class="checkbox-title"><?php _e( 'Allow Comments' ); ?></span>
							</label>

						<?php endif; ?>

						<?php if ( post_type_supports( $screen->post_type, 'trackbacks' ) ) : ?>

							<label class="alignleft">
								<input type="checkbox" name="ping_status" value="open" />
								<span class="checkbox-title"><?php _e( 'Allow Pings' ); ?></span>
							</label>

						<?php endif; ?>

						</div>

					<?php endif; // $bulk ?>

				<?php endif; // post_type_supports( ... comments or pings ) ?>

					<div class="inline-edit-group wp-clearfix">

						<label class="inline-edit-status alignleft">
							<span class="title"><?php _e( 'Status' ); ?></span>
							<select name="_status">
								<?php if ( $bulk ) : ?>
									<option value="-1"><?php _e( '&mdash; No Change &mdash;' ); ?></option>
								<?php endif; // $bulk ?>

								<?php if ( $can_publish ) : // Contributors only get "Unpublished" and "Pending Review". ?>
									<option value="publish"><?php _e( 'Published' ); ?></option>
									<option value="future"><?php _e( 'Scheduled' ); ?></option>
									<?php if ( $bulk ) : ?>
										<option value="private"><?php _e( 'Private' ); ?></option>
									<?php endif; // $bulk ?>
								<?php endif; ?>

								<option value="pending"><?php _e( 'Pending Review' ); ?></option>
								<option value="draft"><?php _e( 'Draft' ); ?></option>
							</select>
						</label>

						<?php if ( 'post' === $screen->post_type && $can_publish && current_user_can( $post_type_object->cap->edit_others_posts ) ) : ?>

							<?php if ( $bulk ) : ?>

								<label class="alignright">
									<span class="title"><?php _e( 'Sticky' ); ?></span>
									<select name="sticky">
										<option value="-1"><?php _e( '&mdash; No Change &mdash;' ); ?></option>
										<option value="sticky"><?php _e( 'Sticky' ); ?></option>
										<option value="unsticky"><?php _e( 'Not Sticky' ); ?></option>
									</select>
								</label>

							<?php else : // $bulk ?>

								<label class="alignleft">
									<input type="checkbox" name="sticky" value="sticky" />
									<span class="checkbox-title"><?php _e( 'Make this post sticky' ); ?></span>
								</label>

							<?php endif; // $bulk ?>

						<?php endif; // 'post' && $can_publish && current_user_can( 'edit_others_posts' ) ?>

					</div>

				<?php if ( $bulk && current_theme_supports( 'post-formats' ) && post_type_supports( $screen->post_type, 'post-formats' ) ) : ?>
					<?php $post_formats = get_theme_support( 'post-formats' ); ?>

					<label class="alignleft">
						<span class="title"><?php _ex( 'Format', 'post format' ); ?></span>
						<select name="post_format">
							<option value="-1"><?php _e( '&mdash; No Change &mdash;' ); ?></option>
							<option value="0"><?php echo get_post_format_string( 'standard' ); ?></option>
							<?php if ( is_array( $post_formats[0] ) ) : ?>
								<?php foreach ( $post_formats[0] as $format ) : ?>
									<option value="<?php echo esc_attr( $format ); ?>"><?php echo esc_html( get_post_format_string( $format ) ); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</label>

				<?php endif; ?>

				</div>
			</fieldset>

			<?php
			list( $columns ) = $this->get_column_info();

			foreach ( $columns as $column_name => $column_display_name ) {
				if ( isset( $core_columns[ $column_name ] ) ) {
					continue;
				}

				if ( $bulk ) {

					/**
					 * Fires once for each column in Bulk Edit mode.
					 *
					 * @since 2.7.0
					 *
					 * @param string $column_name Name of the column to edit.
					 * @param string $post_type   The post type slug.
					 */
					do_action( 'bulk_edit_custom_box', $column_name, $screen->post_type );
				} else {

					/**
					 * Fires once for each column in Quick Edit mode.
					 *
					 * @since 2.7.0
					 *
					 * @param string $column_name Name of the column to edit.
					 * @param string $post_type   The post type slug, or current screen name if this is a taxonomy list table.
					 * @param string $taxonomy    The taxonomy name, if any.
					 */
					do_action( 'quick_edit_custom_box', $column_name, $screen->post_type, '' );
				}
			}
			?>

			<div class="submit inline-edit-save">
				<button type="button" class="button cancel alignleft"><?php _e( 'Cancel' ); ?></button>

				<?php if ( ! $bulk ) : ?>
					<?php wp_nonce_field( 'inlineeditnonce', '_inline_edit', false ); ?>
					<button type="button" class="button button-primary save alignright"><?php _e( 'Update' ); ?></button>
					<span class="spinner"></span>
				<?php else : ?>
					<?php submit_button( __( 'Update' ), 'primary alignright', 'bulk_edit', false ); ?>
				<?php endif; ?>

				<input type="hidden" name="post_view" value="<?php echo esc_attr( $m ); ?>" />
				<input type="hidden" name="screen" value="<?php echo esc_attr( $screen->id ); ?>" />
				<?php if ( ! $bulk && ! post_type_supports( $screen->post_type, 'author' ) ) : ?>
					<input type="hidden" name="post_author" value="<?php echo esc_attr( $post->post_author ); ?>" />
				<?php endif; ?>
				<br class="clear" />

				<div class="notice notice-error notice-alt inline hidden">
					<p class="error"></p>
				</div>
			</div>

			</td></tr>

			<?php
			$bulk++;
		endwhile;
		?>
		</tbody></table>
		</form>
		<?php
	}

	/* Inline Edit End */
}