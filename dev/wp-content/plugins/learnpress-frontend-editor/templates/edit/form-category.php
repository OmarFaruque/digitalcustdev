<?php
/**
 * Template for displaying course categories
 *
 * @author  ThimPress
 * @package LearnPress
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or die();
?>

<e-course-category
        v-if="hasCategory()"
        :categories="$dataStore().categories">
</e-course-category>
<p v-else>
    <?php esc_html_e('No categories!', 'learnpress-frontend-editor');?>
</p>