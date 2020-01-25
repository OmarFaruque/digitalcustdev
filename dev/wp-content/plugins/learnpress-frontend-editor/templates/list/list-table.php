<?php
/**
 * Template for displaying list of a post type
 *
 * @author  ThimPress
 * @package LearnPress/Frontend-Editor/Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or die;
/**
 * @var LP_Addon_Frontend_Editor $frontend_editor
 */

global $frontend_editor;
$post_manage = $frontend_editor->post_manage;
$query       = $post_manage->get_posts();

$post_list_table = LP_Frontend_Editor_Post_List_Table::instance();

$post_list_table->display();