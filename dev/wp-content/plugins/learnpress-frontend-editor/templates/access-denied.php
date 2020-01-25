<?php
/**
 * Template for showing message if user doesn't have permission.
 *
 * @author  ThimPress
 * @package Frontend_Editor/Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) or die;

wp_die( __( 'Ooops! You don\'t have permission to access this page', 'learnpress-frontend-editor' ) );