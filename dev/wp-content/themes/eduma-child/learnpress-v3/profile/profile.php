<?php
/**
 * Template for displaying main user profile page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/profile/profile.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$profile = LP_Global::profile();
global $wp_query;
$var = $wp_query->query_vars;
$user     = LP_Global::user();
$current_user = wp_get_current_user();
$profile2 = LP_Profile::instance();

$allowArray = array('administrator', 'lp_teacher');
$haveAccessProfile = !empty(array_intersect($allowArray, $current_user->roles));

// echo 'have access : ' . $haveAccessProfile . '<br/>';
// echo 'profile user id : ' . $profile2->get_user_data( 'id' ) . '<br/>';
// echo 'get current user id : ' . get_current_user_id() . '<br/>';

if ( $profile->is_public() ) {
	?>

    <div class="learn-press-user-profile profile-container omr" id="learn-press-user-profile">  
        <?php if($profile2->get_user_data( 'id' ) != get_current_user_id()): ?>
            <div class="user-tab">
                <?php do_action( 'learn-press/before-user-profile', $profile ); ?>
            </div>
        <?php endif; ?>
        <div class="profile-tabs unauthorized <?php echo ($profile2->get_user_data( 'id' ) != get_current_user_id()) ? 'not-view' : 'view';  ?>">
            <?php
            /**
             * @since 3.0.0
             **/
            do_action( 'learn-press/user-profile', $profile );
            ?>
        </div>

        <?php
        /**
         * @since 3.0.0
         */
        do_action( 'learn-press/after-user-profile', $profile );
        
        ?>
    </div>

<?php } else {
	_e( 'This user does not public their profile.', 'eduma' );
}