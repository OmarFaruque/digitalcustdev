<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package thim
 */
$page_id = get_option( 'learn_press_frontend_editor_page_id' );
?>

<?php while (have_posts()) : the_post(); ?>

    <?php get_template_part('content', 'page'); ?>

    <?php
    // If comments are open or we have at least one comment, load up the comment template
    if (!is_page( $page_id ) ) {
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
    }
    ?>

<?php endwhile; // end of the loop.  ?>