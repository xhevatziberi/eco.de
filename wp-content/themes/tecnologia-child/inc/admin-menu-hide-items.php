<?php

// Hide specific admin menu items for non-admin users
function hide_admin_menu_items() {
	// if ( ! current_user_can( 'administrator' ) ) {
		// edit-comments.php:
		// remove_menu_page( 'edit-comments.php' );
		// admin.php?page=vamtam_theme_setup:
		remove_menu_page( 'admin.php?page=vamtam_theme_setup' );
		// admin.php?page=envato-elements:
		remove_menu_page( 'admin.php?page=envato-elements' );
		// admin.php?page=vamtam_theme_setup:
		remove_menu_page( 'admin.php?page=vamtam_theme_setup' );
		// }
	}
add_action( 'admin_init', 'hide_admin_menu_items' );
