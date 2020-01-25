<?php
/**
 * Remove SEO from the page used for Frontend Editor.
 *
 * @since 3.0.4
 *
 * @param string $template
 *
 * @return string
 */
function e_remove_wpseo( $template ) {
	$page_id = get_option( 'learn_press_frontend_editor_page_id' );

	if ( $page_id && is_page( $page_id ) ) {
		global $wpseo_front;

		if ( class_exists( 'WPSEO_Frontend' ) ) {
			if ( defined( $wpseo_front ) ) {
				remove_action( 'wp_head', array( $wpseo_front, 'head' ), 1 );
			} else {
				$wp_thing = WPSEO_Frontend::get_instance();
				remove_action( 'wp_head', array( $wp_thing, 'head' ), 1 );
			}
		}
	}

	return $template;
}

add_action( 'template_include', 'e_remove_wpseo' );