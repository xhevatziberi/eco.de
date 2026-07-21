<?php
/**
 * Plugin Name: eco Search UI (SearchWP)
 * Description: Toggleable header search UI + custom SearchWP-powered search template with filters.
 * Version: 1.0.4
 * Author: Batlab
 * Text Domain: eco-search
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('ECO_SEARCH_PATH', plugin_dir_path(__FILE__));
define('ECO_SEARCH_URL', plugin_dir_url(__FILE__));
define('ECO_SEARCH_VER', '1.0.4');

// Core loader
require_once ECO_SEARCH_PATH . 'includes/class-eco-search.php';

// Template override loader (IMPORTANT)
require_once ECO_SEARCH_PATH . 'includes/class-eco-search-template.php';

require_once ECO_SEARCH_PATH . 'includes/class-eco-search-settings.php';

function eco_search_load_textdomain() {
    load_plugin_textdomain( 'eco-search', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'eco_search_load_textdomain', 0 );

add_action('plugins_loaded', function () {
    \ECO_Search\Plugin::instance();
    \ECO_Search\Template::init();
    \ECO_Search\Settings::init();
}, 20);
