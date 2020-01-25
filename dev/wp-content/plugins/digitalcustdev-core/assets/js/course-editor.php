<?php

$packages = array(
	'learnpress-frontend-editor/helpers',
	'learnpress-frontend-editor/base',
	'learnpress-frontend-editor/stores/section',
	'learnpress-frontend-editor/stores/course',
	'learnpress-frontend-editor/components/modal-items',
	'learnpress-frontend-editor/components/course-curriculum',
	'learnpress-frontend-editor/components/course-section',
	'learnpress-frontend-editor/components/course-item',
	'learnpress-frontend-editor/components/item-settings',
	'learnpress-frontend-editor/components/form-fields',
	'learnpress-frontend-editor/components/quiz-editor',
	'learnpress-frontend-editor/components/question-editor',
	'learnpress-frontend-editor/integration',
	'learnpress-frontend-editor/course-editor',
	'learnpress-frontend-editor/post'
);

$expires_offset = 31536000; // 1 year

header( 'Content-Type: application/javascript; charset=UTF-8' );
header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $expires_offset ) . ' GMT' );
header( "Cache-Control: public, max-age=$expires_offset" );

echo ";jQuery(function($){\n";
foreach ( $packages as $package ) {
	readfile( "{$package}.js" );
	echo "\n\n";
}
echo "});";