<?php

function custom_query_callback_active( $query ) {
    $today = date('Ymd'); // Format: 20250423

    $query->set( 'meta_query', array(
        'relation' => 'OR',
        array(
            'key' => 'start_date',
            'value' => $today,
            'compare' => '>',
            'type' => 'NUMERIC',
        ),
        array(
            'key' => 'start_date_2',
            'value' => $today,
            'compare' => '>',
            'type' => 'NUMERIC',
        ),
    ) );
}
add_action( 'elementor/query/active_events', 'custom_query_callback_active' );

function custom_query_callback_past( $query ) {
    $today = date('Ymd');

    $query->set( 'meta_query', array(
        'relation' => 'OR',
        array(
            'key' => 'start_date',
            'value' => $today,
            'compare' => '<=',
            'type' => 'NUMERIC',
        ),
        array(
            'key' => 'start_date_2',
            'value' => $today,
            'compare' => '<=',
            'type' => 'NUMERIC',
        ),
    ) );
}
add_action( 'elementor/query/past_events', 'custom_query_callback_past' );

