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
            
           
                $profile = LP_Global::profile();
                if ( ! e_is_frontend_editor() ) {
                    //Toolbar
                    if ( get_theme_mod( 'thim_toolbar_show', true ) ) {
                        get_template_part( 'inc/header/toolbar' );
                    }

                    //Header style
                    if ( ! e_is_frontend_editor() && $profile->get_user()->get_id() == get_current_user_id()) 
                    {
                        get_template_part( 'inc/header/' . 'header-vprofile' );
                    }
                    
                }

                
                // echo 'profile id: ' . $profile->get_user()->get_id() . '<br/>';
                // echo 'current user id: ' . get_current_user_id() . '<br/>';

                if ( $profile->get_user()->is_guest() ) {
                    return;
                }
                
                if(is_user_logged_in()){
                    if ( ! e_is_frontend_editor() && $profile->get_user()->get_id() == get_current_user_id()) {
                        learn_press_get_template( 'profile/sidebar-navigation.php', array( 'user' => $profile->get_user() ) );
                    }
                }
			?>
        </header>
                
        <div id="main-content">

        <?php 

$user     = LP_Global::user();
// echo 'user id: ' . $user->get_id() . '<br/>';
// echo 'current iser id: ' . get_current_user_id() . '<br/>';

?>