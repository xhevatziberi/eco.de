<?php
namespace ECO_Search;

if (!defined('ABSPATH')) exit;

class Shortcodes {

    public static function init() {
        add_shortcode('eco_search_toggle', [__CLASS__, 'toggle_shortcode']);
        add_shortcode('eco_search_bar', [__CLASS__, 'search_bar_shortcode']);   // NEW
        add_shortcode('eco_search_page', [__CLASS__, 'search_page_shortcode']); // RENAMED
        // Back-compat (optional)
        add_shortcode('eco_search', [__CLASS__, 'search_page_shortcode']);
    }

    public static function toggle_shortcode($atts = []) {
        $atts = shortcode_atts([
            'target' => 'eco-searchbar', // default to header bar
            'class'  => '',
            'label'  => 'Search',
        ], $atts, 'eco_search_toggle');

        $target = sanitize_html_class($atts['target']);
        $class  = sanitize_html_class($atts['class']);

        ob_start(); ?>
        <button
            type="button"
            class="eco-search-toggle <?php echo esc_attr($class); ?>"
            aria-label="<?php echo esc_attr($atts['label']); ?>"
            aria-controls="<?php echo esc_attr($target); ?>"
            aria-expanded="false"
            data-eco-search-toggle="1"
            data-eco-search-target="<?php echo esc_attr($target); ?>"
        >
            <span class="eco-search-ico eco-search-ico--search" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="22" height="22">
                    <path d="M10.5 3a7.5 7.5 0 105.02 13.09l3.2 3.2a1 1 0 001.42-1.41l-3.2-3.2A7.5 7.5 0 0010.5 3zm0 2a5.5 5.5 0 110 11 5.5 5.5 0 010-11z"></path>
                </svg>
            </span>
            <span class="eco-search-ico eco-search-ico--close" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="22" height="22">
                    <path d="M18.3 5.7a1 1 0 00-1.4 0L12 10.6 7.1 5.7a1 1 0 10-1.4 1.4l4.9 4.9-4.9 4.9a1 1 0 101.4 1.4l4.9-4.9 4.9 4.9a1 1 0 001.4-1.4L13.4 12l4.9-4.9a1 1 0 000-1.4z"></path>
                </svg>
            </span>
        </button>
        <?php
        return ob_get_clean();
    }

    /**
     * Header bar: ONLY keyword input. Submits to the Search Page.
     * Usage:
     * [eco_search_bar id="eco-searchbar" search_page="/uber-eco/"]
     */
    public static function search_bar_shortcode($atts = []) {
        $atts = shortcode_atts([
            'id'         => 'eco-searchbar',
            'search_page'=> '', // slug or full URL or "/uber-eco/"
            'placeholder'=> 'Keyword',
        ], $atts, 'eco_search_bar');

        $id = sanitize_html_class($atts['id']);

        // Determine the search page URL
        $action = '';
        $raw = trim((string)$atts['search_page']);
        if ($raw) {
            // allow "/uber-eco/" or full URL
            $action = (strpos($raw, 'http') === 0) ? $raw : home_url($raw);
        } else {
            // fallback: WP search (not recommended for your custom page)
            $action = home_url('/');
        }

        $s = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';

        ob_start(); ?>
        <div id="<?php echo esc_attr($id); ?>" class="eco-searchbar" data-eco-search-container="1" aria-hidden="true">
            <div class="eco-searchbar__inner">
                <form class="eco-searchbar__form" method="get" action="<?php echo esc_url($action); ?>">
                    <label class="eco-sr-only" for="eco-searchbar-s">Keyword</label>
                    <input
                        id="eco-searchbar-s"
                        class="eco-searchbar__input"
                        type="text"
                        name="s"
                        value="<?php echo esc_attr($s); ?>"
                        placeholder="<?php echo esc_attr($atts['placeholder']); ?>"
                        autocomplete="off"
                    />
                    <button type="submit" class="eco-searchbar__btn">find</button>
                </form>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Full search page (filters + results).
     * Put this shortcode on your WP page /uber-eco/
     */
    public static function search_page_shortcode($atts = []) {
        $atts = shortcode_atts([
            'engine'   => 'default',
            'per_page' => 10,
            'title'    => '',
        ], $atts, 'eco_search_page');

        $engine   = sanitize_key($atts['engine']);
        $per_page = max(1, (int)$atts['per_page']);

        $data = Search::run([
            'engine'   => $engine,
            'per_page' => $per_page,
        ]);

        $template = ECO_SEARCH_PATH . 'templates/search-wrap.php';

        ob_start();
        include $template;
        return ob_get_clean();
    }
}
