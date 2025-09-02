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
