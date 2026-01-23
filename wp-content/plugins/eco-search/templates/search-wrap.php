<?php
if (!defined('ABSPATH')) exit;

use ECO_Search\Search;

$req         = $data['request'];
$labels      = Search::content_type_labels();
$date_opts   = Search::date_options();

$topics = get_terms([
    'taxonomy'   => 'topic-tag',
    'hide_empty' => false,
]);

// Submit to this page (your dedicated search page)
$action_url = get_permalink(get_queried_object_id());

// Helpers
$human_label = function($pt) use ($labels) {
    return $labels[$pt] ?? ucfirst($pt);
};

// Group posts by post_type while preserving their original (relevance) order
$grouped = [];
foreach ((array) $data['posts'] as $p) {
    $pt = get_post_type($p);
    if (!$pt) $pt = 'post';
    if (!isset($grouped[$pt])) $grouped[$pt] = [];
    $grouped[$pt][] = $p;
}

// Sort groups by your preferred order (based on labels array order)
$order = array_keys($labels);
uksort($grouped, function($a, $b) use ($order) {
    $ia = array_search($a, $order, true);
    $ib = array_search($b, $order, true);
    $ia = ($ia === false) ? 999 : $ia;
    $ib = ($ib === false) ? 999 : $ib;
    return $ia <=> $ib;
});
?>

<div class="eco-search-page eco-search-scope">
    <div class="eco-search__inner">

        <form class="eco-search__form" method="get" action="<?php echo esc_url($action_url); ?>">
            <div class="eco-search__row eco-search__row--keyword">
                <label class="eco-search__label" for="eco-search-s">Keyword</label>
                <input
                    id="eco-search-s"
                    class="eco-search__input"
                    type="text"
                    name="s"
                    value="<?php echo esc_attr($req['s']); ?>"
                    placeholder="Keyword"
                    autocomplete="off"
                />
            </div>

            <div class="eco-search__filters">
                <div class="eco-search__filter">
                    <label class="eco-search__label" for="eco-search-topic">Topics</label>
                    <select id="eco-search-topic" class="eco-search__select" name="topic">
                        <option value=""><?php echo esc_html__('All Topics', 'eco-search'); ?></option>
                        <?php foreach ($topics as $t): ?>
                            <option value="<?php echo esc_attr($t->slug); ?>" <?php selected($req['topic'], $t->slug); ?>>
                                <?php echo esc_html($t->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="eco-search__filter">
                    <label class="eco-search__label" for="eco-search-date">Date</label>
                    <select id="eco-search-date" class="eco-search__select" name="date">
                        <?php foreach ($date_opts as $k => $v): ?>
                            <option value="<?php echo esc_attr($k); ?>" <?php selected($req['date'], $k); ?>>
                                <?php echo esc_html($v); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="eco-search__filter eco-search__filter--types">
                    <span class="eco-search__label">Content</span>
                    <div class="eco-search__chips">
                        <?php foreach ($labels as $pt => $pt_label): ?>
                            <?php $checked = in_array($pt, (array)$req['types'], true); ?>
                            <label class="eco-search__chip">
                                <input type="checkbox" name="types[]" value="<?php echo esc_attr($pt); ?>" <?php checked($checked); ?>>
                                <span><?php echo esc_html($pt_label); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="eco-search__filter eco-search__filter--submit">
                    <button type="submit" class="eco-search__btn">find</button>
                </div>
            </div>
        </form>

        <div class="eco-search__meta">
            <?php if (!empty($req['s'])): ?>
                <div class="eco-search__meta-line">
                    <span class="eco-search__meta-title">Search results for</span>
                    <span class="eco-search__meta-keyword"><?php echo esc_html($req['s']); ?></span>
                </div>
            <?php endif; ?>
            <div class="eco-search__meta-line eco-search__meta-line--count">
                <?php echo esc_html($data['total']); ?> results
            </div>
        </div>

        <div class="eco-search__results">
            <?php if (!empty($data['posts'])): ?>

                <?php foreach ($grouped as $pt => $posts): ?>
                    <section class="eco-results-group">
                        <header class="eco-results-group__head">
                            <h3 class="eco-results-group__title">
                                <?php echo esc_html($human_label($pt)); ?>
                            </h3>
                            <div class="eco-results-group__count">
                                <?php echo (int) count($posts); ?>
                            </div>
                        </header>

                        <div class="eco-results-group__list">
                            <?php
                            global $post;
                            foreach ($posts as $p) {
                                $post = $p;
                                setup_postdata($post);
                                include ECO_SEARCH_PATH . 'templates/result-item.php';
                            }
                            wp_reset_postdata();
                            ?>
                        </div>
                    </section>
                <?php endforeach; ?>

            <?php else: ?>
                <div class="eco-search__empty">
                    No results found.
                </div>
            <?php endif; ?>
        </div>

        <?php if ($data['max_pages'] > 1): ?>
            <div class="eco-search__pager">
                <?php
                $current = (int)$data['page'];
                $max = (int)$data['max_pages'];

                // Simple pager with prev/next and window
                $prev = max(1, $current - 1);
                $next = min($max, $current + 1);

                $win = 3;
                $start = max(1, $current - $win);
                $end   = min($max, $current + $win);
                ?>

                <a class="eco-search__page eco-search__page--nav <?php echo ($current === 1) ? 'is-disabled' : ''; ?>"
                   href="<?php echo esc_url(Search::build_page_url(['pg' => $prev])); ?>" aria-disabled="<?php echo $current===1?'true':'false'; ?>">
                    Prev
                </a>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <a class="eco-search__page <?php echo ($i === $current) ? 'is-active' : ''; ?>"
                       href="<?php echo esc_url(Search::build_page_url(['pg' => $i])); ?>">
                        <?php echo (int)$i; ?>
                    </a>
                <?php endfor; ?>

                <a class="eco-search__page eco-search__page--nav <?php echo ($current === $max) ? 'is-disabled' : ''; ?>"
                   href="<?php echo esc_url(Search::build_page_url(['pg' => $next])); ?>" aria-disabled="<?php echo $current===$max?'true':'false'; ?>">
                    Next
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>
