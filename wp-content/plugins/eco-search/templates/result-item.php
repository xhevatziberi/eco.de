<?php
if (!defined('ABSPATH')) exit;

$post_id = get_the_ID();
$pt      = get_post_type($post_id);

$img = '';
if (has_post_thumbnail($post_id)) {
    $img = get_the_post_thumbnail_url($post_id, 'medium');
}

$date = get_the_date('d.m.Y', $post_id);

$topic_terms = get_the_terms($post_id, 'topic-tag');
$topic_name = '';
if (!is_wp_error($topic_terms) && !empty($topic_terms)) {
    $topic_name = $topic_terms[0]->name;
}

$type_label = '';
$pt_obj = get_post_type_object($pt);
if ($pt_obj && !empty($pt_obj->labels->singular_name)) {
    $type_label = $pt_obj->labels->singular_name;
} else {
    $type_label = strtoupper($pt ?: 'post');
}

$q = isset($_GET['s']) ? trim((string) wp_unslash($_GET['s'])) : '';
$highlight = function($text) use ($q) {
    $q = trim($q);
    if ($q === '' || strlen($q) < 2) return $text;
    $safe = preg_quote($q, '/');
    return preg_replace('/(' . $safe . ')/i', '<mark class="eco-hl">$1</mark>', $text);
};

$title   = get_the_title($post_id);
$excerpt = get_the_excerpt($post_id);
$excerpt = $excerpt ? wp_trim_words($excerpt, 26) : '';

$allowed = [
    'mark' => ['class' => true],
];
?>
<article class="eco-card">
    <a class="eco-card__media" href="<?php the_permalink(); ?>" aria-hidden="true">
        <?php if ($img): ?>
            <img src="<?php echo esc_url($img); ?>" alt="">
        <?php else: ?>
            <span class="eco-card__ph" aria-hidden="true"></span>
        <?php endif; ?>
    </a>

    <div class="eco-card__body">
        <div class="eco-card__meta">
            <span class="eco-pill eco-pill--type"><?php echo esc_html($type_label); ?></span>
            <span class="eco-card__dot" aria-hidden="true"></span>
            <time class="eco-card__date" datetime="<?php echo esc_attr(get_the_date('c', $post_id)); ?>">
                <?php echo esc_html($date); ?>
            </time>
            <?php if ($topic_name): ?>
                <span class="eco-card__dot" aria-hidden="true"></span>
                <span class="eco-pill eco-pill--topic"><?php echo esc_html($topic_name); ?></span>
            <?php endif; ?>
        </div>

        <h3 class="eco-card__title">
            <a href="<?php the_permalink(); ?>">
                <?php echo wp_kses($highlight(esc_html($title)), $allowed); ?>
            </a>
        </h3>

        <?php if ($excerpt): ?>
            <div class="eco-card__excerpt">
                <?php echo wp_kses($highlight(esc_html($excerpt)), $allowed); ?>
            </div>
        <?php endif; ?>
    </div>
</article>
