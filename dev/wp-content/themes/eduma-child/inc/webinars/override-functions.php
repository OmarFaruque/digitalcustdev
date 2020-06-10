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
		
		
		$newcreatedtime = new DateTime("now", new DateTimeZone( 'UTC' ) );
		$wnewtime = $newcreatedtime->format('Y-m-d h:i:s');

		// $wnewtime = date('Y-m-d h:i:s a', time());
		
		$progress  = strtotime($datewithDuration) - strtotime($wnewtime);
		
		$userTimezone = get_post_meta($item_id, '_lp_timezone', true);
		$browserTimezone = new DateTimeZone($userTimezone);

		$utcTime = new DateTime("now", new DateTimeZone( 'UTC' ) );
		$offset = $browserTimezone->getOffset($utcTime);
		$offsetHours = $offset / 3600;
		$offsetHours = number_format($offsetHours, 2);
		$offsetHours = ($offsetHours > 0) ? '+'.$offsetHours : '-'.$offsetHours;


		if ( is_a( $duration, 'LP_Duration' ) && $duration->get() ) {
			$format = array(
				'day'    => _x( '%s day', 'duration', 'eduma' ),
				'hour'   => _x( '%s hour', 'duration', 'eduma' ),
				'minute' => _x( '%s min', 'duration', 'eduma' ),
				'second' => _x( '%s sec', 'duration', 'eduma' ),
			);

			if($date){
				// echo 'inside 1 <br/>';
				$metaDuration = '';
				// if( strtotime($date) > strtotime($wnewtime) ) $metaDuration = __('Date', 'webinar') . ': ' . date('d.m.Y H:i', strtotime($date)) .' & '.__('Duration', 'webinar').': ' . $duration->to_timer( $format, true );
				if( strtotime($date) > strtotime($wnewtime) ) $metaDuration = __('Date', 'webinar') . ': ' . date('d.m.Y', strtotime($date)) .' in '.date('H:i', strtotime($date)).' (GMT'.$offsetHours.')   '.__('Duration', 'webinar').': ' . $duration->to_timer( $format, true );
				if($progress  >= 0 && $progress <= $duration->get() ) $metaDuration = __('In Progress', 'webinars');
				if(strtotime($datewithDuration) < strtotime($wnewtime) ) $metaDuration = __('Passed', 'webinars');
				echo '<span class="omar meta hh duration">' . $metaDuration . '</span>';
				
			}else{
				// echo 'inside 2 <br/>';
				echo '<span class="meta duration">' . $duration->to_timer( $format, true ) . '</span>';
			}
		} elseif ( is_string( $duration ) && strlen( $duration ) ) {
			// echo 'inside 3 <br/>';
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
	add_theme_support( 'post-thumbnails', array('lp_lesson') );
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

// override yes-no.php from learnpress to theme
require_once(get_stylesheet_directory() . '/inc/webinars/public/partials/yes-no.php');

// Move DigitalCustDev Core Theme Customizations plugin to child theme
require_once(get_stylesheet_directory() . '/digitalcustdev-core/digitalcustdev-core.php');


function thim_tab_profile_filter_titlecustomize( $tab_title, $key ) {
	switch ( $key ) {
		case 'instructor':
			$tab_title = '<i class="fa fa-male"></i><span class="text">' . esc_html__( 'Instructor', 'eduma' ) . '</span>';
		break;
		case 'students':
			$tab_title = '<i class="fa fa-graduation-cap"></i>';
			if(get_author_assignments_not_set_yet() > 0) $tab_title .= '<span class="countr buble-counter">' . get_author_assignments_not_set_yet() . '</span>';
			$tab_title .= '<span class="text">' . esc_html__( 'Students', 'eduma' ) . '</span>';
			if(get_author_assignments_not_set_yet() > 0) $tab_title .= '<span class="countr buble-counter second">' . get_author_assignments_not_set_yet() . '</span>';
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

		

		// if($ipData['timezone']) setcookie("timezone", $ipData['timezone']);
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
			if(get_user_meta(get_current_user_id(), 'lp_switch_view', true) == 'instructor') $status = '';
			// echo 'status: ' . $status . '<br/>';
			// echo (learn_press_is_ajax()) ? 'profile page' : 'not profil lpage';
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

	
			/*
			* get user grade from theme
			*/ 

		function get_item_grade_from_theme($sl, $course_id){
			global $wpdb;
			$sql = "SELECT lum.`meta_value` FROM $wpdb->learnpress_user_itemmeta lum 
			LEFT JOIN $wpdb->learnpress_user_items lu ON lu.`user_item_id`=lum.`learnpress_user_item_id` 
			LEFT JOIN $wpdb->postmeta pm ON lu.`user_id`= pm.`meta_value` AND lu.`ref_id` = pm.`post_id` 
			WHERE lu.`item_id` = ".$course_id." AND lu.`user_id` = ".get_current_user_id()." AND lum.`meta_key`='grade' AND pm.`meta_key`='_user_id'";

			// echo 'sql: ' . $sql . '<br/>';
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

	learn_press_remove_message( '', 'error' );
	$return      = wp_update_user( $update_data );
}





// require_once 'inc/curds/class-lp-user-curd.php';
require_once(get_stylesheet_directory() . '/inc/webinars/class-lp-section-curd.php');

if ( ! class_exists( 'LP_Admin_Editor' ) ) {
	include_once LP_PLUGIN_PATH . '/inc/admin/editor/class-lp-admin-editor.php';
}
if( ! class_exists( 'LP_Admin_Editor_Quiz' )){
	include_once LP_PLUGIN_PATH . '/inc/admin/editor/class-lp-admin-editor-quiz.php';
}

require_once(get_stylesheet_directory() . '/inc/webinars/admin/class-lp-admin-editor-course.php');
require_once(get_stylesheet_directory() . '/inc/webinars/class-lp-user-curd.php');



/*
* set_default_section_to_editor 
* function call on form-lp_course.php
*/

function set_default_section_to_editor($course_id){
	include_once LP_PLUGIN_PATH . 'inc/admin/class-lp-install-sample-data.php';
	require_once(get_stylesheet_directory() . '/inc/webinars/admin/class-lp-sample-data-extend.php');
	$simple_data = new LP_Install_Sample_Data_Extend();
	$setdata = $simple_data->create_sectionscustom($course_id);
	if($setdata){
		echo '<script>location.reload();</script>';
	}
}



/*
* Override html input for front-end editor
*/
function begin_html( $html, $field, $meta ) {
	$page_id = get_option( 'learn_press_frontend_editor_page_id' );
	if ( $page_id && is_page( $page_id ) ) {
		global $frontend_editor;
		$post_manage = $frontend_editor->post_manage;
		$post        = $post_manage->get_post();
		$checked = get_post_meta($post->ID, 'descount_access', true) == '1' ? 'checked' : '';

		
		// ec
		switch($field['id']){
			case '_lp_sale_price':
				$html = '<label class="switch mr-1">
					<input type="checkbox" name="descount_access" value="1" '.$checked.'>
					<span class="slider"></span>
				</label>' . $html;
			break;
			case '_lp_coming_soon':
				$value = empty( $meta ) ? $field['std'] : $meta;
				$true  = ! learn_press_is_negative_value( $value );
				$yes   = 'yes';
				$no    = 'no';
				$html = sprintf(
					'<label class="switch mr-1">
					<input class="omartest from child" type="hidden" name="%s" value="%s">
					<input type="checkbox" class="rwmb-yes-no" name="%s" id="%s" value="%s" %s>
					<span class="slider"></span>
					</label>',
					$field['field_name'],
					$no,
					$field['field_name'],
					$field['id'],
					$yes,
					checked( $true, true, false )
				);
			break;
		}
	}else{
		if($field['id'] == '_lp_price'){
			$field['min'] = 0;
		}
	}
	return $html . ( ! empty( $field['after_input'] ) ? $field['after_input'] : '' );

	
}
add_filter( 'rwmb_html', 'begin_html', 10, 3 );





/*
* add custom field to array
*/

add_filter('e-update-course-meta-data-props', 'addaddditionalfieldtosavedArray');
function addaddditionalfieldtosavedArray( $array ) {
	
	$array[] = 'descount_access';
	return $array;
}
// add_action('wp_head', 'testfunction');
function testfunction(){
	$user = wp_get_current_user();
	echo 'roles <br/><pre>';
	print_r($user->roles);
	echo '</pre>';
}

add_action( 'admin_init', function() {
	// echo 'current post type: ' . get_current_screen() . '<br/>';
    $author = get_role( 'lp_teacher' );
    if ( ! $author->has_cap( 'manage_own_attachments' ) ) {
        $author->add_cap( 'manage_own_attachments' );
    }
});


add_filter('e-course-data-store', 'course_data_override');
function course_data_override($data){
	$course_type = get_post_meta($data['course_ID'], '_course_type', true);
	if($course_type != 'webinar'){
		if(isset($data['post_type_fields']['lp_lesson'])){
			// echo 'Lession Duration <br/>';
			// echo '<pre>';
			// print_r($data['post_type_fields']['lp_lesson']);
			// echo '</pre>';
				$durationindex = array_search('_lp_duration', array_column($data['post_type_fields']['lp_lesson'], 'id'));
				$data['post_type_fields']['lp_lesson'][$durationindex]['name'] = __('Lession Duration', 'webinar');
				// unset($data['post_type_fields']['lp_lesson'][$durationindex]);
		}
	}

	$order = array( '_lp_introduction', '_lp_attachments', '_lp_duration' );
	if(isset($data['post_type_fields']['lp_assignment'])){
		$newarray = array();
		foreach($data['post_type_fields']['lp_assignment'] as $k => $s):
			$index = array_search($order[$k], array_column($data['post_type_fields']['lp_assignment'], 'id'));
			array_push($newarray, $data['post_type_fields']['lp_assignment'][$index]);
		endforeach;
		$data['post_type_fields']['lp_assignment'] = $newarray;
	} // if(isset($data['post_type_fields']['lp_assignment']))
	return $data;
}

// add_filter('e-post-type-fields', 'testfunctiono');
function testfunctiono($data){
	echo 'Omar Te Data <br/><pre>';
	print_r($data);
	echo '</pre>';
	$newarray = array();

	// array_push($data, array('id' => 'e-item-content'));
	$order = array( '_lp_introduction', '_lp_attachments', '_lp_duration' );
	// $indexf = array_search($order[2], array_column($data, 'id'));
	$returnN = false;
	foreach($data as $k => $s):
		$index = array_search($order[$k], array_column($data, 'id'));
		if($index0 && $index1 && $index2){
			$returnN = true;		
		}
		array_push($newarray, $data[$index]);
	endforeach;
	if($returnN){
		echo 'return true omar f';
	}else{
		echo 'return false omar f';
	}
	$returnArray = ($returnN) ? $newarray : $data;
	return $returnArray;
}


// add_action('rwmb_after_save_post', 'aftfersavePost');
function aftfersavePost(){
	echo 'test omar f';
}


add_filter('learn-press/course-settings/payments', 'course_settings_payment_field_override');
function course_settings_payment_field_override($fields){
	$index = array_search('_lp_price', array_column($fields['fields'], 'id'));
	$fields['fields'][$index]['min'] = (!is_admin()) ? 2000 : 0;
	// $fields['fields'][$index]['min'] = (!is_admin()) ? 2000 : 0;
	return $fields;
}

function changeMceDefaults($in) {

    // customize the buttons
    $in['theme_advanced_buttons1'] = 'bold,italic,underline,bullist,numlist,hr,blockquote,link,unlink,justifyleft,justifycenter,justifyright,justifyfull,outdent,indent';         
    $in['theme_advanced_buttons2'] = 'formatselect,pastetext,pasteword,charmap,undo,redo';

    // Keep the "kitchen sink" open
    $in[ 'wordpress_adv_hidden' ] = FALSE;
    return $in;
}
add_filter( 'tiny_mce_before_init', 'changeMceDefaults' );



// add_filter( 'rwmb_html', 'functiontestomar');
function functiontestomar($outer_html, $field, $meta){
	return 'test omar';	
}

function visiable_review_submit($course_type){
	$args = array(
        'author'      	=> get_current_user_id(),
		'orderby'       => 'post_date',
		'order'         => 'DESC',
		'post_status' 	=> 'pending',
		'post_type' 	=> 'lp_course',
        'numberposts' 	=> 1
	);

	if($course_type == 'webinar'){
		$args['meta_query'][] =  array(
            'key'     => '_course_type',
            'value'   => 'webinar',
            'compare' => '=',
        );
	}else{
		$args['meta_query'][] =  array(
            'key'     => '_course_type',
            'compare' => 'NOT EXISTS',
        );
	}

	$latest_post = wp_get_recent_posts( $args );
	$postdate = $latest_post[0]['post_date'];
	$posted = get_the_time('U', $latest_post[0]['ID']);
	$dif = human_time_diff($posted, current_time( 'U' ));

	if(strtotime($dif) < strtotime('24 hours')){
		return false;
	}else{
		return true;
	}
}


/*
* return course making success rate
* Use in profile webinar & course list
*/
function course_making_success($course_id){
	// echo 'Omar course id: ' . $course_id . '<br/>';
	$post = get_post($course_id);
	$marks = 0;
	if(!empty($post->post_title)){
		$marks += 15;
	}
	if(!empty($post->post_content)){
		$marks += 10;
	}
	if(has_post_thumbnail($post->ID)){
		$marks += 15;
	}
	$terms = get_the_terms($post->ID, 'course_category');
	if(!empty($terms)){
		$marks += 05; 
	}

	$tagTerms = get_the_terms($post->ID, 'course_tag');
	if(!empty($tagTerms)){
		$marks += 05; 
	}
	
	$course    = learn_press_get_course( $course_id );
	$items     = $course->get_items();
	if ( ! empty( $items ) ) {
		foreach ( $items as $item ) {
			$post_type = get_post_type( $item );
			switch($post_type){
				case 'lp_lesson':
					$marks += 15;
				break;
				case 'lp_quiz':
					$marks += 10;
				break;
				case 'lp_assignment':
					$marks += 15;
				break;
			}
		}
	}

	$price = get_post_meta($post->ID, '_lp_price', true);
	if(!empty($price)) $marks += 10;
	return $marks;
}

function removeDirectory($path) {
	$files = glob($path . '/*');
	foreach ($files as $file) {
		is_dir($file) ? removeDirectory($file) : unlink($file);
	}
	rmdir($path);
	return;
}


function rr_404_my_event() {
	$page_id = get_option( 'learn_press_frontend_editor_page_id' );
	if ( $page_id && is_page( $page_id ) ) {
		global $frontend_editor;

		$post_manage = $frontend_editor->post_manage;
		$post        = $post_manage->get_post();
		$course_status = get_post_status($post->ID);
		$show_error = array('pending', 'publish');
		if(in_array($course_status, $show_error)):
  			// global $wp_query;
	  		// $wp_query->set_404();
			//   status_header(404);
			//   get_header();
			//   echo '<section class="content-area">';
			//   get_template_part( 'inc/templates/page-title' );
			//   get_template_part( 404 ); 
			//   echo '</section>';
			//   get_footer();
				wp_safe_redirect(get_home_url( '/' ) . '/404');
			  exit();
		endif;
	}
	
  }
  add_action( 'wp', 'rr_404_my_event' );


  /*
  * Add Terms and condition page in backend settings page fsfsf
  */
  add_filter( 'learn-press/admin/settings-pages/become-a-teacher', 'addTermsAndConditionCallback' );
  function addTermsAndConditionCallback($array){
	  $array[] = array(
		  'title' => __('Terms & Condition page', 'webinar'),
		  'id' => 'terms_and_condition_page_id',
		  'default' => '',
		  'type' => 'pages-dropdown'
	  );

	  return $array;
  }




  /*
  * WP_Editor additional css
  */
add_filter('tiny_mce_before_init','wpdocs_theme_editor_dynamic_styles');
function wpdocs_theme_editor_dynamic_styles( $mceInit ) {
	$styles = 'body.mce-content-body span.mce-preview-object.mce-object-iframe { min-width:100%; min-height:450px; }';
	$styles .= 'body.mce-content-body span.mce-preview-object.mce-object-iframe iframe { min-width:100%; min-height:450px; }';
	
	$ext = 'video|iframe[align|longdesc|name|width|height|frameborder|scrolling|marginheight|marginwidth|src|id|class|title|style]';


	$mceInit['forced_root_block'] = false;
	if ( isset( $mceInit['extended_valid_elements'] ) ){
		$mceInit['extended_valid_elements'] .= ',' . $ext;
	}
	else{
		$mceInit['extended_valid_elements'] = $ext;
	}

	if ( isset( $mceInit['content_style'] ) ) {
        $mceInit['content_style'] .= ' ' . $styles . ' ';
    } else {
        $mceInit['content_style'] = $styles . ' ';
    }
    return $mceInit;
}



add_filter( 'learn_press_lesson_meta_box_args', 'thim_custom_add_course_meta', 10, 2 );


if ( !function_exists( 'thim_custom_add_course_meta' ) ) {
	function thim_custom_add_course_meta( $meta_box ) {
		$fields             = $meta_box['fields'];
		$fields[]           = array(
			'name' => esc_html__( 'Media id', 'eduma' ),
			'id'   => '_lp_lesson_video_intro_internal',
			'type' => 'hidden',
			'desc' => esc_html__( '', 'eduma' ),
		);
		$meta_box['fields'] = $fields;

		return $meta_box;
	}
}



/*
* The Content hook 
*/
// add_filter( 'the_editor_content', 'filtercontentforadd_lession_video' );
function filtercontentforadd_lession_video($content){
	global $post;
	$post_type = get_post_type( $post->ID );
	
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && $post_type == 'lp_lesson'){
		$internal_video = get_post_meta( $post->ID, '_lp_lesson_video_intro_internal', true );
		$internal_video_url = wp_get_attachment_url( $internal_video );

		$iframe = '<iframe id="lession_intro_iframe" src="'.$internal_video_url.'" frameborder="0" allowtransparency="true" scrolling="no" class="wpview-sandbox" style="width: 100%; display: block; height: 359px;"></iframe><br/>';
		if($internal_video) $content = $iframe . $content;
	}
	return $content;
	// return $content;
}


/*
* Save lession intro vide while save post from backend. 
*/
add_action( 'save_post', 'set_private_categories' );
function set_private_categories($post_id) {
    // If this is a revision, get real post ID
	$post_type = get_post_type( $post_id );
	if($post_type == 'lp_lesson'){
		$introVideo = isset($_POST["_lp_lesson_video_intro_internal"] ) ? $_POST["_lp_lesson_video_intro_internal"]  : '';
		if(!empty($introVideo)) update_post_meta( $post_id, '_lp_lesson_video_intro_internal', $introVideo );
	}
 
}

// add_filter( 'content_save_pre' , 'filter_post_data' , '99', 2 );
function filter_post_data( $content ) {
	// Change post title
	global $post;
	$post_type = get_post_type( $post->ID );
	$test = '';
	if($post_type == 'lp_lesson'){		
		$content = preg_replace("#<iframe[^>]+>.*?</iframe>#is", '', $content);
		// $string = preg_replace('/<iframe class="lession_intro_iframe"[^>]+\>/i', "", $string);
		

	}

    return $content;
}


/*
 * Add media for lesson
 */
if ( !function_exists( 'thim_content_item_lesson_media' ) ) {
	function thim_content_item_lesson_media() {
		
		$item          	= LP_Global::course_item();
		$user         	= LP_Global::user();
		$course_item   	= LP_Global::course_item();
		$course        	= LP_Global::course();
		$can_view_item 	= $user->can_view_item( $course_item->get_id(), $course->get_id() );
		$media_intro   	= get_post_meta( $item->get_id(), '_lp_lesson_video_intro', true );
		$media_intro 	= ($media_intro != 'undefined') ? $media_intro : false;
		
		$media_introint = get_post_meta( $item->get_id(), '_lp_lesson_video_intro_internal', true );
		
		$media_introint	= ($media_introint) ? json_decode($media_introint) : '';
		$introVideo = '';
		if(!empty($media_introint)){
			$introVideo = '<video width="100%" height="400" controls>
			<source src="'.$media_introint->url.'" type="video/mp4">
			Your browser does not support the video tag.
			</video>';
		}
		if(!empty($media_intro)){
			$introVideo = '<video width="100%" height="400" controls>
			<source src="'.$media_intro.'" type="video/mp4">
			Your browser does not support the video tag.
			</video>';
		}


		if ( !empty( $introVideo ) && !$course_item->is_blocked() && $can_view_item ) {
			?>
			<div class="learn-press-video-intro">
				
			<div class="lp-lesson position-relative">
				<?php //learn_press_get_template( 'single-course/lesson-countdown.php' ); ?>
				<div class="video-content">
					<?php echo $introVideo; ?>
				</div>
				</div>
			</div>
			<?php
		}
	}
}

// add_filter( 'learn-press/row-action-links', 'e_course_row_action_links' );
remove_filter( 'learn-press/row-action-links', 'e_course_row_action_links' );



	/*
	* Send Notificatin before 1 hour to all type of related user. 
	* Source : https://developer.wordpress.org/reference/functions/wp_schedule_event/
	*/
	function addScheduleEventCallbackForWebinar(){
		if(!wp_next_scheduled("webinar_10mbefore")) // webinar_10mbefore callbackScheduleEventForWebinar
        {
			wp_schedule_event( time(), '10min',  'webinar_10mbefore' );
		}

		// Schedule Event for 10 mints
		// if(!wp_next_scheduled("callbackScheduleEventForWebinarOnTenMin"))
        // {
		// 	wp_schedule_event( time(), '10min',  'callbackScheduleEventForWebinarOnTenMin' );
		// }


		// wp_schedule_event( time(), '5min',  array($this, 'callbackScheduleEventForWebinar') );
	}

	function custom_emails_setting( $emails ) {
		if ( ! class_exists( 'LP_Settings_Emails_Group' ) ) {
			include_once LP_PLUGIN_PATH . 'inc/admin/settings/email-groups/class-lp-settings-emails-group.php';
		}
		$emails['LP_Email_Webinar_Update_Evaluated_User']  = include( get_stylesheet_directory() . '/inc/webinars/admin/partials/emails/class-lp-email-evaluated-webinar-update-user.php' );
		$emails['LP_Email_Webinar_Update_Evaluated_Admin'] = include( get_stylesheet_directory() . '/inc/webinars/admin/partials/emails/class-lp-email-evaluated-webinar-update-admin.php' );
		$emails['LP_Email_Webinar_Update_Evaluated_Instructor'] = include( get_stylesheet_directory() . '/inc/webinars/admin/partials/emails/class-lp-email-evaluated-webinar-update-instructor.php' );

		// Notification email template 
		$emails['LP_Email_Webinar_Notification_Evaluated_User']  = include( get_stylesheet_directory() . '/inc/webinars/admin/partials/emails/class-lp-email-evaluated-webinar-notification-user.php' );
		$emails['LP_Email_Webinar_Notification_Evaluated_Admin'] = include( get_stylesheet_directory() . '/inc/webinars/admin/partials/emails/class-lp-email-evaluated-webinar-notification-admin.php' );
		$emails['LP_Email_Webinar_Notification_Evaluated_Instructor'] = include( get_stylesheet_directory() . '/inc/webinars/admin/partials/emails/class-lp-email-evaluated-webinar-notification-instructor.php' );

		// Notification before 10 minutes 
		// Notification email template 
		$emails['LP_Email_Webinar_Notification_Before_Ten_User']  = include( get_stylesheet_directory() . '/inc/webinars/admin/partials/emails/class-lp-email-evaluated-webinar-notification-ten-user.php' );
		$emails['LP_Email_Webinar_Notification_Before_Ten_Admin'] = include( get_stylesheet_directory() . '/inc/webinars/admin/partials/emails/class-lp-email-evaluated-webinar-notification-ten-admin.php' );
		$emails['LP_Email_Webinar_Notification_Before_Ten_Instructor'] = include( get_stylesheet_directory() . '/inc/webinars/admin/partials/emails/class-lp-email-evaluated-webinar-notification-ten-instructor.php' );

		LP_Emails::instance()->emails = $emails;
		
}
	
	// add_action( 'admin_init', 'testCallbackFunction' );
	function testCallbackFunction(){
		callbackScheduleEventForWebinarFunction();
	}


	function callbackScheduleEventForWebinarFunction(){
		if ( class_exists( 'LP_Emails' ) ) {
			$emails = LP_Emails::instance()->emails;
			custom_emails_setting( $emails );
		}


		$newcreatedtime = new DateTime("now", new DateTimeZone( 'UTC' ) );
		$current = $newcreatedtime->format('Y-m-d H:i:s');

		// echo 'now: ' . $current . '<br/>';
		$thistime = date("Y-m-d H:i:s", strtotime('+61 minutes', strtotime($current)));
		// echo 'this time: ' . $thistime . '<br/>';

		
		

		$argc = array(
			'post_type' => LP_LESSON_CPT,
			'post_status' => array( 'publish' ),
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => '_lp_webinar_when_gmt',
					'value' => date( 'Y-m-d', strtotime( $thistime ) ),
					'compare' => 'LIKE'
				),
				array(
					'key' => '_lp_webinar_when_gmt',
					'value' => date( 'Y-m-d H:i', strtotime( $thistime ) ),
					'compare' => '<=', // Return the ones greater than today's date
				),
				array(
					'key' => '_lp_webinar_when_gmt',
					'value' => date( 'Y-m-d H:i', strtotime( $current ) ),
					'compare' => '>=', // Return the ones greater than today's date
				),
				array(
					'key' => '_webinar_ID',
					'compare' => 'EXISTS'
				),
				array(
					'key' => 'email_send_status_1_hour',
					'compare' => 'NOT EXISTS'
				)
			)
		);	

		$webinars = get_posts($argc);

		// echo 'Webinars test <br/><pre>';
		// print_r($webinars);
		// echo '</pre>';

		if(!empty($webinars)):
			foreach($webinars as $swebinars):
				update_post_meta($swebinars->ID, 'email_send_status_1_hour', 1);
				$course = learn_press_get_item_courses( $swebinars->ID );
				$allAuthors = get_post_meta($course[0]->ID, '_lp_co_teacher', false);
				
				$webinar_author = get_post_field( 'post_author', $course[0]->ID );
				array_push($allAuthors, $webinar_author);
				
				
				
				$post_author_id = get_post_field( 'post_author', $swebinars->ID );

				$usreHosts = array();
				foreach($allAuthors as $sauthor):
					if(get_user_meta( $sauthor, 'user_zoom_hostid', true )){
						// dcd_zoom_conference()->enableUserStatistoActive($sauthor);
						// dcd_zoom_conference()->updateZoomUserType($sauthor);
						// $token = dcd_zoom_conference()->zoomWebinarStartToken($sauthor);
						
						array_push($usreHosts, get_user_meta( $sauthor, 'user_zoom_hostid', true ));

					}
				endforeach;

				if(count($usreHosts) > 0):
					// echo 'inside user host: ';
					$webinar_id = get_post_meta( $swebinars->ID, '_webinar_ID', true );
					if ( ! empty( $webinar_id ) ) {
						//Create webinar here
						$postData = array(
							'settings'   => array(
								'alternative_hosts' => implode(',', $usreHosts)
							)
						);
						//Updated
						$update = dcd_zoom_conference()->updateWebinar( $webinar_id, $postData );
					}
				endif;
					do_action( 'learn-press/zoom-notification-lession-instructor', $swebinars->ID, $post_author_id );
					do_action( 'learn-press/zoom-notification-lession-user', $swebinars->ID, $post_author_id );
					do_action( 'learn-press/zoom-notification-lession-admin', $swebinars->ID, $post_author_id );
								
			endforeach; //foreach($webinars as $swebinars):
		endif;


		//Before 10 Min
		callbackScheduleEventForWebinarOnTenMinFunction();
	}


	function my_cron_schedules($schedules){
		if(!isset($schedules["5min"])){
			$schedules["5min"] = array(
				'interval' => 5*60,
				'display' => __('Once every 5 minutes'));
		}
		if(!isset($schedules["30min"])){
			$schedules["30min"] = array(
				'interval' => 30*60,
				'display' => __('Once every 30 minutes'));
		}
		if(!isset($schedules["10min"])){
			$schedules["10min"] = array(
				'interval' => 10*60,
				'display' => __('Once every 10 minutes'));
		}
		return $schedules;
	}




	/*
	* Wp Schedule Event for Top Ten
	* Send Notification before 10 min for start webinar
	*/
	// add_action( 'admin_init', 'callbackScheduleEventForWebinarOnTenMinFunction' );
	function callbackScheduleEventForWebinarOnTenMinFunction(){
		if ( class_exists( 'LP_Emails' ) ) {
			$emails = LP_Emails::instance()->emails;
			custom_emails_setting( $emails );
		}
	
		$newcreatedtime = new DateTime("now", new DateTimeZone( 'UTC' ) );
		$current = $newcreatedtime->format('Y-m-d H:i:s');
		// echo 'curent time: ' . $current . '<br/>';
		
		$thistime = date("Y-m-d H:i:s", strtotime('+14 minutes', strtotime($current)));
		// echo 'Tjhis time: ' . $thistime . '<br/>';
		

		$argc = array(
			'post_type' => LP_LESSON_CPT,
			'post_status' => array( 'publish' ),
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => '_lp_webinar_when_gmt',
					'value' => date( 'Y-m-d', strtotime( $thistime ) ),
					'compare' => 'LIKE'
				),
				array(
					'key' => '_lp_webinar_when_gmt',
					'value' => date( 'Y-m-d H:i', strtotime( $thistime ) ),
					'compare' => '<=', // Return the ones greater than today's date
				),
				array(
					'key' => '_lp_webinar_when_gmt',
					'value' => date( 'Y-m-d H:i', strtotime( $current ) ),
					'compare' => '>=', // Return the ones greater than today's date
				),
				array(
					'key' => '_webinar_ID',
					'compare' => 'EXISTS'
				),
				// array(
				// 	'key' => 'email_send_status_10min',
				// 	'compare' => 'NOT EXISTS'
				// )
			)
		);	

		$webinars = get_posts($argc);
		// echo 'webinars<pre>';
		// print_r($webinars);
		// echo '</pre>';


		if(!empty($webinars)):
			foreach($webinars as $swebinars):
				update_post_meta($swebinars->ID, 'email_send_status_10min', 1);
				$course = learn_press_get_item_courses( $swebinars->ID );
				// $allAuthors = get_post_meta($course[0]->ID, '_lp_co_teacher', false);
				
				$allAuthors = array();
				$webinar_author = get_post_field( 'post_author', $course[0]->ID );
				// array_push($allAuthors, $webinar_author);
				$alternative_host = get_post_meta($swebinars->ID, '_lp_alternative_host', true);


				
				$post_author_id = get_post_field( 'post_author', $swebinars->ID );

				$usreHosts = array();
				$start_url = '';
				// foreach($allAuthors as $sauthor):
				// 	if(get_user_meta( $sauthor, 'user_zoom_hostid', true )){
				// 		dcd_zoom_conference()->enableUserStatistoActive($sauthor, 'activate');
				// 		dcd_zoom_conference()->updateZoomUserType($sauthor);
				// 		$token = dcd_zoom_conference()->zoomWebinarStartToken($sauthor);
						
				// 		array_push($usreHosts, get_user_meta( $sauthor, 'user_zoom_hostid', true ));

				// 	}
				// endforeach;

				
				dcd_zoom_conference()->enableUserStatistoActive($alternative_host, 'activate');
				if(get_option( 'zoom_current_active_hoster')){
					dcd_zoom_conference()->updateZoomUserType(get_option( 'zoom_current_active_hoster'), 1);	
				}

				$update = dcd_zoom_conference()->updateZoomUserType($alternative_host, 2);
				if($update == 'success'){
					update_option( 'zoom_current_active_hoster', $alternative_host);
				}
				$master_host = LP()->settings->get( 'zoom_master_host' );
				$token = dcd_zoom_conference()->zoomWebinarStartToken($master_host);
				$webinarDetails = dcd_zoom_conference()->getZoomWebinarDetails($swebinars->ID);
				$webinarDetails = json_decode($webinarDetails);

				// echo 'webinar details <pre>';
				// print_r($webinarDetails);
				// echo '</pre>';


				// echo 'stgart token: ' . $token . '<br/>';		
				update_post_meta($swebinars->ID, 'start_url', $webinarDetails->start_url);
				update_post_meta($swebinars->ID, 'master_token', $token);
				array_push($usreHosts, $alternative_host);

				
				// echo 'HOsts<pre>';
				// print_r($usreHosts);
				// echo '</pre>';

				if(count($usreHosts) > 0):
					// echo 'inside user host: ';
					$webinar_id = get_post_meta( $swebinars->ID, '_webinar_ID', true );
					if ( ! empty( $webinar_id ) ) {
						//Create webinar here
						$postData = array(
							'settings'   => array(
								'alternative_hosts' => implode(',', $usreHosts)
							)
						);
		
						//Updated
						$update = dcd_zoom_conference()->updateWebinar( $webinar_id, $postData );

					}
				endif;

				if(!empty($token)){
					do_action( 'learn-press/zoom-notification-lession-ten-min-instructor', $swebinars->ID, $post_author_id );
					do_action( 'learn-press/zoom-notification-lession-ten-user', $swebinars->ID, $post_author_id );
					do_action( 'learn-press/zoom-notification-lession-ten-admin', $swebinars->ID, $post_author_id );
				}
				
			endforeach;
		endif;
	}
	
	add_filter('cron_schedules', 'my_cron_schedules');
	add_action('wp', 'addScheduleEventCallbackForWebinar' );
	add_action('webinar_10mbefore', 'callbackScheduleEventForWebinarFunction');






	function cstm_video_conferencing_zoom_api_get_user_transients() {		
		//Check if any transient by name is available		
		$check_transient = get_transient( '_zvc_user_lists' );
		if ( $check_transient ) {
			$users = $check_transient;
		} else {
			$active_user = dcd_zoom_conference()->listUsersByStatus('active');
			$active_user = json_decode( $active_user );

			// Pending User
			$pending_user = dcd_zoom_conference()->listUsersByStatus('pending');
			

			$pending_user = json_decode( $pending_user );
			$userArray = array_merge($active_user->users, $pending_user->users);


			// Inactive User
			$inactive_user = dcd_zoom_conference()->listUsersByStatus('inactive');
			$inactive_user = json_decode( $inactive_user );
			
			$decoded_users = new \stdClass();
			$decoded_users->users = array_merge($userArray, $inactive_user->users);

			
			if ( ! empty( $active_user->code ) && $active_user->code == 300 ) {
				$users = false;
			} else {
				//storing data to transient and getting those data for fast load by setting to fetch every 15 minutes
				set_transient( '_zvc_user_lists', $decoded_users, 900 );
				$users = $decoded_users;
			}
		}

		return $users;
	}




	/*
	* Override timezone list for backend use
	*/

	if ( ! function_exists( 'custom_zvc_get_timezone_options' ) ) {
		function custom_zvc_get_timezone_options() {
			return array(
				'UTC' => __('Universal Time UTC (GMT+00:00)', 'webinar'),
				'Pacific/Midway' => __('Midway Island, Samoa (GMT-11:00)', 'webinar'),
				'Pacific/Pago_Pago' => __('Pago Pago (GMT-11:00)', 'webinar'),
				'Pacific/Honolulu' => __('Hawaii (GMT-10:00)', 'webinar'),
				'America/Anchorage' => __('Alaska (GMT-08:00)', 'webinar'),
				'America/Vancouver' => __('Vancouver (GMT-07:00)', 'webinar'),
				'America/Los_Angeles' => __('Pacific Time (US and Canada) (GMT-07:00)', 'webinar'),
				'America/Tijuana' => __('Tijuana (GMT-07:00)', 'webinar'),
				'America/Phoenix' => __('Arizona (GMT-07:00)', 'webinar'),
				'America/Edmonton' => __('Edmonton (GMT-06:00)', 'webinar'),
				'America/Denver' => __('Mountain Time (US and Canada) (GMT-06:00)', 'webinar'),
				'America/Mazatlan' => __('Mazatlan (GMT-06:00)', 'webinar'),
				'America/Regina' => __('Saskatchewan (GMT-06:00)', 'webinar'),
				'America/Guatemala' => __('Guatemala (GMT-06:00)', 'webinar'),
				'America/El_Salvador' => __('El Salvador (GMT-06:00)', 'webinar'),
				'America/Managua' => __('Managua (GMT-06:00)', 'webinar'),
				'America/Costa_Rica' => __('Costa Rica (GMT-06:00)', 'webinar'),
				'America/Tegucigalpa' => __('Tegucigalpa (GMT-06:00)', 'webinar'),
				'America/Winnipeg' => __('Winnipeg (GMT-05:00)', 'webinar'),
				'America/Chicago' => __('Central Time (US and Canada) (GMT-05:00)', 'webinar'),
				'America/Mexico_City' => __('Mexico City (GMT-05:00)', 'webinar'),
				'America/Panama' => __('Panama (GMT-05:00)', 'webinar'),
				'America/Bogota' => __('Bogota (GMT-05:00)', 'webinar'),
				'America/Lima' => __('Lima (GMT-05:00)', 'webinar'),
				'America/Caracas' => __('Caracas (GMT-04:00)', 'webinar'),
				'America/Montreal' => __('Montreal (GMT-04:00)', 'webinar'),
				'America/New_York' => __('Eastern Time (US and Canada) (GMT-04:00)', 'webinar'),
				'America/Indianapolis' => __('Indiana (East) (GMT-04:00)', 'webinar'),
				'America/Puerto_Rico' => __('Puerto Rico (GMT-04:00)', 'webinar'),
				'America/Santiago' => __('Santiago (GMT-04:00)', 'webinar'),
				'America/Halifax' => __('Halifax (GMT-03:00)', 'webinar'),
				'America/Montevideo' => __('Montevideo (GMT-03:00)', 'webinar'),
				'America/Araguaina' => __('Brasilia (GMT-03:00)', 'webinar'),
				'America/Argentina/Buenos_Aires' => __('Buenos Aires, Georgetown (GMT-03:00)', 'webinar'),
				'America/Sao_Paulo' => __('Sao Paulo (GMT-03:00)', 'webinar'),
				'Canada/Atlantic' => __('Atlantic Time (Canada) (GMT-03:00)', 'webinar'),
				'America/St_Johns' => __('Newfoundland and Labrador (GMT-02:30)', 'webinar'),
				'America/Godthab' => __('Greenland (GMT-02:00)', 'webinar'),
				'Atlantic/Cape_Verde' => __('Cape Verde Islands (GMT-01:00)', 'webinar'),
				'Atlantic/Azores' => __('Azores (GMT+00:00)', 'webinar'),
				'Etc/Greenwich' => __('Greenwich Mean Time (GMT+00:00)', 'webinar'),
				'Atlantic/Reykjavik' => __('Reykjavik (GMT+00:00)', 'webinar'),
				'Africa/Nouakchott' => __('Nouakchott (GMT+00:00)', 'webinar'),
				'Europe/Dublin' => __('Dublin (GMT+01:00)', 'webinar'),
				'Europe/London' => __('London (GMT+01:00)', 'webinar'),
				'Europe/Lisbon' => __('Lisbon (GMT+01:00)', 'webinar'),
				'Africa/Casablanca' => __('Casablanca (GMT+01:00)', 'webinar'),
				'Africa/Bangui' => __('West Central Africa (GMT+01:00)', 'webinar'),
				'Africa/Algiers' => __('Algiers (GMT+01:00)', 'webinar'),
				'Africa/Tunis' => __('Tunis (GMT+01:00)', 'webinar'),
				'Europe/Belgrade' => __('Belgrade, Bratislava, Ljubljana (GMT+02:00)', 'webinar'),
				'CET' => __('Sarajevo, Skopje, Zagreb (GMT+02:00)', 'webinar'),
				'Europe/Oslo' => __('Oslo (GMT+02:00)', 'webinar'),
				'Europe/Copenhagen' => __('Copenhagen (GMT+02:00)', 'webinar'),
				'Europe/Brussels' => __('Brussels (GMT+02:00)', 'webinar'),
				'Europe/Berlin' => __('Amsterdam, Berlin, Rome, Stockholm, Vienna (GMT+02:00)', 'webinar'),
				'Europe/Amsterdam' => __('Amsterdam (GMT+02:00)', 'webinar'),
				'Europe/Rome' => __('Rome (GMT+02:00)', 'webinar'),
				'Europe/Stockholm' => __('Stockholm (GMT+02:00)', 'webinar'),
				'Europe/Vienna' => __('Vienna (GMT+02:00)', 'webinar'),
				'Europe/Luxembourg' => __('Luxembourg (GMT+02:00)', 'webinar'),
				'Europe/Paris' => __('Paris (GMT+02:00)', 'webinar'),
				'Europe/Zurich' => __('Zurich (GMT+02:00)', 'webinar'),
				'Europe/Madrid' => __('Madrid (GMT+02:00)', 'webinar'),
				'Africa/Harare' => __('Harare, Pretoria (GMT+02:00)', 'webinar'),
				'Europe/Warsaw' => __('Warsaw (GMT+02:00)', 'webinar'),
				'Europe/Prague' => __('Prague Bratislava (GMT+02:00)', 'webinar'),
				'Europe/Budapest' => __('Budapest (GMT+02:00)', 'webinar'),
				'Africa/Tripoli' => __('Tripoli (GMT+02:00)', 'webinar'),
				'Africa/Cairo' => __('Cairo (GMT+02:00)', 'webinar'),
				'Africa/Johannesburg' => __('Johannesburg (GMT+02:00)', 'webinar'),
				'Europe/Helsinki' => __('Helsinki (GMT+03:00)', 'webinar'),
				'Africa/Nairobi' => __('Nairobi (GMT+03:00)', 'webinar'),
				'Europe/Sofia' => __('Sofia (GMT+03:00)', 'webinar'),
				'Europe/Istanbul' => __('Istanbul (GMT+03:00)', 'webinar'),
				'Europe/Athens' => __('Athens (GMT+03:00)', 'webinar'),
				'Europe/Bucharest' => __('Bucharest (GMT+03:00)', 'webinar'),
				'Asia/Nicosia' => __('Nicosia (GMT+03:00)', 'webinar'),
				'Asia/Beirut' => __('Beirut (GMT+03:00)', 'webinar'),
				'Asia/Damascus' => __('Damascus (GMT+03:00)', 'webinar'),
				'Asia/Jerusalem' => __('Jerusalem (GMT+03:00)', 'webinar'),
				'Asia/Amman' => __('Amman (GMT+03:00)', 'webinar'),
				'Europe/Moscow' => __('Moscow (GMT+03:00)', 'webinar'),
				'Asia/Baghdad' => __('Baghdad (GMT+03:00)', 'webinar'),
				'Asia/Kuwait' => __('Kuwait (GMT+03:00)', 'webinar'),
				'Asia/Riyadh' => __('Riyadh (GMT+03:00)', 'webinar'),
				'Asia/Bahrain' => __('Bahrain (GMT+03:00)', 'webinar'),
				'Asia/Qatar' => __('Qatar (GMT+03:00)', 'webinar'),
				'Asia/Aden' => __('Aden (GMT+03:00)', 'webinar'),
				'Africa/Khartoum' => __('Khartoum (GMT+02:00)', 'webinar'),
				'Africa/Djibouti' => __('Djibouti (GMT+03:00)', 'webinar'),
				'Africa/Mogadishu' => __('Mogadishu (GMT+03:00)', 'webinar'),
				'Europe/Kiev' => __('Kiev (GMT+03:00)', 'webinar'),
				'Asia/Dubai' => __('Dubai (GMT+04:00)', 'webinar'),
				'Asia/Muscat' => __('Muscat (GMT+04:00)', 'webinar'),
				'Asia/Tehran' => __('Tehran (GMT+04:30)', 'webinar'),
				'Asia/Kabul' => __('Kabul (GMT+04:30)', 'webinar'),
				'Asia/Baku' => __('Baku, Tbilisi, Yerevan (GMT+04:00)', 'webinar'),
				'Asia/Yekaterinburg' => __('Yekaterinburg (GMT+05:00)', 'webinar'),
				'Asia/Tashkent' => __('Islamabad, Karachi, Tashkent (GMT+05:00)', 'webinar'),
				'Asia/Calcutta' => __('India (GMT+05:30)', 'webinar'),
				'Asia/Kolkata' => __('Mumbai, Kolkata, New Delhi (GMT+05:30)', 'webinar'),
				'Asia/Kathmandu' => __('Kathmandu (GMT+05:45)', 'webinar'),
				'Asia/Novosibirsk' => __('Novosibirsk (GMT+07:00)', 'webinar'),
				'Asia/Almaty' => __('Almaty (GMT+06:00)', 'webinar'),
				'Asia/Dacca' => __('Dacca (GMT+06:00)', 'webinar'),
				'Asia/Dhaka' => __('Astana, Dhaka (GMT+06:00)', 'webinar'),
				'Asia/Krasnoyarsk' => __('Krasnoyarsk (GMT+07:00)', 'webinar'),
				'Asia/Bangkok' => __('Bangkok (GMT+07:00)', 'webinar'),
				'Asia/Saigon' => __('Vietnam (GMT+07:00)', 'webinar'),
				'Asia/Jakarta' => __('Jakarta (GMT+07:00)', 'webinar'),
				'Asia/Irkutsk' => __('Irkutsk, Ulaanbaatar (GMT+08:00)', 'webinar'),
				'Asia/Shanghai' => __('Beijing, Shanghai (GMT+08:00)', 'webinar'),
				'Asia/Hong_Kong' => __('Hong Kong (GMT+08:00)', 'webinar'),
				'Asia/Taipei' => __('Taipei (GMT+08:00)', 'webinar'),
				'Asia/Kuala_Lumpur' => __('Kuala Lumpur (GMT+08:00)', 'webinar'),
				'Asia/Singapore' => __('Singapore (GMT+08:00)', 'webinar'),
				'Australia/Perth' => __('Perth (GMT+08:00)', 'webinar'),
				'Asia/Yakutsk' => __('Yakutsk (GMT+09:00)', 'webinar'),
				'Asia/Seoul' => __('Seoul (GMT+09:00)', 'webinar'),
				'Asia/Tokyo' => __('Osaka, Sapporo, Tokyo (GMT+09:00)', 'webinar'),
				'Australia/Darwin' => __('Darwin (GMT+09:30)', 'webinar'),
				'Australia/Adelaide' => __('Adelaide (GMT+09:30)', 'webinar'),
				'Asia/Vladivostok' => __('Vladivostok (GMT+10:00)', 'webinar'),
				'Pacific/Port_Moresby' => __('Guam, Port Moresby (GMT+10:00)', 'webinar'),
				'Australia/Brisbane' => __('Brisbane (GMT+10:00)', 'webinar'),
				'Australia/Sydney' => __('Canberra, Melbourne, Sydney (GMT+10:00)', 'webinar'),
				'Australia/Hobart' => __('Hobart (GMT+10:00)', 'webinar'),
				'Asia/Magadan' => __('Magadan (GMT+11:00)', 'webinar'),
				'SST' => __('Solomon Islands (GMT+11:00)', 'webinar'),
				'Pacific/Noumea' => __('New Caledonia (GMT+11:00)', 'webinar'),
				'Asia/Kamchatka' => __('Kamchatka (GMT+12:00)', 'webinar'),
				'Pacific/Fiji' => __('Fiji Islands, Marshall Islands (GMT+12:00)', 'webinar'),
				'Pacific/Auckland' => __('Auckland, Wellington (GMT+12:00)', 'webinar'),
			);
		}
	}
	

	function get_timezone_offset($remote_tz, $origin_tz = null) {
		if($origin_tz === null) {
			if(!is_string($origin_tz = date_default_timezone_get())) {
				return false; // A UTC timestamp was returned -- bail out!
			}
		}
		$origin_dtz = new DateTimeZone($origin_tz);
		$remote_dtz = new DateTimeZone($remote_tz);
		$origin_dt = new DateTime("now", $origin_dtz);
		$remote_dt = new DateTime("now", $remote_dtz);

	

		
		$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
	
		return $offset;
	}



	/*
	* Next Cron event time
	* Source: https://wordpress.stackexchange.com/questions/83270/when-does-next-cron-job-run-time-from-now
	*/

	function wb_get_next_cron_time( $cron_name ){
		foreach( _get_cron_array() as $timestamp => $crons ){
			if( in_array( $cron_name, array_keys( $crons ) ) ){
				return $timestamp - time();
			}
		}
		return false;
	}