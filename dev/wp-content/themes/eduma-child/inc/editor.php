<?php

add_filter( 'wp_editor_settings', 'dcd_editor_site_settings', 10, 2 );
function dcd_editor_site_settings( $settings, $editor_id ) {
	if ( e_is_frontend_editor() ) {
		$settings['tinymce'] = array(
			'toolbar1' => 'alignleft,aligncenter,alignright,alignjustify,bullist,numlist,outdent, indent, blockquote, strikethrough, cleanup, link, unlink',
			'toolbar2' => 'undo, redo, formatselect, bold, italic, underline, forecolor, backcolor',
			'toolbar3' => '',
			'toolbar4' => '',
		);

		$settings['wpautop']       = true;
		$settings['media_buttons'] = false;
		$settings['quicktags']     = false;
	}

	return $settings;
}