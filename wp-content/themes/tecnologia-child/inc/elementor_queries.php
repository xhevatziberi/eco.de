<?php
// Elementor custom query: "latest_events" - upcoming events
// Usage: in Elementor Posts widget, Query > Source: Custom Query > Query ID: latest
function custom_query_callback_latest( $query ) {
    // ACF date stored as Ymd (e.g. 20250912)
    $today = current_time( 'Ymd' );
    $type  = 'NUMERIC';

    $query->set( 'meta_query', [
        [
            'key'     => 'start_date',
            'value'   => $today,
            'compare' => '>=',
            'type'    => $type,
        ],
    ] );

    // Sort closest upcoming first
    $query->set( 'meta_key', 'start_date' );
    $query->set( 'orderby', 'meta_value_num' );
    $query->set( 'order', 'ASC' );

    // $query->set( 'posts_per_page', 5 );

    // Debug
    // error_log( print_r( $query->get( 'meta_query' ), true ) );
}
add_action( 'elementor/query/latest_events', 'custom_query_callback_latest' );

// Elementor custom query: "past_events" - past events
// Usage: in Elementor Posts widget, Query > Source: Custom Query > Query ID: past_events
function custom_query_callback_past_events( $query ) {
    // ACF date stored as Ymd (e.g. 20250912)
    $today = current_time( 'Ymd' );
    $type  = 'NUMERIC';

    $query->set( 'meta_query', [
        [
            'key'     => 'start_date',
            'value'   => $today,
            'compare' => '<',
            'type'    => $type,
        ],
    ] );

    // Sort closest past first
    $query->set( 'meta_key', 'start_date' );
    $query->set( 'orderby', 'meta_value_num' );
    $query->set( 'order', 'DESC' );

    // $query->set( 'posts_per_page', 5 );

    // Debug
    // error_log( print_r( $query->get( 'meta_query' ), true ) );
}
add_action( 'elementor/query/past_events', 'custom_query_callback_past_events' );

// Elementor custom query: "sort_events_by_start" - all events sorted by start date
// Usage: in Elementor Posts widget, Query > Source: Custom Query > Query ID: sort_events_by_start
function custom_query_callback_sort_events_by_start( $query ) {
    // ACF date stored as Ymd (e.g. 20250912)
    $query->set( 'meta_key', 'start_date' );
    $query->set( 'orderby', 'meta_value_num' );
    $query->set( 'order', 'DESC' );
}
add_action( 'elementor/query/sort_events_by_start', 'custom_query_callback_sort_events_by_start' );



/**
 * Tiny helper: safely fetch ACF term field via term meta (fast)
 */
function ec_get_term_acf_value( $term_id, $field_name ) {
    // ACF stores term fields in wp_termmeta under the field name key
    $val = get_term_meta( (int) $term_id, $field_name, true );
    // WP unserializes term meta automatically; ensure it's an array if needed
    if ($val === '' || $val === null) return [];
    return is_array($val) ? $val : (array) $val;
}

/**
 * EVENTS loop — Query ID: events_in_category
 * Filters by ACF term picker "event_cats_in_cats" (taxonomy: event-category)
 * Orders by meta start_date (Ymd) as NUMERIC
 */
add_action('elementor/query/events_in_category', function ( $query ) {

    // Guard against re-appending when Elementor re-runs the same query
    if ( $query->get('ec__filtered_events') ) return;
    $query->set('ec__filtered_events', true);

    // Set the correct post type for events
    $query->set('post_type', 'event'); // <-- change if your CPT slug differs
    $query->set('ignore_sticky_posts', true);

    // If you don't paginate this widget, keep it light:
    if ( ! $query->get('paged') && ! $query->get('offset') ) {
        $query->set('no_found_rows', true);
    }

    if ( ! is_category() ) return;

    $term = get_queried_object();
    if ( ! $term || empty($term->term_id) ) return;

    // 👇 FAST: read ACF value directly from term meta (no get_field())
    $selected_event_cat_ids = ec_get_term_acf_value( $term->term_id, 'event_cats_in_cats' );
    $selected_event_cat_ids = array_filter( array_map('intval', (array) $selected_event_cat_ids ) );

    if ( empty($selected_event_cat_ids) ) {
        // 👉 If nothing selected, show nothing
        $query->set('post__in', [0]);
        return;
    }

    if ( ! empty($selected_event_cat_ids) ) {
        $tax_query = $query->get('tax_query');
        if ( ! is_array($tax_query) ) $tax_query = [];

        $tax_query[] = [
            'taxonomy'         => 'event-category',
            'field'            => 'term_id',
            'terms'            => $selected_event_cat_ids,
            'include_children' => false,
            'operator'         => 'IN',
        ];
        $query->set('tax_query', $tax_query);
    } else {
        // Optional: show nothing if none selected
        // $query->set('post__in', [0]); return;
    }

    // Sort by ACF date stored as Ymd (e.g. 20250912)
    $query->set('meta_key', 'start_date');
    $query->set('meta_type', 'NUMERIC');
    $query->set('orderby', 'meta_value_num');
    $query->set('order', 'DESC'); // or 'ASC' for soonest first
});

/**
 * TILES loop — Query ID: tiles_in_category
 * Filters by ACF term picker "event_tiles_in_cats" (taxonomy: tile-category)
 * No special ordering
 */
add_action('elementor/query/tiles_in_category', function ( $query ) {

    if ( $query->get('ec__filtered_tiles') ) return;
    $query->set('ec__filtered_tiles', true);

    $query->set('post_type', 'tile'); // <-- change if your CPT slug differs
    $query->set('ignore_sticky_posts', true);
    if ( ! $query->get('paged') && ! $query->get('offset') ) {
        $query->set('no_found_rows', true);
    }

    if ( ! is_category() ) return;

    $term = get_queried_object();
    if ( ! $term || empty($term->term_id) ) return;

    // 👇 FAST: term meta, not get_field()
    $selected_tile_cat_ids = ec_get_term_acf_value( $term->term_id, 'event_tiles_in_cats' );
    $selected_tile_cat_ids = array_filter( array_map('intval', (array) $selected_tile_cat_ids ) );

    if ( empty($selected_tile_cat_ids) ) {
        // 👉 If nothing selected, show nothing
        $query->set('post__in', [0]);
        return;
    }

    if ( ! empty($selected_tile_cat_ids) ) {
        $tax_query = $query->get('tax_query');
        if ( ! is_array($tax_query) ) $tax_query = [];

        $tax_query[] = [
            'taxonomy'         => 'tile-category',
            'field'            => 'term_id',
            'terms'            => $selected_tile_cat_ids,
            'include_children' => false,
            'operator'         => 'IN',
        ];
        $query->set('tax_query', $tax_query);
    }
    // Leave default ordering for tiles
});
