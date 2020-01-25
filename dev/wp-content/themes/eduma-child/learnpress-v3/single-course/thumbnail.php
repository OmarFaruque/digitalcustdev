<?php
/**
 * Template for displaying thumbnail of single course.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/single-course/thumbnail.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

global $post;
$course      = learn_press_get_course();
$video_embed = $course->get_video_embed();
$media_intro = get_post_meta( $post->ID, 'thim_course_media_intro', true );
$video_file  = get_field( 'field_5d52623d7778a', $post->ID ); // Video File
?>

<div class="course-thumbnail">
	<?php
	if ( ! empty( $video_file ) ) {
		?>
        <div class="media-intro">
            <video width="100%" controls>
                <source src="<?php echo $video_file; ?>" type="video/mp4">
            </video>
        </div>
		<?php
	} elseif ( ! empty( $media_intro ) ) {
		?>
        <div class="media-intro">
			<?php echo dcd_convertYoutube( $media_intro ); ?>
        </div>
		<?php
	} elseif ( $video_embed ) {
		?>
        <div class="media-intro"><?php echo $video_embed; ?></div>
		<?php
	} elseif ( has_post_thumbnail() ) {
		$image_title   = get_the_title( get_post_thumbnail_id() ) ? esc_attr( get_the_title( get_post_thumbnail_id() ) ) : '';
		$image_caption = get_post( get_post_thumbnail_id() ) ? esc_attr( get_post( get_post_thumbnail_id() )->post_excerpt ) : '""';
		$image_link    = wp_get_attachment_url( get_post_thumbnail_id() );
		$image         = get_the_post_thumbnail( $post->ID, 'featured-cover', array(
			'title' => $image_title,
			'alt'   => $image_title
		) );

		echo apply_filters( 'learn_press_single_course_image_html', sprintf( '%s', $image ), $post->ID );
	}
	?>
</div>
