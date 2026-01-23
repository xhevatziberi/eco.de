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

    public static function content_type_labels() {
        return apply_filters('eco_search_content_type_labels', [
            'post'     => __('News', 'eco-search'),
            'page'     => __('Pages', 'eco-search'),
            'event'    => __('Events', 'eco-search'),
            'podcast'  => __('Podcasts', 'eco-search'),
            'press'    => __('Press', 'eco-search'),
            'tile'     => __('Tiles', 'eco-search'),
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
        $s = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
        $topic = isset($_GET['topic']) ? sanitize_text_field(wp_unslash($_GET['topic'])) : '';
        $date = isset($_GET['date']) ? sanitize_text_field(wp_unslash($_GET['date'])) : '6m';

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
            'topic' => $topic, // topic-tag term slug
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

    /**
     * IMPORTANT: preserves relevance order while removing duplicates
     */
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
     * Main runner (Approach 2 with SearchWP\Query)
     */
    public static function run($args = []) {
        $req = self::parse_request();

        $engine   = isset($args['engine']) ? sanitize_key($args['engine']) : 'default';
        $per_page = isset($args['per_page']) ? max(1, (int)$args['per_page']) : 10;

        $supported = self::supported_post_types();

        // If user didn't tick any types -> search all supported
        $types = $req['types'] ?: $supported;

        // Keep only supported types
        $types = array_values(array_intersect($types, $supported));

        // 1) Get SearchWP results (IDs in relevance order)
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
                        // SearchWP may or may not fully enforce this; we enforce later strictly
                        'post_type' => $types,
                    ]
                );

                $results = method_exists($query, 'get_results') ? $query->get_results() : [];

                foreach ($results as $r) {
                    // Your var_dump shows WP_Post objects
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

        // Fallback if SearchWP unavailable/failed
        if (!$ids_in_relevance) {
            $fallback_q = new \WP_Query([
                's'                   => $req['s'],
                'post_type'            => $types,
                'post_status'          => 'publish',
                'posts_per_page'       => $max_pool,
                'ignore_sticky_posts'  => true,
                'fields'               => 'ids',
            ]);
            $ids_in_relevance = array_map('intval', (array) $fallback_q->posts);
        }

        // ✅ CRITICAL FIX: remove duplicates while keeping relevance order
        $ids_in_relevance = self::unique_ids_preserve_order($ids_in_relevance);

        // 2) Apply filters strictly: topic-tag, date, post type
        $cutoff = self::date_cutoff_timestamp($req['date']);
        $filtered_ids = [];

        if ($ids_in_relevance) {
            // Get posts matching IDs + post types + topic filter
            // ORDER BY post__in keeps the SearchWP relevance order
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
                // Date filter based on PUBLISHED date for all types (including events for now)
                // === CHANGE HERE LATER (events start_date) ===
                // If you later want event date start:
                // if ($p->post_type === 'event') {
                //   $start = get_field('start_date', $p->ID); // example (ACF)
                //   $ts = $start ? strtotime($start) : strtotime($p->post_date_gmt . ' GMT');
                // } else {
                //   $ts = strtotime($p->post_date_gmt ? $p->post_date_gmt . ' GMT' : $p->post_date);
                // }

                $ts = strtotime($p->post_date_gmt ? ($p->post_date_gmt . ' GMT') : $p->post_date);

                if ($cutoff && $ts < $cutoff) {
                    continue;
                }

                $filtered_ids[] = (int) $p->ID;
            }
        }

        // ✅ Also dedupe filtered list (extra safety)
        $filtered_ids = self::unique_ids_preserve_order($filtered_ids);

        $total     = count($filtered_ids);
        $max_pages = $per_page ? (int) ceil($total / $per_page) : 1;

        // 3) Paginate IDs
        $pg = min(max(1, (int)$req['pg']), max(1, $max_pages));
        $offset = ($pg - 1) * $per_page;
        $page_ids = array_slice($filtered_ids, $offset, $per_page);

        // 4) Fetch posts for the current page preserving order
        $page_posts = [];
        if ($page_ids) {
            $q = new \WP_Query([
                'post__in'             => $page_ids,
                'post_type'            => $types,
                'post_status'          => 'publish',
                'posts_per_page'       => count($page_ids),
                'orderby'              => 'post__in',
                'ignore_sticky_posts'  => true,
            ]);
            $page_posts = $q->posts;
        }

        return [
            'request'    => $req,
            'engine'     => $engine,
            'per_page'   => $per_page,
            'total'      => $total,
            'max_pages'  => $max_pages,
            'page'       => $pg,
            'ids'        => $filtered_ids,
            'posts'      => $page_posts,
            'types'      => $types,
        ];
    }

    /**
     * Build URL for pagination while keeping filters.
     * NOTE: This uses the current page URL (good for your dedicated search page).
     */
    public static function build_page_url($overrides = []) {
        $req = self::parse_request();
        $merged = array_merge($req, $overrides);

        $q = [
            's'     => $merged['s'],
            'topic' => $merged['topic'],
            'date'  => $merged['date'],
            'pg'    => (int) $merged['pg'],
        ];

        $base = remove_query_arg(['s','topic','date','pg','types'], self::current_url());
        $url = add_query_arg($q, $base);

        // Add types[] as repeated query vars
        if (!empty($merged['types'])) {
            foreach ((array)$merged['types'] as $t) {
                $url = add_query_arg('types[]', $t, $url);
            }
        }

        return $url;
    }

    private static function current_url() {
        $scheme = is_ssl() ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $uri  = $_SERVER['REQUEST_URI'] ?? '';
        return esc_url_raw($scheme . '://' . $host . $uri);
    }
}
