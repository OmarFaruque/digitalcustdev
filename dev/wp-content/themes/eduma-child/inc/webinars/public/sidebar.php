<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package thim
 */
$theme_options_data = get_theme_mods();
$sticky_sidebar     = ! empty( $theme_options_data['thim_sticky_sidebar'] ) ? ' sticky-sidebar' : '';
$cls_sidebar        = '';
if ( get_theme_mod( 'thim_header_style', 'header_v1' ) == 'header_v4' ) {
	$cls_sidebar = ' sidebar_' . get_theme_mod( 'thim_header_style' );
}

$terms = get_terms( 
	array(
		'taxonomy' => 'webinar_categories',
		'hide_empty' => false,
	));
?>

<div id="sidebar" class="widget-area col-sm-3" role="complementary">
<?php if(!is_singular( 'lp_course' )): ?>
<aside class="thim-course-filter-wrapper">
			<form action="" name="thim-course-filter" method="POST" class="thim-course-filter">
					
					<h4 class="filter-title"><?php _e('Webinar categories', 'webinar'); ?></h4>
					<ul class="list-cate-filter">
					<?php foreach($terms as $term):
						$checked = (isset($_POST['webinar-cate-filter']) && in_array($term->term_id, $_POST['webinar-cate-filter'])) ? 'checked': '';
						?>
						<li class="term-item">
							<input <?php echo $checked; ?> type="checkbox" name="webinar-cate-filter[]" id="webinar_<?php echo $term->term_id; ?>" value="<?php echo $term->term_id; ?>">
								<label for="webinar_<?php echo $term->term_id; ?>"><?php echo $term->name; ?> <span>(<?php echo $term->count; ?>)</span></label>
						</li>
					<?php endforeach; ?>
					</ul>
					<h4 class="filter-title"><?php _e('Price', 'webinar'); ?></h4>
					<ul class="list-price-filter">
						<li class="price-item">
							<input type="radio" id="price-filter_all" <?php echo (isset($_POST['course-price-filter']) && $_POST['course-price-filter'] == 'all' ) ? 'checked' : ''; ?> name="course-price-filter" value="all">
							<label for="price-filter_all">
								<?php _e('All', 'webinar'); ?><span></span>
							</label>
						</li>
						<li class="price-item">
							<input <?php echo (isset($_POST['course-price-filter']) && $_POST['course-price-filter'] == 'free' ) ? 'checked' : ''; ?> type="radio" id="price-filter_free" name="course-price-filter" value="free">
							<label for="price-filter_free">
								<?php _e('Free', 'webinar'); ?> <span></span>
							</label>
						</li>
						<li class="price-item">
							<input type="radio" id="price-filter_paid" name="course-price-filter" <?php echo (isset($_POST['course-price-filter']) && $_POST['course-price-filter'] == 'paid' ) ? 'checked' : ''; ?> value="paid">
							<label for="price-filter_paid"><?php _e('Paid', 'webinar'); ?><span></span></label>
						</li>
					</ul>
					
					
					<h4 class="filter-title"><?php _e('Webinar Date Status', 'webinar'); ?></h4>
					<ul class="list-date-filter">
						<li class="date-item">
							<input type="radio" id="date-filter_all" <?php echo (isset($_POST['date-filter']) && $_POST['date-filter'] == 'all' ) ? 'checked' : ''; ?> name="date-filter" value="all">
							<label for="date-filter_all"><?php _e('All', 'webinar'); ?><span></span></label>
						</li>
						<li class="date-item">
							<input type="radio" id="date-filter_future" <?php echo (isset($_POST['date-filter']) && $_POST['date-filter'] == 'future' ) ? 'checked' : ''; ?> name="date-filter" value="future">
							<label for="date-filter_future"><?php _e('Future', 'webinar'); ?><span></span></label>
						</li>
						<li class="date-item">
							<input type="radio" id="date-filter_free" name="date-filter" <?php echo (isset($_POST['date-filter']) && $_POST['date-filter'] == 'inprogress' ) ? 'checked' : ''; ?> value="inprogress">
							<label for="date-filter_free"><?php _e('In progress', 'webinar'); ?><span></span></label>
						</li>
						<li class="date-item">
							<input type="radio" id="date-filter_paid" name="date-filter" <?php echo (isset($_POST['date-filter']) && $_POST['date-filter'] == 'passed' ) ? 'checked' : ''; ?> value="passed">
							<label for="date-filter_paid"><?php _e('Passed', 'webinar'); ?><span></span></label>
						</li>
					</ul>
					
				<div class="filter-submit">
					<button type="submit"><?php _e('Filter Results', 'webinar'); ?></button>
				</div>
			</form>
		</aside>
	<?php
	endif; //if(!is_singular( 'lp_course' )):
	do_action( 'thim_before_sidebar_course' ); ?>

	<?php if ( ! dynamic_sidebar( 'webinars-sidebar' ) ) :
		dynamic_sidebar( 'webinars-sidebar' );
	endif; // end sidebar widget area ?>

	<?php do_action( 'thim_after_sidebar_course' ); ?>
</div><!-- #secondary -->
