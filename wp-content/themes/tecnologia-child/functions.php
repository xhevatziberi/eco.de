<?php

function child_styles() {
	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'my-child-theme-style', get_stylesheet_directory_uri() . '/style.css', array( 'vamtam-front-all' ), $version, 'all' );
	wp_enqueue_script( 'my-child-theme-script', get_stylesheet_directory_uri() . '/js/custom.js', array( 'jquery' ), $version, true );
	wp_enqueue_style('eco-agenda', get_stylesheet_directory_uri() . '/css/agenda.css', array(), $version, 'all');
	
	// swiper styles
	wp_enqueue_style( 'swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0', 'all' );
	wp_enqueue_script( 'swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true );

	wp_register_style('eco-sponsors', get_stylesheet_directory_uri() . '/css/sponsors.css', array(), $version, 'all');
	wp_register_script('eco-sponsors', get_stylesheet_directory_uri() . '/js/sponsors.js', array('jquery', 'swiper-js'), $version, true);
}
add_action( 'wp_enqueue_scripts', 'child_styles', 11 );

add_editor_style( 'css/editor-style.css' );

include_once( get_stylesheet_directory() . '/inc/admin-menu-hide-items.php' );
include_once( get_stylesheet_directory() . '/inc/hide-internal-elementor-templates.php' );
include_once( get_stylesheet_directory() . '/inc/shortcodes.php' );
include_once( get_stylesheet_directory() . '/inc/elementor_queries.php' );