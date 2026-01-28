<?php
/**
 * Plugin Name: ECO Search UI (SearchWP)
 * Description: Toggleable header search UI + custom SearchWP-powered search template with filters.
 * Version: 1.0.1
 * Author: Batlab
 */

if (!defined('ABSPATH')) exit;

define('ECO_SEARCH_PATH', plugin_dir_path(__FILE__));
define('ECO_SEARCH_URL', plugin_dir_url(__FILE__));
define('ECO_SEARCH_VER', '1.0.1');

// Core loader
require_once ECO_SEARCH_PATH . 'includes/class-eco-search.php';

// Template override loader (IMPORTANT)
require_once ECO_SEARCH_PATH . 'includes/class-eco-search-template.php';

add_action('plugins_loaded', function () {
    \ECO_Search\Plugin::instance();
    \ECO_Search\Template::init();
}, 20);
