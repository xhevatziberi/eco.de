<?php
/**
 * Admin menu visibility.
 *
 * @package eco-theme
 */

defined( 'ABSPATH' ) || exit;

function eco_hide_admin_menu_items(): void {
	$user_id = get_current_user_id();

	// eco_admin keeps unrestricted access to the existing admin menus.
	if ( 1 === $user_id ) {
		return;
	}

	$menu_pages_to_hide = [
		// Core.
		'edit-comments.php',

		// Plugins.
		'wpseo_dashboard',
		'edit.php?post_type=acf-field-group',
		'pretix_widget',
		'searchwp-forms',
		'hello-elementor',
		'tm/menu/main.php',
		'elementor-home',
	];

	/**
	 * Users selected under Settings > eco Settings > Users with Special Access
	 * may manage the site navigation and Theme Builder structure.
	 *
	 * Everyone else keeps Appearance and Superfly hidden.
	 */
	if ( ! eco_user_has_special_access( $user_id ) ) {
		$menu_pages_to_hide[] = 'themes.php';
		$menu_pages_to_hide[] = 'superfly-menu-options';
	}

	foreach ( $menu_pages_to_hide as $menu_slug ) {
		remove_menu_page( $menu_slug );
	}
}
add_action( 'admin_menu', 'eco_hide_admin_menu_items', 9999 );
