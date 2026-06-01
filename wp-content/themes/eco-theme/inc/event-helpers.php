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

function eco_event_is_valid_hex( $color ): bool {
	return is_string( $color ) && (bool) preg_match( '/^#([A-Fa-f0-9]{3}){1,2}$/', trim( $color ) );
}

function eco_event_get_color( $post_id = null ): string {
	$color = eco_event_get_field( 'event_color', $post_id, '' );

	return eco_event_is_valid_hex( $color ) ? trim( $color ) : '#c2cf00';
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

function eco_event_get_location_line( $post_id = null ): string {
	$post_id       = $post_id ?: get_the_ID();
	$mode          = eco_event_get_field( 'event_mode', $post_id, 'onsite' );
	$location_name = eco_event_get_field( 'location_name', $post_id, '' );
	$city          = eco_event_get_field( 'city', $post_id, '' );

	if ( $mode === 'online' ) {
		return __( 'Online', 'eco-theme' );
	}

	if ( $mode === 'hybrid' ) {
		$place = $city ?: $location_name;
		return $place ? sprintf( '%s + %s', __( 'Online', 'eco-theme' ), $place ) : __( 'Hybrid', 'eco-theme' );
	}

	return $location_name ?: $city;
}

function eco_event_get_label( $post_id = null ): string {
	$labels = [
		'event'         => __( 'Event', 'eco-theme' ),
		'eco_event'     => __( 'eco Event', 'eco-theme' ),
		'partner_event' => __( 'Partner Event', 'eco-theme' ),
		'webinar'       => __( 'Webinar', 'eco-theme' ),
		'workshop'      => __( 'Workshop', 'eco-theme' ),
		'conference'    => __( 'Conference', 'eco-theme' ),
		'highlight'     => __( 'Highlight', 'eco-theme' ),
		'training'      => __( 'Training', 'eco-theme' ),
		'award'         => __( 'Award', 'eco-theme' ),
	];

	$value = eco_event_get_field( 'event_label', $post_id, '' );

	return $labels[ $value ] ?? '';
}

function eco_event_get_title( $post_id = null ): string {
	$post_id = $post_id ?: get_the_ID();
	$title   = eco_event_get_field( 'teaser_title', $post_id, '' );

	return $title ?: get_the_title( $post_id );
}

function eco_event_get_registration_button( $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();

	if ( eco_event_is_past( $post_id ) ) {
		return [];
	}

	$type  = eco_event_get_field( 'registration_type', $post_id, 'external' );
	$label = eco_event_get_field( 'registration_button_label', $post_id, '' ) ?: __( 'Jetzt anmelden', 'eco-theme' );

	if ( $type === 'pretix' && eco_event_get_field( 'pretix_shortcode', $post_id, '' ) ) {
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

	return $colors[ $type ] ?? $colors['other'];
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
		$color_type = $group['color_type'] ?? 'other';
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
			'partners'   => $partners,
		];
	}

	return $normalized_groups;
}