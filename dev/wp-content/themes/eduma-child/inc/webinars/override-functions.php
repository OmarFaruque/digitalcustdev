<?php 
if ( !function_exists( 'thim_get_page_title' ) ) {
	function thim_get_page_title( $custom_title, $front_title ) {
		$heading_title = esc_html__( 'Page title', 'eduma' );
		if ( get_post_type() == 'product' ) {
			if ( !empty( $custom_title ) ) {
				$heading_title = $custom_title;
			} else {
				$heading_title = woocommerce_page_title( false );
			}
		} elseif ( get_post_type() == 'lpr_course' || get_post_type() == 'lpr_quiz' || get_post_type() == 'lp_course' || get_post_type() == 'lp_quiz' || thim_check_is_course() || thim_check_is_course_taxonomy() ) {
			if ( is_single() ) {
				if ( !empty( $custom_title ) ) {
                    $heading_title = $custom_title;
				} else {
					$course_cat = get_the_terms( get_the_ID(), 'course_category' );
					$course_tag = learn_press_is_course_tag();
					if ( !empty( $course_cat ) ) {
						$heading_title = $course_cat[0]->name;
					} elseif ( $course_tag ) {
						remove_query_arg( 'none' );
						$term          = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
						$heading_title = $term->name;
					} else {
                        $heading_title = __( 'Course', 'eduma' );
                        $term          = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );


                        if($term){
                            switch($term->taxonomy){
                                case 'webinar_categories':
                                case 'webinar_tag':
                                    $heading_title = $term->name;
                                break;
                            }
                        }
                        $course_type = get_post_meta( get_the_ID(), '_course_type', true );
                        if($course_type == 'webinar'){
                            $heading_title = get_the_title( get_the_ID() );
                        }                        

                    }
                    
				}
			} else {
				$heading_title = thim_learnpress_page_title( false );
			}
		} elseif ( get_post_type() == 'lp_collection' ) {
			$heading_title = learn_press_collections_page_title( false );
		} elseif ( ( is_category() || is_archive() || is_search() || is_404() ) && !thim_use_bbpress() ) {
			if ( get_post_type() == 'tp_event' ) {
				$heading_title = esc_html__( 'Events', 'eduma' );
			} elseif ( get_post_type() == 'our_team' ) {
				$heading_title = esc_html__( 'Our Team', 'eduma' );
			} else {
				$heading_title = thim_archive_title();
			}
		} elseif ( thim_use_bbpress() ) {
			$heading_title = thim_forum_title();
		} elseif ( is_page() || is_single() ) {
			if ( is_single() ) {
				if ( $custom_title ) {
					$heading_title = $custom_title;
				} else {
					$heading_title = get_the_title();
					if ( get_post_type() == 'post' ) {
						$category = get_the_category();
						if ( $category ) {
							//$category_id   = get_cat_ID( $category[0]->cat_name );
							$heading_title = $category[0]->cat_name;
						} else {
							$heading_title = get_the_title();
						}
					}
					if ( get_post_type() == 'tp_event' ) {
						$heading_title = esc_html__( 'Events', 'eduma' );
					}
					if ( get_post_type() == 'portfolio' ) {
						//$heading_title = esc_html__( 'Portfolio', 'eduma' );
					}
					if ( get_post_type() == 'our_team' ) {
						$heading_title = esc_html__( 'Our Team', 'eduma' );
					}
					if ( get_post_type() == 'testimonials' ) {
						$heading_title = esc_html__( 'Testimonials', 'eduma' );
					}
				}

			} else {
				$heading_title = !empty( $custom_title ) ? $custom_title : get_the_title();
			}
		} elseif ( !is_front_page() && is_home() ) {
			$heading_title = !empty( $front_title ) ? $front_title : esc_html__( 'Blog', 'eduma' );;
		}

		return $heading_title;
	}
}



/**
 * Breadcrumb for LearnPress
 */
if ( ! function_exists( 'thim_learnpress_breadcrumb' ) ) {
	function thim_learnpress_breadcrumb() {
		// Do not display on the homepage
		if ( is_front_page() || is_404() ) {
			return;
		}
		$webinar_page_id  = (function_exists('learn_press_get_page_id')) ? learn_press_get_page_id( 'webinar' ) : '';

        
		// Get the query & post information
		global $post;
        $taxonomies = get_object_taxonomies( 'lp_course', 'objects' );
        
		// Build the breadcrums
		echo '<ul itemprop="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList" id="breadcrumbs" class="breadcrumbs">';

        // Home page
        echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_html( get_home_url() ) . '" title="' . esc_attr__( 'Home', 'eduma' ) . '"><span itemprop="name">' . esc_html__( 'Home', 'eduma' ) . '</span></a></li>';
        
        
        
        $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
        if($term){
            $termName = $term->name;
            switch($term->taxonomy){
                case 'webinar_categories':
                    $tax_label = $taxonomies['webinar_categories']->label;
                    echo '<li class="itemlistOM 1" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . get_the_permalink( $webinar_page_id ) . '" title="' . $tax_label . '"><span itemprop="name">' . __('All Webinars', 'webinar') . '</span></a></li>';
                    echo '<li class="itemlistOM 2" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name">' . $termName . '</span></li>';
                break;
                case 'webinar_tag':
                    $tax_label = $taxonomies['webinar_tag']->label;
                    echo '<li class="itemlistOM 3" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . get_the_permalink( $webinar_page_id ) . '" title="' . $tax_label . '"><span itemprop="name">' . __('All Webinars', 'webinar') . '</span></a></li>';
                    echo '<li class="itemlistOM 4" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name">' . $termName . '</span></li>';
                break;
            }
		}
		
        
        if( is_single() ) {
			$categories = get_the_terms( $post, 'course_category' );
			$postmetas = get_post_meta( $post->ID, '_course_type', true );
			if ( get_post_type() == 'lp_course' ) {
				// All courses
				if($postmetas == 'webinar'){
					echo '<li class="itemlistOM 9" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_the_permalink( $webinar_page_id ) ) . '" title="' . esc_attr__( 'All courses', 'eduma' ) . '"><span itemprop="name">' . esc_html__( 'All Webinars', 'eduma' ) . '</span></a></li>';
				}else{
					echo '<li class="itemlistOM 5" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_post_type_archive_link( 'lp_course' ) ) . '" title="' . esc_attr__( 'All courses', 'eduma' ) . '"><span itemprop="name">' . esc_html__( 'All courses', 'eduma' ) . '</span></a></li>';
				}
			}elseif ( learn_press_is_course_tag() ) {
				remove_query_arg( 'none' );
				$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
				// All courses
				echo '<li class="tag66o" property="itemListElement" typeof="ListItem"><a property="item" typeof="WebPage" href="' . esc_url( get_post_type_archive_link( 'lp_course' ) ) . '" title="' . esc_attr__( 'All courses', 'eduma' ) . '"><span property="name">' . esc_html__( 'All courses', 'eduma' ) . '</span></a><meta property="position" content="2"></li>';
				echo '<li><span title="' . esc_attr( $term->name ) . '">' . esc_html( $term->name ) . '</span></li>';
			}else {
				if(get_the_title( get_post_meta( $post->ID, '_lp_course', true ) ) != '') echo '<li class="itemlistOM 6" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_permalink( get_post_meta( $post->ID, '_lp_course', true ) ) ) . '" title="' . esc_attr( get_the_title( get_post_meta( $post->ID, '_lp_course', true ) ) ) . '"><span itemprop="name">' . esc_html( get_the_title( get_post_meta( $post->ID, '_lp_course', true ) ) ) . '</span></a></li>';
			}

			// Single post (Only display the first category)
			if ( isset( $categories[0] ) ) {
				echo '<li class="itemlistOM 7" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_term_link( $categories[0] ) ) . '" title="' . esc_attr( $categories[0]->name ) . '"><span itemprop="name">' . esc_html( $categories[0]->name ) . '</span></a></li>';
			}
			if(get_the_title() != '') echo '<li class="itemlistOM 81" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr( get_the_title() ) . '">' . esc_html( get_the_title() ) . '</span></li>';

		} else if ( learn_press_is_course_taxonomy() || learn_press_is_course_tag() ) {
            
			// All courses
			echo '<li class="allcourse" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_post_type_archive_link( 'lp_course' ) ) . '" title="' . esc_attr__( 'All courses', 'eduma' ) . '"><span itemprop="name">' . esc_html__( 'All courses', 'eduma' ) . '</span></a></li>';

			// Category page
			echo '<li class="itemlistOM 8" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr( learn_press_single_term_title( '', false ) ) . '">' . esc_html( learn_press_single_term_title( '', false ) ) . '</span></li>';
		} else if ( ! empty( $_REQUEST['s'] ) && ! empty( $_REQUEST['ref'] ) && ( $_REQUEST['ref'] == 'course' ) ) {
            
			// All courses
			echo '<li  class="allcourse" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_post_type_archive_link( 'lp_course' ) ) . '" title="' . esc_attr__( 'All courses', 'eduma' ) . '"><span itemprop="name">' . esc_html__( 'All courses', 'eduma' ) . '</span></a></li>';

			// Search result
			echo '<li class="itemlistOM 9" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr__( 'Search results for:', 'eduma' ) . ' ' . esc_attr( get_search_query() ) . '">' . esc_html__( 'Search results for:', 'eduma' ) . ' ' . esc_html( get_search_query() ) . '</span></li>';
		} else {
			echo '<li  class="allcourse" itemprop="itemListElement-1" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr__( 'All courses', 'eduma' ) . '">' . esc_html__( 'All courses', 'eduma' ) . '</span></li>';
        }
		echo '</ul>';
	}
}



if ( !function_exists( 'thim_item_meta_duration' ) ) {
	function thim_item_meta_duration( $item ) {
		$duration = $item->get_duration();
		$item_id = $item->get_id();
		$date = get_post_meta( $item_id, '_lp_webinar_when', true );
		
		$date = str_replace('/', '-', $date);
		
		$datewithDuration = date('Y-m-d h:i:s', strtotime($date) + $duration->get() );
		$wnewtime = date('Y-m-d h:i:s a', time());
		
		$progress  = strtotime($datewithDuration) - strtotime($wnewtime);
		
		if ( is_a( $duration, 'LP_Duration' ) && $duration->get() ) {
			$format = array(
				'day'    => _x( '%s day', 'duration', 'eduma' ),
				'hour'   => _x( '%s hour', 'duration', 'eduma' ),
				'minute' => _x( '%s min', 'duration', 'eduma' ),
				'second' => _x( '%s sec', 'duration', 'eduma' ),
			);

			if($date){
				$metaDuration = '';
				// if( strtotime($date) > strtotime($wnewtime) ) $metaDuration = __('Date', 'webinar') . ': ' . date('d.m.Y H:i', strtotime($date)) .' & '.__('Duration', 'webinar').': ' . $duration->to_timer( $format, true );
				if( strtotime($date) > strtotime($wnewtime) ) $metaDuration = __('Date', 'webinar') . ': ' . date('d.m.Y', strtotime($date)) .' in '.date('H:i', strtotime($date)).'   '.__('Duration', 'webinar').': ' . $duration->to_timer( $format, true );
				if($progress  >= 0 && $progress <= $duration->get() ) $metaDuration = __('In Progress', 'webinars');
				if(strtotime($datewithDuration) < strtotime($wnewtime) ) $metaDuration = __('Passed', 'webinars');
				echo '<span class="meta hh duration">' . $metaDuration . '</span>';
				
			}else{
				echo '<span class="meta duration">' . $duration->to_timer( $format, true ) . '</span>';
			}
		} elseif ( is_string( $duration ) && strlen( $duration ) ) {
			echo '<span class="meta dd duration">' . $duration . '</span>';
		}
	}
}


function is_rebon( $status ){
	$page = get_queried_object_id();
	// $course_page_id = learn_press_get_page_id( 'webinar' );
	if($status !='') echo('<div class="ribbon ribbon-top-left"><span>'.$status.'</span></div>');
}


function thim_sc_get_wibner_categories( $cats = false ) {
	global $wpdb;
	$query = $wpdb->get_results( $wpdb->prepare(
		"
				  SELECT      t1.term_id, t2.name
				  FROM        $wpdb->term_taxonomy AS t1
				  INNER JOIN $wpdb->terms AS t2 ON t1.term_id = t2.term_id
				  WHERE t1.taxonomy = %s
				  AND t1.count > %d
				  ",
		'webinar_categories', 0
	) );

	if ( ! $cats ) {
		$cats = array();
	}
	if ( ! empty( $query ) ) {
		foreach ( $query as $key => $value ) {
			$cats[ $value->name ] = $value->term_id;
		}
	}

	return $cats;
}


function get_course_lessons($course_id){
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

// add_filter('rewrite_rules_array', 'poe_rewrite_rules');
// function poe_rewrite_rules($rules) {
//     $custom = array('page/([0-9]+)/?$' => 'index.php?page_id=11365&home=yes');
//     return $custom + $rules;
// }

// add_action( 'init', 'my_rewrites_init' );
// function my_rewrites_init(){
//     add_rewrite_rule(
//         'page/([^/]+)/?',
//         'index.php?page_id=11365&pa=$matches[1]',
//         'top' );
// }


add_action('init', 'dcc_rewrite_tags');
function dcc_rewrite_tags() {
    add_rewrite_tag('%page%', '([^&]+)');
}

add_action('init', 'dcc_rewrite_rules');
function dcc_rewrite_rules() {
	$pages = explode('/', $_SERVER['REQUEST_URI']);
	$pages = array_filter($pages);
	$paged = end($pages);
	$webinar_page_id = (function_exists('learn_press_get_page_id')) ? learn_press_get_page_id( 'webinar' ) : '';
	if(in_array('page', $pages) ) add_rewrite_rule('^webinars/(.+)/?$','index.php?page_id='.$webinar_page_id.'&paged='.$paged.'','top');
	

}

if ( ! function_exists( 'thim_wrapper_loop_end' ) ) :
	function thim_wrapper_loop_end() {
		global $wp_query;
		global $post;
		$class_col = thim_wrapper_layout();
		$get_post_type = get_post_type();
		$course_type = get_post_meta( $post->ID, '_course_type', 'true' );

		if ( is_404() ) {
			$class_col = 'col-sm-12 full-width';
		}
		echo '</main>';

		$term_list =  $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

		$webinar_page_id = learn_press_get_page_id( 'webinar' );
		$webinar = false;
		if($term_list){
			if($term_list->taxonomy == 'webinar_categories' || $term_list->taxonomy == 'webinar_tag') $webinar = true;
		}
		if ( $class_col != 'col-sm-12 full-width' ) {
			if ($get_post_type == "lpr_course" || $get_post_type == "lpr_quiz" || $get_post_type == "lp_course" || $get_post_type == "lp_quiz" || thim_check_is_course() || thim_check_is_course_taxonomy() ) {
				if($course_type == 'webinar') $webinar = true;
				if(!$webinar && !is_page($webinar_page_id)) get_sidebar( 'courses' );
				if($webinar) include(THIM_CHILD_THEME_DIR . 'inc/webinars/public/sidebar.php'); 
				
			} else if ( $get_post_type == "tp_event" ) {
				get_sidebar( 'events' );
			} else if ( $get_post_type == "product" ) {
				get_sidebar( 'shop' );
			} else {
				get_sidebar();
			}
		}
		echo '</div>';

		do_action( 'thim_after_site_content' );

		echo '</div>';
	}
endif;






//REtur function
function allcourseidsbytime($args, $session){
	// 	echo 'sesson: ' . $session . '<br/><br/>';
				$currentdate = date('Y-m-d h:i:s');
				$posts_ids = array();
				$getallcourse = get_posts($args);
				foreach($getallcourse as $scourse){
					$course_id = $scourse->ID;
					$lessons = get_course_lessons($course_id);
					$past = false;
					$future = false;
					foreach($lessons as $lesson){
						$lessonDate = get_post_meta( $lesson, '_lp_webinar_when', true );
						$lessonDate = str_replace('/', '-', $lessonDate);
						// echo 'course & session: '.$session.' (' . get_the_title($course_id) . '): '  . date('Y-m-d h:i:s', strtotime($lessonDate)) . '<br/>';
						$lessonDate = ($lessonDate) ? date('Y-m-d h:i:s', strtotime($lessonDate)) : '';
						if($lessonDate !='' && $lessonDate <= $currentdate) $past = true;
						if($lessonDate !='' && $lessonDate >= $currentdate) $future = true;
					} // End Foreach
					if($session == 'passed'){
						if($past && $future == false) array_push($posts_ids, $scourse->ID);
					}elseif($session == 'inprogress'){
						if($past == true && $future == true) array_push($posts_ids, $scourse->ID);
					}else{
						if($past == false && $future == true) array_push($posts_ids, $scourse->ID);
					}
				} // End Foreach
				if(count($posts_ids) <= 0 ) $posts_ids = array(0);
				return $posts_ids;
}


// Removes a class from the body_class array.
 
add_filter( 'body_class', 'customizebodyclass');
function customizebodyclass($class){
	global $post;	
	$webinar_page_id  = learn_press_get_page_id( 'webinar' );
	
	if($webinar_page_id == $post->ID) $class[] = 'learnpress-archive-webinars';
	// if(is_post_type_archive( 'lp_course' )) $class[] = 'course-filter-active';
	return $class;
}

add_action( 'pre_get_posts', 'test' );
function test($query){
	if( $query->is_main_query() && !is_admin() && is_post_type_archive( 'lp_course' ) ) {
		$metaQuery = array(
			'relation' => 'OR', 
			array(
				'key' => '_course_type',
				'compare' => 'NOT EXISTS'
			)
		);
		$query->set( 'meta_query', $metaQuery );
		// $query->set( 'meta_key', '_course_type' );
		// $query->set( 'meta_compare', 'NOT EXISTS' );
	}
}



// lp_inheance file add 
require_once(get_stylesheet_directory() . '/inc/webinars/admin/lp_enhance.php');

// require_once 'inc/curds/class-lp-user-curd.php';
require_once(get_stylesheet_directory() . '/inc/webinars/class-lp-user-curd.php');

// Move DigitalCustDev Core Theme Customizations plugin to child theme
require_once(get_stylesheet_directory() . '/digitalcustdev-core/digitalcustdev-core.php');


function thim_tab_profile_filter_titlecustomize( $tab_title, $key ) {
	switch ( $key ) {
		case 'instructor':
			$tab_title = '<i class="fa fa-male"></i><span class="text">' . esc_html__( 'Instructor', 'eduma' ) . '</span>';
		break;
		case 'students':
			$tab_title = '<i class="fa fa-graduation-cap"></i>
			<span class="countr buble-counter">' . get_author_assignments_not_set_yet() . '</span>
			<span class="text">' . esc_html__( 'Students', 'eduma' ) . '</span>
			<span class="countr buble-counter second">' . get_author_assignments_not_set_yet() . '</span>';
		break;
		case 'reports':
			$tab_title = '<i class="fa fa-file-text"></i><span class="text">' . esc_html__( 'Reports', 'eduma' ) . '</span>';
		break;
	}
	return $tab_title;
}
add_filter( 'learn_press_profile_instructor_tab_title', 'thim_tab_profile_filter_titlecustomize', 200, 2 );
add_filter( 'learn_press_profile_students_tab_title', 'thim_tab_profile_filter_titlecustomize', 200, 2 );
add_filter( 'learn_press_profile_reports_tab_title', 'thim_tab_profile_filter_titlecustomize', 200, 2 );



	




	/**
	 * Query own courses of an user.
	 *
	 * @param int    $user_id
	 * @param string $args
	 *
	 * @return LP_Query_List_Table
	 */
	function query_own_courses_custom( $user_id, $args = '' ) {
		global $wpdb, $wp;
		$paged = 1;
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		

		$webinar_posts = $wpdb->get_results(
			$wpdb->prepare("
				SELECT p.ID   
				FROM $wpdb->posts AS p
				LEFT JOIN $wpdb->postmeta AS pmeta ON (pmeta.post_id = p.id) 
				WHERE p.post_author = %d AND 
				p.post_type = %s 
				AND (pmeta.meta_key = '_course_type' AND pmeta.meta_value = 'webinar') 
				", $user_id, LP_COURSE_CPT), OBJECT);
	
			
		// $postMeta = get_post_meta( 11555, '_course_type', true );
		// echo 'course type: ' . LP_COURSE_CPT . '</br>';
		// echo 'post metas: ' . $postMeta . '<br/>';
		// echo '<pre>';
		// print_r($webinar_posts);
		// echo '</pre>';

		$excerptPosts = array();
		foreach($webinar_posts as $swp) array_push($excerptPosts, $swp->ID);

		if ( ! empty( $wp->query_vars['view_id'] ) ) {
			$paged = absint( $wp->query_vars['view_id'] );
		}

		$paged = max( $paged, 1 );
		$args  = wp_parse_args(
			$args, array(
				'paged'  => $paged,
				'limit'  => 10,
				'status' => ''
			)
		);


		

		$cache_key = sprintf( 'own-courses-%d-%s', $user_id, md5( build_query( $args ) ) );

		if ( false === ( $courses = LP_Object_Cache::get( $cache_key, 'learn-press/user-courses' ) ) ) {

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
						} elseif ( $args['status'] !== '*' ) {
							$where .= $wpdb->prepare( " AND post_status = %s", $args['status'] );
						}
					}
				} else {
					$where .= $wpdb->prepare( " AND post_status NOT IN (%s, %s)", array( 'trash', 'auto-draft' ) );
				}

				// excerptPosts
				if(count($excerptPosts) > 0) $where = $where . $wpdb->prepare( " AND ID NOT IN (" . implode(',', $excerptPosts) . ")" );
				$where = $where . $wpdb->prepare( " AND post_type = %s AND post_author = %d", LP_COURSE_CPT, $user_id );
				
				if(isset($args['orderby']) && isset($args['order'])){
					$where .= " ORDER BY post_status ASC";
					$where .= ", post_date " . $args['order'];
				} 
				$sql   = "
					SELECT SQL_CALC_FOUND_ROWS ID
					FROM {$wpdb->posts} c
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
			}
			catch ( Exception $ex ) {
				learn_press_add_message( $ex->getMessage() );
			}

			LP_Object_Cache::set( $cache_key, $courses, 'learn-press/user-courses' );
		}


		$courses['single'] = __( 'course', 'learnpress' );
		$courses['plural'] = __( 'courses', 'learnpress' );

		return new LP_Query_List_Table( $courses );
	}




	
	/**
	 * Draft Counter.
	 *
	 * @param int    $user_id
	 * @param string $args
	 *
	 * @return LP_Query_List_Table
	 */
	function query_draft_counter($user_id, $course_type = '' ) {
		global $wpdb, $wp;
		$paged = 1;
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		

		if($course_type == 'webinar'){
			$webinar_posts = $wpdb->get_results(
				$wpdb->prepare("
					SELECT p.ID   
					FROM $wpdb->posts AS p
					LEFT JOIN $wpdb->postmeta AS pmeta ON (pmeta.post_id = p.id) 
					WHERE p.post_author = %d AND 
					p.post_type = %s 
					AND p.post_status = 'draft' 
					AND (pmeta.meta_key = '_course_type' AND pmeta.meta_value = 'webinar') 
					GROUP BY p.ID 
					", $user_id, LP_COURSE_CPT), OBJECT);
		}else{
			$webinar_posts = $wpdb->get_results(
				$wpdb->prepare("
					SELECT p.ID   
					FROM $wpdb->posts AS p
					LEFT JOIN $wpdb->postmeta AS pmeta ON (pmeta.post_id = p.id) 
					WHERE p.post_author = %d AND 
					p.post_type = %s 
					AND p.post_status = 'draft' 
					AND (pmeta.meta_key = '_course_type' AND pmeta.meta_value = 'webinar') 
					GROUP BY p.ID 
					", $user_id, LP_COURSE_CPT), OBJECT);
					

					$excerptPosts = array();
					foreach($webinar_posts as $swp) array_push($excerptPosts, $swp->ID);

					$course_posts = $wpdb->get_results(
						$wpdb->prepare("
							SELECT p.ID   
							FROM $wpdb->posts AS p
							LEFT JOIN $wpdb->postmeta AS pmeta ON (pmeta.post_id = p.id) 
							WHERE p.post_author = %d AND 
							p.post_type = %s 
							AND p.post_status = 'draft' 
							AND p.ID NOT IN (" . implode(',', $excerptPosts) . ") 
							GROUP BY p.ID 
							", $user_id, LP_COURSE_CPT), OBJECT);
		
					$webinar_posts = $course_posts;		
		}
	

		$excerptPosts = array();
		foreach($webinar_posts as $swp) array_push($excerptPosts, $swp->ID);

		return count($excerptPosts);
	}




	add_action( 'init', function () {

		// echo '<pre>';
		// print_r($_COOKIE);
		// echo '</pre>';


		$plugin_dir = ABSPATH . 'wp-content/plugins/learnpress/';
		include_once $plugin_dir .'inc/admin/lp-admin-functions.php';
		// call functions such as genesis_register_sidebar() in here
		// learn_press_gradebook_get_user_data();
	} );



	if ( !function_exists( 'thim_courses_loop_item_thumbnail' ) ) {
		function thim_courses_loop_item_thumbnail( $course = null ) {
			$course = LP_Global::course();
			global $frontend_editor;
			global $wpdb;
			$post_manage = $frontend_editor->post_manage;
			
			
			
			$arraystatus = array('draft');		
			
			if(in_array($course->get_post_status(), $arraystatus)){
				if(!wp_doing_ajax()){
					$url = $post_manage->get_edit_post_link( get_post_type(), get_the_id() );
				}else{
					$url = $frontend_editor->get_url( 'edit-post' . '/' . get_the_id() );
				}
			}
			else {
				$url = get_the_permalink( $course->get_id() );
			}

			
			// echo 'course status: ' . $course->get_post_status() . '<br/>';
			$course_id = $course->get_id();
			$lessons = get_course_lessons($course_id);
			
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
				//echo 'status: ' . $status . '<br/>';
			echo '<div class="course-thumbnail from-override">';
			echo '<a class="thumb" href="' . esc_url( $url ) . '" >';
			echo thim_get_feature_image( get_post_thumbnail_id( $course->get_id() ), 'full', apply_filters( 'thim_course_thumbnail_width', 400 ), apply_filters( 'thim_course_thumbnail_height', 320 ), $course->get_title() );
			echo (is_rebon($status));
			echo '</a>';
			do_action( 'thim_inner_thumbnail_course' );
			echo '<a class="course-readmore" href="' . esc_url( get_the_permalink( $course->get_id() ) ) . '">' . esc_html__( 'Read More', 'eduma' ) . '</a>';
			echo '</div>';
		}
	}






	function custom_get_days( $leading_zero = false, $duration ) {
		$days = $duration ? ( $duration - $duration % ( 3600 * 24 ) ) / ( 3600 * 24 ) : 0;

		return $days < 10 && $leading_zero ? "0{$days}" : $days;
	}
	function custom_to_timer( $duration,  $format = '', $remove_empty = false ) {
		$day    = custom_get_days(false, $duration);
		$mod    = $duration - $day * 24 * 3600;
		$hour   = ( $mod - ( $mod % 3600 ) ) / 3600;
		$mod    = $mod - $hour * 3600;
		$minute = ( $mod - $mod % 60 ) / 60;
		$second = $mod - $minute * 60;

		$parts = array();

		if ( $day ) {
			$parts['day'] = $day;
		}

		if ( $hour ) {
			$parts['hour'] = $hour;
		}

		$parts['minute'] = $minute;
		// $parts['second'] = $second;

		foreach ( $parts as $k => $v ) {
			if ( $v < 10 ) {
				$parts[ $k ] = "0{$v}";
			}
		}

		if ( $format ) {

			foreach ( array( 'day', 'hour', 'minute', 'second' ) as $p ) {
				if ( $remove_empty && array_key_exists( $p, $parts ) && intval( $parts[ $p ] ) == 0 ) {
					unset( $parts[ $p ] );
				}
				if ( ! empty( $format[ $p ] ) && ! empty( $parts[ $p ] ) ) {
					$parts[ $p ] = sprintf( $format[ $p ], $parts[ $p ] );
				}
			}

			return join( ' ', $parts );
		}

		return join( ':', $parts );
	}





		function get_author_assignments_not_set_yet($author_id = null){

			$total = 0;
			$assignments = get_author_assignments();
			foreach($assignments as $sassignment){
				$avutotal = not_avualate($sassignment->ID);
				if($avutotal > 0) $total+=$avutotal;
			}
			return $total;
		}


	/**
		 * @param $assignment
		 *
		 * @return array|null|object
		 */
		function get_author_assignments( $author_id = null ) {
			global $wpdb;
			if(!$author_id){
				$author_id = get_current_user_id();
			}
			
			$args = array(
				'author'        =>  $author_id,
				'post_type' 	=> 	LP_ASSIGNMENT_CPT,
				'orderby'       =>  'post_date',
				'order'         =>  'ASC',
				'posts_per_page' => -1
				);
			$assignments = get_posts($args);
			// $assignment = LP_Assignment::get_assignment( $assignment );
			// $query      = $wpdb->prepare( "
			// 	SELECT DISTINCT user_item.* FROM {$wpdb->users} AS student
			// 	INNER JOIN {$wpdb->prefix}learnpress_user_items AS user_item 
			// 	ON user_item.user_id = student.ID 
			// 	INNER JOIN {$wpdb->prefix}posts AS posts 
			// 	ON user_item.ref_id = posts.ID 
			// 	WHERE posts.post_author = %d AND user_item.item_type = %s AND user_item.status IN (%s, %s)
			// ", $author_id, LP_ASSIGNMENT_CPT, 'completed', 'evaluated' );

			// $students = $wpdb->get_results( $query, ARRAY_A );
			return $assignments;
		}





/**
		 * Get filters for purchased courses tab.
		 *
		 * @param string $current_filter
		 *Use in purchased.php
		 * @return array
		 */
		function get_purchased_courses_filters( $current_filter = '' ) {
			$profile = learn_press_get_profile();
			$url      = $profile->get_current_url();

			$defaults = array(
				'all'          => sprintf( '<a href="%s">%s</a>', esc_url( $url ), __( 'All', 'learnpress' ) ),
				'progress'     => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'progress', $url ) ), __( 'Progress', 'learnpress' ) ),
				'passed'       => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'passed', $url ) ), __( 'Passed', 'learnpress' ) ),
				'failed'       => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'failed', $url ) ), __( 'Failed', 'learnpress' ) ),
				'not-enrolled' => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'not-enrolled', $url ) ), __( 'Not enrolled', 'learnpress' ) )
			);

			if ( ! $current_filter ) {
				$keys           = array_keys( $defaults );
				$current_filter = reset( $keys );
			}

			foreach ( $defaults as $k => $v ) {
				if ( $k === $current_filter ) {
					$defaults[ $k ] = sprintf( '<span>%s</span>', strip_tags( $v ) );
				}
			}

			return apply_filters(
				'learn-press/profile/purchased-courses-filters',
				$defaults
			);
		}


		/**
		 * Get filters for own courses tab.
		 *
		 * @param string $current_filter
		 *
		 * @return array
		 */
		function get_own_courses_filters_custom( $current_filter = '' ) {
			$profile = learn_press_get_profile();
			$url      = $profile->get_current_url();
			$defaults = array(
				'all'     => sprintf( '<a href="%s">%s</a>', esc_url( $url ), __( 'All', 'learnpress' ) ),
				'draft' => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'draft', $url ) ), __( 'Draft', 'learnpress' ) ),
				'publish' => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'publish', $url ) ), __( 'Publish', 'learnpress' ) ),
				'pending' => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'pending', $url ) ), __( 'Pending', 'learnpress' ) ),
				'closest-webinar-lesson' => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'closest-webinar-lesson', $url ) ), __( 'Closest webinar lesson', 'learnpress' ) )
			);

			if ( ! $current_filter ) {
				$keys           = array_keys( $defaults );
				$current_filter = reset( $keys );
			}

			foreach ( $defaults as $k => $v ) {
				if ( $k === $current_filter ) {
					$defaults[ $k ] = sprintf( '<span>%s</span>', strip_tags( $v ) );
				}
			}

			return apply_filters(
				'learn-press/profile/own-courses-filters',
				$defaults
			);
		}




		/**
		 * Get filters for purchased webinar tab.
		 *
		 * @param string $current_filter
		 *
		 * @return array
		 */
		function get_purchased_webinar_filters( $current_filter = '' ) {
			$profile = learn_press_get_profile();
			$url      = $profile->get_current_url( false );
			$defaults = array(
				'all'          		=> sprintf( '<a href="%s">%s</a>', esc_url( $url ), __( 'All', 'learnpress' ) ),
				'progress'  		=> sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'progress', $url ) ), __( 'Progress', 'learnpress' ) ),
				'passed'  			=> sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'passed', $url ) ), __( 'Passed', 'learnpress' ) ),
				'failed'  			=> sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'failed', $url ) ), __( 'Failed', 'learnpress' ) ),
				'closest-webinar-lesson'  	=> sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'closest-webinar-lesson', $url ) ), __( 'Closest webinar lesson', 'learnpress' ) )
				// 'past'       		=> sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'past', $url ) ), __( 'Past Webinars', 'learnpress' ) )
			);

			if ( ! $current_filter ) {
				$keys           = array_keys( $defaults );
				$current_filter = reset( $keys );
			}

			foreach ( $defaults as $k => $v ) {
				if ( $k === $current_filter ) {
					$defaults[ $k ] = sprintf( '<span>%s</span>', strip_tags( $v ) );
				}
			}

			return apply_filters(
				'learn-press/profile/purchased-courses-filters',
				$defaults
			);
		}


		/*
		* Logout hook for replace user switch meta key
		*/
		add_action( 'wp_login', 'switchusermetas', 10, 2);
		function switchusermetas($user_login){			
			$user = get_user_by('login', $user_login);
			$role = $user->roles;
			if (in_array('administrator', $role) || in_array('lp_teacher', $role)) update_user_meta( $user->ID, "lp_switch_view", 'instructor');
		}


		function not_avualate($assignment_id){
			global $wpdb;
			$sql = "SELECT count(*) as `total` FROM $wpdb->learnpress_user_itemmeta lum 
			LEFT JOIN $wpdb->learnpress_user_items lu ON lu.`user_item_id`=lum.`learnpress_user_item_id` 
			WHERE lu.`item_id` = ".$assignment_id." AND lum.`meta_key`='_lp_assignment_evaluate_author' AND lum.`meta_value`='0'";
			$results = $wpdb->get_row($sql, OBJECT);
			return $results->total;
		}


		/*
		* REturn evulated total
		* Use in instructor-assignments.php
		*/
		function avualate($assignment_id){
			global $wpdb;
			$sql = "SELECT count(*) as `total` FROM $wpdb->learnpress_user_itemmeta lum 
			LEFT JOIN $wpdb->learnpress_user_items lu ON lu.`user_item_id`=lum.`learnpress_user_item_id` 
			WHERE lu.`item_id` = ".$assignment_id." AND lum.`meta_key`='_lp_assignment_evaluate_author' AND lum.`meta_value`!='0'";
			$results = $wpdb->get_row($sql, OBJECT);
			return $results->total;
		}

		// add_action('wp_head', 'testfunction');
		function testfunction(){
			
			// get_template_part( 'digitalcustdev-core\views\webinars\tpl-list', 'purchased' );
		}

	
			/*
			* get user grade from theme
			*/ 

		function get_item_grade_from_theme($sl, $course_id){
			global $wpdb;
			$sql = "SELECT lum.`meta_value` FROM $wpdb->learnpress_user_itemmeta lum 
			LEFT JOIN $wpdb->learnpress_user_items lu ON lu.`user_item_id`=lum.`learnpress_user_item_id` 
			LEFT JOIN $wpdb->postmeta pm ON lu.`user_id`= pm.`meta_value` AND lu.`ref_id` = pm.`post_id` 
			WHERE lu.`item_id` = ".$course_id." AND lum.`meta_key`='grade' AND pm.`meta_key`='_user_id'";

			$results = $wpdb->get_row($sql, OBJECT);
			return $results->meta_value;

		}


add_filter( 'learn-press/update-profile-basic-information-data', 'updateProfileUserMeta');
function updateProfileUserMeta(){
	$user_id = get_current_user_id();

	$update_data = array(
		'ID'           => $user_id,
		'first_name'   => filter_input( INPUT_POST, 'first_name', FILTER_SANITIZE_STRING ),
		'last_name'    => filter_input( INPUT_POST, 'last_name', FILTER_SANITIZE_STRING ),
		'display_name' => filter_input( INPUT_POST, 'display_name', FILTER_SANITIZE_STRING ),
		'nickname'     => filter_input( INPUT_POST, 'nickname', FILTER_SANITIZE_STRING ),
		'description'  => filter_input( INPUT_POST, 'description', FILTER_SANITIZE_STRING )
	);

	$metaArray = array(
		'major' => filter_input( INPUT_POST, 'major', FILTER_SANITIZE_STRING ),
		'facebook' => filter_input( INPUT_POST, 'facebook', FILTER_SANITIZE_STRING ), 
		'twitter' => filter_input( INPUT_POST, 'twitter', FILTER_SANITIZE_STRING ),
		'instagram' => filter_input( INPUT_POST, 'instagram', FILTER_SANITIZE_STRING ),
		'google' => filter_input( INPUT_POST, 'google', FILTER_SANITIZE_STRING ),
		'linkedin' => filter_input( INPUT_POST, 'linkedin', FILTER_SANITIZE_STRING ),
		'youtube' => filter_input( INPUT_POST, 'youtube', FILTER_SANITIZE_STRING )
	);

	update_user_meta( $user_id, 'lp_info', $metaArray );


	$return      = wp_update_user( $update_data );
}
		