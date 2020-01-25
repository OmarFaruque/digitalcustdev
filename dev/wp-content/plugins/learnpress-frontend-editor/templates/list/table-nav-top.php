<?php
/**
 * Display action in the top of list posts.
 *
 * @author  ThimPress
 * @package Frontend_Editor/Templates
 * @version 3.0.0
 */
defined( 'ABSPATH' ) or die;

/**
 * @var LP_Addon_Frontend_Editor             $frontend_editor
 * @var WP_Query                             $wp_query
 * @var WP_Query                             $e_wp_query
 * @var LP_Addon_Frontend_Editor_Post_Manage $post_manage
 */
global $frontend_editor, $wp_query, $e_wp_query;

$post_manage = $frontend_editor->post_manage;
$sort        = $wp_query->get( 'sort' );
$sortby      = $wp_query->get( 'sortby' );

?>
<div id="e-table-nav-top" class="e-table-nav">
    <div class="e-search-courses">
        <input type="text" name="search"
               placeholder="<?php esc_attr_e( 'Search course', 'learnpress-frontend-editor' ); ?>"
               value="<?php echo $wp_query->get( 'search' ); ?>">
        <button type="submit"
                class="search-button"><?php esc_html_e( 'Search', 'learnpress-frontend-editor' ); ?></button>
    </div>

    <div class="e-table-actions">
        <div class="e-table-actions-left">
            <ul class="e-post-filter-status">
				<?php
				$filter_html = '<li>' . join( '|</li><li>', $post_manage->post_counts() ) . '</li>';
				echo $filter_html;
				?>
            </ul>
        </div>

        <div class="e-sort-courses">
            <select name="sortby" onchange="this.form.submit();">
                <option value=""><?php esc_html_e( 'None', 'learnpress-frontend-editor' ); ?></option>
                <option value="title" <?php selected( $sortby === 'title' ); ?>><?php esc_html_e( 'Course Name', 'learnpress-frontend-editor' ); ?></option>
                <option value="date" <?php selected( $sortby === 'date' ); ?>><?php esc_html_e( 'Course Date', 'learnpress-frontend-editor' ); ?></option>
            </select>
            <a href="<?php echo $sort === 'asc' ? remove_query_arg( 'sort' ) : add_query_arg( 'sort', 'asc' ); ?>"
               class="e-button-icon e-button sort up<?php echo $sortby === 'asc' ? ' active' : ''; ?>"></a>
            <a href="<?php echo $sort === 'desc' ? remove_query_arg( 'sort' ) : add_query_arg( 'sort', 'desc' ); ?>"
               class="e-button-icon e-button sort down<?php echo $sortby === 'desc' ? ' active' : ''; ?>"></a>
        </div>
    </div>
    <input type="hidden" name="sort" value="<?php echo $wp_query->get( 'sort' ); ?>">
</div>