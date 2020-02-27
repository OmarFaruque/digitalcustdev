<?php
/**
 * Template for displaying user profile cover image.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/profile/profile-cover.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$profile = LP_Profile::instance();

$user = $profile->get_user();
?>

<div class="user-info">

    <div class="author-avatar">
		<?php
		$profile_picture_src = $user->get_upload_profile_src( 270 );
		if ( ! empty( $profile_picture_src ) ) {
			?>
            <img alt="Admin bar avatar" src="<?php echo $profile_picture_src; ?>" class="avatar avatar-270 photo" height="270" width="270">
			<?php
		} else {
			?>
            <img alt="Admin bar avatar" src="<?php echo get_stylesheet_directory_uri() . '/assets/images/user-placeholder.png'; ?>" class="avatar avatar-270 photo" height="270" width="270">
			<?php
		}
		?>
    </div>

    <div class="user-information">

		<?php
		$lp_info = get_the_author_meta( 'lp_info', $user->get_id() );
		?>
        <ul class="thim-author-social">
			<?php if ( isset( $lp_info['facebook'] ) && $lp_info['facebook'] ) : ?>
                <li>
                    <a href="<?php echo esc_url( $lp_info['facebook'] ); ?>" class="facebook"><i class="fa fa-facebook"></i></a>
                </li>
			<?php endif; ?>

			<?php if ( isset( $lp_info['twitter'] ) && $lp_info['twitter'] ) : ?>
                <li>
                    <a href="<?php echo esc_url( $lp_info['twitter'] ); ?>" class="twitter"><i class="fa fa-twitter"></i></a>
                </li>
			<?php endif; ?>

			<?php if ( isset( $lp_info['instagram'] ) && $lp_info['instagram'] ) : ?>
                <li>
                    <a href="<?php echo esc_url( $lp_info['instagram'] ); ?>" class="instagram"><i
                                class="fa fa-instagram"></i></a>
                </li>
			<?php endif; ?>

			<?php if ( isset( $lp_info['google'] ) && $lp_info['google'] ) : ?>
                <li>
                    <a href="<?php echo esc_url( $lp_info['google'] ); ?>" class="google-plus"><i class="fa fa-google-plus"></i></a>
                </li>
			<?php endif; ?>

			<?php if ( isset( $lp_info['linkedin'] ) && $lp_info['linkedin'] ) : ?>
                <li>
                    <a href="<?php echo esc_url( $lp_info['linkedin'] ); ?>" class="linkedin"><i class="fa fa-linkedin"></i></a>
                </li>
			<?php endif; ?>

			<?php if ( isset( $lp_info['youtube'] ) && $lp_info['youtube'] ) : ?>
                <li>
                    <a href="<?php echo esc_url( $lp_info['youtube'] ); ?>" class="youtube"><i class="fa fa-youtube"></i></a>
                </li>
			<?php endif; ?>
        </ul>
        <h3 class="author-name"><?php echo learn_press_get_profile_display_name( $user ); ?></h3>
        <!-- <p class="author-role"><?php //echo $user->get_role(); ?></p> -->

        <p><?php echo get_user_meta( $user->get_id(), 'description', true ); ?></p>
    </div>
</div>


