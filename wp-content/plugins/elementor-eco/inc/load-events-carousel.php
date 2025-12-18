<?php
/**
 * Standalone AJAX endpoint for the Events Carousel.
 * Action: eco_load_events_carousel
 *
 * Safe to run alongside your existing endpoints (e.g., eco_get_event_days).
 * Does not modify or reuse any previous handlers.
 */

if (!defined('ABSPATH')) exit;

// Public + logged-in access
add_action('wp_ajax_eco_load_events_carousel', 'ecocar_load_events_handler');
add_action('wp_ajax_nopriv_eco_load_events_carousel', 'ecocar_load_events_handler');

/* ---------------------------
 * Helpers (namespaced/prefixed)
 * --------------------------- */

function ecocar_param($key, $default = null) {
  if (isset($_GET[$key]))  return wp_unslash($_GET[$key]);
  if (isset($_POST[$key])) return wp_unslash($_POST[$key]);
  return $default;
}

function ecocar_s($v) {
  return is_scalar($v) ? trim((string)$v) : '';
}

/**
 * Collect term names from first existing taxonomy in the list.
 */
function ecocar_term_names($post_id, $tax_candidates) {
  foreach ((array)$tax_candidates as $tax) {
    if (!taxonomy_exists($tax)) continue;
    $terms = get_the_terms($post_id, $tax);
    if (!is_wp_error($terms) && !empty($terms)) {
      return array_values(array_map(static function($t){ return $t->name; }, $terms));
    }
  }
  return [];
}

/**
 * Shape one event item for the carousel.
 * Adjust ACF keys here if your project uses different names.
 */
function ecocar_shape_event($post) {
  $id   = $post->ID;
  $img  = get_the_post_thumbnail_url($id, 'large');

  // ACF fields (adjust if needed)
  $start_date = get_field('start_date', $id);
  $end_date   = get_field('end_date', $id);
  $start_time = get_field('start_time', $id);
  $end_time   = get_field('end_time', $id);
  $location   = get_field('city', $id);

  $teaser_title = get_field('teaser_title', $id);
  $teaser_short = get_field('teaser_short_description', $id);

  $tickets_url  = get_field('tickets_url', $id);
  $has_tickets  = !empty($tickets_url);

  // Repeater: other_dates
  $other_dates = [];
  if (function_exists('have_rows') && have_rows('other_dates', $id)) {
    while (have_rows('other_dates', $id)) {
      the_row();
      $other_dates[] = [
        'start_date' => ecocar_s(get_sub_field('start_date')),
        'end_date'   => ecocar_s(get_sub_field('end_date')),
        'start_time' => ecocar_s(get_sub_field('start_time')),
        'end_time'   => ecocar_s(get_sub_field('end_time')),
        'location'   => ecocar_s(get_sub_field('city')),
      ];
    }
    // In case something else hooks into have_rows afterwards:
    if (function_exists('reset_rows')) { reset_rows(); }
  }

  // Categories / tags (support either custom or core taxonomies)
  $categories = ecocar_term_names($id, ['event-category','category']);
  $tags       = ecocar_term_names($id, ['event-tag','post_tag']);

  return [
    'id'           => $id,
    'title'        => get_the_title($id),
    'link'         => get_permalink($id),
    'thumbnail'    => $img ?: '',
    'teaser_title' => ecocar_s($teaser_title),
    'teaser_short_description' => ecocar_s($teaser_short),

    'categories'   => $categories,
    'tags'         => $tags,

    'start_date'   => ecocar_s($start_date),
    'end_date'     => ecocar_s($end_date),
    'start_time'   => ecocar_s($start_time),
    'end_time'     => ecocar_s($end_time),
    'location'     => ecocar_s($location),

    'other_dates'  => $other_dates,
    'has_tickets'  => (bool) $has_tickets,
    'tickets_url'  => $has_tickets ? esc_url_raw($tickets_url) : '',
  ];
}

/* ---------------------------
 * Main handler
 * --------------------------- */
function ecocar_load_events_handler() {

    // Parameters expected by the carousel
    $per_page = (int) ecocar_param('per_page', 6);
    $per_page = max(1, min(50, $per_page));

    $orderby = sanitize_key(ecocar_param('orderby', 'start_date')); // start_date|title|date
    $order   = strtoupper(ecocar_param('order', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

    $upcoming = !empty(ecocar_param('upcoming', 0)); // 1 to include only future events

    // Build query
    $args = [
        'post_type'      => 'event',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'no_found_rows'  => true,
        'suppress_filters'=> false,
    ];

    // Order rules
    if ($orderby === 'start_date') {
        // Order by ACF start_date (Ymd)
        $args['meta_key'] = 'start_date';
        $args['orderby']  = 'meta_value';
        $args['order']    = $order;
    } elseif ($orderby === 'title') {
        $args['orderby']  = 'title';
        $args['order']    = $order;
    } else {
        $args['orderby']  = 'date';
        $args['order']    = $order;
    }

    // Meta filters
    $meta_query = [];

    if ($upcoming) {
        $meta_query[] = [
        'key'     => 'start_date',
        'value'   => date('Ymd'),
        'type'    => 'NUMERIC',
        'compare' => '>=',
        ];
    }

    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }

    // Filter by taxonomy terms coming from ACF field "event_cats_in_cats"
    // Expected format: "12,34,56" (term IDs). Taxonomy: event-category
    $cats_param = ecocar_param('cats', '');
    $cats_ids   = array_filter(array_map('intval', array_map('trim', explode(',', (string)$cats_param))));
    if (!empty($cats_ids)) {
        $args['tax_query'] = isset($args['tax_query']) ? (array)$args['tax_query'] : [];
        $args['tax_query'][] = [
            'taxonomy'         => 'event-category',   // as requested
            'field'            => 'term_id',
            'terms'            => $cats_ids,
            'include_children' => false,
            'operator'         => 'IN',
        ];
    }


    /**
     * Let themes/plugins tweak args if they want (won’t affect your old endpoints).
     */
    $args = apply_filters('eco_events_carousel_query_args', $args);

    // Query & shape
    $q = new WP_Query($args);
    $out = [];

    if ($q->have_posts()) {
        foreach ($q->posts as $post) {
        $out[] = ecocar_shape_event($post);
        }
    }

    wp_send_json($out);
}
