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
 * Get a breadcrumb parent page ID from an ACF/SCF Options field.
 *
 * The field should be a Post Object field limited to Pages and should return
 * either a Post ID or a WP_Post object. The default-language page can be
 * selected in the Options page; WPML will resolve its current translation.
 *
 * @param string $field_name ACF/SCF Options field name.
 * @return int
 */
function eco_theme_get_breadcrumb_parent_page_id( $field_name ) {
	$field_name = sanitize_key( $field_name );

	if ( empty( $field_name ) || ! function_exists( 'get_field' ) ) {
		return 0;
	}

	$value = get_field( $field_name, 'option' );

	if ( $value instanceof WP_Post ) {
		$page_id = absint( $value->ID );
	} elseif ( is_array( $value ) && ! empty( $value['ID'] ) ) {
		$page_id = absint( $value['ID'] );
	} else {
		$page_id = absint( $value );
	}

	if ( ! $page_id ) {
		return 0;
	}

	/**
	 * WPML: convert the selected default-language page ID to the page in the
	 * current language.
	 */
	if ( function_exists( 'icl_object_id' ) || has_filter( 'wpml_object_id' ) ) {
		$translated_page_id = apply_filters(
			'wpml_object_id',
			$page_id,
			'page',
			true
		);

		if ( $translated_page_id ) {
			$page_id = absint( $translated_page_id );
		}
	}

	$page = get_post( $page_id );

	if ( ! $page || 'page' !== $page->post_type || 'publish' !== $page->post_status ) {
		return 0;
	}

	return $page_id;
}

/**
 * Get the News breadcrumb parent page ID.
 *
 * ACF/SCF field:
 * Field Label: News Breadcrumb Parent Page
 * Field Name: news_breadcrumb_parent_page
 * Field Type: Post Object
 * Post Type: Page
 * Return Format: Post ID
 * Location: Options Page
 *
 * @return int
 */
function eco_theme_get_news_breadcrumb_parent_page_id() {
	return eco_theme_get_breadcrumb_parent_page_id( 'news_breadcrumb_parent_page' );
}

/**
 * Get the Studies breadcrumb parent page ID.
 *
 * ACF/SCF field:
 * Field Label: Studies Breadcrumb Parent Page
 * Field Name: studies_breadcrumb_parent_page
 * Field Type: Post Object
 * Post Type: Page
 * Return Format: Post ID
 * Location: Options Page
 *
 * @return int
 */
function eco_theme_get_studies_breadcrumb_parent_page_id() {
	return eco_theme_get_breadcrumb_parent_page_id( 'studies_breadcrumb_parent_page' );
}

/**
 * Force selected post types to use a real WordPress page as breadcrumb parent.
 *
 * Examples:
 * Home > News > Post title
 * Home > News > Press title
 * Home > Studies > Study title
 */
add_filter( 'wpseo_breadcrumb_links', 'eco_theme_yoast_parent_page_breadcrumbs' );

function eco_theme_yoast_parent_page_breadcrumbs( $links ) {
	if ( is_admin() || ! is_singular() ) {
		return $links;
	}

	$post_type = get_post_type();

	$parent_pages = [
		'post'  => eco_theme_get_news_breadcrumb_parent_page_id(),
		'press' => eco_theme_get_news_breadcrumb_parent_page_id(),
		'study' => eco_theme_get_studies_breadcrumb_parent_page_id(),
	];

	if ( ! isset( $parent_pages[ $post_type ] ) ) {
		return $links;
	}

	$parent_page_id = absint( $parent_pages[ $post_type ] );

	if ( ! $parent_page_id ) {
		return $links;
	}

	$parent_page = get_post( $parent_page_id );

	if ( ! $parent_page || 'page' !== $parent_page->post_type || 'publish' !== $parent_page->post_status ) {
		return $links;
	}

	$parent_crumb = [
		'url'  => get_permalink( $parent_page_id ),
		'text' => get_the_title( $parent_page_id ),
	];

	/**
	 * Keep Home and the current item.
	 * Replace everything between them with the selected parent page.
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
