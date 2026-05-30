<?php
/**
 * Eco Child Theme
 * @package eco-child
 * @author Batlab
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ECO_VERSION', '2.0.0' );

function eco_child_scripts_styles() {
	wp_enqueue_style( 'eco-fonts', get_stylesheet_directory_uri() . '/assets/css/ecofonts.css', [], ECO_VERSION );
	wp_enqueue_style( 'eco-child-style', get_stylesheet_directory_uri() . '/style.css', ['hello-elementor-theme-style'], ECO_VERSION );

	if ( is_singular( 'event' ) ) {
		wp_enqueue_style( 'eco-event-single', get_stylesheet_directory_uri() . '/assets/css/event-single.css', [], ECO_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'eco_child_scripts_styles', 20 );

require_once get_stylesheet_directory() . '/inc/fonts.php';
include_once( get_stylesheet_directory() . '/inc/hide-internal-elementor-templates.php' );
require_once get_stylesheet_directory() . '/inc/admin-post-featured-image-column.php';
include_once( get_stylesheet_directory() . '/inc/tile-redirect.php' );
require_once get_stylesheet_directory() . '/inc/event-helpers.php';