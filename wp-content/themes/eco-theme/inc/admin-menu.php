<?php

defined( 'ABSPATH' ) || exit;

function eco_hide_admin_menu_items(): void {
	$current_user = wp_get_current_user();

	if ( 'eco_admin' === $current_user->user_login ) {
		return;
	}

	$menu_pages_to_hide = [
		// Core.
		'themes.php',
		// 'plugins.php',
		// 'users.php',
        'edit-comments.php',

		// Plugins.
		'superfly-menu-options',
		'wpseo_dashboard',
		// 'wpforms-overview',
		'edit.php?post_type=acf-field-group',
		'pretix_widget',
		'searchwp-forms',
		'hello-elementor',
		'tm/menu/main.php',
        'elementor-home',
	];

	foreach ( $menu_pages_to_hide as $menu_slug ) {
		remove_menu_page( $menu_slug );
	}
}
add_action( 'admin_menu', 'eco_hide_admin_menu_items', 9999 );