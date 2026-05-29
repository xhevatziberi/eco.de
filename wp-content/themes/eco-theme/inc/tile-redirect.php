<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'template_redirect', function () {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$post_id = get_queried_object_id();

	if ( ! $post_id ) {
		return;
	}

	$post = get_post( $post_id );

	if ( ! $post || $post->post_type !== 'tile' ) {
		return;
	}

	$disabled = false;

	if ( function_exists( 'get_field' ) ) {
		$disabled = get_field( 'disable_tile_page', $post_id );
	} else {
		$disabled = get_post_meta( $post_id, 'disable_tile_page', true );
	}

	if ( empty( $disabled ) ) {
		return;
	}

	global $wp_query;

	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();

	$template = get_404_template();

	if ( ! empty( $template ) && file_exists( $template ) ) {
		include $template;
		exit;
	}

	// Fallback if the theme has no 404.php.
	wp_die(
		esc_html__( 'This page is not available.', 'elementor-eco' ),
		esc_html__( 'Page not found', 'elementor-eco' ),
		[ 'response' => 404 ]
	);
}, 1 );