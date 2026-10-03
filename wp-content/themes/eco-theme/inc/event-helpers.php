<?php
/**
 * Event helper functions for eco-theme.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function eco_event_get_field( string $field, $post_id = null, $default = null ) {
	$post_id = $post_id ?: get_the_ID();

	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $field, $post_id );
	} else {
		$value = get_post_meta( $post_id, $field, true );
	}

	return ( $value !== null && $value !== '' && $value !== false ) ? $value : $default;
}

function eco_event_get_boolean_field( string $field, $post_id = null, bool $default = false ): bool {
	$post_id = $post_id ?: get_the_ID();

	if ( metadata_exists( 'post', $post_id, $field ) ) {
		return (bool) get_post_meta( $post_id, $field, true );
	}

	if ( function_exists( 'get_field_object' ) ) {
		$field_object = get_field_object( $field, $post_id, false, false );

		if ( is_array( $field_object ) && array_key_exists( 'default_value', $field_object ) ) {
			return (bool) $field_object['default_value'];
		}
	}

	return $default;
}

function eco_event_is_valid_hex( $color ): bool {
	return is_string( $color ) && (bool) preg_match( '/^#([A-Fa-f0-9]{3}){1,2}$/', trim( $color ) );
}

function eco_event_normalize_hex( $color ): string {
	if ( ! eco_event_is_valid_hex( $color ) ) {
		return '';
	}

	$color = strtolower( trim( $color ) );

	if ( strlen( $color ) === 4 ) {
		$color = sprintf(
			'#%1$s%1$s%2$s%2$s%3$s%3$s',
			$color[1],
			$color[2],
			$color[3]
		);
	}

	return $color;
}

function eco_event_get_default_color( $layout = 'small' ): string {
	return 'big' === $layout ? '#e2001a' : '#c2cf00';
}

function eco_event_get_color( $post_id = null, $layout = null ): string {
	$post_id = $post_id ?: get_the_ID();
	$color   = eco_event_normalize_hex( eco_event_get_field( 'event_color', $post_id, '' ) );

	if ( $color ) {
		return $color;
	}

	if ( null === $layout ) {
		$layout = eco_event_get_field( 'event_layout', $post_id, 'small' );
	}

	return eco_event_get_default_color( 'big' === $layout ? 'big' : 'small' );
}

function eco_event_get_relative_luminance( string $color ): float {
	$color = eco_event_normalize_hex( $color );

	if ( ! $color ) {
		return 0.0;
	}

	$channels = [
		hexdec( substr( $color, 1, 2 ) ) / 255,
		hexdec( substr( $color, 3, 2 ) ) / 255,
		hexdec( substr( $color, 5, 2 ) ) / 255,
	];

	foreach ( $channels as &$channel ) {
		$channel = $channel <= 0.04045
			? $channel / 12.92
			: pow( ( $channel + 0.055 ) / 1.055, 2.4 );
	}
	unset( $channel );

	return ( 0.2126 * $channels[0] ) + ( 0.7152 * $channels[1] ) + ( 0.0722 * $channels[2] );
}

function eco_event_get_contrast_ratio( string $foreground, string $background ): float {
	$foreground_luminance = eco_event_get_relative_luminance( $foreground );
	$background_luminance = eco_event_get_relative_luminance( $background );

	$lighter = max( $foreground_luminance, $background_luminance );
	$darker  = min( $foreground_luminance, $background_luminance );

	return ( $lighter + 0.05 ) / ( $darker + 0.05 );
}

function eco_event_get_contrast_color( $post_id = null, $accent_color = '' ): string {
	$post_id = $post_id ?: get_the_ID();
	$choice  = eco_event_get_field( 'event_content_color', $post_id, 'automatic' );

	if ( 'light' === $choice ) {
		return '#ffffff';
	}

	if ( 'dark' === $choice ) {
		return '#111111';
	}

	$accent_color = eco_event_normalize_hex( $accent_color ?: eco_event_get_color( $post_id ) );

	$dark  = '#111111';
	$light = '#ffffff';

	return eco_event_get_contrast_ratio( $light, $accent_color ) >= eco_event_get_contrast_ratio( $dark, $accent_color )
		? $light
		: $dark;
}

function eco_event_is_members_only( $post_id = null ): bool {
	$post_id = $post_id ?: get_the_ID();

	return (bool) eco_event_get_field( 'members_only', $post_id, false );
}

function eco_event_get_members_only_label(): string {
	return __( 'Members Only', 'eco-theme' );
}

function eco_event_get_image_url( string $field = 'hero_image', $post_id = null, string $size = 'large' ): string {
	$post_id = $post_id ?: get_the_ID();
	$image   = eco_event_get_field( $field, $post_id, null );

	if ( is_array( $image ) ) {
		if ( ! empty( $image['ID'] ) ) {
			$url = wp_get_attachment_image_url( (int) $image['ID'], $size );
			return $url ?: '';
		}

		if ( ! empty( $image['url'] ) ) {
			return esc_url_raw( $image['url'] );
		}
	}

	if ( is_numeric( $image ) ) {
		$url = wp_get_attachment_image_url( (int) $image, $size );
		return $url ?: '';
	}

	if ( is_string( $image ) && filter_var( $image, FILTER_VALIDATE_URL ) ) {
		return esc_url_raw( $image );
	}

	if ( $field === 'hero_image' ) {
		$url = eco_event_get_image_url( 'card_image', $post_id, $size );
		if ( $url ) {
			return $url;
		}
	}

	return get_the_post_thumbnail_url( $post_id, $size ) ?: '';
}

function eco_event_parse_datetime( $date, $time = '', $end_of_day = false ): ?DateTimeImmutable {
	if ( empty( $date ) ) {
		return null;
	}

	$date = preg_replace( '/[^0-9]/', '', (string) $date );

	if ( strlen( $date ) !== 8 ) {
		return null;
	}

	$time = trim( (string) $time );

	if ( $time === '' ) {
		$time = $end_of_day ? '23:59' : '00:00';
	}

	$timezone = wp_timezone();
	$dt       = DateTimeImmutable::createFromFormat( 'Ymd H:i', $date . ' ' . $time, $timezone );

	return $dt ?: null;
}

function eco_event_get_occurrences( $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();
	$items   = [];

	$start_date = eco_event_get_field( 'start_date', $post_id, '' );
	$start_time = eco_event_get_field( 'start_time', $post_id, '' );
	$end_date   = eco_event_get_field( 'end_date', $post_id, '' ) ?: $start_date;
	$end_time   = eco_event_get_field( 'end_time', $post_id, '' );

	if ( $start_date ) {
		$items[] = [
			'label'      => '',
			'start_date' => $start_date,
			'start_time' => $start_time,
			'end_date'   => $end_date,
			'end_time'   => $end_time,
		];
	}

	$other_dates = eco_event_get_field( 'other_dates', $post_id, [] );

	if ( is_array( $other_dates ) ) {
		foreach ( $other_dates as $row ) {
			if ( empty( $row['start_date'] ) ) {
				continue;
			}

			$items[] = [
				'label'      => $row['date_label'] ?? '',
				'start_date' => $row['start_date'],
				'start_time' => $row['start_time'] ?? '',
				'end_date'   => ! empty( $row['end_date'] ) ? $row['end_date'] : $row['start_date'],
				'end_time'   => $row['end_time'] ?? '',
			];
		}
	}

	foreach ( $items as &$item ) {
		$item['start'] = eco_event_parse_datetime( $item['start_date'], $item['start_time'] );
		$item['end']   = eco_event_parse_datetime( $item['end_date'], $item['end_time'], true );
	}

	unset( $item );

	usort(
		$items,
		static function ( $a, $b ) {
			$a_time = $a['start'] instanceof DateTimeInterface ? $a['start']->getTimestamp() : 0;
			$b_time = $b['start'] instanceof DateTimeInterface ? $b['start']->getTimestamp() : 0;
			return $a_time <=> $b_time;
		}
	);

	return $items;
}

function eco_event_is_past( $post_id = null ): bool {
	$occurrences = eco_event_get_occurrences( $post_id );

	if ( empty( $occurrences ) ) {
		return false;
	}

	$now = new DateTimeImmutable( 'now', wp_timezone() );

	foreach ( $occurrences as $occurrence ) {
		$end = $occurrence['end'] ?? null;

		if ( $end instanceof DateTimeInterface && $end >= $now ) {
			return false;
		}
	}

	return true;
}

function eco_event_format_date( $date ): string {
	$dt = eco_event_parse_datetime( $date );

	if ( ! $dt ) {
		return '';
	}

	return wp_date( 'd. F Y', $dt->getTimestamp(), wp_timezone() );
}

function eco_event_format_time( $time ): string {
	$time = trim( (string) $time );
	return $time !== '' ? $time : '';
}

function eco_event_format_occurrence_date( array $occurrence ): string {
	$start_date = eco_event_format_date( $occurrence['start_date'] ?? '' );
	$end_date   = eco_event_format_date( $occurrence['end_date'] ?? ( $occurrence['start_date'] ?? '' ) );

	if ( $start_date && $end_date && $start_date !== $end_date ) {
		return sprintf( '%s – %s', $start_date, $end_date );
	}

	return $start_date;
}

function eco_event_format_occurrence_time( array $occurrence, $post_id = null ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( eco_event_get_boolean_field( 'all_day_event', $post_id, false ) ) {
		return '';
	}

	$start_time = eco_event_format_time( $occurrence['start_time'] ?? '' );
	$end_time   = eco_event_format_time( $occurrence['end_time'] ?? '' );

	if ( $start_time && $end_time ) {
		return sprintf( '%s – %s', $start_time, $end_time );
	}

	return $start_time;
}

function eco_event_get_occurrence_display_items( $post_id = null ): array {
	$post_id     = $post_id ?: get_the_ID();
	$occurrences = eco_event_get_occurrences( $post_id );
	$items       = [];

	foreach ( $occurrences as $index => $occurrence ) {
		$items[] = [
			'label'      => trim( (string) ( $occurrence['label'] ?? '' ) ),
			'date'       => eco_event_format_occurrence_date( $occurrence ),
			'time'       => eco_event_format_occurrence_time( $occurrence, $post_id ),
			'is_primary' => 0 === $index,
		];
	}

	return $items;
}

function eco_event_get_date_line( $post_id = null ): string {
	$occurrences = eco_event_get_occurrences( $post_id );

	if ( empty( $occurrences ) ) {
		return '';
	}

	$first = $occurrences[0];
	$last  = $occurrences[ count( $occurrences ) - 1 ];

	$first_date = eco_event_format_date( $first['start_date'] ?? '' );
	$last_date  = eco_event_format_date( $last['end_date'] ?? ( $last['start_date'] ?? '' ) );

	if ( count( $occurrences ) > 1 && $first_date && $last_date && $first_date !== $last_date ) {
		return sprintf( '%s – %s', $first_date, $last_date );
	}

	$start_time = eco_event_format_time( $first['start_time'] ?? '' );
	$end_time   = eco_event_format_time( $first['end_time'] ?? '' );
	$time_line  = '';

	if ( $start_time && $end_time ) {
		$time_line = sprintf( '%s – %s', $start_time, $end_time );
	} elseif ( $start_time ) {
		$time_line = $start_time;
	}

	return trim( $first_date . ( $time_line ? ', ' . $time_line : '' ) );
}

function eco_event_get_location_place( $post_id = null ): string {
	$post_id       = $post_id ?: get_the_ID();
	$location_name = trim( (string) eco_event_get_field( 'location_name', $post_id, '' ) );
	$city          = trim( (string) eco_event_get_field( 'city', $post_id, '' ) );

	if ( $location_name && $city ) {
		return sprintf( '%s, %s', $location_name, $city );
	}

	return $location_name ?: $city;
}

function eco_event_get_location_line( $post_id = null ): string {
	$post_id = $post_id ?: get_the_ID();
	$mode    = eco_event_get_field( 'event_mode', $post_id, 'onsite' );
	$place   = eco_event_get_location_place( $post_id );

	if ( 'online' === $mode ) {
		return __( 'Online', 'eco-theme' );
	}

	if ( 'hybrid' === $mode ) {
		return $place ? sprintf( '%s + %s', __( 'Online', 'eco-theme' ), $place ) : __( 'Hybrid', 'eco-theme' );
	}

	return $place;
}

function eco_event_get_first_term_name( string $taxonomy, $post_id = null ): string {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || ! taxonomy_exists( $taxonomy ) ) {
		return '';
	}

	$terms = wp_get_post_terms( $post_id, $taxonomy );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}

	return sanitize_text_field( $terms[0]->name );
}

function eco_event_get_label( $post_id = null ): string {
	return eco_event_get_first_term_name( 'event-label', $post_id );
}

function eco_event_get_format( $post_id = null ): string {
	return eco_event_get_first_term_name( 'event-format', $post_id );
}

function eco_event_get_title( $post_id = null ): string {
	$post_id = $post_id ?: get_the_ID();
	$title   = eco_event_get_field( 'teaser_title', $post_id, '' );

	return $title ?: get_the_title( $post_id );
}

function eco_event_get_google_maps_embed_url( $post_id = null ): string {
	$post_id = $post_id ?: get_the_ID();

	$parts = array_filter(
		array_map(
			'trim',
			[
				(string) eco_event_get_field( 'location_name', $post_id, '' ),
				(string) eco_event_get_field( 'street', $post_id, '' ),
				trim(
					(string) eco_event_get_field( 'zip_plz', $post_id, '' ) . ' ' .
					(string) eco_event_get_field( 'city', $post_id, '' )
				),
				(string) eco_event_get_field( 'country', $post_id, '' ),
			]
		)
	);

	if ( empty( $parts ) ) {
		return '';
	}

	return 'https://www.google.com/maps?q=' . rawurlencode( implode( ', ', $parts ) ) . '&output=embed';
}

function eco_event_get_section_heading( string $section, $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();

	$defaults = [
		'intro' => [
			'eyebrow' => '',
			'title'   => __( 'About the event', 'eco-theme' ),
		],
		'partners' => [
			'eyebrow' => __( 'Network', 'eco-theme' ),
			'title'   => __( 'Partners', 'eco-theme' ),
		],
		'impressions' => [
			'eyebrow' => '',
			'title'   => __( 'Impressions', 'eco-theme' ),
		],
		'speakers' => [
			'eyebrow' => __( 'People', 'eco-theme' ),
			'title'   => __( 'Speakers', 'eco-theme' ),
		],
		'agenda' => [
			'eyebrow' => __( 'Program', 'eco-theme' ),
			'title'   => __( 'Agenda', 'eco-theme' ),
		],
		'location' => [
			'eyebrow' => __( 'Place', 'eco-theme' ),
			'title'   => __( 'Location', 'eco-theme' ),
		],
		'registration' => [
			'eyebrow' => __( 'Participation', 'eco-theme' ),
			'title'   => __( 'Registration', 'eco-theme' ),
		],
		'faq' => [
			'eyebrow' => __( 'Questions', 'eco-theme' ),
			'title'   => __( 'Frequently asked questions', 'eco-theme' ),
		],
		'contacts' => [
			'eyebrow' => __( 'Contact', 'eco-theme' ),
			'title'   => __( 'Your Contacts', 'eco-theme' ),
		],
		'series' => [
			'eyebrow' => '',
			'title'   => '',
		],
	];

	$default = $defaults[ $section ] ?? [ 'eyebrow' => '', 'title' => '' ];

	$eyebrow = trim( (string) eco_event_get_field( $section . '_section_eyebrow', $post_id, '' ) );
	$title   = trim( (string) eco_event_get_field( $section . '_section_title', $post_id, '' ) );

	if ( 'series' === $section ) {
		$legacy_series_heading = trim( (string) eco_event_get_field( 'event_series_heading', $post_id, '' ) );

		if ( '' === $title && '' !== $legacy_series_heading ) {
			$title = $legacy_series_heading;
		}
	}

	return [
		'eyebrow' => '' !== $eyebrow ? $eyebrow : $default['eyebrow'],
		'title'   => '' !== $title ? $title : $default['title'],
	];
}

function eco_event_get_pretix_code( $post_id = null ): string {
	$post_id = $post_id ?: get_the_ID();

	$value = eco_event_get_field( 'pretix_shortcode', $post_id, '' );
	$value = is_string( $value ) ? trim( $value ) : '';

	if ( $value === '' ) {
		return '';
	}

	// Backwards compatibility: if an old full shortcode was saved, extract the code from the shop_url.
	if ( preg_match( '#pretix\.eu/eco/([^/"\']+)/?#i', $value, $matches ) ) {
		$value = $matches[1];
	}

	// Backwards compatibility: if someone pasted only a Pretix URL, still extract the code.
	if ( preg_match( '#^https?://pretix\.eu/eco/([^/"\']+)/?#i', $value, $matches ) ) {
		$value = $matches[1];
	}

	// Keep only a safe Pretix event slug.
	$value = sanitize_title( $value );

	return $value;
}

function eco_event_get_pretix_shortcode( $post_id = null ): string {
	$code = eco_event_get_pretix_code( $post_id );

	if ( $code === '' ) {
		return '';
	}

	return sprintf(
		'[pretix_widget shop_url="https://pretix.eu/eco/%s/"]',
		esc_attr( $code )
	);
}

function eco_event_get_registration_button( $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( eco_event_is_past( $post_id ) ) {
		return [];
	}

	$type  = eco_event_get_field( 'registration_type', $post_id, 'external' );
	$label = eco_event_get_field( 'registration_button_label', $post_id, '' ) ?: __( 'Register now', 'eco-theme' );

	if ( $type === 'pretix' && eco_event_get_pretix_code( $post_id ) ) {
		return [
			'url'    => '#registration',
			'label'  => $label,
			'target' => '',
		];
	}

	if ( $type === 'external' ) {
		$url = eco_event_get_field( 'registration_url', $post_id, '' );

		if ( $url ) {
			return [
				'url'    => $url,
				'label'  => $label,
				'target' => '_blank',
			];
		}
	}

	return [];
}

function eco_event_the_content_area() {
	$content = trim( get_the_content() );

	if ( $content === '' ) {
		return;
	}
	?>
	<section class="eco-event-section eco-event-content-area">
		<div class="eco-event-container">
			<?php the_content(); ?>
		</div>
	</section>
	<?php
}

function eco_event_get_ical_url( $post_id = null ): string {
	$post_id = $post_id ?: get_the_ID();
	return add_query_arg( 'ical', '1', get_permalink( $post_id ) );
}

function eco_event_escape_ical_text( string $text ): string {
	$text = wp_strip_all_tags( $text );
	$text = str_replace( [ "\\", ";", ",", "\r\n", "\r", "\n" ], [ "\\\\", "\\;", "\\,", "\\n", "\\n", "\\n" ], $text );
	return $text;
}

function eco_event_format_ical_datetime( ?DateTimeInterface $date ): string {
	if ( ! $date ) {
		return '';
	}
	return $date->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Ymd\THis\Z' );
}

function eco_event_maybe_serve_ical(): void {
	if ( ! is_singular( 'event' ) || empty( $_GET['ical'] ) ) {
		return;
	}

	$post_id     = get_queried_object_id();
	$occurrences = eco_event_get_occurrences( $post_id );

	if ( empty( $occurrences ) ) {
		status_header( 404 );
		exit;
	}

	$title       = eco_event_escape_ical_text( get_the_title( $post_id ) );
	$description = eco_event_escape_ical_text( eco_event_get_field( 'teaser_text', $post_id, '' ) ?: wp_strip_all_tags( get_the_excerpt( $post_id ) ) );
	$location    = eco_event_escape_ical_text( eco_event_get_location_line( $post_id ) );
	$permalink   = get_permalink( $post_id );
	$now         = gmdate( 'Ymd\THis\Z' );

	$lines = [
		'BEGIN:VCALENDAR',
		'VERSION:2.0',
		'PRODID:-//eco//Events//DE',
		'CALSCALE:GREGORIAN',
		'METHOD:PUBLISH',
	];

	foreach ( $occurrences as $index => $occurrence ) {
		$start = $occurrence['start'] ?? null;
		$end   = $occurrence['end'] ?? null;

		if ( ! $start instanceof DateTimeInterface ) {
			continue;
		}

		if ( ! $end instanceof DateTimeInterface || $end <= $start ) {
			$end = $start->modify( '+1 hour' );
		}

		$lines[] = 'BEGIN:VEVENT';
		$lines[] = 'UID:' . $post_id . '-' . $index . '@' . wp_parse_url( home_url(), PHP_URL_HOST );
		$lines[] = 'DTSTAMP:' . $now;
		$lines[] = 'DTSTART:' . eco_event_format_ical_datetime( $start );
		$lines[] = 'DTEND:' . eco_event_format_ical_datetime( $end );
		$lines[] = 'SUMMARY:' . $title;
		if ( $description ) {
			$lines[] = 'DESCRIPTION:' . $description;
		}
		if ( $location ) {
			$lines[] = 'LOCATION:' . $location;
		}
		$lines[] = 'URL:' . esc_url_raw( $permalink );
		$lines[] = 'END:VEVENT';
	}

	$lines[] = 'END:VCALENDAR';

	nocache_headers();
	header( 'Content-Type: text/calendar; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="event-' . $post_id . '.ics"' );
	echo implode( "\r\n", $lines );
	exit;
}

function eco_event_normalize_posts( $items ): array {
	if ( empty( $items ) ) {
		return [];
	}

	if ( $items instanceof WP_Post ) {
		$items = [ $items ];
	}

	if ( ! is_array( $items ) ) {
		$items = [ $items ];
	}

	$posts = [];

	foreach ( $items as $item ) {
		$post_id = $item instanceof WP_Post ? $item->ID : (int) $item;
		if ( $post_id > 0 ) {
			$posts[] = $post_id;
		}
	}

	return array_values( array_unique( $posts ) );
}

function eco_event_get_partner_tier_colors(): array {
	return [
		'platinum' => '#6E6F73',
		'gold'     => '#D9AE30',
		'silver'   => '#DADADA',
		'other'    => '#000000',
	];
}

function eco_event_get_partner_tier_color( $type ): string {
	$colors = eco_event_get_partner_tier_colors();
	$type   = is_string( $type ) ? trim( $type ) : '';

	if ( '' === $type ) {
		return '';
	}

	return $colors[ $type ] ?? '';
}

function eco_event_normalize_image_url( $image, string $size = 'medium' ): string {
	if ( is_array( $image ) ) {
		if ( ! empty( $image['ID'] ) ) {
			$url = wp_get_attachment_image_url( (int) $image['ID'], $size );
			return $url ?: '';
		}

		if ( ! empty( $image['id'] ) ) {
			$url = wp_get_attachment_image_url( (int) $image['id'], $size );
			return $url ?: '';
		}

		if ( ! empty( $image['url'] ) ) {
			return esc_url_raw( $image['url'] );
		}
	}

	if ( is_numeric( $image ) ) {
		$url = wp_get_attachment_image_url( (int) $image, $size );
		return $url ?: '';
	}

	if ( is_string( $image ) && filter_var( $image, FILTER_VALIDATE_URL ) ) {
		return esc_url_raw( $image );
	}

	return '';
}

function eco_event_normalize_post_id( $post ): int {
	if ( $post instanceof WP_Post ) {
		return (int) $post->ID;
	}

	if ( is_object( $post ) && ! empty( $post->ID ) ) {
		return (int) $post->ID;
	}

	return is_numeric( $post ) ? (int) $post : 0;
}

function eco_event_get_partner_groups( $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();
	$groups  = eco_event_get_field( 'partner_groups', $post_id, [] );

	if ( empty( $groups ) || ! is_array( $groups ) ) {
		return [];
	}

	$normalized_groups = [];

	foreach ( $groups as $group ) {
		$heading    = trim( (string) ( $group['heading'] ?? '' ) );
		$color_type = trim( (string) ( $group['color_type'] ?? '' ) );
		$logo_size  = trim( (string) ( $group['logo_size'] ?? 'normal' ) );
		$logo_size  = in_array( $logo_size, [ 'large', 'normal', 'small' ], true ) ? $logo_size : 'normal';
		$partners   = [];

		$members = $group['members'] ?? [];
		if ( ! empty( $members ) && ! is_array( $members ) ) {
			$members = [ $members ];
		}

		if ( is_array( $members ) ) {
			foreach ( $members as $member ) {
				$member_id = eco_event_normalize_post_id( $member );

				if ( ! $member_id ) {
					continue;
				}

				$name    = get_the_title( $member_id );
				$logo    = get_the_post_thumbnail_url( $member_id, 'medium' ) ?: '';
				$website = function_exists( 'get_field' ) ? get_field( 'website', $member_id ) : get_post_meta( $member_id, 'website', true );

				$partners[] = [
					'name' => $name,
					'logo' => $logo,
					'url'  => $website ?: '',
				];
			}
		}

		$custom_partners = $group['custom_partners'] ?? [];

		if ( is_array( $custom_partners ) ) {
			foreach ( $custom_partners as $custom ) {
				$logo = eco_event_normalize_image_url( $custom['logo'] ?? '', 'medium' );
				$name = trim( (string) ( $custom['name'] ?? '' ) );
				$url  = trim( (string) ( $custom['url'] ?? '' ) );

				if ( ! $name && ! $logo ) {
					continue;
				}

				if ( ! $name && ! empty( $custom['logo']['alt'] ) ) {
					$name = trim( (string) $custom['logo']['alt'] );
				}

				$partners[] = [
					'name' => $name,
					'logo' => $logo,
					'url'  => $url,
				];
			}
		}

		if ( empty( $partners ) ) {
			continue;
		}

		usort(
			$partners,
			static function ( $a, $b ) {
				return strcasecmp( $a['name'] ?? '', $b['name'] ?? '' );
			}
		);

		$normalized_groups[] = [
			'heading'    => $heading,
			'color_type' => $color_type,
			'color'      => eco_event_get_partner_tier_color( $color_type ),
			'logo_size'  => $logo_size,
			'partners'   => $partners,
		];
	}

	return $normalized_groups;
}

/**
 * Return the configured position for the free-form Elementor event content.
 */
function eco_event_get_elementor_content_position( $post_id = null ): string {
	$post_id = $post_id ?: get_the_ID();

	$allowed = [
		'after_hero',
		'after_intro',
		'after_agenda',
		'before_registration',
		'after_registration',
	];

	$position = (string) eco_event_get_field( 'elementor_content_position', $post_id, 'after_agenda' );

	return in_array( $position, $allowed, true ) ? $position : 'after_agenda';
}

/**
 * Render the free-form Elementor event content once, at the selected position.
 */
function eco_event_render_elementor_content_at( string $position, $post_id = null ): void {
	static $rendered = [];

	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || isset( $rendered[ $post_id ] ) ) {
		return;
	}

	if ( eco_event_get_elementor_content_position( $post_id ) !== $position ) {
		return;
	}

	$rendered[ $post_id ] = true;
	eco_event_the_content_area();
}

/**
 * Return Event Series terms assigned to an event.
 *
 * @return WP_Term[]
 */
function eco_event_get_series_terms( $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || ! taxonomy_exists( 'event-series' ) ) {
		return [];
	}

	$terms = wp_get_post_terms( $post_id, 'event-series' );

	return is_wp_error( $terms ) ? [] : $terms;
}

/**
 * Choose the date used to order an event inside a series.
 *
 * Upcoming-only lists use the next still-active occurrence. Lists that
 * include past events use the first occurrence so ASC/DESC stays chronological.
 */
function eco_event_get_series_sort_timestamp( $post_id, bool $include_past = false ): int {
	$occurrences = eco_event_get_occurrences( $post_id );

	if ( empty( $occurrences ) ) {
		return PHP_INT_MAX;
	}

	if ( $include_past ) {
		$first = reset( $occurrences );
		$start = $first['start'] ?? null;

		return $start instanceof DateTimeInterface ? $start->getTimestamp() : PHP_INT_MAX;
	}

	$now = new DateTimeImmutable( 'now', wp_timezone() );

	foreach ( $occurrences as $occurrence ) {
		$end   = $occurrence['end'] ?? null;
		$start = $occurrence['start'] ?? null;

		if ( $end instanceof DateTimeInterface && $end >= $now && $start instanceof DateTimeInterface ) {
			return $start->getTimestamp();
		}
	}

	$last  = end( $occurrences );
	$start = $last['start'] ?? null;

	return $start instanceof DateTimeInterface ? $start->getTimestamp() : PHP_INT_MAX;
}

/**
 * Return related events from the same Event Series.
 *
 * The current event is excluded. Upcoming events are shown by default.
 */
function eco_event_get_series_events( $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( ! $post_id || ! eco_event_get_boolean_field( 'show_event_series', $post_id, true ) ) {
		return [];
	}

	$terms = eco_event_get_series_terms( $post_id );

	if ( empty( $terms ) ) {
		return [];
	}

	$term_ids = array_values(
		array_filter(
			array_map(
				static function ( $term ) {
					return $term instanceof WP_Term ? (int) $term->term_id : 0;
				},
				$terms
			)
		)
	);

	if ( empty( $term_ids ) ) {
		return [];
	}

	$query = new WP_Query(
		[
			'post_type'              => 'event',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'post__not_in'           => [ $post_id ],
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'tax_query'              => [
				[
					'taxonomy' => 'event-series',
					'field'    => 'term_id',
					'terms'    => $term_ids,
				],
			],
		]
	);

	$include_past = (bool) eco_event_get_field( 'event_series_include_past', $post_id, false );
	$order        = strtoupper( (string) eco_event_get_field( 'event_series_order', $post_id, 'ASC' ) );
	$order        = 'DESC' === $order ? 'DESC' : 'ASC';
	$max_events   = absint( eco_event_get_field( 'event_series_max', $post_id, 3 ) );
	$max_events   = max( 1, min( 12, $max_events ?: 3 ) );

	$events = [];

	foreach ( $query->posts as $event_id ) {
		if ( ! $include_past && eco_event_is_past( $event_id ) ) {
			continue;
		}

		$events[] = [
			'id'        => (int) $event_id,
			'timestamp' => eco_event_get_series_sort_timestamp( $event_id, $include_past ),
		];
	}

	usort(
		$events,
		static function ( $a, $b ) use ( $order ) {
			$comparison = ( $a['timestamp'] ?? PHP_INT_MAX ) <=> ( $b['timestamp'] ?? PHP_INT_MAX );

			if ( 0 === $comparison ) {
				$comparison = ( $a['id'] ?? 0 ) <=> ( $b['id'] ?? 0 );
			}

			return 'DESC' === $order ? -$comparison : $comparison;
		}
	);

	$events = array_slice( $events, 0, $max_events );

	return array_values(
		array_map(
			static function ( $event ) {
				return (int) $event['id'];
			},
			$events
		)
	);
}
