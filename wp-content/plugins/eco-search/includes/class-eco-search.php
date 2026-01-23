<?php
namespace ECO_Search;

if (!defined('ABSPATH')) exit;

final class Plugin {
    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        require_once ECO_SEARCH_PATH . 'includes/class-eco-search-assets.php';
        require_once ECO_SEARCH_PATH . 'includes/class-eco-search-taxonomy.php';
        require_once ECO_SEARCH_PATH . 'includes/class-eco-search-search.php';
        require_once ECO_SEARCH_PATH . 'includes/class-eco-search-shortcodes.php';

        Assets::init();
        Taxonomy::init();
        Search::init();
        Shortcodes::init();
    }
}
