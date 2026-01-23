<?php
namespace ECO_Search;

if (!defined('ABSPATH')) exit;

class Assets {

    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue'], 999);
    }

    public static function enqueue() {
        wp_register_style(
            'eco-search-ui',
            ECO_SEARCH_URL . 'assets/css/eco-search.css',
            [],
            ECO_SEARCH_VER
        );

        wp_register_script(
            'eco-search-ui',
            ECO_SEARCH_URL . 'assets/js/eco-search.js',
            [],
            ECO_SEARCH_VER,
            true
        );

        wp_enqueue_style('eco-search-ui');
        wp_enqueue_script('eco-search-ui');

        wp_localize_script('eco-search-ui', 'EcoSearch', [
            'containerId' => 'search-container',
        ]);

        wp_add_inline_style('eco-search-ui', '.eco-search-scope{ }');
    }
}
