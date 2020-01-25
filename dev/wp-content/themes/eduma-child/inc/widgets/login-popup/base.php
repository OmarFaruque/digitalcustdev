<div class="thim-link-login thim-login-popup">
	<?php
	$layout               = isset( $instance['layout'] ) ? $instance['layout'] : 'base';
	$profile_text         = $logout_text = $login_text = $register_text = '';
	$registration_enabled = get_option( 'users_can_register' );
	global $current_user;

	if ( 'base' == $layout ) {
		$profile_text  = isset( $instance['text_profile'] ) ? $instance['text_profile'] : '';
		$logout_text   = isset( $instance['text_logout'] ) ? $instance['text_logout'] : '';
		$login_text    = isset( $instance['text_login'] ) ? $instance['text_login'] : '';
		$register_text = isset( $instance['text_register'] ) ? $instance['text_register'] : '';
	} else {
		$profile_text = '<i class="ion-android-person"></i>';
		$logout_text  = '<i class="ion-ios-redo"></i>';
		$login_text   = '<i class="ion-android-person"></i>';
	}

	//	$user = wp_get_current_user();
	if ( ( $page_id = get_option( 'learn_press_frontend_editor_page_id' ) ) && $page_id ) {
		if ( get_post( $page_id ) ) {
			$root_slug = get_post_field( 'post_name', $page_id );
		}
	}

	$url = get_home_url() . '/' . $root_slug;
	if ( is_user_logged_in() ) {
		if ( current_user_can( 'lp_teacher' ) || current_user_can( 'bbp_keymaster' ) ) {
//			echo '<a class="logout" href="' . esc_url( $url ) . '">' . '<i class="icon ion-wrench"></i> Редактор курсов' . '</a>';
		}
	}

	// Login popup link output
if ( is_user_logged_in() ) {
		if ( thim_plugin_active( 'learnpress/learnpress.php' ) && $profile_text ) {
			if ( thim_is_new_learnpress( '1.0' ) ) {
				$user_main_role = get_user_meta( get_current_user_id(), '_user_main_role', true );
				?>
                <div class="thim-switch-roles-sub-nav">
                	<?php if ( is_page( 'profile' ) ) {
							?>
                    <a style="cursor: pointer;" class="profile profile-switch-roles" data-security="<?php echo wp_create_nonce( 'switching-role' ); ?>"> <?php echo in_array( 'lp_teacher', $current_user->roles ) ? 'Student' : 'Instructor'; ?> </a>

                    <?php } 
                    else
                    { ?>
                    	<a class="profile" href="<?php echo esc_url( learn_press_user_profile_link() ); ?>"> <?php echo $profile_text; ?> </a>

                   <?php } ?>
					
                </div>
				<?php
			} else {
				echo '<a class="profile" href="' . esc_url( apply_filters( 'learn_press_instructor_profile_link', '#', get_current_user_id(), '' ) ) . '">' . ( $profile_text ) . '</a>';
			}
		}


		if ( $login_text ) {
			?>
            <a class="logout" href="<?php echo esc_url( wp_logout_url( apply_filters( 'thim_default_logout_redirect', ( ! empty( $_SERVER['HTTPS'] ) ? "https" : "http" ) . '://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) ); ?>"><?php echo( $logout_text ); ?></a>
			<?php
		}

	} else {
		if ( $registration_enabled && 'base' == $layout ) {
			if ( $register_text ) {
				echo '<a class="register js-show-popup" href="' . esc_url( thim_get_register_url() ) . '">' . ( $register_text ) . '</a>';
			}
		}

		if ( $login_text ) {
			echo '<a class="login js-show-popup" href="' . esc_url( thim_get_login_page_url() ) . '">' . ( $login_text ) . '</a>';
		}
	}
	// End login popup link output
	?>
</div>
