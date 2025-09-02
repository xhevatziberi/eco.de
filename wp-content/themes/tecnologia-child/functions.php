<?php

function child_styles() {
	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'my-child-theme-style', get_stylesheet_directory_uri() . '/style.css', array( 'vamtam-front-all' ), $version, 'all' );
	wp_enqueue_script( 'my-child-theme-script', get_stylesheet_directory_uri() . '/js/custom.js', array( 'jquery' ), $version, true );
	wp_enqueue_style('eco-agenda', get_stylesheet_directory_uri() . '/css/agenda.css', array(), $version, 'all');
}
add_action( 'wp_enqueue_scripts', 'child_styles', 11 );

add_editor_style( 'css/editor-style.css' );

include_once( get_stylesheet_directory() . '/inc/admin-menu-hide-items.php' );
include_once( get_stylesheet_directory() . '/inc/hide-internal-elementor-templates.php' );
include_once( get_stylesheet_directory() . '/inc/shortcodes.php' );