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

// add svg mask to head:
add_action('wp_head', function () {
    ?>
    <svg viewBox="0 0 132.292 132.292" width="0" height="0" style="position:absolute;">
        <defs>
            <!-- Mask is relative to the element's bounding box -->
            <mask id="svg-mask-eco"
                  maskUnits="objectBoundingBox"
                  maskContentUnits="objectBoundingBox">
                
                <!-- start with everything hidden -->
                <rect x="0" y="0" width="1" height="1" fill="black" />

                <!-- your shape, scaled from 0–132 space into 0–1 space -->
                <path
                    d="M7.782 7.782C8.755 6.808 35.992 0 66.146 0 96.3 0 123.537 6.81 124.51 7.782c.973.974 7.782 28.21 7.782 58.364 0 30.155-6.81 57.391-7.782 58.364-.973.974-28.21 7.782-58.364 7.782-30.155 0-57.391-6.81-58.364-7.782C6.809 123.536 0 96.3 0 66.146 0 35.991 6.81 8.755 7.782 7.782"
                    fill="white"
                    transform="scale(0.007559)" />
                <!-- 0.007559 ≈ 1 / 132.292 -->
            </mask>
        </defs>
    </svg>
    <?php
});
