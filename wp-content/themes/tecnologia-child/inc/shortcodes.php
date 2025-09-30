<?php

// Shortcode for displaying event date and time. Used in countdown widget.
function eco_event_datetime_shortcode($atts) {
    $post_id = get_the_ID();

    $date = get_field('start_date', $post_id); // Expected: Y-m-d
    $time = get_field('start_time', $post_id); // Expected: H:i

    if (!$date || !$time) {
        return '';
    }

    return esc_html($date . ' ' . $time);
}
add_shortcode('eco_event_datetime', 'eco_event_datetime_shortcode');


// Agenda shortcode
function eco_agenda_shortcode() {
    if (!have_rows('agenda')) {
        return '<p>Keine Agenda verfügbar.</p>';
    }

    ob_start();
    echo '<div class="eco-agenda">';

    while (have_rows('agenda')) {
        the_row();
        $time = get_sub_field('time');
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        $speakers = get_sub_field('speakers');

        echo '<div class="eco-agenda-item">';
        echo '<div class="eco-agenda-time">' . esc_html($time) . '</div>';
        echo '<div class="eco-agenda-content">';
        echo '<div class="eco-agenda-title">' . esc_html($title) . '</div>';

        if (!empty($description)) {
            echo '<div class="eco-agenda-description">' . apply_filters('the_content', $description) . '</div>';
        }

        if (!empty($speakers) && is_array($speakers)) {
            echo '<ul class="eco-agenda-speakers">';
            foreach ($speakers as $speaker) {
                $name = get_the_title($speaker);
                $img = get_the_post_thumbnail_url($speaker, 'thumbnail');
                $position = get_field('position', $speaker->ID);
                $company = get_field('company', $speaker->ID);

                $tooltip_html = '<strong>' . esc_html($name) . '</strong>';
                if ($position || $company) {
                    $tooltip_html .= '<br>';
                    if ($position) $tooltip_html .= esc_html($position);
                    if ($position && $company) $tooltip_html .= ', ';
                    if ($company) $tooltip_html .= esc_html($company);
                }

                if ($img) {
                    echo '<li>';
                    echo '<img src="' . esc_url($img) . '" alt="' . esc_attr($name) . '">';
                    echo '<div class="eco-tooltip">' . $tooltip_html . '</div>';
                    echo '</li>';
                }
            }
            echo '</ul>';
        }



        echo '</div></div>';
    }

    echo '</div>';
    return ob_get_clean();
}
add_shortcode('eco_event_agenda', 'eco_agenda_shortcode');


// Dynamic People shortcode
add_shortcode('eco_dynamic_people', function($atts) {
	$atts = shortcode_atts([
		'acf_field' => '', // ACF relationship field name
		'post_id'   => get_the_ID()
	], $atts);

	if (empty($atts['acf_field'])) return '';

	$acf_value = get_field($atts['acf_field'], $atts['post_id']);
	if (!is_array($acf_value)) return '';

	$ids = array_map(function($item) {
		return is_object($item) ? $item->ID : $item;
	}, $acf_value);

	if (empty($ids)) return '';

	// Use Elementor class directly to reuse the render logic
	$widget = new \ElementorEco\Widgets\People();
	return $widget->render_people_by_ids($ids);
});


// === Event Sponsors Carousel [eco_events_sponsors_carousel] ===
// [eco_events_sponsors_carousel]
add_shortcode('eco_events_sponsors_carousel', function($atts) {
    $atts = shortcode_atts([
        'acf_field'       => 'sponsors',       // ACF relationship field on current post
        'post_id'         => get_the_ID(),     // Or pass another post id
        'slides_to_show'  => 4,
        'space_between'   => 16,               // px
        'autoplay'        => 'true',           // 'true' | 'false'
        'autoplay_delay'  => 2500,             // ms
        'loop'            => 'false',           // 'true' | 'false'
        'arrows'          => 'false',           // 'true' | 'false'
        'dots'            => 'true',          // 'true' | 'false'
        'img_size'        => 'medium_large',   // WP image size for logos
        'nofollow'        => 'true',
        'sponsored'       => 'true',
    ], $atts, 'eco_events_sponsors_carousel');

    $post_id  = (int) $atts['post_id'];
    $field    = sanitize_key($atts['acf_field']);
    $sponsors = function_exists('get_field') ? get_field($field, $post_id) : null;

    if (!is_array($sponsors) || empty($sponsors)) {
        return '';
    }

    // Enqueue assets only when shortcode is used
    wp_enqueue_style('eco-sponsors');
    wp_enqueue_script('eco-sponsors');

    // Build slides
    $slides_html = '';
    foreach ($sponsors as $sponsor_post) {
        $sid = is_object($sponsor_post) ? $sponsor_post->ID : (int) $sponsor_post;
        if (!$sid) { continue; }

        $img_id  = get_post_thumbnail_id($sid);
        $img_src = $img_id ? wp_get_attachment_image_src($img_id, $atts['img_size']) : null;
        $img_tag = $img_src ? sprintf(
            '<img src="%s" alt="%s" loading="lazy" />',
            esc_url($img_src[0]),
            esc_attr(get_the_title($sid))
        ) : sprintf('<div class="eco-sponsor-fallback">%s</div>', esc_html(get_the_title($sid)));

        $website = function_exists('get_field') ? get_field('website', $sid) : '';
        $website = $website ? esc_url($website) : '';

        $rels = ['noopener','noreferrer'];
        if ($atts['nofollow'] === 'true')  { $rels[] = 'nofollow'; }
        if ($atts['sponsored'] === 'true') { $rels[] = 'sponsored'; }
        $rel_attr = implode(' ', $rels);

        $content = $website
            ? sprintf('<a href="%s" target="_blank" rel="%s">%s</a>', $website, esc_attr($rel_attr), $img_tag)
            : $img_tag;

        $slides_html .= '<div class="swiper-slide eco-sponsor-slide">'.$content.'</div>';
    }

    // Unique wrapper
    $uid = 'eco-sponsors-' . wp_generate_uuid4();

    // Data attributes (JS reads these)
    $data = sprintf(
        'data-slides="%d" data-space="%d" data-autoplay="%s" data-delay="%d" data-loop="%s" data-arrows="%s" data-dots="%s"',
        (int)$atts['slides_to_show'],
        (int)$atts['space_between'],
        esc_attr($atts['autoplay']),
        (int)$atts['autoplay_delay'],
        esc_attr($atts['loop']),
        esc_attr($atts['arrows']),
        esc_attr($atts['dots'])
    );

    // Markup with Swiper structure + Fallback grid
    ob_start(); ?>
    <div id="<?php echo esc_attr($uid); ?>" class="eco-sponsors-wrap" <?php echo $data; ?>>
        <div class="swiper">
            <div class="swiper-wrapper">
                <?php echo $slides_html; ?>
            </div>
            <div class="swiper-button-prev" aria-label="Previous"></div>
            <div class="swiper-button-next" aria-label="Next"></div>
            <div class="swiper-pagination" aria-label="Pagination"></div>
        </div>

        <div class="eco-sponsors-fallback">
            <?php
            // Simple grid fallback (same content without swiper wrappers)
            foreach ($sponsors as $sponsor_post) {
                $sid = is_object($sponsor_post) ? $sponsor_post->ID : (int) $sponsor_post;
                if (!$sid) { continue; }
                $img_id  = get_post_thumbnail_id($sid);
                $img_src = $img_id ? wp_get_attachment_image_src($img_id, $atts['img_size']) : null;
                $img_tag = $img_src ? sprintf(
                    '<img src="%s" alt="%s" loading="lazy" />',
                    esc_url($img_src[0]),
                    esc_attr(get_the_title($sid))
                ) : sprintf('<div class="eco-sponsor-fallback">%s</div>', esc_html(get_the_title($sid)));

                $website = function_exists('get_field') ? get_field('website', $sid) : '';
                $website = $website ? esc_url($website) : '';

                $rels = ['noopener','noreferrer'];
                if ($atts['nofollow'] === 'true')  { $rels[] = 'nofollow'; }
                if ($atts['sponsored'] === 'true') { $rels[] = 'sponsored'; }
                $rel_attr = implode(' ', $rels);

                echo '<div class="eco-sponsor-item">';
                echo $website ? sprintf('<a href="%s" target="_blank" rel="%s">%s</a>', $website, esc_attr($rel_attr), $img_tag) : $img_tag;
                echo '</div>';
            }
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
});

// test
add_shortcode('xhevat', function($atts) {
    return '';
});