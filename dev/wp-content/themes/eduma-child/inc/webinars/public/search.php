
<?php
$testimonials = $_COOKIE['testimonials'];
$show_description = get_theme_mod('thim_learnpress_cate_show_description');
$show_desc        = !empty($show_description) ? $show_description : '';
$cat_desc         = term_description();

$total = $testimonials->found_posts;


if ( $total == 0 ) {
	$message = '<p class="message message-error">' . esc_html__( 'No webinars found!', 'eduma' ) . '</p>';
	$index   = esc_html__( 'There are no webinars courses!', 'eduma' );
} elseif ( $total == 1 ) {
	$index = esc_html__( 'Showing only one result', 'eduma' );
} else {
	$courses_per_page = absint( learn_press_get_page_id( 'wcount' ) );
	$paged            = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	

	$from = 1 + ( $paged - 1 ) * $courses_per_page;
	$to   = ( $paged * $courses_per_page > $total ) ? $total : $paged * $courses_per_page;

	if ( $from == $to ) {
		$index = sprintf(
			esc_html__( 'Showing last course of %s results', 'eduma' ),
			$total
		);
	} else {
		$index = sprintf(
			esc_html__( 'Showing %s-%s of %s results', 'eduma' ),
			$from,
			$to,
			$total
		);
	}
}

$cookie_name = 'course_switch';
$layout      = ( ! empty( $_COOKIE[ $cookie_name ] ) ) ? $_COOKIE[ $cookie_name ] : 'grid-layout';

$default_order = apply_filters( 'thim_default_order_course_option', array(
	'newly-published' => esc_html__( 'Newly published', 'eduma' ),
	'alphabetical'    => esc_html__( 'Alphabetical', 'eduma' ),
	'most-members'    => esc_html__( 'Most members', 'eduma' )
) );;

/**
 * @since 3.0.0
 */
do_action( 'learn-press/before-main-content' );

/**
 * @since 3.0.0
 */
do_action( 'learn-press/archive-description' );

?>
	<div class="thim-course-top switch-layout-container <?php if ( $show_desc && $cat_desc ) {
		echo 'has_desc';
	} ?>">
		<div class="thim-course-switch-layout switch-layout">
			<a href="#" class="list switchToGrid<?php echo ( $layout == 'grid-layout' ) ? ' switch-active' : ''; ?>"><i class="fa fa-th-large"></i></a>
			<a href="#" class="grid switchToList<?php echo ( $layout == 'list-layout' ) ? ' switch-active' : ''; ?>"><i class="fa fa-list-ul"></i></a>
		</div>
		<div class="course-index">
			<span><?php echo( $index ); ?></span>
		</div>
		<form id="webnartopform" action="" method="POST">
		<?php if ( get_theme_mod( 'thim_display_course_sort', true ) ): ?>
			<div class="thim-course-order">
				<select name="orderby">
					<?php
					foreach ( $default_order as $k => $v ) {
						$selected = (isset($_POST['orderby']) && $_POST['orderby'] == esc_attr( $k )) ? 'selected' : '';
						echo '<option '.$selected.' value="' . esc_attr( $k ) . '">' . ( $v ) . '</option>';
					}
					?>
				</select>
			</div>
		<?php endif; ?>
		<div class="courses-searching">
			
				<input type="text" value="<?php echo (isset($_POST['ws'])) ? $_POST['ws'] : '';  ?>" name="ws" placeholder="<?php esc_attr_e( 'Search our webinars', 'eduma' ) ?>"  autocomplete="off" />
                <input type="hidden" name="meta_value" value="webinar">
				<button type="submit"><i class="fa fa-search"></i></button>
				<span class="widget-search-close"></span>
		</div>
		</form>
	</div>
