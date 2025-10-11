<?php
/**
 * Download iCal for single Event:
 * - visit: https://yoursite.tld/event/some-event/?eco_ics=1
 * - or use the [event_ics_button] shortcode below
 */
add_action('template_redirect', function () {
  if (!is_singular('event') || empty($_GET['eco_ics'])) {
    return;
  }

  $post_id = get_the_ID();
  $title   = html_entity_decode(get_the_title($post_id), ENT_QUOTES);
  $url     = get_permalink($post_id);
  $desc    = wp_strip_all_tags(get_the_excerpt($post_id));
  $site_tz = get_option('timezone_string') ?: 'UTC';
  $site    = parse_url(home_url(), PHP_URL_HOST) ?: 'example.com';

  // Address (your actual ACF keys)
  $venue   = get_field('venue', $post_id)         ?: get_post_meta($post_id,'venue',true);
  $street  = get_field('street_1', $post_id)      ?: get_post_meta($post_id,'street_1',true);
  $zip     = get_field('zip_plz', $post_id)       ?: get_post_meta($post_id,'zip_plz',true);
  $city    = get_field('venue_city', $post_id)    ?: get_post_meta($post_id,'venue_city',true);
  $country = get_field('venue_country', $post_id) ?: get_post_meta($post_id,'venue_country',true);

  $location = trim(implode(', ', array_filter([
    $venue,
    $street,
    trim($zip.' '.$city),
    $country,
  ])));

  // --- helpers --------------------------------------------------------------
  $esc = function ($s) {
    // RFC5545 text escaping
    $s = str_replace('\\', '\\\\', $s);
    $s = str_replace(';',  '\;',  $s);
    $s = str_replace(',',  '\,',  $s);
    $s = str_replace("\r\n", "\n", $s);
    $s = str_replace("\n",  '\\n', $s);
    return $s;
  };
  $to_utc_ical = function ($date, $time, $site_tz) {
    // $date: 'Ymd' or 'Y-m-d' or 'd.m.Y'
    // $time: 'H:i' or 'H:i:s' or '2:00 am'
    if (!$date) return '';
    // normalize date
    if (preg_match('/^\d{8}$/', $date)) {
      $d = substr($date,0,4).'-'.substr($date,4,2).'-'.substr($date,6,2);
    } elseif (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $date)) {
      [$dd,$mm,$yy] = explode('.', $date); $d = "$yy-$mm-$dd";
    } else {
      $d = $date;
    }
    // normalize time
    if ($time) {
      if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time)) {
        $t = substr($time,0,5);
      } else {
        $dt = date_create($time);
        $t  = $dt ? $dt->format('H:i') : '00:00';
      }
    } else {
      $t = '00:00';
    }
    try {
      $tz  = new DateTimeZone($site_tz);
      $dt  = new DateTime("$d $t:00", $tz);
      $dt->setTimezone(new DateTimeZone('UTC'));
      return $dt->format('Ymd\THis\Z');
    } catch (\Throwable $e) {
      return '';
    }
  };

  // build slots: main meta + other_dates (ACF repeater)
  $slots = [];
  $slots[] = [
    'start_date' => get_post_meta($post_id, 'start_date', true),
    'end_date'   => get_post_meta($post_id, 'end_date', true) ?: get_post_meta($post_id, 'start_date', true),
    'start_time' => get_post_meta($post_id, 'start_time', true),
    'end_time'   => get_post_meta($post_id, 'end_time', true),
  ];
  if (function_exists('get_field')) {
    $other = get_field('other_dates', $post_id);
    if (is_array($other)) {
      foreach ($other as $r) {
        $slots[] = [
          'start_date' => $r['start_date'] ?? '',
          'end_date'   => $r['end_date']   ?? ($r['start_date'] ?? ''),
          'start_time' => $r['start_time'] ?? '',
          'end_time'   => $r['end_time']   ?? '',
        ];
      }
    }
  }
  // sort by start
  usort($slots, function($a,$b){
    $ak = ($a['start_date'] ?? '').' '.($a['start_time'] ?? '');
    $bk = ($b['start_date'] ?? '').' '.($b['start_time'] ?? '');
    return strcmp($ak, $bk);
  });

  // compose ICS
  $lines = [];
  $lines[] = 'BEGIN:VCALENDAR';
  $lines[] = 'VERSION:2.0';
  $lines[] = 'CALSCALE:GREGORIAN';
  $lines[] = 'METHOD:PUBLISH';
  $lines[] = 'PRODID:-//eco.de//Events//EN';
  $lines[] = 'X-WR-CALNAME:' . $esc($title);
  $lines[] = 'X-WR-TIMEZONE:' . $site_tz;

  $now_utc = gmdate('Ymd\THis\Z');
  $seq = 0;

  foreach ($slots as $i => $s) {
    if (empty($s['start_date'])) continue;

    $dtstart = $to_utc_ical($s['start_date'], $s['start_time'] ?? '', $site_tz);
    $dtend   = $to_utc_ical($s['end_date'] ?: $s['start_date'], $s['end_time'] ?? '', $site_tz);

    // Fallback: if no end time, add +1h
    if (!$dtend && $dtstart) {
      $tmp = DateTime::createFromFormat('Ymd\THis\Z', $dtstart, new DateTimeZone('UTC'));
      if ($tmp) { $tmp->modify('+1 hour'); $dtend = $tmp->format('Ymd\THis\Z'); }
    }

    $lines[] = 'BEGIN:VEVENT';
    $lines[] = 'UID:' . $post_id . '-' . (++$seq) . '@' . $site;
    $lines[] = 'DTSTAMP:' . $now_utc;
    if ($dtstart) $lines[] = 'DTSTART:' . $dtstart;
    if ($dtend)   $lines[] = 'DTEND:'   . $dtend;
    $lines[] = 'SUMMARY:' . $esc($title);
    if ($location) $lines[] = 'LOCATION:' . $esc($location);
    $lines[] = 'URL:' . esc_url_raw($url);
    if ($desc) $lines[] = 'DESCRIPTION:' . $esc($desc);
    $lines[] = 'END:VEVENT';
  }

  $lines[] = 'END:VCALENDAR';
  $ics = implode("\r\n", $lines) . "\r\n";

  // output
  nocache_headers();
  header('Content-Type: text/calendar; charset=utf-8');
  header('Content-Disposition: attachment; filename="'.sanitize_title($title).'.ics"');
  echo $ics;
  exit;
});

// [event_ics_button label="Download iCal" class="btn btn-light"]
add_shortcode('event_ics_button', function($atts){
  if (!is_singular('event')) return '';
  $a = shortcode_atts([
    'label' => 'Download iCal',
    'class' => 'eco-btn',
  ], $atts, 'event_ics_button');
  $href = add_query_arg('eco_ics','1', get_permalink());
  return '<a class="'.esc_attr($a['class']).'" href="'.esc_url($href).'">'.esc_html($a['label']).'</a>';
});

// [event_ics_url] or [event_ics_url id="123"]
add_shortcode('event_ics_url', function($atts){
  $a = shortcode_atts([
    'id' => '', // optional: pass a post ID when used in loops
  ], $atts, 'event_ics_url');

  $post_id = $a['id'] !== '' ? intval($a['id']) : (is_singular('event') ? get_the_ID() : 0);
  if (!$post_id) return '';

  $url = add_query_arg('eco_ics','1', get_permalink($post_id));
  return esc_url($url); // return plain URL for Dynamic Tags
});
