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

	if ( ! $post || 'tile' !== $post->post_type ) {
		return;
	}

	$disabled = false;
	$link     = '';

	if ( function_exists( 'get_field' ) ) {
		$disabled = (bool) get_field( 'disable_tile_page', $post_id );
		$link     = get_field( 'link', $post_id );
	} else {
		$disabled = (bool) get_post_meta( $post_id, 'disable_tile_page', true );
		$link     = get_post_meta( $post_id, 'link', true );
	}

	if ( is_array( $link ) && ! empty( $link['url'] ) ) {
		$link = $link['url'];
	}

	$link = is_string( $link ) ? trim( $link ) : '';

	if ( $link ) {
		$scheme = wp_parse_url( $link, PHP_URL_SCHEME );

		if ( in_array( strtolower( (string) $scheme ), [ 'http', 'https' ], true ) ) {
			$current_url = get_permalink( $post_id );

			if ( ! $current_url || untrailingslashit( $current_url ) !== untrailingslashit( $link ) ) {
				wp_redirect( esc_url_raw( $link ), 302, 'eco Tile Redirect' ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
				exit;
			}
		}
	}

	if ( ! $disabled ) {
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
		esc_html__( 'This page is not available.', 'eco-theme' ),
		esc_html__( 'Page not found', 'eco-theme' ),
		[ 'response' => 404 ]
	);
}, 1 );
