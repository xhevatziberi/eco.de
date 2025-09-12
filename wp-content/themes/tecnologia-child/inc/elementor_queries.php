<?php
// Elementor custom query: "latest_events"
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
    error_log( print_r( $query->get( 'meta_query' ), true ) );
}
add_action( 'elementor/query/latest_events', 'custom_query_callback_latest' );
