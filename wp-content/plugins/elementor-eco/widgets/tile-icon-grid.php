<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TileIconGrid extends Widget_Base {

	public function get_name() {
		return 'eco-tile-icon-grid';
	}

	public function get_title() {
		return __( 'Tile Icon Grid', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'eco-tile-icon-grid-style' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'query_section',
			[
				'label' => __( 'Query', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'include_terms',
			[
				'label'       => __( 'Terms', 'elementor-eco' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => $this->get_tile_term_options(),
				'description' => __( 'Select one or more tile tags/categories. Leave empty to show all tiles.', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'   => __( 'Number of Tiles', 'elementor-eco' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5,
				'min'     => 1,
				'max'     => 100,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __( 'Order By', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'menu_order',
				'options' => [
					'menu_order' => __( 'Menu Order', 'elementor-eco' ),
					'title'      => __( 'Title', 'elementor-eco' ),
					'date'       => __( 'Date', 'elementor-eco' ),
					'ID'         => __( 'ID', 'elementor-eco' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => __( 'Order', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => [
					'ASC'  => 'ASC',
					'DESC' => 'DESC',
				],
			]
		);

		$this->end_controls_section();
	}

	private function get_tile_term_options() {
		$options = [];

		$taxonomies = [
			'tile-tag'      => __( 'Tile Tags', 'elementor-eco' ),
			'tile-category' => __( 'Tile Categories', 'elementor-eco' ),
		];

		foreach ( $taxonomies as $taxonomy => $label ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}

			$terms = get_terms(
				[
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				]
			);

			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				continue;
			}

			foreach ( $terms as $term ) {
				$key             = $taxonomy . '|' . $term->slug;
				$options[ $key ] = $label . ': ' . $term->name;
			}
		}

		return $options;
	}

	private function build_terms_tax_query( $selected_terms ) {
		if ( empty( $selected_terms ) || ! is_array( $selected_terms ) ) {
			return [];
		}

		$grouped_terms = [];

		foreach ( $selected_terms as $selected_term ) {
			if ( ! is_string( $selected_term ) || strpos( $selected_term, '|' ) === false ) {
				continue;
			}

			list( $taxonomy, $slug ) = explode( '|', $selected_term, 2 );

			if ( ! in_array( $taxonomy, [ 'tile-tag', 'tile-category' ], true ) ) {
				continue;
			}

			if ( empty( $slug ) ) {
				continue;
			}

			$grouped_terms[ $taxonomy ][] = sanitize_title( $slug );
		}

		if ( empty( $grouped_terms ) ) {
			return [];
		}

		$tax_query = [
			'relation' => 'OR',
		];

		foreach ( $grouped_terms as $taxonomy => $slugs ) {
			$tax_query[] = [
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => array_unique( $slugs ),
			];
		}

		return $tax_query;
	}

	private function get_icon_url_from_value( $value ) {
		if ( empty( $value ) ) {
			return '';
		}

		if ( is_string( $value ) && filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return $value;
		}

		if ( is_numeric( $value ) ) {
			$url = wp_get_attachment_image_url( absint( $value ), 'full' );
			return $url ? $url : '';
		}

		if ( is_array( $value ) ) {
			if ( ! empty( $value['url'] ) && is_string( $value['url'] ) ) {
				return $value['url'];
			}

			if ( ! empty( $value['ID'] ) ) {
				$url = wp_get_attachment_image_url( absint( $value['ID'] ), 'full' );
				return $url ? $url : '';
			}

			if ( ! empty( $value['id'] ) ) {
				$url = wp_get_attachment_image_url( absint( $value['id'] ), 'full' );
				return $url ? $url : '';
			}
		}

		return '';
	}

	private function normalize_tile_link( $link, $fallback = '' ) {
		if ( empty( $link ) ) {
			return $fallback;
		}

		if ( is_string( $link ) ) {
			return $link;
		}

		if ( is_array( $link ) && ! empty( $link['url'] ) && is_string( $link['url'] ) ) {
			return $link['url'];
		}

		return $fallback;
	}


	private function is_tile_page_disabled( $post_id ) {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return (bool) get_field( 'disable_tile_page', $post_id );
	}

	private function render_icon( $icon ) {
		if ( empty( $icon ) ) {
			return '';
		}

		$type  = '';
		$value = '';

		if ( is_array( $icon ) ) {
			$type  = $icon['type'] ?? '';
			$value = $icon['value'] ?? '';
		} elseif ( is_string( $icon ) ) {
			$value = $icon;
		}

		if ( empty( $value ) ) {
			return '';
		}

		if (
			( is_string( $type ) && in_array( $type, [ 'dashicon', 'dashicons' ], true ) )
			|| ( is_string( $value ) && strpos( $value, 'dashicons-' ) === 0 )
		) {
			return '<span class="eco-tile-icon__dashicon dashicons ' . esc_attr( $value ) . '" aria-hidden="true"></span>';
		}

		$url = $this->get_icon_url_from_value( $value );

		if ( ! empty( $url ) ) {
			if ( function_exists( 'eco_theme_render_icon_url' ) ) {
				return eco_theme_render_icon_url(
					$url,
					'eco-tile-icon__svg',
					'eco-tile-icon__image',
					false
				);
			}

			return '<img class="eco-tile-icon__image" src="' . esc_url( $url ) . '" alt="" loading="lazy">';
		}

		if ( is_string( $value ) ) {
			return '<span class="eco-tile-icon__custom ' . esc_attr( $value ) . '" aria-hidden="true"></span>';
		}

		return '';
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$orderby_allowed = [ 'menu_order', 'title', 'date', 'ID' ];
		$orderby         = ! empty( $settings['orderby'] ) && in_array( $settings['orderby'], $orderby_allowed, true )
			? $settings['orderby']
			: 'menu_order';

		$args = [
			'post_type'      => 'tile',
			'post_status'    => 'publish',
			'posts_per_page' => ! empty( $settings['posts_per_page'] ) ? absint( $settings['posts_per_page'] ) : 5,
			'orderby'        => $orderby,
			'order'          => ! empty( $settings['order'] ) && $settings['order'] === 'DESC' ? 'DESC' : 'ASC',
		];

		$tax_query = $this->build_terms_tax_query( $settings['include_terms'] ?? [] );

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		$query = new \WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return;
		}

		?>
		<div class="eco-tile-icon-grid">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();

				$post_id = get_the_ID();

				$title       = get_the_title();
				$link              = function_exists( 'get_field' ) ? get_field( 'link', $post_id ) : '';
				$icon              = function_exists( 'get_field' ) ? get_field( 'icon', $post_id ) : '';
				$description       = function_exists( 'get_field' ) ? get_field( 'description', $post_id ) : '';
				$tile_page_disabled = $this->is_tile_page_disabled( $post_id );

				if ( empty( $description ) ) {
					$description = get_the_excerpt( $post_id );
				}

				$link = $tile_page_disabled ? '' : $this->normalize_tile_link( $link, get_permalink( $post_id ) );
				$tag  = $tile_page_disabled ? 'div' : 'a';
				?>
				<article class="eco-tile-icon-card<?php echo $tile_page_disabled ? ' eco-tile-icon-card--disabled' : ''; ?>">
					<<?php echo esc_html( $tag ); ?> class="eco-tile-icon-card__link"<?php echo ! $tile_page_disabled ? ' href="' . esc_url( $link ) . '"' : ''; ?>>
						<?php if ( ! empty( $icon ) ) : ?>
							<div class="eco-tile-icon-card__icon">
								<?php echo $this->render_icon( $icon ); ?>
							</div>
						<?php endif; ?>

						<h4 class="eco-tile-icon-card__title">
							<?php echo esc_html( $title ); ?>
						</h4>

						<?php if ( ! empty( $description ) ) : ?>
							<div class="eco-tile-icon-card__description">
								<?php echo esc_html( $description ); ?>
							</div>
						<?php endif; ?>

						<div class="eco-tile-icon-card__more">
							<?php esc_html_e( 'Learn more', 'elementor-eco' ); ?>
							<span aria-hidden="true">→</span>
						</div>
					</<?php echo esc_html( $tag ); ?>>
				</article>
			<?php endwhile; ?>
		</div>
		<?php

		wp_reset_postdata();
	}
}