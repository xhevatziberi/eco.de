<?php
/**
 * Hide events and tiles sections based on category settings in Elementor category template
 */
add_action('wp_head', function () {
    if (!is_category()) {
        return;
    }

    $term = get_queried_object();
    if (!$term || empty($term->term_id)) {
        return;
    }

    $show_events = get_field('show_events_in_cats', $term);
    $show_tiles  = get_field('show_tiles_in_cats', $term);

    // Hide events if disabled
    if (!$show_events) {
        echo '<style>#events_in_category{display:none!important;}</style>';
    }

    // Hide tiles if disabled
    if (!$show_tiles) {
        echo '<style>#tiles_in_category{display:none!important;}</style>';
    }
});
