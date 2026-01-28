<?php
namespace ECO_Search;

if (!defined('ABSPATH')) exit;

class Search {

    public static function init() {
        // reserved for future (AJAX, REST, etc.)
    }

    public static function supported_post_types() {
        return Taxonomy::supported_post_types();
    }

    /**
     * UI labels for "Content" filter.
     * We show "Other" instead of "Pages" (but pages are still searched).
     */
    public static function content_type_labels() {
        return apply_filters('eco_search_content_type_labels', [
            'post'     => __('News', 'eco-search'),
            'page'     => __('Other', 'eco-search'), // was "Pages"
            'event'    => __('Events', 'eco-search'),
            'podcast'  => __('Podcasts', 'eco-search'),
            'press'    => __('Press', 'eco-search'),
            // 'tile'     => __('Tiles', 'eco-search'),
            'download' => __('Downloads', 'eco-search'),
            'paper'    => __('Papers', 'eco-search'),
            'study'    => __('Studies', 'eco-search'),
        ]);
    }

    public static function date_options() {
        return [
            '6m'  => __('Last 6 months', 'eco-search'),
            '12m' => __('Last 12 months', 'eco-search'),
            '30d' => __('Last 30 days', 'eco-search'),
            'all' => __('All time', 'eco-search'),
        ];
    }

    public static function parse_request() {
        $s     = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
        $topic = isset($_GET['topic']) ? sanitize_text_field(wp_unslash($_GET['topic'])) : '';
        $date  = isset($_GET['date']) ? sanitize_key(wp_unslash($_GET['date'])) : '6m';

        // Validate date
        $allowed_dates = array_keys(self::date_options());
        if (!in_array($date, $allowed_dates, true)) {
            $date = '6m';
        }

        $types = [];
        if (!empty($_GET['types'])) {
            foreach ((array) $_GET['types'] as $t) {
                $t = sanitize_key(wp_unslash($t));
                if ($t) $types[] = $t;
            }
        }

        $page = isset($_GET['pg']) ? max(1, (int) $_GET['pg']) : 1;

        return [
            's'     => $s,
            'topic' => $topic,
            'date'  => $date,
            'types' => array_values(array_unique($types)),
            'pg'    => $page,
        ];
    }

    public static function date_cutoff_timestamp($date_key) {
        if ($date_key === 'all') return 0;

        $now = current_time('timestamp');

        switch ($date_key) {
            case '30d':
                return strtotime('-30 days', $now);
            case '12m':
                return strtotime('-12 months', $now);
            case '6m':
            default:
                return strtotime('-6 months', $now);
        }
    }

    private static function unique_ids_preserve_order(array $ids): array {
        $seen = [];
        $out  = [];
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id <= 0) continue;
            if (isset($seen[$id])) continue;
            $seen[$id] = true;
            $out[] = $id;
        }
        return $out;
    }

    private static function topic_tax_query($topic_slug) {
        if (!$topic_slug) return [];

        return [
            [
                'taxonomy' => 'topic-tag',
                'field'    => 'slug',
                'terms'    => [$topic_slug],
            ]
        ];
    }

    /**
     * Normalize types selection:
     * - If none selected => use all supported
     * - If selected => keep only supported
     * - Always allow "page" (it will appear in UI as "Other")
     */
    private static function normalize_types(array $requested_types, array $supported): array {
        $types = array_values(array_filter(array_map('sanitize_key', $requested_types)));

        if (empty($types)) {
            $types = $supported;
        }

        $types = array_values(array_intersect($types, $supported));

        // Ensure 'page' stays searchable if supported (so "Other" works reliably)
        if (in_array('page', $supported, true) && !in_array('page', $types, true)) {
            $types[] = 'page';
        }

        return array_values(array_unique($types));
    }

    public static function run($args = []) {
        $req = self::parse_request();

        $engine   = isset($args['engine']) ? sanitize_key($args['engine']) : 'default';
        $per_page = isset($args['per_page']) ? max(1, (int)$args['per_page']) : 10;

        $supported = self::supported_post_types();

        // Normalize types (pages always included if supported)
        $types = self::normalize_types((array)$req['types'], (array)$supported);

        $max_pool = (int) apply_filters('eco_search_searchwp_pool_size', 1000);
        $ids_in_relevance = [];

        if (class_exists('\SearchWP\Query')) {
            try {
                $query = new \SearchWP\Query(
                    $req['s'],
                    [
                        'engine'    => $engine,
                        'fields'    => 'all',
                        'page'      => 1,
                        'per_page'  => $max_pool,
                        'post_type' => $types,
                    ]
                );

                $results = method_exists($query, 'get_results') ? $query->get_results() : [];

                foreach ($results as $r) {
                    if (is_object($r) && isset($r->ID)) {
                        $ids_in_relevance[] = (int) $r->ID;
                    } elseif (is_numeric($r)) {
                        $ids_in_relevance[] = (int) $r;
                    } elseif (is_array($r) && isset($r['ID'])) {
                        $ids_in_relevance[] = (int) $r['ID'];
                    }
                }
            } catch (\Throwable $e) {
                $ids_in_relevance = [];
            }
        }

        if (!$ids_in_relevance) {
            $fallback_q = new \WP_Query([
                's'                  => $req['s'],
                'post_type'           => $types,
                'post_status'         => 'publish',
                'posts_per_page'      => $max_pool,
                'ignore_sticky_posts' => true,
                'fields'              => 'ids',
            ]);
            $ids_in_relevance = array_map('intval', (array) $fallback_q->posts);
        }

        // de-dupe relevance IDs
        $ids_in_relevance = self::unique_ids_preserve_order($ids_in_relevance);

        $cutoff = self::date_cutoff_timestamp($req['date']);
        $filtered_ids = [];

        if ($ids_in_relevance) {
            $posts = get_posts([
                'post__in'       => $ids_in_relevance,
                'posts_per_page' => -1,
                'post_type'      => $types,
                'post_status'    => 'publish',
                'orderby'        => 'post__in',
                'fields'         => 'all',
                'tax_query'      => self::topic_tax_query($req['topic']),
            ]);

            foreach ($posts as $p) {
                // Published date filter for all types (including events for now)
                // === CHANGE HERE LATER (events start_date) ===
                $ts = strtotime($p->post_date_gmt ? ($p->post_date_gmt . ' GMT') : $p->post_date);

                if ($cutoff && $ts < $cutoff) continue;
                $filtered_ids[] = (int) $p->ID;
            }
        }

        $filtered_ids = self::unique_ids_preserve_order($filtered_ids);

        $total     = count($filtered_ids);
        $max_pages = $per_page ? (int) ceil($total / $per_page) : 1;

        $pg = min(max(1, (int)$req['pg']), max(1, $max_pages));
        $offset   = ($pg - 1) * $per_page;
        $page_ids = array_slice($filtered_ids, $offset, $per_page);

        $page_posts = [];
        if ($page_ids) {
            $q = new \WP_Query([
                'post__in'            => $page_ids,
                'post_type'           => $types,
                'post_status'         => 'publish',
                'posts_per_page'      => count($page_ids),
                'orderby'             => 'post__in',
                'ignore_sticky_posts' => true,
            ]);
            $page_posts = $q->posts;
        }

        return [
            'request'   => $req,
            'engine'    => $engine,
            'per_page'  => $per_page,
            'total'     => $total,
            'max_pages' => $max_pages,
            'page'      => $pg,
            'ids'       => $filtered_ids,
            'posts'     => $page_posts,
            'types'     => $types,
        ];
    }

    /**
     * Build URL for pagination while keeping filters.
     * For global WP search override, always build from home_url('/').
     */
    public static function build_page_url($overrides = []) {
        $req = self::parse_request();
        $merged = array_merge($req, $overrides);

        $base = home_url('/');

        $args = [];

        if ($merged['s'] !== '') {
            $args['s'] = $merged['s'];
        }

        if (!empty($merged['topic'])) {
            $args['topic'] = $merged['topic'];
        }

        // omit default date to keep URLs clean
        if (!empty($merged['date']) && $merged['date'] !== '6m') {
            $args['date'] = $merged['date'];
        }

        // omit pg=1
        $pg = (int) ($merged['pg'] ?? 1);
        if ($pg > 1) {
            $args['pg'] = $pg;
        }

        $url = add_query_arg($args, $base);

        // Keep types only when user specified them
        if (!empty($merged['types'])) {
            foreach ((array) $merged['types'] as $t) {
                $t = sanitize_key($t);
                if ($t) {
                    $url = add_query_arg('types[]', $t, $url);
                }
            }
        }

        return $url;
    }
}
