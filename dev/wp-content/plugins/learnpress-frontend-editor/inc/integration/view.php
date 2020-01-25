<?php
if ( e_is_active_fill_in_blank() ) {
	$view = dirname( LP_ADDON_FILL_IN_BLANK_FILE ) . '/inc/admin/views/answer-editor.php';

	if ( file_exists( $view ) ) {
		?>
        <script type="text/x-template" id="tmpl-fib-question-answers">
            <div>
                <div class="e-section-heading"><?php esc_html_e( 'Question Passage', 'learnpress-frontend-editor' ); ?></div>
                <div id="admin-editor-lp_quiz">
                    <div class="admin-fib-question-editor">
						<?php include $view; ?>
                    </div>
                </div>
            </div>
        </script>
		<?php
	}
}