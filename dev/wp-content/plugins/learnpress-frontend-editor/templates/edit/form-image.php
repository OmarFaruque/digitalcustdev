<?php
/**
 * Template for displaying thumbnail of the post if it set
 *
 * @author  ThimPress
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or die;

$post = frontend_editor()->post_manage->get_post();
$attachment = has_post_thumbnail( $post->ID ) ? get_the_post_thumbnail( $post->ID ) : '';
?>
<div class="e-post-attachment<?php echo $attachment ? ' has-attachment' : ''; ?>">
    <div class="post-attachment">
		<?php if ( $attachment ) {
			echo $attachment;
		} ?>

    </div>
    <input type="hidden" id="_thumbnail_id" name="_thumbnail_id"
           value="<?php echo get_post_thumbnail_id( $post->ID ); ?>">
    <button type="button" class="set-attachment"></button>
    <p class="remove-attachment"><a href=""></a></p>
</div>