<?php
/**
 * Template for displaying students manager of assignment on FrontEnd Editor.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/assignments/frontend-editor/manager-link.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Assignments/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();?>
<span class="duplicate-link" @click="_clone" @mousedown="_startAnim" @mouseup="_stopAnim" title="<?php esc_attr_e( 'Clone this item', 'learnpress-frontend-editor' ); ?>"><i class="fa fa-copy"></i></span>