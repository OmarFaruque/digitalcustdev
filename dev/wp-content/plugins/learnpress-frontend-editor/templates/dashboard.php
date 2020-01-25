<?php
/**
 * @var LP_Addon_Frontend_Editor $frontend_editor
 */

/**
 * Get page header
 */
LP_Addon_Frontend_Editor::get_header();

// Content
LP_Addon_Frontend_Editor::instance()->get_template('dashboard-no-header');

/**
 * Get page footer
 */
LP_Addon_Frontend_Editor::get_footer();
