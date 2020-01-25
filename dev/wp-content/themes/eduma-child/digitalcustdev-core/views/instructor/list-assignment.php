<?php 
/*
* Assignment List of Instructor's Student
*/

$assignments = get_author_assignments();
if(count($assignments) > 0):
?>

<div id="assignments">
    <table class="table table-hover">
        <thead>
            <tr>
                <th><?php _e('Title', 'webinar'); ?></th>
                <th><?php _e('Course', 'webinar'); ?></th>
                <th><?php _e('Students', 'webinar'); ?></th>
                <th><?php _e('Mark', 'webinar'); ?></th>
                <th><?php _e('Passing Grade', 'webinar'); ?></th>
                <th><?php _e('Duration', 'webinar'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($assignments as $single): 
                $curd = new LP_Assignment_CURD();
                $courses = learn_press_get_item_courses( $single->ID );
                // echo '<pre>';
                // print_r( $courses );
                // echo '</pre>';
                ?>
                <tr>
                    <td><?php echo $single->post_title; ?></td>
                    <td>
                    <?php 
                        if ( $courses ) {
                            foreach ( $courses as $course ) {
                                echo '<div><a href="' . esc_url( get_the_permalink( $course->ID ) ) . '">' . get_the_title( $course->ID ) . '</a>';
                                echo '</div>';
                            }
                        } else {
                            _e( 'Not assigned yet', 'learnpress-assignments' );
                        }
                    ?>
                    </td>
                    <td>
                        <?php 
                            $count = count( $curd->get_students( $single->ID ) );
                            echo '<span class="lp-label-counter' . ( ! $count ? ' disabled' : '' ) . '">' . $count . '</span>';
                        ?>
                    </td>
                    <td>
                        <?php 
                            $maximum_mark = ( get_post_meta( $single->ID, '_lp_mark', true ) ) ? get_post_meta( $single->ID, '_lp_mark', true ) : 10;
                            echo $maximum_mark;
                        ?>
                    </td>
                    <td>
                        <?php
                            $passing_grade = ( get_post_meta( $single->ID, '_lp_passing_grade', true ) ) ? get_post_meta( $single->ID, '_lp_passing_grade', true ) : 7;
                            echo $passing_grade;
                        ?>
                    </td>
                    <td>
                        <?php 
                            $duration = learn_press_human_time_to_seconds( get_post_meta( $single->ID, '_lp_duration', true ) );
                            if ( $duration > 86399 ) {
                                echo get_post_meta( $single->ID, '_lp_duration', true ) . '(s)';
                            } elseif ( $duration >= 600 ) {
                                echo date( 'H:i:s', $duration );
                            } elseif ( $duration > 0 ) {
                                echo date( 'i:s', $duration );
                            } else {
                                echo '-';
                            }
                        ?>
                    </td>
                </tr>
                
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>

