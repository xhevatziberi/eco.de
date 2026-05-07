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

// [event_slots] – display all event dates/times/locations in single event template
add_shortcode('event_slots', function () {
  if (!is_singular('event')) return '';

  $post_id = get_the_ID();

  // helpers
  $norm_date = function($s){
    if (!$s) return '';
    if (preg_match('/^\d{8}$/', $s)) return substr($s,0,4).'-'.substr($s,4,2).'-'.substr($s,6,2);
    if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $s)) { [$d,$m,$y]=explode('.',$s); return "$y-$m-$d"; }
    return $s;
  };
  $norm_time = function($t){
    if (!$t) return '';
    if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/',$t)) return substr($t,0,5);
    $dt = date_create($t);
    return $dt ? $dt->format('H:i') : '';
  };

  // main (meta fields)
  $main = [[
    'start_date' => $norm_date(get_post_meta($post_id, 'start_date', true)),
    'end_date'   => $norm_date(get_post_meta($post_id, 'end_date', true) ?: get_post_meta($post_id, 'start_date', true)),
    'start_time' => $norm_time(get_post_meta($post_id, 'start_time', true)),
    'end_time'   => $norm_time(get_post_meta($post_id, 'end_time', true)),
    'location'   => get_post_meta($post_id, 'city', true),
  ]];

  // other_dates repeater (ACF)
  $other = [];
  if (function_exists('get_field')) {
    $raw = get_field('other_dates', $post_id);
    if ($raw && is_array($raw)) {
      foreach ($raw as $r) {
        $other[] = [
          'start_date' => $norm_date($r['start_date'] ?? ''),
          'end_date'   => $norm_date($r['end_date'] ?? ($r['start_date'] ?? '')),
          'start_time' => $norm_time($r['start_time'] ?? ''),
          'end_time'   => $norm_time($r['end_time'] ?? ''),
          'location'   => $r['location'] ?? ($r['city'] ?? ''),
        ];
      }
    }
  }

  // merge + sort
  $slots = array_merge($main, $other);
  usort($slots, function($a,$b){
    return strcmp(($a['start_date'] ?? '') . ($a['start_time'] ?? ''), ($b['start_date'] ?? '') . ($b['start_time'] ?? ''));
  });

  if (empty($slots)) return '';

  // render
  ob_start(); ?>
  <div class="event-terms-list" data-count="<?php echo count($slots); ?>">
    <ul class="etl">
      <?php foreach ($slots as $s):
        $sd = $s['start_date'] ? date_i18n('d.m.Y', strtotime($s['start_date'])) : '';
        $ed = $s['end_date'] ? date_i18n('d.m.Y', strtotime($s['end_date'])) : '';
        $st = $s['start_time']; $et = $s['end_time']; $loc = $s['location'];
      ?>
      <li class="etl-item">
        <span class="etl-date">📅 <?php echo esc_html($sd . ($ed && $ed !== $sd ? ' – '.$ed : '')); ?></span>
        <?php if ($st): ?>
        <span class="etl-time">⏰ <?php echo esc_html($st . ($et && $et !== $st ? '–'.$et : '')); ?></span>
        <?php endif; ?>
        <?php if ($loc): ?>
        <span class="etl-loc">📍 <?php echo esc_html($loc); ?></span>
        <?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php return ob_get_clean();
});

// [event_location_block color="white"] – display event venue details in single event template
add_shortcode('event_location_block', function($atts){
  if (!is_singular('event')) return '';

  $post_id = get_the_ID();

    $atts = shortcode_atts([
        'color' => '' // white | black
    ], $atts);

  // read exactly your field keys
  $venue    = get_field('venue', $post_id)          ?: get_post_meta($post_id,'venue',true);
  $street   = get_field('street_1', $post_id)       ?: get_post_meta($post_id,'street_1',true);
  $zip      = get_field('zip_plz', $post_id)        ?: get_post_meta($post_id,'zip_plz',true);
  $city     = get_field('venue_city', $post_id)     ?: get_post_meta($post_id,'venue_city',true);
  $country  = get_field('venue_country', $post_id)  ?: get_post_meta($post_id,'venue_country',true);
  $venueUrl = get_field('venue_url', $post_id)      ?: get_post_meta($post_id,'venue_url',true);

  ob_start(); ?>
  <div class="eco-loc eco-loc--compact eco-loc--<?php echo esc_attr($atts['color']); ?>">
    <?php if ($venue): ?>
      <div class="eco-loc__row">
        <span class="eco-loc__label">Venue</span>
        <span class="eco-loc__val"><?php echo esc_html($venue); ?></span>
      </div>
    <?php endif; ?>

    <?php if ($street): ?>
      <div class="eco-loc__row">
        <span class="eco-loc__label">Street</span>
        <span class="eco-loc__val"><?php echo esc_html($street); ?></span>
      </div>
    <?php endif; ?>

    <?php if ($zip || $city): ?>
      <div class="eco-loc__row">
        <span class="eco-loc__label">City</span>
        <span class="eco-loc__val"><?php echo esc_html(trim($zip.' '.$city)); ?></span>
      </div>
    <?php endif; ?>

    <?php if ($country): ?>
      <div class="eco-loc__row">
        <span class="eco-loc__label">Country</span>
        <span class="eco-loc__val"><?php echo esc_html($country); ?></span>
      </div>
    <?php endif; ?>
  </div>
  <?php
  return ob_get_clean();
});



// test
add_shortcode('xhevat', function($atts) {
    return '';
});


// [eco_downloads] – Download library with optional category filter
//
// Usage:
//   [eco_downloads]                         – all downloads + category filter tabs
//   [eco_downloads category="my-cat-slug"]  – downloads from a specific category (no filter tabs)
//   [eco_downloads orderby="title" order="ASC"]
//
add_shortcode('eco_downloads', function($atts) {
    $atts = shortcode_atts([
        'category' => '',      // taxonomy slug; empty = show all with filter tabs
        'orderby'  => 'date',  // date | title | download_count
        'order'    => 'DESC',  // ASC | DESC
    ], $atts, 'eco_downloads');

    $category = sanitize_text_field($atts['category']);
    $order    = in_array(strtoupper($atts['order']), ['ASC', 'DESC'], true) ? strtoupper($atts['order']) : 'DESC';

    // Map human-friendly orderby to WP_Query args
    $orderby  = 'date';
    $meta_key = '';
    if ($atts['orderby'] === 'title') {
        $orderby = 'title';
    } elseif ($atts['orderby'] === 'download_count') {
        $orderby  = 'meta_value_num';
        $meta_key = '_download_count';
    }

    // Build query
    $query_args = [
        'post_type'      => 'dlm_download',
        'post_status'    => 'publish',
        'numberposts'    => -1,
        'orderby'        => $orderby,
        'order'          => $order,
    ];
    if ($meta_key) {
        $query_args['meta_key'] = $meta_key;
    }
    if ($category) {
        $query_args['tax_query'] = [[
            'taxonomy'         => 'dlm_download_category',
            'field'            => 'slug',
            'terms'            => $category,
            'include_children' => true,
        ]];
    }

    $download_posts = get_posts($query_args);

    if (empty($download_posts)) {
        return '<p class="eco-downloads__empty">Keine Downloads verfügbar.</p>';
    }

    // Build category filter tabs (only when not scoped to a single category)
    $show_filters = empty($category);
    $filter_terms = [];
    if ($show_filters) {
        $all_terms = get_terms([
            'taxonomy'   => 'dlm_download_category',
            'hide_empty' => true,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ]);
        if (!is_wp_error($all_terms)) {
            $filter_terms = $all_terms;
        }
    }

    // Unique ID so multiple instances on the same page each work independently
    $uid = 'eco-downloads-' . wp_unique_id();

    // Enqueue downloads stylesheet (registered in functions.php)
    wp_enqueue_style('eco-downloads');

    ob_start();
    ?>
    <div class="eco-downloads" id="<?php echo esc_attr($uid); ?>">

        <?php if ($show_filters && !empty($filter_terms)): ?>
        <div class="eco-downloads__filters" role="tablist" aria-label="<?php esc_attr_e('Kategorien filtern', 'tecnologia-child'); ?>">
            <button class="eco-downloads__filter eco-downloads__filter--active"
                    data-filter="all" role="tab" aria-selected="true">
                <?php esc_html_e('Alle', 'tecnologia-child'); ?>
            </button>
            <?php foreach ($filter_terms as $term): ?>
            <button class="eco-downloads__filter"
                    data-filter="<?php echo esc_attr($term->slug); ?>"
                    role="tab" aria-selected="false">
                <?php echo esc_html($term->name); ?>
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="eco-downloads__list">
            <?php foreach ($download_posts as $dl_post):
                $id      = $dl_post->ID;
                $excerpt = $dl_post->post_excerpt
                           ? $dl_post->post_excerpt
                           : wp_trim_words(wp_strip_all_tags($dl_post->post_content), 20, '…');

                // Category slugs for JS filtering
                $item_terms = get_the_terms($id, 'dlm_download_category');
                $item_cats  = [];
                $item_names = [];
                if ($item_terms && !is_wp_error($item_terms)) {
                    foreach ($item_terms as $t) {
                        $item_cats[]  = $t->slug;
                        $item_names[] = $t->name;
                    }
                }
                $data_cats = esc_attr(implode(' ', $item_cats));

                // Download count
                $dl_count = (int) get_post_meta($id, '_download_count', true);

                // File size – stored on the most recent published version post
                $version_posts = get_posts([
                    'post_type'      => 'dlm_download_version',
                    'post_parent'    => $id,
                    'post_status'    => 'publish',
                    'numberposts'    => 1,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                    'fields'         => 'ids',
                ]);
                $filesize = '';
                if (!empty($version_posts)) {
                    $raw_size = get_post_meta($version_posts[0], '_filesize', true);
                    if ($raw_size) {
                        $filesize = esc_html($raw_size);
                    }
                }
            ?>
            <div class="eco-downloads__item" data-categories="<?php echo $data_cats; ?>">
                <div class="eco-downloads__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                        <line x1="12" y1="18" x2="12" y2="12"/>
                        <polyline points="9,15 12,18 15,15"/>
                    </svg>
                </div>

                <div class="eco-downloads__info">
                    <h3 class="eco-downloads__title"><?php echo esc_html(get_the_title($id)); ?></h3>

                    <?php if ($excerpt): ?>
                    <p class="eco-downloads__excerpt"><?php echo esc_html($excerpt); ?></p>
                    <?php endif; ?>

                    <div class="eco-downloads__meta">
                        <?php if ($filesize): ?>
                        <span class="eco-downloads__size"><?php echo $filesize; ?></span>
                        <?php endif; ?>
                        <?php if ($dl_count > 0): ?>
                        <span class="eco-downloads__count">
                            <?php echo esc_html(number_format_i18n($dl_count)); ?> Downloads
                        </span>
                        <?php endif; ?>
                        <?php if (!empty($item_names)): ?>
                        <span class="eco-downloads__categories"><?php echo esc_html(implode(', ', $item_names)); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="eco-downloads__action">
                    <?php echo do_shortcode('[download id="' . esc_attr($id) . '"]'); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
    <?php if ($show_filters && !empty($filter_terms)): ?>
    <script>
    (function() {
        var wrap = document.getElementById(<?php echo wp_json_encode($uid); ?>);
        if (!wrap) return;
        var filters = wrap.querySelectorAll('.eco-downloads__filter');
        var items   = wrap.querySelectorAll('.eco-downloads__item');
        filters.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var cat = this.getAttribute('data-filter');
                filters.forEach(function(b) {
                    b.classList.remove('eco-downloads__filter--active');
                    b.setAttribute('aria-selected', 'false');
                });
                this.classList.add('eco-downloads__filter--active');
                this.setAttribute('aria-selected', 'true');
                items.forEach(function(item) {
                    if (cat === 'all') {
                        item.style.display = '';
                    } else {
                        var cats = (item.getAttribute('data-categories') || '').split(' ');
                        item.style.display = cats.indexOf(cat) !== -1 ? '' : 'none';
                    }
                });
            });
        });
    })();
    </script>
    <?php endif;

    return ob_get_clean();
});