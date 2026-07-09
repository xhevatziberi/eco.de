<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Usage: eco_theme_breadcrumbs() or [eco_theme_breadcrumbs] shortcode

/**
 * Shortcode for theme breadcrumbs.
 *
 * Usage:
 * [eco_theme_breadcrumbs]
 */
add_shortcode( 'eco_theme_breadcrumbs', 'eco_theme_breadcrumbs_shortcode' );

function eco_theme_breadcrumbs_shortcode() {
	ob_start();

	eco_theme_breadcrumbs();

	return ob_get_clean();
}

/**
 * Output Yoast breadcrumbs in the theme.
 */
function eco_theme_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	if ( ! function_exists( 'yoast_breadcrumb' ) ) {
		return;
	}

	yoast_breadcrumb(
		'<nav class="eco-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'eco-theme' ) . '">',
		'</nav>'
	);
}

/**
 * Get the News breadcrumb parent page ID from ACF Options.
 *
 * ACF field:
 * Field Label: News Breadcrumb Parent Page
 * Field Name: news_breadcrumb_parent_page
 * Field Type: Post Object
 * Post Type: Page
 * Return Format: Post ID
 */
function eco_theme_get_news_breadcrumb_parent_page_id() {
	$page_id = 0;

	if ( function_exists( 'get_field' ) ) {
		$page_id = (int) get_field( 'news_breadcrumb_parent_page', 'option' );
	}

	if ( ! $page_id ) {
		return 0;
	}

	/**
	 * WPML: convert selected default-language page ID to current language.
	 *
	 * In ACF Options, select the default language News page.
	 * This will automatically use the translated page on English/German/etc.
	 */
	if ( function_exists( 'icl_object_id' ) || has_filter( 'wpml_object_id' ) ) {
		$translated_page_id = apply_filters(
			'wpml_object_id',
			$page_id,
			'page',
			true
		);

		if ( $translated_page_id ) {
			$page_id = (int) $translated_page_id;
		}
	}

	$page = get_post( $page_id );

	if ( ! $page || 'page' !== $page->post_type || 'publish' !== $page->post_status ) {
		return 0;
	}

	return $page_id;
}

/**
 * Force selected post types to use a real WordPress page as breadcrumb parent.
 *
 * Example:
 * Home > News > Post title
 * Home > News > Press title
 */
add_filter( 'wpseo_breadcrumb_links', 'eco_theme_yoast_parent_page_breadcrumbs' );

function eco_theme_yoast_parent_page_breadcrumbs( $links ) {
	if ( is_admin() || ! is_singular() ) {
		return $links;
	}

	$post_type = get_post_type();

	// Only affect normal posts and press CPT.
	if ( ! in_array( $post_type, [ 'post', 'press' ], true ) ) {
		return $links;
	}

	$news_page_id = eco_theme_get_news_breadcrumb_parent_page_id();

	if ( ! $news_page_id ) {
		return $links;
	}

	$parent_pages = [
		'post'  => $news_page_id,
		'press' => $news_page_id,
	];

	$parent_page_id = $parent_pages[ $post_type ] ?? 0;

	if ( ! $parent_page_id ) {
		return $links;
	}

	$parent_page = get_post( $parent_page_id );

	if ( ! $parent_page || 'publish' !== $parent_page->post_status ) {
		return $links;
	}

	$parent_crumb = [
		'url'  => get_permalink( $parent_page_id ),
		'text' => get_the_title( $parent_page_id ),
	];

	/**
	 * Keep Home and current item.
	 * Replace everything between them with our selected parent page.
	 */
	$first = array_shift( $links );
	$last  = array_pop( $links );

	$new_links = [];

	if ( $first ) {
		$new_links[] = $first;
	}

	$new_links[] = $parent_crumb;

	if ( $last ) {
		$new_links[] = $last;
	}

	return $new_links;
}