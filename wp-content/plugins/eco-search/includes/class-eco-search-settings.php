<?php
namespace ECO_Search;

if (!defined('ABSPATH')) exit;

class Settings {

    const OPT_QUICK_TOPICS = 'eco_search_quick_topics'; // array of term IDs in order

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'admin_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_assets']);
    }

    public static function admin_menu() {
        add_options_page(
            __('ECO Search', 'eco-search'),
            __('ECO Search', 'eco-search'),
            'manage_options',
            'eco-search',
            [__CLASS__, 'render_page']
        );
    }

    public static function register_settings() {
        register_setting('eco_search_settings', self::OPT_QUICK_TOPICS, [
            'type'              => 'array',
            'sanitize_callback' => [__CLASS__, 'sanitize_quick_topics'],
            'default'           => [],
        ]);
    }

    public static function sanitize_quick_topics($value) {
        $out = [];
        if (is_array($value)) {
            foreach ($value as $id) {
                $id = (int) $id;
                if ($id > 0) $out[] = $id;
            }
        }
        // unique but keep order
        $seen = [];
        $final = [];
        foreach ($out as $id) {
            if (isset($seen[$id])) continue;
            $seen[$id] = true;
            $final[] = $id;
        }
        return $final;
    }

    public static function enqueue_admin_assets($hook) {
        if ($hook !== 'settings_page_eco-search') return;

        wp_enqueue_script('jquery-ui-sortable');

        wp_add_inline_style('wp-admin', self::admin_css());
        wp_add_inline_script('jquery-ui-sortable', self::admin_js());
    }

    private static function admin_css() {
        return '
        #eco-search-topics-selected {
            margin: 10px 0 0;
            padding: 0;
        }
        #eco-search-topics-selected li {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            margin: 0 0 8px;
            padding: 10px 12px;
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 10px;
            cursor: move;
        }
        #eco-search-topics-selected .eco-handle {
            font-weight:600;
            color:#1d2327;
        }
        #eco-search-topics-selected .eco-remove {
            cursor:pointer;
            color:#b32d2e;
            font-weight:600;
        }
        .eco-search-add-row{
            display:flex;
            gap:10px;
            align-items:center;
            margin-top:12px;
        }
        ';
    }

    private static function admin_js() {
        return '
        (function($){
            function refreshEmptyState(){
                var $list = $("#eco-search-topics-selected");
                $("#eco-search-empty").toggle($list.children("li").length === 0);
            }

            $(function(){
                var $list = $("#eco-search-topics-selected");

                $list.sortable({
                    axis: "y",
                    containment: "parent"
                });

                $list.on("click", ".eco-remove", function(e){
                    e.preventDefault();
                    var $li = $(this).closest("li");
                    var termId = $li.data("term-id");
                    var label  = $li.find(".eco-handle").text();

                    // Add back to select dropdown
                    var $sel = $("#eco-search-topics-add");
                    if ($sel.find("option[value=\'"+termId+"\']").length === 0) {
                        $("<option/>").val(termId).text(label).appendTo($sel);
                    }

                    $li.remove();
                    refreshEmptyState();
                });

                $("#eco-search-topics-add-btn").on("click", function(e){
                    e.preventDefault();

                    var $sel = $("#eco-search-topics-add");
                    var termId = $sel.val();
                    if (!termId) return;

                    var label = $sel.find("option:selected").text();

                    // Prevent duplicates
                    if ($list.find("li[data-term-id=\'"+termId+"\']").length) return;

                    var $li = $("<li/>").attr("data-term-id", termId);
                    $li.append("<span class=\\"eco-handle\\">"+label+"</span>");
                    $li.append("<input type=\\"hidden\\" name=\\"' . esc_js(self::OPT_QUICK_TOPICS) . '[]\\" value=\\""+termId+"\\"/>");
                    $li.append("<a href=\\"#\\" class=\\"eco-remove\\">Remove</a>");
                    $list.append($li);

                    // Remove from select
                    $sel.find("option:selected").remove();
                    $sel.val("");

                    refreshEmptyState();
                });

                refreshEmptyState();
            });
        })(jQuery);
        ';
    }

    public static function get_quick_topics_terms() {
        $ids = get_option(self::OPT_QUICK_TOPICS, []);
        $ids = self::sanitize_quick_topics($ids);

        if (!$ids) return [];

        $terms = get_terms([
            'taxonomy'   => 'topic-tag',
            'hide_empty' => false,
            'include'    => $ids,
        ]);

        if (is_wp_error($terms) || !is_array($terms)) return [];

        // order by saved IDs
        $by_id = [];
        foreach ($terms as $t) $by_id[(int)$t->term_id] = $t;

        $ordered = [];
        foreach ($ids as $id) {
            if (isset($by_id[$id])) $ordered[] = $by_id[$id];
        }

        return $ordered;
    }

    public static function render_page() {
        if (!current_user_can('manage_options')) return;

        $selected_ids = get_option(self::OPT_QUICK_TOPICS, []);
        $selected_ids = self::sanitize_quick_topics($selected_ids);

        $selected_terms = [];
        if ($selected_ids) {
            $selected_terms = self::get_quick_topics_terms();
        }

        // Terms available to add (exclude selected)
        $all_terms = get_terms([
            'taxonomy'   => 'topic-tag',
            'hide_empty' => false,
        ]);
        if (is_wp_error($all_terms) || !is_array($all_terms)) $all_terms = [];

        $available = [];
        $selected_map = array_flip($selected_ids);
        foreach ($all_terms as $t) {
            if (!isset($selected_map[(int)$t->term_id])) {
                $available[] = $t;
            }
        }

        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('ECO Search', 'eco-search'); ?></h1>

            <form method="post" action="options.php">
                <?php
                settings_fields('eco_search_settings');
                ?>

                <h2><?php echo esc_html__('Header quick topics', 'eco-search'); ?></h2>
                <p><?php echo esc_html__('Select and reorder the topics that appear under the header search input.', 'eco-search'); ?></p>

                <p id="eco-search-empty" style="display:none; padding:10px 12px; background:#fff; border:1px dashed #c3c4c7; border-radius:10px;">
                    <?php echo esc_html__('No topics selected yet. Add some below.', 'eco-search'); ?>
                </p>

                <ul id="eco-search-topics-selected">
                    <?php foreach ($selected_terms as $t): ?>
                        <li data-term-id="<?php echo (int)$t->term_id; ?>">
                            <span class="eco-handle"><?php echo esc_html($t->name); ?></span>
                            <input type="hidden" name="<?php echo esc_attr(self::OPT_QUICK_TOPICS); ?>[]" value="<?php echo (int)$t->term_id; ?>" />
                            <a href="#" class="eco-remove">Remove</a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="eco-search-add-row">
                    <select id="eco-search-topics-add">
                        <option value=""><?php echo esc_html__('Add topic…', 'eco-search'); ?></option>
                        <?php foreach ($available as $t): ?>
                            <option value="<?php echo (int)$t->term_id; ?>">
                                <?php echo esc_html($t->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button id="eco-search-topics-add-btn" class="button button-secondary">
                        <?php echo esc_html__('Add', 'eco-search'); ?>
                    </button>
                </div>

                <?php submit_button(__('Save', 'eco-search')); ?>
            </form>
        </div>
        <?php
    }
}
