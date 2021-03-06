<?php

global $post;
$limit        = $instance['limit'];
$item_visible = $instance['slider-options']['item_visible'];
$pagination   = $instance['slider-options']['show_pagination'] ? $instance['slider-options']['show_pagination'] : 0;
$navigation   = $instance['slider-options']['show_navigation'] ? $instance['slider-options']['show_navigation'] : 0;
$autoplay     = isset( $instance['slider-options']['auto_play'] ) ? $instance['slider-options']['auto_play'] : 0;
$featured     = ! empty( $instance['featured'] ) ? true : false;
$thumb_w      = ( ! empty( $instance['thumbnail_width'] ) && '' != $instance['thumbnail_width'] ) ? $instance['thumbnail_width'] : apply_filters( 'thim_course_thumbnail_width', 450 );
$thumb_h      = ( ! empty( $instance['thumbnail_height'] ) && '' != $instance['thumbnail_height'] ) ? $instance['thumbnail_height'] : apply_filters( 'thim_course_thumbnail_height', 400 );

$condition = array(
	'post_type'           => 'lp_course',
	'post_status' => 'publish',
	'posts_per_page'      => $limit,
	'ignore_sticky_posts' => true,
	'meta_key' => '_course_type',
	'meta_value' => 'webinar'
);
$sort      = $instance['order'];


if ( $sort == 'category' && $instance['cat_id'] && $instance['cat_id'] != 'all' ) {
	if ( get_term( $instance['cat_id'], 'webinar_categories' ) ) {
		$condition['tax_query'] = array(
			array(
				'taxonomy' => 'webinar_categories',
				'field'    => 'term_id',
				'terms'    => $instance['cat_id']
			),
		);
	}
}


if ( $sort == 'popular' ) {
	global $wpdb;
	$query = $wpdb->prepare( "
	  SELECT ID, a+IF(b IS NULL, 0, b) AS students FROM(
		SELECT p.ID as ID, IF(pm.meta_value, pm.meta_value, 0) as a, (
	SELECT COUNT(*)
  FROM (SELECT COUNT(item_id), item_id, user_id FROM {$wpdb->prefix}learnpress_user_items GROUP BY item_id, user_id) AS Y
  GROUP BY item_id
  HAVING item_id = p.ID
) AS b
FROM {$wpdb->posts} p
LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id  AND pm.meta_key = %s
WHERE p.post_type = %s AND p.post_status = %s
GROUP BY ID
) AS Z
ORDER BY students DESC
	  LIMIT 0, $limit
 ", '_lp_students', 'lp_course', 'publish' );

	$post_in = $wpdb->get_col( $query );

	$condition['post__in'] = $post_in;
	$condition['orderby']  = 'post__in';

}

if ( $featured ) {
	$condition['meta_query'] = array(
		array(
			'key'   => '_lp_featured',
			'value' => 'yes',
		)
	);
}
require_once(THIM_CHILD_THEME_DIR . 'inc/webinars/public/template/additional-query.php');
$the_query = new WP_Query( $condition );

if ( $the_query->have_posts() ) :
	if ( $instance['title'] ) {
		echo ent2ncr( $args['before_title'] . $instance['title'] . $args['after_title'] );
	}

	?>
	<div class="thim-carousel-wrapper thim-course-carousel thim-course-grid" data-visible="<?php echo esc_attr( $item_visible ); ?>"
	     data-pagination="<?php echo esc_attr( $pagination ); ?>" data-navigation="<?php echo esc_attr( $navigation ); ?>" data-autoplay="<?php echo esc_attr( $autoplay ); ?>">
		<?php while ( $the_query->have_posts() ) : $the_query->the_post();
		$course = learn_press_get_course( get_the_ID() );
		$course_id = $course->get_id();
		$lessons = $this->get_course_lessons($course_id);
		
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

		if( is_plugin_active( 'learnpress-course-review/learnpress-course-review.php' ) ) {
			$num_ratings = learn_press_get_course_rate_total( get_the_ID() ) ? learn_press_get_course_rate_total( get_the_ID() ) : 0;
			$course_rate = learn_press_get_course_rate( get_the_ID() );
		}
		
		?>
			<div class="course-item">

				<div class="course-thumbnail">
					<a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>">
						<?php echo thim_get_feature_image( get_post_thumbnail_id( get_the_ID() ), 'full', $thumb_w, $thumb_h, get_the_title() ); ?>
						<?php is_rebon($status); ?>
					</a>
					<?php do_action( 'thim_inner_thumbnail_course' ); ?>
					<a class="course-readmore" href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>"><?php echo esc_html__( 'Read More', 'eduma' ); ?></a>
				</div>

				<div class="thim-course-content">
					<?php
					learn_press_courses_loop_item_instructor();

					the_title( sprintf( '<h2 class="course-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
					do_action( 'learn_press_after_the_title' );
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
		<?php
		endwhile;
		?>
	</div>

	<script type="text/javascript">
		jQuery(document).ready(function(){
			"use strict";

			if (jQuery("body").hasClass("vc_editor")) {
				jQuery('.thim-carousel-wrapper').each(function() {
					var item_visible = jQuery(this).data('visible') ? parseInt(
						jQuery(this).data('visible')) : 4,
						item_desktopsmall = jQuery(this).data('desktopsmall') ? parseInt(
							jQuery(this).data('desktopsmall')) : item_visible,
						itemsTablet = jQuery(this).data('itemtablet') ? parseInt(
							jQuery(this).data('itemtablet')) : 2,
						itemsMobile = jQuery(this).data('itemmobile') ? parseInt(
							jQuery(this).data('itemmobile')) : 1,
						pagination = !!jQuery(this).data('pagination'),
						navigation = !!jQuery(this).data('navigation'),
						autoplay = jQuery(this).data('autoplay') ? parseInt(
							jQuery(this).data('autoplay')) : false,
						navigation_text = (jQuery(this).data('navigation-text') &&
							jQuery(this).data('navigation-text') === '2') ? [
							'<i class=\'fa fa-long-arrow-left \'></i>',
							'<i class=\'fa fa-long-arrow-right \'></i>',
						] : [
							'<i class=\'fa fa-chevron-left \'></i>',
							'<i class=\'fa fa-chevron-right \'></i>',
						];

					jQuery(this).owlCarousel({
						items            : item_visible,
						itemsDesktop     : [1200, item_visible],
						itemsDesktopSmall: [1024, item_desktopsmall],
						itemsTablet      : [768, itemsTablet],
						itemsMobile      : [480, itemsMobile],
						navigation       : navigation,
						pagination       : pagination,
						lazyLoad         : true,
						autoPlay         : autoplay,
						navigationText   : navigation_text
					});
				});
			}
		});
	</script>
<?php
endif;
wp_reset_postdata();
