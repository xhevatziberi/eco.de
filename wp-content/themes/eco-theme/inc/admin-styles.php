<?php
/**
 * WordPress admin styles.
 *
 * @package eco-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue custom styles on WordPress admin post-list screens.
 *
 * @param string $hook_suffix Current admin page hook.
 */
function eco_theme_enqueue_admin_styles( $hook_suffix ) {
	/*
	 * edit.php is the list screen for posts, pages and custom post types.
	 *
	 * Examples:
	 * /wp-admin/edit.php
	 * /wp-admin/edit.php?post_type=page
	 * /wp-admin/edit.php?post_type=tile
	 */
	if ( 'edit.php' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_style(
		'eco-theme-admin',
		get_stylesheet_directory_uri() . '/assets/css/admin.css',
		array(),
		ECO_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'eco_theme_enqueue_admin_styles' );