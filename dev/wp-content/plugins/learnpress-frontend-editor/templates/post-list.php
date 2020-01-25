<?php
/**
 * @var LP_Addon_Frontend_Editor $frontend_editor
 */

global $frontend_editor;
$post_manage = $frontend_editor->post_manage;
$query       = $post_manage->get_posts();

$post_list_table = LP_Frontend_Editor_Post_List_Table::instance();

?>
<h3 class="e-page-title">
	<?php echo $post_manage->get_name(); ?>

    <a class="e-button-icon e-button add e-new-post"
       data-nonce="<?php echo wp_create_nonce( 'e-new-post' ); ?>"
       data-type="<?php echo $post_manage->get_post_type(); ?>"
       href="<?php echo $post_manage->get_new_post_link(); ?>"><i class="dashicons dashicons-plus-alt"></i>
    </a>
</h3>
<form id="e-list-posts">

	<?php
    $template = $frontend_editor->locate_template( 'list/list-table-' . $post_manage->get_post_type() );
	if ( file_exists( $template ) ) {
		include $template;
	} else {
		$frontend_editor->get_template( 'list/list-table' );
	}
	?>

</form>
