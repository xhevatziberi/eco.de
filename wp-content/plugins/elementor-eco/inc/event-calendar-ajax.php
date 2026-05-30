<?php
namespace ElementorEco;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EventCalendarAjax {

	public static function init() {
		add_action( 'wp_ajax_eco_event_calendar_load', [ __CLASS__, 'ajax_load' ] );
		add_action( 'wp_ajax_nopriv_eco_event_calendar_load', [ __CLASS__, 'ajax_load' ] );
	}

	public static function ajax_load() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'eco_event_calendar' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid request.', 'elementor-eco' ) ], 403 );
		}

		$settings = [];
		if ( isset( $_POST['settings'] ) ) {
			$decoded = json_decode( wp_unslash( $_POST['settings'] ), true );
			if ( is_array( $decoded ) ) {
				$settings = self::sanitize_settings( $decoded );
			}
		}

		$year        = isset( $_POST['year'] ) ? absint( $_POST['year'] ) : (int) current_time( 'Y' );
		$month       = isset( $_POST['month'] ) ? absint( $_POST['month'] ) : (int) current_time( 'n' );
		$selected_day = isset( $_POST['day'] ) ? absint( $_POST['day'] ) : 0;
		$source      = isset( $_POST['source'] ) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : ( $settings['default_filter'] ?? 'all' );
		$page        = isset( $_POST['page'] ) ? max( 1, absint( $_POST['page'] ) ) : 1;

		$year  = max( 1970, min( 2099, $year ) );
		$month = max( 1, min( 12, $month ) );

		$data = self::build_calendar_data( $settings, $year, $month, $selected_day, $source, $page );

		wp_send_json_success( $data );
	}

	public static function sanitize_settings( $settings ) {
		$allowed = [
			'posts_per_page'     => 2,
			'show_excerpt'       => 'yes',
			'excerpt_length'     => 16,
			'show_load_more'     => 'yes',
			'show_filter'        => 'yes',
			'default_filter'     => 'all',
			'image_ratio'        => '16-9',
			'empty_title'        => '',
			'empty_text'         => '',
			'filter_all_label'   => '',
			'filter_eco_label'   => '',
			'filter_partner_label' => '',
			'filter_past_label'  => '',
		];

		$out = [];
		foreach ( $allowed as $key => $default ) {
			$out[ $key ] = $settings[ $key ] ?? $default;
		}

		$out['posts_per_page'] = max( 1, min( 12, absint( $out['posts_per_page'] ) ) );
		$out['excerpt_length'] = max( 5, min( 60, absint( $out['excerpt_length'] ) ) );
		$out['show_excerpt']   = $out['show_excerpt'] === 'yes' ? 'yes' : '';
		$out['show_load_more'] = $out['show_load_more'] === 'yes' ? 'yes' : '';
		$out['show_filter']    = $out['show_filter'] === 'yes' ? 'yes' : '';
		$out['default_filter'] = in_array( $out['default_filter'], [ 'all', 'eco-event', 'partner-event', 'past' ], true ) ? $out['default_filter'] : 'all';
		$out['image_ratio']    = in_array( $out['image_ratio'], [ '16-9', '4-3', '3-2', '1-1' ], true ) ? $out['image_ratio'] : '16-9';

		foreach ( [ 'empty_title', 'empty_text', 'filter_all_label', 'filter_eco_label', 'filter_partner_label', 'filter_past_label' ] as $text_key ) {
			$out[ $text_key ] = sanitize_text_field( $out[ $text_key ] );
		}

		return $out;
	}

	public static function build_calendar_data( $settings, $year, $month, $selected_day = 0, $source = 'all', $page = 1 ) {
		$settings = self::sanitize_settings( $settings );
		$source   = in_array( $source, [ 'all', 'eco-event', 'partner-event', 'past' ], true ) ? $source : 'all';
		$page     = max( 1, absint( $page ) );

		$month_start = sprintf( '%04d%02d01', $year, $month );
		$month_end   = date( 'Ymt', strtotime( sprintf( '%04d-%02d-01', $year, $month ) ) );
		$today       = current_time( 'Ymd' );

		$events = self::get_event_items( $source, $month_start, $month_end, $selected_day );
		$days_with_events = self::get_days_with_events( $source, $month_start, $month_end );

		$per_page = $settings['posts_per_page'];
		$total    = count( $events );
		$offset   = ( $page - 1 ) * $per_page;
		$sliced   = array_slice( $events, $offset, $per_page );

		$html = '';
		foreach ( $sliced as $event ) {
			$html .= self::render_event_card( $event, $settings, false );
		}

		if ( $page === 1 && empty( $html ) ) {
			$html = self::render_empty_state( $settings );
		}

		return [
			'calendar_html' => self::render_calendar( $year, $month, $selected_day, $days_with_events, $today ),
			'cards_html'    => $html,
			'has_more'      => ( $offset + $per_page ) < $total,
			'page'          => $page,
			'total'         => $total,
			'month_label'   => self::get_month_label( $year, $month ),
		];
	}

	public static function get_event_items( $source, $month_start, $month_end, $selected_day = 0 ) {
		$posts = self::get_candidate_events( $source );
		$today = current_time( 'Ymd' );
		$items = [];
		$selected_date = $selected_day ? substr( $month_start, 0, 6 ) . str_pad( (string) $selected_day, 2, '0', STR_PAD_LEFT ) : '';

		foreach ( $posts as $post ) {
			$occurrences = self::get_event_occurrences( $post->ID );
			if ( empty( $occurrences ) ) {
				continue;
			}

			$last_end = self::get_last_occurrence_end( $occurrences );
			$is_past  = $last_end && $last_end < $today;

			if ( $source === 'past' ) {
				if ( ! $is_past ) {
					continue;
				}
				$items[] = [
					'post_id'     => $post->ID,
					'occurrence'  => self::get_sort_occurrence_for_past( $occurrences ),
					'is_past'     => true,
					'sort_date'   => $last_end,
				];
				continue;
			}

			$matching_occurrence = null;
			foreach ( $occurrences as $occurrence ) {
				$start = $occurrence['start_date'];
				$end   = $occurrence['end_date'] ?: $start;
				$in_month = $start <= $month_end && $end >= $month_start;

				if ( ! $in_month ) {
					continue;
				}

				if ( $selected_date && ! ( $start <= $selected_date && $end >= $selected_date ) ) {
					continue;
				}

				$matching_occurrence = $occurrence;
				break;
			}

			if ( ! $matching_occurrence ) {
				continue;
			}

			$items[] = [
				'post_id'     => $post->ID,
				'occurrence'  => $matching_occurrence,
				'is_past'     => $is_past,
				'sort_date'   => $matching_occurrence['start_date'],
			];
		}

		usort(
			$items,
			function ( $a, $b ) use ( $source ) {
				if ( $source === 'past' ) {
					return strcmp( $b['sort_date'], $a['sort_date'] );
				}

				return strcmp( $a['sort_date'], $b['sort_date'] );
			}
		);

		return $items;
	}

	public static function get_days_with_events( $source, $month_start, $month_end ) {
		$posts = self::get_candidate_events( $source === 'past' ? 'all' : $source );
		$today = current_time( 'Ymd' );
		$days  = [];

		foreach ( $posts as $post ) {
			$occurrences = self::get_event_occurrences( $post->ID );
			$last_end    = self::get_last_occurrence_end( $occurrences );
			$is_past     = $last_end && $last_end < $today;

			if ( $source === 'past' && ! $is_past ) {
				continue;
			}

			foreach ( $occurrences as $occurrence ) {
				$start = $occurrence['start_date'];
				$end   = $occurrence['end_date'] ?: $start;

				if ( $start > $month_end || $end < $month_start ) {
					continue;
				}

				$range_start = max( $start, $month_start );
				$range_end   = min( $end, $month_end );
				$cursor      = strtotime( self::ymd_to_date( $range_start ) );
				$end_ts      = strtotime( self::ymd_to_date( $range_end ) );

				while ( $cursor <= $end_ts ) {
					$day_key = date( 'j', $cursor );
					$day_ymd = date( 'Ymd', $cursor );
					$days[ $day_key ] = [
						'past' => $day_ymd < $today,
					];
					$cursor = strtotime( '+1 day', $cursor );
				}
			}
		}

		return $days;
	}

	public static function get_candidate_events( $source = 'all' ) {
		$args = [
			'post_type'              => 'event',
			'post_status'            => 'publish',
			'posts_per_page'         => 300,
			'orderby'                => 'meta_value_num',
			'meta_key'               => 'start_date',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		];

		if ( in_array( $source, [ 'eco-event', 'partner-event' ], true ) ) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'event-source',
					'field'    => 'slug',
					'terms'    => [ $source ],
				],
			];
		}

		return get_posts( $args );
	}

	public static function get_event_occurrences( $post_id ) {
		$occurrences = [];
		$start_date  = self::clean_ymd( get_post_meta( $post_id, 'start_date', true ) );

		if ( $start_date ) {
			$occurrences[] = [
				'start_date' => $start_date,
				'start_time' => self::clean_time( get_post_meta( $post_id, 'start_time', true ) ),
				'end_date'   => self::clean_ymd( get_post_meta( $post_id, 'end_date', true ) ) ?: $start_date,
				'end_time'   => self::clean_time( get_post_meta( $post_id, 'end_time', true ) ),
				'label'      => '',
			];
		}

		$other_dates = function_exists( 'get_field' ) ? get_field( 'other_dates', $post_id ) : [];
		if ( is_array( $other_dates ) ) {
			foreach ( $other_dates as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}

				$row_start = self::clean_ymd( $row['start_date'] ?? '' );
				if ( ! $row_start ) {
					continue;
				}

				$occurrences[] = [
					'start_date' => $row_start,
					'start_time' => self::clean_time( $row['start_time'] ?? '' ),
					'end_date'   => self::clean_ymd( $row['end_date'] ?? '' ) ?: $row_start,
					'end_time'   => self::clean_time( $row['end_time'] ?? '' ),
					'label'      => sanitize_text_field( $row['date_label'] ?? '' ),
				];
			}
		}

		usort(
			$occurrences,
			function ( $a, $b ) {
				return strcmp( $a['start_date'] . $a['start_time'], $b['start_date'] . $b['start_time'] );
			}
		);

		return $occurrences;
	}

	public static function get_last_occurrence_end( $occurrences ) {
		$last = '';
		foreach ( $occurrences as $occurrence ) {
			$end = $occurrence['end_date'] ?: $occurrence['start_date'];
			if ( $end > $last ) {
				$last = $end;
			}
		}
		return $last;
	}

	public static function get_sort_occurrence_for_past( $occurrences ) {
		if ( empty( $occurrences ) ) {
			return [];
		}
		return end( $occurrences );
	}

	public static function render_calendar( $year, $month, $selected_day, $days_with_events, $today ) {
		$first_ts      = strtotime( sprintf( '%04d-%02d-01', $year, $month ) );
		$days_in_month = (int) date( 't', $first_ts );
		$first_weekday = (int) date( 'N', $first_ts ); // 1 = Monday.
		$month_label   = self::get_month_label( $year, $month );

		ob_start();
		?>
		<div class="eco-event-calendar__month-head">
			<button type="button" class="eco-event-calendar__nav" data-direction="prev" aria-label="<?php esc_attr_e( 'Previous month', 'elementor-eco' ); ?>">‹</button>
			<span class="eco-event-calendar__month-label"><?php echo esc_html( $month_label ); ?></span>
			<button type="button" class="eco-event-calendar__nav" data-direction="next" aria-label="<?php esc_attr_e( 'Next month', 'elementor-eco' ); ?>">›</button>
		</div>

		<div class="eco-event-calendar__weekdays" aria-hidden="true">
			<span><?php esc_html_e( 'Mo', 'elementor-eco' ); ?></span>
			<span><?php esc_html_e( 'Di', 'elementor-eco' ); ?></span>
			<span><?php esc_html_e( 'Mi', 'elementor-eco' ); ?></span>
			<span><?php esc_html_e( 'Do', 'elementor-eco' ); ?></span>
			<span><?php esc_html_e( 'Fr', 'elementor-eco' ); ?></span>
			<span><?php esc_html_e( 'Sa', 'elementor-eco' ); ?></span>
			<span><?php esc_html_e( 'So', 'elementor-eco' ); ?></span>
		</div>

		<div class="eco-event-calendar__days">
			<?php for ( $blank = 1; $blank < $first_weekday; $blank++ ) : ?>
				<span class="eco-event-calendar__day eco-event-calendar__day--blank"></span>
			<?php endfor; ?>

			<?php for ( $day = 1; $day <= $days_in_month; $day++ ) :
				$ymd       = sprintf( '%04d%02d%02d', $year, $month, $day );
				$has_event = isset( $days_with_events[ (string) $day ] );
				$is_past   = $ymd < $today;
				$classes   = [ 'eco-event-calendar__day' ];
				if ( $has_event ) {
					$classes[] = 'eco-event-calendar__day--has-event';
				}
				if ( $is_past ) {
					$classes[] = 'eco-event-calendar__day--past';
				}
				if ( $selected_day === $day ) {
					$classes[] = 'eco-event-calendar__day--selected';
				}
				?>
				<button type="button" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-day="<?php echo esc_attr( $day ); ?>">
					<span><?php echo esc_html( $day ); ?></span>
				</button>
			<?php endfor; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	public static function render_event_card( $event, $settings, $echo = true ) {
		$post_id     = absint( $event['post_id'] ?? 0 );
		$occurrence  = $event['occurrence'] ?? [];
		$is_past     = ! empty( $event['is_past'] );
		$title       = get_post_meta( $post_id, 'teaser_title', true );
		$title       = $title ? $title : get_the_title( $post_id );
		$excerpt     = get_post_meta( $post_id, 'teaser_text', true );
		$excerpt     = $excerpt ? $excerpt : get_the_excerpt( $post_id );
		$category    = self::get_primary_term_name( $post_id, 'event-category' );
		$label       = self::get_event_label( $post_id );
		$image_url   = self::get_event_card_image_url( $post_id );
		$location    = self::get_event_location_label( $post_id );
		$date_label  = self::format_event_date( $occurrence['start_date'] ?? '' );
		$ratio       = $settings['image_ratio'] ?? '16-9';

		ob_start();
		?>
		<a class="eco-event-calendar-card<?php echo $is_past ? ' eco-event-calendar-card--past' : ''; ?>" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
			<div class="eco-event-calendar-card__image eco-event-calendar-card__image--<?php echo esc_attr( $ratio ); ?>">
				<?php if ( $image_url ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
				<?php endif; ?>

				<?php if ( $label ) : ?>
					<span class="eco-event-calendar-card__badge"><?php echo esc_html( $label ); ?></span>
				<?php endif; ?>
				<?php if ( $is_past ) : ?>
					<span class="eco-event-calendar-card__past-badge"><?php esc_html_e( 'Past', 'elementor-eco' ); ?></span>
				<?php endif; ?>
			</div>

			<div class="eco-event-calendar-card__body">
				<?php if ( $category ) : ?>
					<div class="eco-event-calendar-card__category"><?php echo esc_html( $category ); ?></div>
				<?php endif; ?>

				<h3 class="eco-event-calendar-card__title"><?php echo esc_html( $title ); ?></h3>

				<?php if ( ! empty( $settings['show_excerpt'] ) && $settings['show_excerpt'] === 'yes' && $excerpt ) : ?>
					<div class="eco-event-calendar-card__excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $excerpt ), absint( $settings['excerpt_length'] ?? 16 ) ) ); ?></div>
				<?php endif; ?>

				<div class="eco-event-calendar-card__meta">
					<?php if ( $date_label ) : ?>
						<span><?php echo self::icon_svg( 'calendar' ); ?> <?php echo esc_html( $date_label ); ?></span>
					<?php endif; ?>
					<?php if ( $location ) : ?>
						<span><?php echo self::icon_svg( 'location' ); ?> <?php echo esc_html( $location ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</a>
		<?php
		$html = ob_get_clean();

		if ( $echo ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		return $html;
	}

	public static function render_empty_state( $settings ) {
		$title = ! empty( $settings['empty_title'] ) ? $settings['empty_title'] : __( 'Keine Veranstaltungen gefunden', 'elementor-eco' );
		$text  = ! empty( $settings['empty_text'] ) ? $settings['empty_text'] : __( 'Für diesen Zeitraum sind aktuell keine Veranstaltungen verfügbar.', 'elementor-eco' );

		ob_start();
		?>
		<div class="eco-event-calendar-empty">
			<div class="eco-event-calendar-empty__icon" aria-hidden="true"><?php echo self::icon_svg( 'calendar' ); ?></div>
			<h3><?php echo esc_html( $title ); ?></h3>
			<p><?php echo esc_html( $text ); ?></p>
		</div>
		<?php
		return ob_get_clean();
	}

	public static function get_event_card_image_url( $post_id ) {
		$image = function_exists( 'get_field' ) ? get_field( 'card_image', $post_id ) : null;
		if ( is_array( $image ) && ! empty( $image['sizes']['large'] ) ) {
			return $image['sizes']['large'];
		}
		if ( is_array( $image ) && ! empty( $image['url'] ) ) {
			return $image['url'];
		}
		if ( is_numeric( $image ) ) {
			return wp_get_attachment_image_url( absint( $image ), 'large' );
		}
		return get_the_post_thumbnail_url( $post_id, 'large' );
	}

	public static function get_event_label( $post_id ) {
		$label = get_post_meta( $post_id, 'event_label', true );
		$map = [
			'event'         => __( 'Event', 'elementor-eco' ),
			'eco_event'     => __( 'eco Event', 'elementor-eco' ),
			'partner_event' => __( 'Partner Event', 'elementor-eco' ),
			'webinar'       => __( 'Webinar', 'elementor-eco' ),
			'workshop'      => __( 'Workshop', 'elementor-eco' ),
			'conference'    => __( 'Conference', 'elementor-eco' ),
			'highlight'     => __( 'Highlight', 'elementor-eco' ),
			'training'      => __( 'Training', 'elementor-eco' ),
			'award'         => __( 'Award', 'elementor-eco' ),
		];
		return $map[ $label ] ?? ( $label ? ucwords( str_replace( [ '_', '-' ], ' ', $label ) ) : __( 'Event', 'elementor-eco' ) );
	}

	public static function get_event_location_label( $post_id ) {
		$mode = get_post_meta( $post_id, 'event_mode', true );
		$city = get_post_meta( $post_id, 'city', true );
		$name = get_post_meta( $post_id, 'location_name', true );

		if ( $mode === 'online' ) {
			return __( 'Online', 'elementor-eco' );
		}

		if ( $mode === 'hybrid' ) {
			return $city ? sprintf( __( 'Online / %s', 'elementor-eco' ), $city ) : __( 'Online', 'elementor-eco' );
		}

		return $city ? $city : $name;
	}

	public static function get_primary_term_name( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}
		return $terms[0]->name;
	}

	public static function format_event_date( $ymd ) {
		$ymd = self::clean_ymd( $ymd );
		if ( ! $ymd ) {
			return '';
		}
		return date_i18n( 'd.m.Y', strtotime( self::ymd_to_date( $ymd ) ) );
	}

	public static function get_month_label( $year, $month ) {
		return date_i18n( 'F Y', strtotime( sprintf( '%04d-%02d-01', $year, $month ) ) );
	}

	public static function clean_ymd( $value ) {
		$value = is_scalar( $value ) ? preg_replace( '/[^0-9]/', '', (string) $value ) : '';
		return strlen( $value ) === 8 ? $value : '';
	}

	public static function clean_time( $value ) {
		$value = is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
		return preg_match( '/^\d{2}:\d{2}$/', $value ) ? $value : '';
	}

	public static function ymd_to_date( $ymd ) {
		return substr( $ymd, 0, 4 ) . '-' . substr( $ymd, 4, 2 ) . '-' . substr( $ymd, 6, 2 );
	}

	public static function icon_svg( $icon ) {
		if ( $icon === 'location' ) {
			return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-5.1 7-12a7 7 0 0 0-14 0c0 6.9 7 12 7 12Z"/><circle cx="12" cy="9" r="2.5"/></svg>';
		}
		return '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg>';
	}
}
