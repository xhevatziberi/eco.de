<?php
add_action('wp_ajax_nopriv_eco_load_events', 'eco_load_events_callback');
add_action('wp_ajax_eco_load_events', 'eco_load_events_callback');

function eco_load_events_callback() {
    $date     = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';
    $month    = isset($_GET['month']) ? sanitize_text_field($_GET['month']) : ''; // new
    $category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
    $tag      = isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '';

    if (!$date && !$month) {
        wp_send_json([]); // no date or month provided
    }

    $meta_query = [];

    if ($month) {
        // Example: '2025-08' → 20250801 to 20250831
        $month = preg_replace('/[^0-9\-]/', '', $month);
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            wp_send_json([]); // invalid format
        }

        [$y, $m] = explode('-', $month);
        $from = $y . $m . '01';
        $to = $y . $m . '31'; // safe assumption for WP date comparison

        $meta_query[] = [
            'key'     => 'start_date',
            'value'   => [$from, $to],
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC'
        ];
    } elseif ($date) {
        $date = str_replace('-', '', $date); // convert to Ymd
        $meta_query[] = [
            'key'     => 'start_date',
            'value'   => $date,
            'compare' => '=',
            'type'    => 'NUMERIC'
        ];
    }

    $args = [
        'post_type'      => 'event',
        'posts_per_page' => -1,
        'meta_query'     => $meta_query,
        'orderby'        => 'meta_value',
        'meta_key'       => 'start_date',
        'order'          => 'ASC',
    ];

    if ($category) {
        $args['tax_query'][] = [
            'taxonomy' => 'event-category',
            'field'    => 'slug',
            'terms'    => $category,
        ];
    }

    if ($tag) {
        $args['tax_query'][] = [
            'taxonomy' => 'event-tag',
            'field'    => 'slug',
            'terms'    => $tag,
        ];
    }

    $query = new \WP_Query($args);
    $results = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $results[] = [
                'title'                    => get_the_title(),
                'link'                     => get_permalink(),
                'start_date'               => get_post_meta(get_the_ID(), 'start_date', true),
                'end_date'                 => get_post_meta(get_the_ID(), 'end_date', true),
                'time'                     => get_post_meta(get_the_ID(), 'time', true),
                'location'                 => get_post_meta(get_the_ID(), 'location', true),
                'description'              => get_the_excerpt(),
                'thumbnail'                => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                'categories'               => wp_get_post_terms(get_the_ID(), 'event-category', ['fields' => 'names']),
                'tags'                     => wp_get_post_terms(get_the_ID(), 'event-tag', ['fields' => 'names']),
                'id'                       => get_the_ID(),
                'teaser_short_description' => get_field('teaser_short_description'),
            ];
        }
    }

    wp_reset_postdata();
    wp_send_json($results);
}


add_action('wp_ajax_nopriv_eco_get_event_days', 'eco_get_event_days');
add_action('wp_ajax_eco_get_event_days', 'eco_get_event_days');

function eco_get_event_days() {
	$data = json_decode(file_get_contents('php://input'), true);
	if (!is_array($data['dates'])) {
		wp_send_json([]);
	}

	global $wpdb;
	$dates = array_map('sanitize_text_field', $data['dates']);

	// Adjust for your post type and ACF field name
	$query = new WP_Query([
		'post_type' => 'event',
		'posts_per_page' => -1,
		'meta_query' => [
			[
				'key' => 'start_date',
				'value' => $dates,
				'compare' => 'IN',
				'type' => 'DATE'
			]
		],
        'orderby'        => 'meta_value',
        'meta_key'       => 'start_date',
        'order'          => 'DESC',
		'fields' => 'ids'
	]);

	$event_dates = [];
    if ($query->have_posts()) {
        foreach ($query->posts as $post_id) {
            $date = get_field('start_date', $post_id);
            if ($date) {
                $event_dates[] = $date;
            }
        }
    }

    wp_send_json(array_values(array_unique($event_dates)));
}
