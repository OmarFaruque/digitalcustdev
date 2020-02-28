<?php
/**
 * Template for displaying gradebook of a course in user profile page.
 *
 * * This template can be overridden by copying it to yourtheme/learnpress/addons/gradebook/course.php.
 *
 * @author  ThimPress
 * @package LearnPress/Gradebook/Templates
 * @version 3.0.4
 */


/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
require_once LP_ADDON_ASSIGNMENTS_INC . 'admin/class-student-list-table.php';
if ( ! isset( $_REQUEST['cid'] ) ) {
    return;
} 

$assignment     = learn_press_get_item_courses( $_REQUEST['cid'] );
$course         = learn_press_get_course( $assignment[0]->ID );
$list_table     = new LP_Student_Assignment_List_Table( $_REQUEST['cid'] );
$list_item      = $list_table->items;

// $course_data = $user->get_course_data( $lp_course->get_id() );



// $course = learn_press_get_course($_REQUEST['cid']);
// $course = new LP_Gradebook_Course($course->get_id());

?>
    <table>
        <tr>
            <th><?php _e( 'Course', 'learnpress-gradebook' ); ?></th>
            <td>
                <a href="<?php echo $course->get_permalink(); ?>"><?php echo $course->get_title(); ?></a>
            </td>
        </tr>
        <tr>
            <th><?php _e( 'Instructor', 'learnpress-gradebook' ); ?></th>
            <td><?php echo $course->get_instructor_name(); ?></td>
        </tr>
    </table>

<?php if ( $items = $course->get_items() ) { ?>

    <div class="gradebook-top-nav 78">
        <form method="post">
            <input type="text" name="search" placeholder="<?php _e( 'Student name, email', 'learnpress-gradebook' ); ?>"
                   value="<?php echo esc_html( LP_Request::get( 'search' ) ); ?>">
            <button><?php _e( 'Filter', 'learnpress-gradebook' ); ?></button>
        </form>
    </div>

    <table class="gradebook-list">
        <thead>
        <tr>
            <th class="course-item-user fixed-column">
				<?php _e( 'Student', 'learnpress-gradebook' ); ?>
            </th>
            <th class="user-grade fixed-column">
				<?php _e( 'Status', 'learnpress-gradebook' ); ?>
            </th>
            <th class="user-mark fixed-column">
				<?php _e( 'Mark', 'learnpress-gradebook' ); ?>
            </th>
            <th class="user-result fixed-column">
				<?php _e( 'Result', 'learnpress-gradebook' ); ?>
            </th>
            <th class="user-action fixed-column">
				<?php _e( 'Action', 'learnpress-gradebook' ); ?>
            </th>
        </tr>
        </thead>
        <tbody>
		<?php 
		if ( $list_item ) {
			foreach ( $list_item as $item ) {
                $user = $item['user'];
                
                $lp_assignment = $item['assignment'];
                $assignment_id = $lp_assignment->get_id();
            
                $course_data = $user->get_course_data( $course->get_id() );
                if ( false !== $assignment_item = $course_data->get_item( $assignment_id ) ) {
                    $user_item_id = $assignment_item->get_user_item_id();
                } else {
                    $user_item_id = 0;
                }
                $mark         = learn_press_get_user_item_meta( $user_item_id, '_lp_assignment_mark', true );
                $instructor   = learn_press_get_user_item_meta( $user_item_id, '_lp_assignment_evaluate_author', true );
                $evaluated    = $user->has_item_status( array( 'evaluated' ), $assignment_id, $course->get_id() );
				// $user        = learn_press_get_user( $user_id );
                // $course_data = $user->get_course_data( $course->get_id() );
                if(isset($_REQUEST['search']) && $_REQUEST['search'] != ''){
                    $return = true;
                    if(stripos($user->get_display_name(), $_REQUEST['search'] ) !== FALSE ){
                        $return = false;    
                    }

                    if(stripos($user->get_email(), $_REQUEST['search']) !== FALSE   ){
                        $return = false;
                    }

                    if($return){
                        break;
                    }
                }
                
                
				?>
                <tr>
                    <th class="course-item-user fixed-column">
						<?php echo $user->get_display_name(); ?>
                        <!-- (<a href="mailto:<?php // echo $user->get_email(); ?>"><?php //echo $user->get_email(); ?></a>) -->
                    </th>
                    <th class="user-grade fixed-column">
                        <?php 
                            $status = $evaluated ? __( 'Evaluated', 'learnpress-assignments' ) : __( 'Not evaluate', 'learnpress-assignments' );
                            echo $status;
                        ?>
                    </th>
                    <th class="user-grade fixed-column">
						<?php echo $mark ? $mark : '-'; ?>
                    </th>
					<th class="user-grade fixed-column" >
                        <?php 
                            if ( ! $evaluated ) {
                                echo '-';
                            } else {
                                $pass   = $mark >= $lp_assignment->get_data( 'passing_grade' );
                                $result = $pass ? __( 'Passed', 'learnpress-assignments' ) : __( 'Failed', 'learnpress-assignments' ); ?>
                                <a href="<?php echo esc_url( add_query_arg( array( 'filter_result' => $pass ? 'passed' : 'failed' ) ) ); ?>"><?php echo $result; ?></a>
                                <?php
                            }
                        ?>
                    </th>
                    <th>
                        <div class="assignment-students-actions" data-user_id="<?php echo esc_attr( $user->get_id() ); ?>"
                            data-assignment_id="<?php echo esc_attr( $lp_assignment->get_id() ); ?>"
                            data-recommend="<?php if(!$user_item_id){esc_attr__( 'Something wrong! Should delete this!', 'learnpress-assignments' );}?>"
                            data-user-item-id="<?php echo esc_attr( $user_item_id ); ?>">

                            <?php
                            
                            // $editurl = wp_nonce_url( add_query_arg( array( 'user_id' => $user->get_id() ), 'admin.php?page=assignment-evaluate' ), 'learn-press-assignment-' . $lp_assignment->get_id(), 'assignment-nonce' ) . '&assignment_id=' . $lp_assignment->get_id();
                            // $editurl = get_admin_url() . $editurl;
                            // https://digitalcustdev.ru/dev/wp-admin/admin.php?page=assignment-evaluate&amp;user_id=120&amp;assignment-nonce=507fa3d823&amp;assignment_id=10838
                            
                            $editurl = get_home_url() . '/assignment-evaluate/?assignment_id='.$lp_assignment->get_id().'&user_id=' . $user->get_id();
                            printf( '<a href="%s" class="o view" title="%s"><i class="dashicons dashicons-welcome-write-blog"></i></a>', $editurl, esc_attr__( 'Evaluate', 'learnpress-assignments' ) );
                            // printf( '<a href="%s" class="delete" title="%s"><i class="dashicons dashicons-trash"></i></a>', '#', esc_attr__( 'Delete submission', 'learnpress-assignments' ) );
                            // printf( '<a href="%s" class="reset" title="%s"><i class="dashicons dashicons-update"></i></a>', '#', esc_attr__( 'Reset result', 'learnpress-assignments' ) );
                            if ( $evaluated ) {
                                // printf( '<a href="%s" class="reset" title="%s"><i class="dashicons dashicons-update"></i></a>', '#', esc_attr__( 'Reset result', 'learnpress-assignments' ) );
                                // printf( '<a href="%s" class="send-mail" title="%s"><i class="dashicons dashicons-email-alt"></i></a>', '#', esc_attr__( 'Send evaluated mail', 'learnpress-assignments' ) );
                            }
                            ?>
                        </div>
                    </th>
                </tr>
				<?php
			}
		}
		?>
        </tbody>
    </table>
    <!-- <ul class="list-table-nav">
        <li class="nav-text">
			<?php
			/**
			 * @var $query_user LP_Query_List_Table
			 */
			?>
			<?php // echo $query_user->get_offset_text(); ?>
        </li>
        <li class="nav-pages">
			<?php // $query_user->get_nav_numbers( true ); ?>
        </li>
    </ul> -->
    <!-- <p>
        <a href="<?php // echo learn_press_gradebook_export_url( $course->get_id() ); ?>"><?php// _e( 'Export', 'learnpress-gradebook' ); ?></a>
    </p> -->

<?php } else {
	learn_press_display_message( __( 'No data.', 'learnpress-gradebook' ), 'error' );
}
