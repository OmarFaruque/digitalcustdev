<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package thim
 */
?><!DOCTYPE html>
<html itemscope itemtype="http://schema.org/WebPage" <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php esc_url( bloginfo( 'pingback_url' ) ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'digitalcustdev-profile-page' ); ?> id="thim-body">

<?php do_action( 'thim_before_body' ); ?>

<!-- Mobile Menu-->
<div class="mobile-menu-wrapper">
    <div class="mobile-menu-inner">
        <div class="icon-wrapper">
            <div class="menu-mobile-effect navbar-toggle close-icon" data-effect="mobile-effect">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </div>
        </div>
        <nav class="mobile-menu-container mobile-effect">
			<?php get_template_part( 'inc/header/menu-profilemobile' ); ?>
        </nav>
    </div>
</div>

<div id="wrapper-container" class="wrapper-container">
    <div class="content-pusher">
        <header id="masthead" class=" omm site-header affix-top header-profile header_overlay header_v4">
			<?php
			if ( ! e_is_frontend_editor() ) {
				//Toolbar
				if ( get_theme_mod( 'thim_toolbar_show', true ) ) {
					get_template_part( 'inc/header/toolbar' );
				}

				//Header style
				get_template_part( 'inc/header/' . 'header-vprofile' );
			}

			$profile = LP_Global::profile();

			if ( $profile->get_user()->is_guest() ) {
				return;
			}

			if ( ! e_is_frontend_editor() ) {
				learn_press_get_template( 'profile/sidebar-navigation.php', array( 'user' => $profile->get_user() ) );
			}
			?>
        </header>

        <div id="main-content">