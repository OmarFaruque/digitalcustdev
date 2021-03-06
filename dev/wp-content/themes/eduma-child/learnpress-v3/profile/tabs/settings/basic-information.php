<?php
/**
 * Template for displaying editing basic information form of user in profile page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/settings/tabs/basic-information.php.
 *
 * @author  ThimPress
 * @package  Learnpress/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$profile = LP_Profile::instance();

if ( ! isset( $section ) ) {
    $section = 'basic-information';
}

$user = $profile->get_user();
?>
<h3 class="profile-heading"><?php _e('General Settings', 'webinar'); ?></h3>
<form method="post" id="your-profile" name="profile-basic-information"
      enctype="multipart/form-data" class="learn-press-form">
    <div class="learn-press-subtab-content om4">
        <?php
        /**
         * @since 3.0.0
         */
        do_action( 'learn-press/before-profile-basic-information-fields', $profile );
        $user_info = get_the_author_meta( 'lp_info', $user->get_id() );
        ?>
        <ul class="lp-form-field-wrap">
            <?php
            /**
             * @since 3.0.0
             */
            do_action( 'learn-press/begin-profile-basic-information-fields', $profile );
            // @deprecated
            do_action( 'learn_press_before_' . $section . '_edit_fields' );
            ?>

            <li class="lp-form-field">
                <label for="lp-form-field-label"><?php _e( 'Biographical Info', 'eduma' ); ?></label>
                <div class="lp-form-field-input">
                <textarea name="description" id="description" rows="5"
                          cols="30"><?php esc_html_e( $user->get_data( 'description' ) ); ?></textarea>
                    <p class="description"><?php _e( 'Share a little biographical information to fill out your profile. This may be shown publicly.', 'eduma' ); ?></p>
                </div>
            </li>
            <li class="form-field mt-1">
                <label for="lp-form-field-label"><?php _e( 'First Name', 'eduma' ); ?></label>
                <div class="lp-form-field-input">
                    <input type="text" name="first_name" id="first_name"
                           value="<?php echo esc_attr( $user->get_data( 'first_name' ) ); ?>"
                           class="regular-text">
                </div>
            </li>
            <li class="form-field mt-1">
                <label for="lp-form-field-label"><?php _e( 'Last Name', 'eduma' ); ?></label>
                <div class="lp-form-field-input">
                    <input type="text" name="last_name" id="last_name"
                           value="<?php echo esc_attr( $user->get_data( 'last_name' ) ); ?>"
                           class="regular-text">
                </div>
            </li>
            <li class="form-field mt-1">
                <label for="lp-form-field-label"><?php _e( 'Nickname', 'eduma' ); ?></label>
                <div class="lp-form-field-input">
                    <input type="text" name="nickname" id="nickname"
                           value="<?php echo esc_attr( $user->get_data( 'nickname' ) ) ?>"
                           class="regular-text"/>
                </div>
            </li>
            <li class="form-field mt-1">
                <label for="lp-form-field-label"><?php _e( 'Display name publicly as', 'eduma' ); ?></label>
                <div class="lp-form-field-input">
                    <?php learn_press_profile_list_display_names(); ?>
                </div>
            </li>
            <li class="form-field mt-1">
                <label for="lp-form-field-label"><?php _e( 'Major', 'eduma' ); ?></label>
                <div class="lp-form-field-input">
                <input type="text" name="major" id="major"
                           value="<?php echo esc_attr( $user_info['major'] ); ?>"
                           class="regular-text"/>
                </div>
            </li>
            <li class="form-field mt-1">
                <label for="lp-form-field-label"><?php _e( 'Facebook', 'eduma' ); ?></label>
                <div class="lp-form-field-input">
                <input type="text" name="facebook" id="facebook"
                           value="<?php echo esc_attr( $user_info['facebook'] ); ?>"
                           class="regular-text"/>
                </div>
            </li>
            <li class="form-field mt-1">
                <label for="lp-form-field-label"><?php _e( 'Twitter', 'eduma' ); ?></label>
                <div class="lp-form-field-input">
                <input type="text" name="twitter" id="twitter"
                           value="<?php echo esc_attr( $user_info['twitter'] ); ?>"
                           class="regular-text"/>
                </div>
            </li>
            <li class="form-field mt-1">
                <label for="lp-form-field-label"><?php _e( 'Instagram', 'eduma' ); ?></label>
                <div class="lp-form-field-input">
                <input type="text" name="instagram" id="instagram"
                           value="<?php echo esc_attr( $user_info['instagram'] ); ?>"
                           class="regular-text"/>
                </div>
            </li>
            <li class="form-field mt-1">
                <label for="lp-form-field-label"><?php _e( 'Google Plus', 'eduma' ); ?></label>
                <div class="lp-form-field-input">
                <input type="text" name="google" id="google"
                           value="<?php echo esc_attr( $user_info['google'] ); ?>"
                           class="regular-text"/>
                </div>
            </li>
            <li class="form-field mt-1">
                <label for="lp-form-field-label"><?php _e( 'Linkedin', 'eduma' ); ?></label>
                <div class="lp-form-field-input">
                <input type="text" name="linkedin" id="linkedin"
                           value="<?php echo esc_attr( $user_info['linkedin'] ); ?>"
                           class="regular-text"/>
                </div>
            </li>
            <li class="form-field mt-1">
                <label for="lp-form-field-label"><?php _e( 'Youtube', 'eduma' ); ?></label>
                <div class="lp-form-field-input">
                <input type="text" name="youtube" id="youtube"
                           value="<?php echo esc_attr( $user_info['youtube'] ); ?>"
                           class="regular-text"/>
                </div>
            </li>

            <?php
            // @deprecated
            do_action( 'learn_press_after_' . $section . '_edit_fields' );

            /**
             * @since 3.0.0
             */
            do_action( 'learn-press/end-profile-basic-information-fields', $profile );

            ?>
        </ul>

        <?php
        /**
         * @since 3.0.0
         */
        do_action( 'learn-press/after-profile-basic-information-fields', $profile );
        ?>

        <p>
            <input type="hidden" name="save-profile-basic-information"
                   value="<?php echo wp_create_nonce( 'learn-press-save-profile-basic-information' ); ?>"/>
        </p>

        <button type="submit" name="submit"><?php _e( 'Save changes', 'eduma' ); ?></button>
    </div>

</form>

<style>
    .message.message-success{display:none;}
</style>