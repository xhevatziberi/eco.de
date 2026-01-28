<?php
namespace ECO_Search;

if (!defined('ABSPATH')) exit;

class Template {

    public static function init() {
        // Override ALL search requests (?s=...) with our template
        add_filter('template_include', [__CLASS__, 'template_include'], 99);

        // Optional: prevent canonical redirects messing with query vars
        add_filter('redirect_canonical', [__CLASS__, 'disable_canonical_on_search'], 10, 2);
    }

    public static function template_include($template) {
        if (is_search()) {
            $custom = ECO_SEARCH_PATH . 'templates/search-theme-override.php';
            if (file_exists($custom)) {
                return $custom;
            }
        }
        return $template;
    }

    public static function disable_canonical_on_search($redirect, $requested_url) {
        if (is_search()) {
            return false;
        }
        return $redirect;
    }
}
