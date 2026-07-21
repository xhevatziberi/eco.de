<?php
namespace ElementorEco;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ContentCardsAjax {

	public static function init() {
		add_action( 'wp_ajax_eco_content_cards_load_more', [ __CLASS__, 'ajax_load_more' ] );
		add_action( 'wp_ajax_nopriv_eco_content_cards_load_more', [ __CLASS__, 'ajax_load_more' ] );
	}

	public static function build_query_args( $settings, $page = 1 ) {
		$post_types = ! empty( $settings['post_types'] ) && is_array( $settings['post_types'] )
			? array_map( 'sanitize_key', $settings['post_types'] )
			: [ 'post' ];

		$allowed_post_types = [ 'post', 'event', 'podcast', 'press', 'study' ];
		$post_types         = array_values( array_intersect( $post_types, $allowed_post_types ) );

		if ( empty( $post_types ) ) {
			$post_types = [ 'post' ];
		}

		$card_style       = $settings['card_style'] ?? 'default';
		$featured_post_id = ! empty( $settings['featured_post_id'] ) ? absint( $settings['featured_post_id'] ) : 0;

		if ( $card_style === 'featured' && $featured_post_id ) {
			$featured_post_type = get_post_type( $featured_post_id );

			return [
				'post_type'           => $featured_post_type ? $featured_post_type : 'any',
				'post_status'         => 'publish',
				'posts_per_page'      => 1,
				'post__in'            => [ $featured_post_id ],
				'orderby'             => 'post__in',
				'ignore_sticky_posts' => true,
			];
		}

		$posts_per_page = ! empty( $settings['posts_per_page'] ) ? absint( $settings['posts_per_page'] ) : 3;
		$base_offset    = ! empty( $settings['offset'] ) ? absint( $settings['offset'] ) : 0;
		$page           = max( 1, absint( $page ) );
		$offset         = $base_offset + ( ( $page - 1 ) * $posts_per_page );

		$args = [
			'post_type'           => $post_types,
			'post_status'         => 'publish',
			'posts_per_page'      => $posts_per_page,
			'offset'              => $offset,
			'ignore_sticky_posts' => true,
		];

		$manual_ids = self::parse_ids( $settings['manual_ids'] ?? '' );

		if ( ! empty( $manual_ids ) ) {
			$args['post__in'] = $manual_ids;
		}

		if ( ! empty( $settings['exclude_current'] ) && $settings['exclude_current'] === 'yes' && is_singular() ) {
			$args['post__not_in'] = [ get_the_ID() ];
		}

		$orderby = ! empty( $settings['orderby'] ) ? sanitize_key( $settings['orderby'] ) : 'date';
		$order   = ! empty( $settings['order'] ) && $settings['order'] === 'ASC' ? 'ASC' : 'DESC';

		if ( $orderby === 'event_start' ) {
			$args['meta_key'] = 'start_date';
			$args['orderby']  = 'meta_value_num';
			$args['order']    = $order;
		} elseif ( $orderby === 'post__in' && ! empty( $manual_ids ) ) {
			$args['orderby'] = 'post__in';
		} elseif ( in_array( $orderby, [ 'date', 'title', 'menu_order', 'rand' ], true ) ) {
			$args['orderby'] = $orderby;

			if ( $orderby !== 'rand' ) {
				$args['order'] = $order;
			}
		} else {
			$args['orderby'] = 'date';
			$args['order']   = $order;
		}

		if ( ( $settings['query_source'] ?? 'manual' ) === 'current_acf_terms' ) {
			$tax_query = self::build_tax_query_from_current_acf_terms( $settings );

			if ( isset( $tax_query['__eco_empty_acf_terms'] ) ) {
				$args['post__in'] = [ 0 ];
			} elseif ( ! empty( $tax_query ) ) {
				$args['tax_query'] = $tax_query;
			}
		} else {
			$tax_query = self::build_tax_query_from_settings( $settings );

			if ( ! empty( $tax_query ) ) {
				$args['tax_query'] = $tax_query;
			}
		}

		$event_filter = $settings['event_filter'] ?? 'all';

		if ( in_array( 'event', $post_types, true ) && in_array( $event_filter, [ 'future', 'past' ], true ) ) {
			$today = current_time( 'Ymd' );

			$args['meta_query'] = [
				[
					'key'     => 'start_date',
					'value'   => $today,
					'compare' => $event_filter === 'future' ? '>=' : '<',
					'type'    => 'NUMERIC',
				],
			];
		}

		return $args;
	}

	private static function parse_ids( $ids_string ) {
		if ( empty( $ids_string ) || ! is_string( $ids_string ) ) {
			return [];
		}

		$ids = array_filter( array_map( 'absint', explode( ',', $ids_string ) ) );

		return array_values( array_unique( $ids ) );
	}

	private static function build_tax_query_from_settings( $settings ) {
		if ( empty( $settings['include_terms'] ) || ! is_array( $settings['include_terms'] ) ) {
			return [];
		}

		$grouped = [];

		foreach ( $settings['include_terms'] as $item ) {
			if ( ! is_string( $item ) || strpos( $item, '|' ) === false ) {
				continue;
			}

			list( $taxonomy, $slug ) = explode( '|', $item, 2 );

			if ( ! taxonomy_exists( $taxonomy ) || empty( $slug ) ) {
				continue;
			}

			$grouped[ $taxonomy ][] = sanitize_title( $slug );
		}

		if ( empty( $grouped ) ) {
			return [];
		}

		$tax_query = [
			'relation' => 'OR',
		];

		foreach ( $grouped as $taxonomy => $slugs ) {
			$tax_query[] = [
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => array_unique( $slugs ),
			];
		}

		return $tax_query;
	}

	private static function build_tax_query_from_current_acf_terms( $settings ) {
		$query_source = $settings['query_source'] ?? 'manual';

		if ( $query_source !== 'current_acf_terms' ) {
			return [];
		}

		$context_post_id = ! empty( $settings['context_post_id'] )
			? absint( $settings['context_post_id'] )
			: get_queried_object_id();

		if ( ! $context_post_id ) {
			return [ '__eco_empty_acf_terms' => true ];
		}

		$field_names_raw = $settings['acf_term_fields'] ?? '';

		if ( empty( $field_names_raw ) || ! is_string( $field_names_raw ) ) {
			return [ '__eco_empty_acf_terms' => true ];
		}

		$field_names = array_filter(
			array_map(
				'trim',
				explode( ',', $field_names_raw )
			)
		);

		if ( empty( $field_names ) ) {
			return [ '__eco_empty_acf_terms' => true ];
		}

		$grouped_terms = [];

		foreach ( $field_names as $field_name ) {
			$field_name = sanitize_key( $field_name );

			if ( empty( $field_name ) ) {
				continue;
			}

			$field_object = function_exists( 'get_field_object' )
				? get_field_object( $field_name, $context_post_id, false, false )
				: null;

			$field_taxonomy = '';

			if ( is_array( $field_object ) && ! empty( $field_object['taxonomy'] ) ) {
				$field_taxonomy = sanitize_key( $field_object['taxonomy'] );
			}

			$value = self::get_field_value( $field_name, $context_post_id );

			if ( empty( $value ) ) {
				continue;
			}

			$items = is_array( $value ) ? $value : [ $value ];

			foreach ( $items as $item ) {
				$term_id  = 0;
				$taxonomy = $field_taxonomy;

				if ( is_numeric( $item ) ) {
					$term_id = absint( $item );
				} elseif ( is_object( $item ) && ! empty( $item->term_id ) ) {
					$term_id = absint( $item->term_id );

					if ( empty( $taxonomy ) && ! empty( $item->taxonomy ) ) {
						$taxonomy = sanitize_key( $item->taxonomy );
					}
				} elseif ( is_array( $item ) && ! empty( $item['term_id'] ) ) {
					$term_id = absint( $item['term_id'] );

					if ( empty( $taxonomy ) && ! empty( $item['taxonomy'] ) ) {
						$taxonomy = sanitize_key( $item['taxonomy'] );
					}
				}

				if ( ! $term_id ) {
					continue;
				}

				if ( empty( $taxonomy ) ) {
					$term = get_term( $term_id );

					if ( $term && ! is_wp_error( $term ) && ! empty( $term->taxonomy ) ) {
						$taxonomy = sanitize_key( $term->taxonomy );
					}
				}

				if ( empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) ) {
					continue;
				}

				$grouped_terms[ $taxonomy ][] = $term_id;
			}
		}

		if ( empty( $grouped_terms ) ) {
			return [ '__eco_empty_acf_terms' => true ];
		}

		$tax_query = [ 'relation' => 'OR' ];

		foreach ( $grouped_terms as $taxonomy => $term_ids ) {
			$tax_query[] = [
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => array_values( array_unique( array_map( 'absint', $term_ids ) ) ),
			];
		}

		return $tax_query;
	}

	private static function get_field_value( $field, $post_id ) {
		if ( function_exists( 'get_field' ) ) {
			return get_field( $field, $post_id );
		}

		return get_post_meta( $post_id, $field, true );
	}

	private static function get_image_url( $post_id, $post_type ) {
		// if ( $post_type === 'podcast' ) {
		// 	$cover = self::get_field_value( 'cover_image', $post_id );

		// 	if ( is_array( $cover ) && ! empty( $cover['url'] ) ) {
		// 		return $cover['url'];
		// 	}

		// 	if ( is_numeric( $cover ) ) {
		// 		$url = wp_get_attachment_image_url( absint( $cover ), 'large' );

		// 		if ( $url ) {
		// 			return $url;
		// 		}
		// 	}
		// }

		if ( $post_type === 'event' ) {
			$big_image = self::get_field_value( 'big_image', $post_id );

			if ( is_array( $big_image ) && ! empty( $big_image['url'] ) ) {
				return $big_image['url'];
			}

			if ( is_numeric( $big_image ) ) {
				$url = wp_get_attachment_image_url( absint( $big_image ), 'large' );

				if ( $url ) {
					return $url;
				}
			}
		}

		return get_the_post_thumbnail_url( $post_id, 'large' );
	}

	private static function get_card_link( $post_id, $post_type ) {
		if ( $post_type === 'event' ) {
			$forwarding = self::get_field_value( 'forwarding', $post_id );

			if ( ! empty( $forwarding ) && is_string( $forwarding ) ) {
				return $forwarding;
			}
		}

		return get_permalink( $post_id );
	}

	private static function get_category_taxonomy( $post_type ) {
		$map = [
			'post'    => 'category',
			'event'   => 'event-category',
			'podcast' => 'podcast-category',
			'press'   => 'press-category',
			'study'   => 'study-category',
		];

		return $map[ $post_type ] ?? 'category';
	}

	private static function get_category_terms( $post_id, $post_type ) {
		$taxonomy = self::get_category_taxonomy( $post_type );

		if ( ! taxonomy_exists( $taxonomy ) ) {
			return [];
		}

		$terms = get_the_terms( $post_id, $taxonomy );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return [];
		}

		return array_values( $terms );
	}

	private static function get_category_label( $post_id, $post_type ) {
		$terms = self::get_category_terms( $post_id, $post_type );

		if ( empty( $terms ) ) {
			return '';
		}

		return $terms[0]->name;
	}

	private static function get_category_labels( $post_id, $post_type ) {
		$terms = self::get_category_terms( $post_id, $post_type );

		if ( empty( $terms ) ) {
			return '';
		}

		$names = wp_list_pluck( $terms, 'name' );
		$names = array_filter( array_map( 'sanitize_text_field', $names ) );

		return implode( ', ', $names );
	}

	private static function get_badge_label( $post_id, $post_type, $settings ) {
		$source = $settings['badge_source'] ?? 'auto';

		if ( $source === 'hide' ) {
			return '';
		}

		if ( $source === 'custom' ) {
			return sanitize_text_field( $settings['badge_custom_text'] ?? '' );
		}

		if ( $source === 'acf' ) {
			$field = ! empty( $settings['acf_badge_field'] ) ? sanitize_key( $settings['acf_badge_field'] ) : 'event_label';
			$value = self::get_field_value( $field, $post_id );

			return is_string( $value ) ? self::format_badge_value( $value ) : '';
		}

		if ( $source === 'term' ) {
			return self::get_category_label( $post_id, $post_type );
		}

		if ( $source === 'post_type' ) {
			return self::post_type_badge( $post_type );
		}

		if ( $source === 'auto' ) {
			if ( $post_type === 'event' ) {
				$event_label = self::get_field_value( 'event_label', $post_id );

				if ( ! empty( $event_label ) && is_string( $event_label ) ) {
					return self::format_badge_value( $event_label );
				}
			}

			return self::post_type_badge( $post_type );
		}

		return '';
	}

	private static function format_badge_value( $value ) {
		$value = str_replace( [ '_', '-' ], ' ', $value );
		$value = trim( $value );

		$return = ucwords( $value );
		$return = preg_replace( '/\bEco\b/u', 'eco', $return );

		return $return;
	}

	private static function post_type_badge( $post_type ) {
		$labels = [
			'post'    => 'News',
			'event'   => 'Event',
			'podcast' => 'Podcast',
			'press'   => 'Presse',
			'study'   => 'Study',
		];

		return $labels[ $post_type ] ?? ucfirst( $post_type );
	}

	private static function get_date_label( $post_id, $post_type ) {
		if ( $post_type === 'event' ) {
			$start_date = self::get_field_value( 'start_date', $post_id );

			if ( ! empty( $start_date ) ) {
				return self::format_event_date( $start_date );
			}
		}

		return get_the_date( 'j. F Y', $post_id );
	}

	private static function format_event_date( $date ) {
		if ( preg_match( '/^\d{8}$/', (string) $date ) ) {
			$dt = \DateTime::createFromFormat( 'Ymd', $date );

			if ( $dt ) {
				return $dt->format( 'd.m.Y' );
			}
		}

		return $date;
	}

	private static function get_location_label( $post_id, $post_type ) {
		if ( $post_type !== 'event' ) {
			return '';
		}

		$city       = self::get_field_value( 'city', $post_id );
		$venue_city = self::get_field_value( 'venue_city', $post_id );
		$venue      = self::get_field_value( 'venue', $post_id );

		if ( ! empty( $city ) ) {
			return $city;
		}

		if ( ! empty( $venue_city ) ) {
			return $venue_city;
		}

		if ( ! empty( $venue ) ) {
			return $venue;
		}

		return '';
	}

	private static function get_excerpt_text( $post_id, $post_type, $length ) {
		if ( $post_type === 'podcast' ) {
			$short_description = self::get_field_value( 'short_description', $post_id );

			if ( ! empty( $short_description ) && is_string( $short_description ) ) {
				return wp_trim_words( wp_strip_all_tags( $short_description ), $length );
			}
		}

		if ( $post_type === 'event' ) {
			$teaser = self::get_field_value( 'teaser_short_description', $post_id );

			if ( ! empty( $teaser ) && is_string( $teaser ) ) {
				return wp_trim_words( wp_strip_all_tags( $teaser ), $length );
			}
		}

		$excerpt = get_the_excerpt( $post_id );

		if ( ! empty( $excerpt ) ) {
			return wp_trim_words( wp_strip_all_tags( $excerpt ), $length );
		}

		return wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), $length );
	}

	public static function render_cards_html( $query, $settings ) {
		ob_start();

		while ( $query->have_posts() ) {
			$query->the_post();

			$post_id     = get_the_ID();
			$post_type   = get_post_type( $post_id );
			$style       = ! empty( $settings['card_style'] ) ? sanitize_key( $settings['card_style'] ) : 'default';
			$image_ratio = ! empty( $settings['image_ratio'] ) ? sanitize_html_class( $settings['image_ratio'] ) : '16-9';
			$image_url   = self::get_image_url( $post_id, $post_type );
			$link        = self::get_card_link( $post_id, $post_type );
			$badge       = self::get_badge_label( $post_id, $post_type, $settings );
			$category    = self::get_category_labels( $post_id, $post_type );
			$date        = self::get_date_label( $post_id, $post_type );
			$location    = self::get_location_label( $post_id, $post_type );
			$excerpt     = self::get_excerpt_text( $post_id, $post_type, ! empty( $settings['excerpt_length'] ) ? absint( $settings['excerpt_length'] ) : 22 );

			$show_image     = ! empty( $settings['show_image'] ) && $settings['show_image'] === 'yes';
			$show_category  = ! empty( $settings['show_category'] ) && $settings['show_category'] === 'yes';
			$show_excerpt   = ! empty( $settings['show_excerpt'] ) && $settings['show_excerpt'] === 'yes';
			$show_date      = ! empty( $settings['show_date'] ) && $settings['show_date'] === 'yes';
			$show_location  = ! empty( $settings['show_location'] ) && $settings['show_location'] === 'yes';
			$link_full_card = ! empty( $settings['link_full_card'] ) && $settings['link_full_card'] === 'yes';

			$featured_image_position = ! empty( $settings['featured_image_position'] ) && $settings['featured_image_position'] === 'right' ? 'right' : 'left';
			$badge_on_image = ! in_array( $style, [ 'featured', 'overlay' ], true );

			$classes = [
				'eco-content-card',
				'eco-content-card--' . $style,
				'eco-content-card--type-' . sanitize_html_class( $post_type ),
			];

			if ( $style === 'featured' ) {
				$classes[] = 'eco-content-card--featured-image-' . sanitize_html_class( $featured_image_position );
			}

			$tag = $link_full_card ? 'a' : 'article';
			?>
			<<?php echo esc_html( $tag ); ?>
				class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
				<?php if ( $link_full_card ) : ?>
					href="<?php echo esc_url( $link ); ?>"
				<?php endif; ?>
			>
				<?php if ( $show_image && ! empty( $image_url ) ) : ?>
					<div class="eco-content-card__image eco-content-card__image--<?php echo esc_attr( $image_ratio ); ?>">
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">

						<?php if ( $badge_on_image && ! empty( $badge ) ) : ?>
							<span class="eco-content-card__badge"><?php echo esc_html( $badge ); ?></span>
						<?php endif; ?>
					</div>
				<?php elseif ( ! empty( $badge ) ) : ?>
					<span class="eco-content-card__badge eco-content-card__badge--inline"><?php echo esc_html( $badge ); ?></span>
				<?php endif; ?>

				<div class="eco-content-card__body">
					<?php if ( ! $badge_on_image && ! empty( $badge ) ) : ?>
						<span class="eco-content-card__badge eco-content-card__badge--featured">
							<?php echo esc_html( $badge ); ?>
						</span>
					<?php endif; ?>

					<?php if ( $show_category && ! empty( $category ) ) : ?>
						<div class="eco-content-card__category"><?php echo esc_html( $category ); ?></div>
					<?php endif; ?>

					<h5 class="eco-content-card__title">
						<?php if ( ! $link_full_card ) : ?>
							<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
						<?php else : ?>
							<?php echo esc_html( get_the_title() ); ?>
						<?php endif; ?>
					</h5>

					<?php if ( $show_excerpt && ! empty( $excerpt ) ) : ?>
						<div class="eco-content-card__excerpt"><?php echo esc_html( $excerpt ); ?></div>
					<?php endif; ?>

					<?php if ( $show_date || ( $show_location && ! empty( $location ) ) ) : ?>
						<div class="eco-content-card__meta">
							<?php if ( $show_date && ! empty( $date ) ) : ?>
								<span class="eco-content-card__meta-item eco-icon eco-icon-calendar">
									<?php echo esc_html( $date ); ?>
								</span>
							<?php endif; ?>

							<?php if ( $show_location && ! empty( $location ) ) : ?>
								<span class="eco-content-card__meta-item eco-icon eco-icon-map-pin">
									<?php echo esc_html( $location ); ?>
								</span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</<?php echo esc_html( $tag ); ?>>
			<?php
		}

		wp_reset_postdata();

		return ob_get_clean();
	}

	public static function ajax_load_more() {
		check_ajax_referer( 'eco_content_cards_nonce', 'nonce' );

		$settings = [];

		if ( ! empty( $_POST['settings'] ) ) {
			$decoded = json_decode( wp_unslash( $_POST['settings'] ), true );

			if ( is_array( $decoded ) ) {
				$settings = $decoded;
			}
		}

		$page = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;

		$args  = self::build_query_args( $settings, $page );
		$query = new \WP_Query( $args );

		if ( ! $query->have_posts() ) {
			wp_send_json_success(
				[
					'html'     => '',
					'has_more' => false,
				]
			);
		}

		$html           = self::render_cards_html( $query, $settings );
		$posts_per_page = ! empty( $settings['posts_per_page'] ) ? absint( $settings['posts_per_page'] ) : 3;
		$base_offset    = ! empty( $settings['offset'] ) ? absint( $settings['offset'] ) : 0;
		$loaded         = $base_offset + ( $page * $posts_per_page );
		$has_more       = $query->found_posts > $loaded;

		wp_send_json_success(
			[
				'html'     => $html,
				'has_more' => $has_more,
			]
		);
	}
}

ContentCardsAjax::init();