<?php
/**
 * @author Deepen.
 * @created_on 8/8/19
 */

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
			'ajax'     => false,
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
			'post_title'    => __( 'Webinar Title', $this->plugin_text_domain ),
			'post_author'   => __( 'Author', $this->plugin_text_domain ),
			'post_webinars' => __( 'Linked Webinars', $this->plugin_text_domain ),
			'post_date'     => _x( 'Created On', 'column name', $this->plugin_text_domain ),
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

		$orderby = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'post_date';
		$order   = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'ASC';

		$query = "
		    SELECT p.ID, p.post_title, p.post_date, p.post_status, p.post_type, p.post_author, pm.meta_key, pm.meta_value
		    FROM $wpdb->posts p, $wpdb->postmeta pm
		    WHERE p.ID = pm.post_id 
		    AND pm.meta_key = '_course_type' 
		    AND p.post_type = 'lp_course' 
		    ORDER BY $orderby $order
    	";

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
		return sprintf( '<label class="screen-reader-text" for="webinar_' . $item['ID'] . '">' . sprintf( __( 'Select %s' ), $item['webinar_id'] ) . '</label>' . "<input type='checkbox' name='webinars[]' id='webinar_{$item['ID']}' value='{$item['ID']}' />" );
	}

	protected function column_post_author( $item ) {
		$user      = get_userdata( $item['post_author'] );
		$user_info = $user->display_name;

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

		return $row_value . $this->row_actions( $actions );
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
                    <li><a href="<?php echo get_edit_post_link( $item ) ?>"><?php echo $webinar->topic; ?></a></li>
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
}