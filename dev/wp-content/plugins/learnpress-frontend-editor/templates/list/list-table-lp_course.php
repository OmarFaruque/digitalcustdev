<?php
/**
 * Template for displaying list of course.
 *
 * @author  ThimPress
 * @package LearnPress/Frontend-Editor/Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or die;

/**
 * @var LP_Addon_Frontend_Editor $frontend_editor
 */

global $frontend_editor, $e_wp_query, $wp_query, $post;
$post_manage = $frontend_editor->post_manage;
$query       = $post_manage->get_posts();

$post_list_table = LP_Frontend_Editor_Post_List_Table::instance();

$course_curd = new LP_Course_CURD();
?>
<?php $frontend_editor->get_template( 'list/table-nav-top' ); ?>

<?php if ( $e_wp_query->have_posts() ) { ?>
    <ul class="e-list-posts list-courses">
		<?php while ( $e_wp_query->have_posts() ): $e_wp_query->the_post(); ?>

			<?php
			$course = learn_press_get_course( get_the_ID() );

			if ( ! $title = get_the_title() ) {
				$title = __( '(No title)', 'learnpress-frontend-editor' );
			}
			?>
            <li class="e-course">
                <div class="e-course-thumbnail">
					<?php echo $course->get_image(); ?>
                </div>
                <div class="e-course-info">
                    <p class="e-course-title">
						<?php if ( get_post_status() === 'trash' ) { ?>
                            <span><?php echo esc_attr( $title ); ?></span>
						<?php } else { ?>
                            <a href="<?php echo $post_manage->get_edit_post_link( get_post_type(), get_the_ID() ); ?>"><?php echo esc_attr( $title ); ?></a>
						<?php } ?>
                        <span class="e-label <?php echo esc_attr( get_post_status() ); ?>"><?php echo e_post_status( $post ); ?></span>
                    </p>
                    <p class="e-course-meta">
                        <span><?php printf( __( 'Created by <strong>%s</strong>', 'learnpress-frontend-editor' ), get_the_author_link() ); ?></span>
                        <span><?php echo get_the_date(); ?></span>
                    </p>

                    <div class="e-course-general-info">
						<?php
						/**
						 * @since 3.0.0
						 */
						do_action( 'fe/before-list-courses-meta', get_the_ID() );
						?>

						<?php $count_users = $course_curd->count_enrolled_users( get_the_ID() ); ?>
                        <span class="e-course-users"><?php echo $count_users > 1 ? sprintf( _n( '%d user', '%d users', $count_users, 'learnpress-frontend-editor' ), $count_users ) : sprintf( __( '%d user', 'learnpress-frontend-editor' ), $count_users ); ?></span>
                        <span class="e-course-duration"><?php
							if ( $duration = e_post_duration() ) {
								echo $duration;
							} else {
								esc_html_e( 'Not set', 'learnpress-frontend-editor' );
							}
							?>
                        </span>
						<?php if ( has_term( '', 'course_category', get_the_ID() ) ) { ?>
                            <span class="e-course-category"><?php the_terms( get_the_ID(), 'course_category' ); ?></span>
						<?php } ?>

						<?php
						/**
						 * @since 3.0.0
						 */
						do_action( 'fe/after-list-courses-meta', get_the_ID() );
						?>
                    </div>

                    <div class="e-course-actions">
						<?php if ( $post_manage->get_filter_status() !== 'trash' ) { ?>
                            <a class="trash e-button"
                               href="<?php echo $post_manage->get_edit_post_link( get_post_type(), get_the_ID() ); ?>"
                               @click="_deleteCourse($event, <?php echo get_the_ID(); ?>)"><?php _e( 'Trash', 'learnpress-frontend-editor' ); ?></a>

							<?php if ( get_post_status() === 'publish' ) { ?>
                                <a class="e-button view"
                                   href="<?php echo get_the_permalink(); ?>"><?php _e( 'View', 'learnpress-frontend-editor' ); ?></a>
							<?php } ?>
						<?php } else { ?>
                            <a class="restore e-button"
                               @click="_restoreCourse($event, <?php echo get_the_ID(); ?>)"><?php _e( 'Restore', 'learnpress-frontend-editor' ); ?></a>
                            <div class="trash permanently e-button-dropdown-list">
                                <span class="e-button-label e-button"
                                      @click="_deleteCourse($event, <?php echo get_the_ID(); ?>, true)"><?php _e( 'Delete Permanently', 'learnpress-frontend-editor' ); ?></span>
                                <ul>
                                    <li>
                                        <a @click="_deleteCourse($event, <?php echo get_the_ID(); ?>, true, 'trash')"><?php esc_html_e( 'And move items to trash', 'learnpress-frontend-editor' ); ?></a>
                                    </li>
                                    <li>
                                        <a @click="_deleteCourse($event, <?php echo get_the_ID(); ?>, true, 'permanently')"><?php esc_html_e( 'And delete permanently items', 'learnpress-frontend-editor' ); ?></a>
                                    </li>
                                </ul>
                            </div>
						<?php } ?>
                    </div>
                </div>
            </li>
		<?php endwhile; ?>
    </ul>
	<?php
	$frontend_editor->get_template( 'list/table-nav-bottom' );
} else {
	?>
    <p><?php esc_html_e( 'No course found!', 'learnpress-frontend-editor' ); ?></p>
<?php }

