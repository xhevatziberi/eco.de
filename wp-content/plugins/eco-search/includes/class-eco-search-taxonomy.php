<?php
namespace ECO_Search;

if (!defined('ABSPATH')) exit;

class Taxonomy {

    public static function init() {
        add_action('init', [__CLASS__, 'register'], 11);
        add_action('registered_post_type', [__CLASS__, 'maybe_attach_to_new_post_type'], 10, 2);
    }

    public static function register() {
        $labels = [
            'name'                       => __('Topics', 'eco-search'),
            'singular_name'              => __('Topic', 'eco-search'),
            'search_items'               => __('Search Topics', 'eco-search'),
            'all_items'                  => __('All Topics', 'eco-search'),
            'edit_item'                  => __('Edit Topic', 'eco-search'),
            'update_item'                => __('Update Topic', 'eco-search'),
            'add_new_item'               => __('Add New Topic', 'eco-search'),
            'new_item_name'              => __('New Topic Name', 'eco-search'),
            'menu_name'                  => __('Topics', 'eco-search'),
        ];

        register_taxonomy('topic-tag', [], [
            'hierarchical'          => false,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => false,
            'show_tagcloud'         => false,
            'show_in_rest'          => true,
            'rewrite'               => ['slug' => 'topic'],
            'public'                => true,
        ]);

        self::attach_to_supported_post_types();
    }

    public static function attach_to_supported_post_types() {
        $pts = self::supported_post_types();

        foreach ($pts as $pt) {
            if (post_type_exists($pt)) {
                register_taxonomy_for_object_type('topic-tag', $pt);
            }
        }
    }

    public static function maybe_attach_to_new_post_type($post_type, $args) {
        $pts = self::supported_post_types();
        if (in_array($post_type, $pts, true)) {
            register_taxonomy_for_object_type('topic-tag', $post_type);
        }
    }

    public static function supported_post_types() {
        // Your current + future list:
        // Posts, Pages, Events (event), Podcasts (podcast), Press (press), Tiles (tile),
        // Future: Downloads, Papers, Studies
        return apply_filters('eco_search_supported_post_types', [
            'post',
            'page',
            'event',
            'podcast',
            'press',
            'download',
            'paper',
            'study',
        ]);
    }
}
