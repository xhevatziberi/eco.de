<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Usage: eco_theme_breadcrumbs() or [eco_theme_breadcrumbs] shortcode.

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
 * Normalize a Post Object field value to a post ID.
 *
 * @param mixed $value Field value.
 * @return int
 */
function eco_theme_normalize_post_object_id( $value ) {
	if ( $value instanceof WP_Post ) {
		return absint( $value->ID );
	}

	if ( is_array( $value ) && ! empty( $value['ID'] ) ) {
		return absint( $value['ID'] );
	}

	return absint( $value );
}

/**
 * Resolve a post ID to its current WPML language.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function eco_theme_get_translated_post_id( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id ) {
		return 0;
	}

	$post_type = get_post_type( $post_id );

	if ( ! $post_type ) {
		return 0;
	}

	if ( function_exists( 'icl_object_id' ) || has_filter( 'wpml_object_id' ) ) {
		$translated_post_id = apply_filters(
			'wpml_object_id',
			$post_id,
			$post_type,
			true
		);

		if ( $translated_post_id ) {
			$post_id = absint( $translated_post_id );
		}
	}

	return $post_id;
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

	$page_id = eco_theme_normalize_post_object_id( get_field( $field_name, 'option' ) );

	if ( ! $page_id ) {
		return 0;
	}

	$page_id = eco_theme_get_translated_post_id( $page_id );
	$page    = get_post( $page_id );

	if ( ! $page || 'page' !== $page->post_type || 'publish' !== $page->post_status ) {
		return 0;
	}

	return $page_id;
}

/**
 * Get the News breadcrumb parent page ID.
 *
 * @return int
 */
function eco_theme_get_news_breadcrumb_parent_page_id() {
	return eco_theme_get_breadcrumb_parent_page_id( 'news_breadcrumb_parent_page' );
}

/**
 * Get the Studies breadcrumb parent page ID.
 *
 * @return int
 */
function eco_theme_get_studies_breadcrumb_parent_page_id() {
	return eco_theme_get_breadcrumb_parent_page_id( 'studies_breadcrumb_parent_page' );
}

/**
 * Get the Podcasts breadcrumb parent page ID.
 *
 * @return int
 */
function eco_theme_get_podcasts_breadcrumb_parent_page_id() {
	return eco_theme_get_breadcrumb_parent_page_id( 'podcasts_breadcrumb_parent_page' );
}

/**
 * Get the manually selected breadcrumb parent for a normal Page.
 *
 * SCF field:
 * Field Name: breadcrumb_parent
 * Field Type: Post Object
 * Post Types: Page, Tile
 * Return Format: Post ID
 *
 * @param int|null $page_id Page ID. Defaults to the queried object.
 * @return int
 */
function eco_theme_get_page_breadcrumb_parent_id( $page_id = null ) {
	if ( null === $page_id ) {
		$page_id = get_queried_object_id();
	}

	$page_id = absint( $page_id );

	if ( ! $page_id || 'page' !== get_post_type( $page_id ) || ! function_exists( 'get_field' ) ) {
		return 0;
	}

	$parent_id = eco_theme_normalize_post_object_id( get_field( 'breadcrumb_parent', $page_id ) );

	if ( ! $parent_id || $parent_id === $page_id ) {
		return 0;
	}

	$parent_id = eco_theme_get_translated_post_id( $parent_id );
	$parent    = get_post( $parent_id );

	if (
		! $parent
		|| 'publish' !== $parent->post_status
		|| ! in_array( $parent->post_type, [ 'page', 'tile' ], true )
	) {
		return 0;
	}

	return $parent_id;
}

/**
 * Build breadcrumb entries for a selected parent and its WordPress ancestors.
 *
 * This preserves a hierarchy such as:
 * Home > Parent Tile > Child Tile > Page
 *
 * @param int $parent_id Selected parent ID.
 * @return array<int, array<string, mixed>>
 */
function eco_theme_build_parent_breadcrumb_chain( $parent_id ) {
	$parent_id = absint( $parent_id );

	if ( ! $parent_id ) {
		return [];
	}

	$ancestor_ids = array_reverse( get_post_ancestors( $parent_id ) );
	$post_ids     = array_merge( $ancestor_ids, [ $parent_id ] );
	$crumbs       = [];

	foreach ( $post_ids as $post_id ) {
		$post_id = absint( $post_id );
		$post    = get_post( $post_id );

		if ( ! $post || 'publish' !== $post->post_status ) {
			continue;
		}

		$crumb = [
			'id'   => $post_id,
			'text' => get_the_title( $post_id ),
		];

		$can_link = true;

		// A disabled Tile intentionally has no public single page.
		if ( 'tile' === $post->post_type && function_exists( 'get_field' ) ) {
			$can_link = ! (bool) get_field( 'disable_tile_page', $post_id );
		}

		if ( $can_link ) {
			$crumb['url'] = get_permalink( $post_id );
		}

		$crumbs[] = $crumb;
	}

	return $crumbs;
}

/**
 * Replace the middle of Yoast's breadcrumb list while keeping Home/current item.
 *
 * @param array $links        Yoast breadcrumb links.
 * @param array $parent_links Parent breadcrumb links.
 * @return array
 */
function eco_theme_replace_breadcrumb_parents( $links, $parent_links ) {
	if ( empty( $links ) || empty( $parent_links ) ) {
		return $links;
	}

	$first = reset( $links );
	$last  = end( $links );

	$new_links = [];

	if ( $first ) {
		$new_links[] = $first;
	}

	foreach ( $parent_links as $parent_link ) {
		$new_links[] = $parent_link;
	}

	if ( $last && $last !== $first ) {
		$new_links[] = $last;
	}

	return $new_links;
}

/**
 * Customize Yoast breadcrumb parents.
 *
 * - Normal Pages may explicitly select a Page or Tile as breadcrumb parent.
 * - News, Press, Studies and Podcasts use their configured parent pages from
 *   Settings > eco Settings.
 */
add_filter( 'wpseo_breadcrumb_links', 'eco_theme_yoast_parent_page_breadcrumbs' );

function eco_theme_yoast_parent_page_breadcrumbs( $links ) {
	if ( is_admin() || ! is_singular() ) {
		return $links;
	}

	$post_type = get_post_type();

	if ( 'page' === $post_type ) {
		$parent_id = eco_theme_get_page_breadcrumb_parent_id();

		if ( ! $parent_id ) {
			return $links;
		}

		return eco_theme_replace_breadcrumb_parents(
			$links,
			eco_theme_build_parent_breadcrumb_chain( $parent_id )
		);
	}

	$parent_pages = [
		'post'    => eco_theme_get_news_breadcrumb_parent_page_id(),
		'press'   => eco_theme_get_news_breadcrumb_parent_page_id(),
		'study'   => eco_theme_get_studies_breadcrumb_parent_page_id(),
		'podcast' => eco_theme_get_podcasts_breadcrumb_parent_page_id(),
	];

	if ( ! isset( $parent_pages[ $post_type ] ) ) {
		return $links;
	}

	$parent_page_id = absint( $parent_pages[ $post_type ] );

	if ( ! $parent_page_id ) {
		return $links;
	}

	return eco_theme_replace_breadcrumb_parents(
		$links,
		eco_theme_build_parent_breadcrumb_chain( $parent_page_id )
	);
}
