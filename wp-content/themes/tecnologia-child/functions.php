<?php

function child_styles() {
	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'my-child-theme-style', get_stylesheet_directory_uri() . '/style.css', array( 'vamtam-front-all' ), $version, 'all' );
	wp_enqueue_script( 'my-child-theme-script', get_stylesheet_directory_uri() . '/js/custom.js', array( 'jquery' ), $version, true );
	wp_enqueue_style('eco-loops', get_stylesheet_directory_uri() . '/css/loops.css', array(), $version, 'all');
	wp_enqueue_style('eco-agenda', get_stylesheet_directory_uri() . '/css/agenda.css', array(), $version, 'all');
	
	// swiper styles
	wp_enqueue_style( 'swiper-css', '/wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.css?ver=8.4.5', array(), '11.0.0', 'all' ); //temporary fix
	// wp_enqueue_style( 'swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0', 'all' );
	wp_enqueue_script( 'swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true );

	wp_register_style('eco-sponsors', get_stylesheet_directory_uri() . '/css/sponsors.css', array(), $version, 'all');
	wp_register_script('eco-sponsors', get_stylesheet_directory_uri() . '/js/sponsors.js', array('jquery', 'swiper-js'), $version, true);

	wp_enqueue_style('eco-events', get_stylesheet_directory_uri() . '/css/events.css', array(), $version, 'all');
}
add_action( 'wp_enqueue_scripts', 'child_styles', 11 );

add_editor_style( 'css/editor-style.css' );

include_once( get_stylesheet_directory() . '/inc/admin-menu-hide-items.php' );
include_once( get_stylesheet_directory() . '/inc/hide-internal-elementor-templates.php' );
include_once( get_stylesheet_directory() . '/inc/shortcodes.php' );
include_once( get_stylesheet_directory() . '/inc/ical.php' );
include_once( get_stylesheet_directory() . '/inc/elementor_queries.php' );
include_once( get_stylesheet_directory() . '/inc/misc.php' );

add_image_size( 'events-small', 267, 150, true ); // Crop mode, 16:9 ratio
add_image_size( 'post-medium', 480, 270, true ); // Crop mode, 16:9 ratio
add_image_size( 'post-large', 1920, 1080, true ); // Crop mode, 16:9 ratio
add_filter( 'intermediate_image_sizes_advanced', function( $sizes ) {
    unset( $sizes['1536x1536'], $sizes['2048x2048'] );
    return $sizes;
});

