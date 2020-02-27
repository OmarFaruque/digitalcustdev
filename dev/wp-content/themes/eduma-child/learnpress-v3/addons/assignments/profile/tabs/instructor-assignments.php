<?php
/**
 * Template for displaying assignments tab in user profile page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/assignments/profile/tabs/assignments.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Assignments/Templates
 * @version  3.0.2
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$assignments = get_author_assignments();
?>

<div class="learn-press-subtab-content">
	<h3 class="profile-heading"><?php _e( 'Assignments - Courses', 'learnpress-assignments' ); ?></h3>

	<?php if ( $assignments ) { ?>
		<table class="lp-list-table profile-list-assignments profile-list-table cutm">
			<thead>
			<tr>
				<th class="column-course"><?php _e( 'Course / Webinars', 'learnpress-assignments' ); ?></th>
				<th class="column-not-evaluated"><?php _e( 'Not evaluated', 'learnpress-assignments' ); ?></th>
				<th class="column-time-interval"><?php _e( 'Evaluated', 'learnpress-assignments' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $assignments as $user_assignment ) { ?>
				<?php
				/**
				 * @var $user_assignment LP_User_Item_Assignment
				 */
				$assignment     = learn_press_get_assignment( $user_assignment->ID );
                $courses        = learn_press_get_item_courses( array( $user_assignment->ID ) );
                $not_avutotal   = not_avualate($user_assignment->ID);
                $evulated_total = avualate($user_assignment->ID);
                

				// for case assignment was un-assign from course
				if ( ! $courses ) {
					continue;
				}

				if($not_avutotal <= 0 && $evulated_total <= 0){
					continue;
				}

                $course       = learn_press_get_course( $courses[0]->ID );
                
				?>
				<tr>
					<td class="column-course">
						<?php if ( $courses ) { ?>
							<a href="<?php echo '?cid=' . $user_assignment->ID; //$courses[0]->ID; //$course->get_permalink() ?>">
								<?php echo $course->get_title( 'display' ); ?>
							</a>
						<?php } ?>
					</td>
					
					<td class="column-not-evulated">
						<?php echo ($not_avutotal > 0 ) ? $not_avutotal : '-'; ?>
                    </td>
                    <td class="column-evulated">
						<?php echo ($evulated_total > 0 ) ? $evulated_total : '-'; ?>
					</td>
				</tr>
				<?php continue; ?>
				<tr>
					<td colspan="4"></td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
			<tr class="list-table-nav">
				<td colspan="2" class="nav-text">
					<?php// echo $query->get_offset_text(); ?>
				</td>
				<td colspan="4" class="nav-pages">
					<?php // $query->get_nav_numbers( true ); ?>
				</td>
			</tr>
			</tfoot>
		</table>

	<?php } else { ?>
		<?php learn_press_display_message( __( 'No assignments!', 'learnpress-assignments' ) ); ?>
	<?php } ?>
</div>
