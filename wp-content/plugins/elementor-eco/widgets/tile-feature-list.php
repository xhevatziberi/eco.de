<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TileFeatureList extends Widget_Base {

	public function get_name() {
		return 'eco-tile-feature-list';
	}

	public function get_title() {
		return __( 'Tile Feature List', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'eco-tile-feature-list-style' ];
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
			'query_source',
			[
				'label'   => __( 'Query Source', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'terms',
				'options' => [
					'terms'    => __( 'Terms / All Tiles', 'elementor-eco' ),
					'children' => __( 'Child Tiles of Current Tile', 'elementor-eco' ),
				],
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
				'condition'   => [
					'query_source' => 'terms',
				],
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'   => __( 'Number of Tiles', 'elementor-eco' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4,
				'min'     => 1,
				'max'     => 50,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'     => __( 'Order By', 'elementor-eco' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'menu_order',
				'options'   => [
					'menu_order' => __( 'Menu Order', 'elementor-eco' ),
					'title'      => __( 'Title', 'elementor-eco' ),
					'date'       => __( 'Date', 'elementor-eco' ),
					'ID'         => __( 'ID', 'elementor-eco' ),
				],
				'condition' => [
					'query_source' => 'terms',
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'     => __( 'Order', 'elementor-eco' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'ASC',
				'options'   => [
					'ASC'  => 'ASC',
					'DESC' => 'DESC',
				],
				'condition' => [
					'query_source' => 'terms',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'layout_section',
			[
				'label' => __( 'Layout', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'alternating',
			[
				'label'        => __( 'Alternate Layout', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-eco' ),
				'label_off'    => __( 'No', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'start_image_position',
			[
				'label'   => __( 'First Image Position', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => __( 'Left', 'elementor-eco' ),
					'right' => __( 'Right', 'elementor-eco' ),
				],
			]
		);

		$this->add_control(
			'show_number',
			[
				'label'        => __( 'Show Number', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-eco' ),
				'label_off'    => __( 'No', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'number_format',
			[
				'label'       => __( 'Number Format', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '%02d',
				'description' => __( 'Example: %02d gives 01, 02, 03. Use %d for 1, 2, 3.', 'elementor-eco' ),
				'condition'   => [
					'show_number' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_link',
			[
				'label'        => __( 'Link Items', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-eco' ),
				'label_off'    => __( 'No', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'image_ratio',
			[
				'label'   => __( 'Image Ratio', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '16-9',
				'options' => [
					'16-9' => '16:9',
					'4-3'  => '4:3',
					'3-2'  => '3:2',
					'1-1'  => '1:1',
					'4-5'  => '4:5',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Style', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'accent_color',
			[
				'label'     => __( 'Accent Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e2001a',
				'selectors' => [
					'{{WRAPPER}} .eco-tile-feature-list' => '--eco-tile-feature-accent: {{VALUE}}; --eco-tile-feature-number-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'placeholder_background',
			[
				'label'     => __( 'Placeholder Background', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f3f4f6',
				'selectors' => [
					'{{WRAPPER}} .eco-tile-feature-list' => '--eco-tile-feature-placeholder-bg: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'placeholder_color',
			[
				'label'     => __( 'Placeholder Text Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e2001a',
				'selectors' => [
					'{{WRAPPER}} .eco-tile-feature-list' => '--eco-tile-feature-placeholder-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'number_color',
			[
				'label'     => __( 'Number Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eco-tile-feature-list' => '--eco-tile-feature-number-color: {{VALUE}};',
				],
				'condition' => [
					'show_number' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Title Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1d1d1f',
				'selectors' => [
					'{{WRAPPER}} .eco-tile-feature-list__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => __( 'Description Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6f6f6f',
				'selectors' => [
					'{{WRAPPER}} .eco-tile-feature-list__description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'      => __( 'Row Gap', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 56,
					'unit' => 'px',
				],
				'range'      => [
					'px' => [
						'min' => 16,
						'max' => 140,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eco-tile-feature-list' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'      => __( 'Column Gap', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 48,
					'unit' => 'px',
				],
				'range'      => [
					'px' => [
						'min' => 16,
						'max' => 120,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eco-tile-feature-list__item' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label'      => __( 'Image Width', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'default'    => [
					'size' => 40,
					'unit' => '%',
				],
				'range'      => [
					'%' => [
						'min' => 25,
						'max' => 60,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eco-tile-feature-list' => '--eco-tile-feature-image-width: {{SIZE}}%;',
				],
			]
		);

		$this->add_responsive_control(
			'image_radius',
			[
				'label'      => __( 'Image Radius', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 6,
					'unit' => 'px',
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 40,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eco-tile-feature-list__image' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __( 'Title Typography', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-tile-feature-list__title',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'label'    => __( 'Description Typography', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-tile-feature-list__description',
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
				$options[ $taxonomy . '|' . $term->slug ] = $label . ': ' . $term->name;
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

	private function get_description( $post_id ) {
		if ( ! function_exists( 'get_field' ) ) {
			return '';
		}

		$overview_description = (string) get_field( 'theme_overview_description', $post_id );

		if ( '' !== trim( $overview_description ) ) {
			return $overview_description;
		}

		return (string) get_field( 'description', $post_id );
	}

	private function get_valid_hex_color( $color ) {
		if ( is_string( $color ) && preg_match( '/^#([A-Fa-f0-9]{3}){1,2}$/', trim( $color ) ) ) {
			return trim( $color );
		}

		return '';
	}

	private function get_tile_accent_color( $post_id ) {
		$color = function_exists( 'get_field' ) ? get_field( 'color', $post_id ) : '';
		$color = $this->get_valid_hex_color( $color );

		return $color ? $color : '#e2001a';
	}

	private function is_tile_page_disabled( $post_id ) {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return (bool) get_field( 'disable_tile_page', $post_id );
	}

	private function get_empty_state_accent_color( $settings = [] ) {
		$context_post_id = get_queried_object_id();

		if ( $context_post_id && get_post_type( $context_post_id ) === 'tile' ) {
			$color = function_exists( 'get_field' ) ? get_field( 'color', $context_post_id ) : '';
			$color = $this->get_valid_hex_color( $color );

			if ( $color ) {
				return $color;
			}
		}

		if ( ! empty( $settings['accent_color'] ) ) {
			$color = $this->get_valid_hex_color( $settings['accent_color'] );

			if ( $color ) {
				return $color;
			}
		}

		return '#e2001a';
	}

	private function get_first_letter( $title ) {
		$title = trim( wp_strip_all_tags( (string) $title ) );

		if ( $title === '' ) {
			return '?';
		}

		if ( function_exists( 'mb_substr' ) ) {
			return mb_substr( $title, 0, 1 );
		}

		return substr( $title, 0, 1 );
	}

	private function render_empty_state( $settings = [] ) {
		$accent_color = $this->get_empty_state_accent_color( $settings );
		?>
		<div class="eco-empty-state eco-tile-feature-list__empty" style="--eco-empty-state-accent: <?php echo esc_attr( $accent_color ); ?>;">
			<div class="eco-empty-state__icon" aria-hidden="true">
				<svg viewBox="0 0 24 24">
					<path d="M12 5v14"></path>
					<path d="M5 12h14"></path>
				</svg>
			</div>

			<h3 class="eco-empty-state__title">
				<?php esc_html_e( 'Keine Themen gefunden', 'elementor-eco' ); ?>
			</h3>

			<p class="eco-empty-state__text">
				<?php esc_html_e( 'Für diesen Bereich sind aktuell keine passenden Themen verfügbar.', 'elementor-eco' ); ?>
			</p>
		</div>
		<?php
	}

	protected function render() {
		$settings     = $this->get_settings_for_display();
		$query_source = ! empty( $settings['query_source'] ) ? $settings['query_source'] : 'terms';

		$orderby_allowed = [ 'menu_order', 'title', 'date', 'ID' ];
		$orderby         = ! empty( $settings['orderby'] ) && in_array( $settings['orderby'], $orderby_allowed, true )
			? $settings['orderby']
			: 'menu_order';

		$args = [
			'post_type'      => 'tile',
			'post_status'    => 'publish',
			'posts_per_page' => ! empty( $settings['posts_per_page'] ) ? absint( $settings['posts_per_page'] ) : 4,
			'orderby'        => $orderby,
			'order'          => ! empty( $settings['order'] ) && $settings['order'] === 'DESC' ? 'DESC' : 'ASC',
		];

		if ( $query_source === 'children' ) {
			$current_tile_id = get_queried_object_id();

			if ( ! $current_tile_id || get_post_type( $current_tile_id ) !== 'tile' ) {
				$this->render_empty_state( $settings );
				return;
			}

			$args['post_parent'] = $current_tile_id;
			$args['orderby']     = 'menu_order';
			$args['order']       = 'ASC';
		} else {
			$tax_query = $this->build_terms_tax_query( $settings['include_terms'] ?? [] );

			if ( ! empty( $tax_query ) ) {
				$args['tax_query'] = $tax_query;
			}
		}

		$query = new \WP_Query( $args );

		if ( ! $query->have_posts() ) {
			$this->render_empty_state( $settings );
			return;
		}

		$show_number          = ! empty( $settings['show_number'] ) && $settings['show_number'] === 'yes';
		$show_link            = ! empty( $settings['show_link'] ) && $settings['show_link'] === 'yes';
		$alternating          = ! empty( $settings['alternating'] ) && $settings['alternating'] === 'yes';
		$start_image_position = ! empty( $settings['start_image_position'] ) && $settings['start_image_position'] === 'right' ? 'right' : 'left';
		$image_ratio          = ! empty( $settings['image_ratio'] ) ? sanitize_html_class( $settings['image_ratio'] ) : '16-9';
		$number_format        = ! empty( $settings['number_format'] ) ? $settings['number_format'] : '%02d';

		?>
		<div class="eco-tile-feature-list">
			<?php
			$index = 0;

			while ( $query->have_posts() ) :
				$query->the_post();

				$index++;
				$post_id      = get_the_ID();
				$title        = get_the_title();
				$description  = $this->get_description( $post_id );
				$image_url    = get_the_post_thumbnail_url( $post_id, 'large' );
				$link         = function_exists( 'get_field' ) ? get_field( 'link', $post_id ) : '';
				$link         = $this->normalize_tile_link( $link, get_permalink( $post_id ) );
				$accent_color = $this->get_tile_accent_color( $post_id );
				$link_disabled = $this->is_tile_page_disabled( $post_id );

				$image_position = $start_image_position;

				if ( $alternating && $index % 2 === 0 ) {
					$image_position = $start_image_position === 'left' ? 'right' : 'left';
				}

				$classes = [
					'eco-tile-feature-list__item',
					'eco-tile-feature-list__item--image-' . $image_position,
				];

				$tag = ( $show_link && ! $link_disabled && ! empty( $link ) ) ? 'a' : 'article';
				?>
				<<?php echo esc_html( $tag ); ?>
					class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
					style="--eco-tile-feature-accent: <?php echo esc_attr( $accent_color ); ?>; --eco-tile-feature-number-bg: <?php echo esc_attr( $accent_color ); ?>; --eco-tile-feature-placeholder-color: <?php echo esc_attr( $accent_color ); ?>;"
					<?php if ( $tag === 'a' ) : ?>
						href="<?php echo esc_url( $link ); ?>"
					<?php endif; ?>
				>
					<div class="eco-tile-feature-list__media">
						<div class="eco-tile-feature-list__image eco-tile-feature-list__image--<?php echo esc_attr( $image_ratio ); ?> <?php echo empty( $image_url ) ? 'eco-tile-feature-list__image--placeholder' : ''; ?>">
							<?php if ( ! empty( $image_url ) ) : ?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
							<?php else : ?>
								<div class="eco-tile-feature-list__placeholder">
									<span><?php echo esc_html( $this->get_first_letter( $title ) ); ?></span>
								</div>
							<?php endif; ?>

							<?php if ( $show_number ) : ?>
								<span class="eco-tile-feature-list__number">
									<?php echo esc_html( sprintf( $number_format, $index ) ); ?>
								</span>
							<?php endif; ?>
						</div>
					</div>

					<div class="eco-tile-feature-list__content">
						<span class="eco-tile-feature-list__line"></span>

						<h3 class="eco-tile-feature-list__title">
							<?php echo esc_html( $title ); ?>
						</h3>

						<?php if ( ! empty( $description ) ) : ?>
							<div class="eco-tile-feature-list__description">
								<?php echo wp_kses( $description, array( 'br' => array() ) ); ?>
							</div>
						<?php endif; ?>
					</div>
				</<?php echo esc_html( $tag ); ?>>
			<?php endwhile; ?>
		</div>
		<?php

		wp_reset_postdata();
	}
}
