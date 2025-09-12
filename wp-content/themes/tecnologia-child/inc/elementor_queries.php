<?php

// Sort events by start_date ascending

function custom_query_callback_latest( $query ) {

    // Sort by start_date ascending
    $query->set( 'meta_key', 'start_date' );
    $query->set( 'orderby', 'meta_value_num' );
    $query->set( 'order', 'ASC' );

    // log query, located in the debug.log
    error_log( print_r( $query->get( 'meta_query' ), true ) );
}
add_action( 'elementor/query/latest_events', 'custom_query_callback_latest' );