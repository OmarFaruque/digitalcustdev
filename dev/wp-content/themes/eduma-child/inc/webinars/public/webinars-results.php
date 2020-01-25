<?php
    $where = $_REQUEST["s"];
    echo($where);
	ob_start();
	$nargs = array(
		'post_type'   => 'lp_course',
		'post_status' => 'publish',
        'meta_value' => 'webinar',
        's' => $where
	   );
	$testimonials = new WP_Query( $nargs );
	//$items = $this->get_webinars();
	do_action( 'learn_press_before_main_content' );

/**
 * @since 3.0.0
 */
do_action( 'learn-press/before-main-content' );

/**
 * @deprecated
 */
do_action( 'learn_press_archive_description' );

/**
 * @since 3.0.0
 */
include('search.php');
do_action( 'learn-press/archive-description' );
do_action( 'learn_press_before_courses_loop' );
	echo('<h4>' . $args['quotes-title'] . '</h4>');
	do_action( 'learn-press/before-courses-loop' );
	learn_press_begin_courses_loop();
    echo('<div id="thim-course-archive" class="thim-course-grid" data-cookie="grid-layout">');
	while ( $testimonials->have_posts() ) : $testimonials->the_post();

		learn_press_get_template_part( 'content', 'course' );

	endwhile;
    echo('</div>');
	learn_press_end_courses_loop();
	do_action( 'learn_press_after_courses_loop' );
	do_action( 'learn-press/after-courses-loop' );
	do_action( 'learn-press/after-main-content' );

/**
 * @deprecated
 */
do_action( 'learn_press_after_main_content' );
			//echo('<li><b>' . $item->post_title . ' | </b>' . $item->post_content . '</li>');
    // foreach
		//echo ('</ul>');

	// $output = ob_get_contents();
	// ob_end_clean();

	// return $output
//}