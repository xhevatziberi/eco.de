<?php
/**
 * Add featured image column to selected admin post type lists.
 */

function eco_featured_image_admin_post_types() {
    return [
        'post',
        'tile',
        'press',
    ];
}

/**
 * Add featured image column.
 */
function eco_add_featured_image_admin_column($columns) {
    $new_columns = [];
    $inserted = false;
    $i = 0;

    foreach ($columns as $key => $label) {
        $new_columns[$key] = $label;
        $i++;

        // Insert as 4th column if possible.
        if ($i === 3) {
            $new_columns['featured_image'] = __('Image', 'eco');
            $inserted = true;
        }
    }

    // Fallback: add last.
    if (!$inserted) {
        $new_columns['featured_image'] = __('Image', 'eco');
    }

    return $new_columns;
}

/**
 * Render featured image column.
 */
function eco_render_featured_image_admin_column($column, $post_id) {
    if ($column !== 'featured_image') {
        return;
    }

    if (has_post_thumbnail($post_id)) {
        echo get_the_post_thumbnail($post_id, [50, 50], [
            'style' => 'width:50px;height:50px;object-fit:cover;border-radius:4px;',
        ]);
    } else {
        echo '&mdash;';
    }
}

/**
 * Register hooks for each post type.
 */
foreach (eco_featured_image_admin_post_types() as $post_type) {
    add_filter("manage_{$post_type}_posts_columns", 'eco_add_featured_image_admin_column');
    add_action("manage_{$post_type}_posts_custom_column", 'eco_render_featured_image_admin_column', 10, 2);
}

/**
 * Make image column narrow.
 */
add_action('admin_head-edit.php', function () {
    echo '<style>
        .column-featured_image {
            width: 70px;
            text-align: center;
        }

        .column-featured_image img {
            display: inline-block;
        }
    </style>';
});