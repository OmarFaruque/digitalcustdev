
<?php 
/*
* Template Name: Webinars
*/
// wp_reset_query();

$cookie_name = 'course_switch';
$layout      = ( ! empty( $_COOKIE[ $cookie_name ] ) ) ? $_COOKIE[ $cookie_name ] : 'grid-layout';

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$wcount = (learn_press_get_page_id( 'wcount' )) ? learn_press_get_page_id( 'wcount' ) : 8;

$condition = array(
	'post_type'           => 'lp_course',
	'post_status' => 'publish',
	'posts_per_page'      => $wcount,
	'ignore_sticky_posts' => true,
	'meta_query' => array(
		'relation' => 'AND',    
		array(
			'key' => '_course_type',
			'value' => 'webinar'
		)
	),
    'paged' =>  $paged
);

require_once(THIM_CHILD_THEME_DIR . 'inc/webinars/public/template/additional-query.php');
$theQuery = new WP_Query( $condition );
$_COOKIE['testimonials'] = $theQuery;
$columns = 3;
?>
<div id="webinars" class="site-main col-sm-9 alignleft"> 
<div class="thim-widget-courses template-grid-v35">
<?php
include(THIM_CHILD_THEME_DIR . 'inc/webinars/public/search.php');
if ( $theQuery->have_posts() ) :
	?>
	<div class="thim-widget-courses-wrapper omr">
		<div id="thim-course-archive" class="<?php echo ( $layout == 'list-layout' ) ? 'thim-course-list' : 'thim-course-grid'; ?>" data-cookie="grid-layout">
			<?php while ( $theQuery->have_posts() ) : $theQuery->the_post(); 
				$course = learn_press_get_course( get_the_ID() );
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
			
			?>
				<div class="lpr_course <?php echo 'course-grid-' . $columns; ?>">
					<div class="course-item">
						<div class="course-thumbnail">
							<a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>">
								<?php echo thim_get_feature_image( get_post_thumbnail_id( get_the_ID() ), 'full', 450, 400, get_the_title() ); ?>
								<?php is_rebon($status); ?>
							</a>
							<?php do_action( 'thim_inner_thumbnail_course' ); ?>
							<a class="course-readmore" href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>"><?php echo esc_html__( 'Read More', 'eduma' ); ?></a>
						</div>

						<div class="thim-course-content">
							<?php learn_press_courses_loop_item_instructor(); ?>
							<?php
							the_title( sprintf( '<h2 class="course-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
							?>

							<?php if ( thim_plugin_active( 'learnpress-coming-soon-courses/learnpress-coming-soon-courses.php' ) && learn_press_is_coming_soon( get_the_ID() ) ): ?>
								<div class="message message-warning learn-press-message coming-soon-message">
									<?php esc_html_e( 'Coming soon', 'eduma' ) ?>
								</div>
							<?php else: ?>
								<div class="course-meta">
									<?php learn_press_courses_loop_item_instructor(); ?>
									<?php thim_course_ratings(); ?>
									<?php learn_press_courses_loop_item_students(); ?>
									<?php thim_course_ratings_count(); ?>
									<?php learn_press_courses_loop_item_price(); ?>
								</div>

								<?php learn_press_courses_loop_item_price(); ?>
							<?php endif; ?>

							<div class="course-readmore">
								<a href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Read More', 'eduma' ); ?></a>
							</div>
						</div>
					</div>
				</div>
			<?php
			endwhile;
			?>
		</div>
	</div>
	<div class="clear"></div>
<?php
endif;

?>
</div></div></div>
<?php include(THIM_CHILD_THEME_DIR . 'inc/webinars/public/sidebar.php'); 

if ( $theQuery->max_num_pages > 1 ) {
?>
<nav class="learn-press-pagination navigation pagination om">
    <?php
    echo paginate_links( apply_filters( 'learn_press_pagination_args', array(
        'base'         => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ),
        'format'       => '',
        'add_args'     => '',
        'current'      => max( 1, get_query_var( 'paged' ) ),
        'total'        => $theQuery->max_num_pages,
        'prev_text'    => max( 1, get_query_var( 'paged' ) )-1,
        'next_text'    => max( 1, get_query_var( 'paged' ) )+1,
        'type'         => 'list',
        'end_size'     => 3,
        'mid_size'     => 3
    ) ) );
    ?>
</nav>
<?php } ?>
<?php wp_reset_postdata(); ?>