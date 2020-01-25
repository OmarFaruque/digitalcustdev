<ul class="nav navbar-nav mobile">
	<?php
	$profile    = LP_Profile::instance();
	$profile_id = $profile->get_user()->get_id();
	$tabs       = $profile->get_tabs()->tabs();

	if ( ! user_can( $profile_id, 'edit_lp_courses' ) ) {
		unset( $tabs['instructor'] );
	}
	?>
    <li>
        <a href="<?php echo home_url(); ?>"><i class="fa fa-home fa-2x"></i>
            <span class="text">
                <?php
                $thim_logo = get_theme_mod( 'thim_logo', false );
                $style     = '';
                if ( ! empty( $thim_logo ) ) {
	                if ( is_numeric( $thim_logo ) ) {
		                $logo_attachment = wp_get_attachment_image_src( $thim_logo, 'full' );
		                if ( $logo_attachment ) {
			                $src   = $logo_attachment[0];
			                $style = 'width="' . $logo_attachment[1] . '" height="' . $logo_attachment[2] . '"';
		                } else {
			                $src   = get_template_directory_uri() . '/images/logo.png';
			                $style = 'width="153" height="40"';
		                }
	                } else {
		                $src = $thim_logo;
	                }
                } else {
	                $src   = get_template_directory_uri() . '/images/logo-sticky.png';
	                $style = 'width="153" height="40"';
                }

                $src = thim_ssl_secure_url( $src );
                echo '<img src="' . $src . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" ' . $style . '>';
                ?>
                </span>
        </a>
    </li>
	<?php
	foreach ( $tabs as $tab_key => $tab_data ) {

		if ( $tab_data->is_hidden() || ! $tab_data->user_can_view() ) {
			continue;
		}

		$slug        = $profile->get_slug( $tab_data, $tab_key );
		$link        = $profile->get_tab_link( $tab_key, true );
		$tab_classes = array( esc_attr( $tab_key ) );

		if ( $profile->is_current_tab( $tab_key ) ) {
			$tab_classes[] = 'active';
		}
		?>

        <li class="<?php echo join( ' ', $tab_classes ) ?>">
            <!--tabs omar-->
            <a href="<?php echo esc_url( $link ); ?>" data-slug="<?php echo esc_attr( $link ); ?>">
				<?php echo apply_filters( 'learn_press_profile_' . $tab_key . '_tab_title', esc_html( $tab_data['title'] ), $tab_key ); ?>
            </a>

        </li>
		<?php
	}

	if ( is_active_sidebar( 'menu_right' ) ) {
		dynamic_sidebar( 'menu_right' );
	}
	?>
</ul>
<?php
