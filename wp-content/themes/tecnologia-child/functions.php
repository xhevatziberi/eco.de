<?php

function child_styles() {
	wp_enqueue_style( 'my-child-theme-style', get_stylesheet_directory_uri() . '/style.css', array( 'vamtam-front-all' ), false, 'all' );
	wp_enqueue_script( 'my-child-theme-script', get_stylesheet_directory_uri() . '/js/custom.js', array( 'jquery' ), false, true );
}
add_action( 'wp_enqueue_scripts', 'child_styles', 11 );


// hide menu items for non-admins
function hide_admin_menu_items() {
	// if ( ! current_user_can( 'administrator' ) ) {
		// edit-comments.php:
		remove_menu_page( 'edit-comments.php' );
		// admin.php?page=vamtam_theme_setup:
		remove_menu_page( 'admin.php?page=vamtam_theme_setup' );
		// admin.php?page=envato-elements:
		remove_menu_page( 'admin.php?page=envato-elements' );
		// admin.php?page=vamtam_theme_setup:
		remove_menu_page( 'admin.php?page=vamtam_theme_setup' );
	// }
}
add_action( 'admin_init', 'hide_admin_menu_items' );

function filter_events( $query ) {
	echo '<pre>';
	echo 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
	echo '</pre>';
	// Get current meta Query
	$meta_query = $query->get( 'meta_query' );

	// If there is no meta query when this filter runs, it should be initialized as an empty array.
	if ( ! $meta_query ) {
		$meta_query = [];
	}

	// Append our meta query
	$meta_query[] = [
		'key' => 'project_type',
		'value' => [ 'design', 'development' ],
		'compare' => 'in',
	];

	$query->set( 'meta_query', $meta_query );
}
add_action( 'elementor/query/{$query_id}', 'filter_events' );

// include_once('inc/event_queries.php');