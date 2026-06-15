<?php
namespace ElementorEco\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MemberCarousel extends Widget_Base {

	public function get_name() {
		return 'eco-member-carousel';
	}

	public function get_title() {
		return __( 'Member Carousel', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'eco-member-carousel-style' ];
	}

	public function get_script_depends() {
		return [ 'eco-member-carousel-script' ];
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
			'number_of_members',
			[
				'label'   => __( 'Number of Members', 'elementor-eco' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 12,
				'min'     => 1,
				'max'     => 100,
				'step'    => 1,
			]
		);

		$this->add_control(
			'member_categories',
			[
				'label'       => __( 'Member Categories', 'elementor-eco' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->get_term_options( 'member-category' ),
				'default'     => [],
			]
		);

		$this->add_control(
			'member_tags',
			[
				'label'       => __( 'Member Tags', 'elementor-eco' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->get_term_options( 'member-tag' ),
				'default'     => [],
			]
		);

		$this->add_control(
			'taxonomy_relation',
			[
				'label'     => __( 'Category and Tag Relation', 'elementor-eco' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'AND',
				'options'   => [
					'AND' => __( 'Match both', 'elementor-eco' ),
					'OR'  => __( 'Match either', 'elementor-eco' ),
				],
				'condition' => [
					'member_categories!' => [],
					'member_tags!'       => [],
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __( 'Order By', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'join_date',
				'options' => [
					'join_date' => __( 'Join Date', 'elementor-eco' ),
					'date'      => __( 'Published Date', 'elementor-eco' ),
					'title'     => __( 'Title', 'elementor-eco' ),
					'rand'      => __( 'Random', 'elementor-eco' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => __( 'Order', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => [
					'DESC' => __( 'Descending', 'elementor-eco' ),
					'ASC'  => __( 'Ascending', 'elementor-eco' ),
				],
				'condition' => [
					'orderby!' => 'rand',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'carousel_section',
			[
				'label' => __( 'Carousel', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => __( 'Columns', 'elementor-eco' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 5,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'min'            => 1,
				'max'            => 10,
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'label'        => __( 'Show Arrows', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_dots',
			[
				'label'        => __( 'Show Dots', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'loop',
			[
				'label'        => __( 'Loop', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => __( 'Autoplay', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'autoplay_delay',
			[
				'label'     => __( 'Autoplay Delay', 'elementor-eco' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4000,
				'min'       => 1000,
				'max'       => 15000,
				'step'      => 100,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'        => __( 'Pause on Hover', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'open_new_tab',
			[
				'label'        => __( 'Open Website in New Tab', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'box_style_section',
			[
				'label' => __( 'Boxes', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'space_between',
			[
				'label'      => __( 'Space Between', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 60 ],
				],
				'default'    => [ 'size' => 16, 'unit' => 'px' ],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label'      => __( 'Padding', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 80 ],
				],
				'default'    => [ 'size' => 24, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-member-carousel__item' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => __( 'Background Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eco-member-carousel__item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => __( 'Border Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e5e5e5',
				'selectors' => [
					'{{WRAPPER}} .eco-member-carousel__item' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 40 ],
				],
				'default'    => [ 'size' => 8, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-member-carousel__item' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .eco-member-carousel__item',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'logo_style_section',
			[
				'label' => __( 'Logos', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'logo_width',
			[
				'label'      => __( 'Maximum Width', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'%' => [ 'min' => 10, 'max' => 100 ],
				],
				'default'    => [ 'size' => 82, 'unit' => '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-member-carousel__image' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'logo_height',
			[
				'label'      => __( 'Maximum Height', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range'      => [
					'%' => [ 'min' => 10, 'max' => 100 ],
				],
				'default'    => [ 'size' => 70, 'unit' => '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-member-carousel__image' => 'max-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'navigation_style_section',
			[
				'label' => __( 'Navigation', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'navigation_color',
			[
				'label'     => __( 'Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1d1d1f',
				'selectors' => [
					'{{WRAPPER}} .eco-member-carousel__prev, {{WRAPPER}} .eco-member-carousel__next' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eco-member-carousel__pagination .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$members  = $this->get_members( $settings );

		if ( empty( $members ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'No members found for the selected query.', 'elementor-eco' ) . '</p>';
			}
			return;
		}

		$columns_desktop = max( 1, (int) ( $settings['columns'] ?? 5 ) );
		$columns_tablet  = max( 1, (int) ( $settings['columns_tablet'] ?? min( 3, $columns_desktop ) ) );
		$columns_mobile  = max( 1, (int) ( $settings['columns_mobile'] ?? min( 2, $columns_tablet ) ) );
		$space_desktop   = isset( $settings['space_between']['size'] ) ? (int) $settings['space_between']['size'] : 16;
		$space_tablet    = isset( $settings['space_between_tablet']['size'] ) ? (int) $settings['space_between_tablet']['size'] : $space_desktop;
		$space_mobile    = isset( $settings['space_between_mobile']['size'] ) ? (int) $settings['space_between_mobile']['size'] : $space_tablet;

		$this->add_render_attribute(
			'carousel',
			[
				'class'                  => 'eco-member-carousel',
				'data-columns'           => $columns_desktop,
				'data-columns-tablet'    => $columns_tablet,
				'data-columns-mobile'    => $columns_mobile,
				'data-space'             => max( 0, $space_desktop ),
				'data-space-tablet'      => max( 0, $space_tablet ),
				'data-space-mobile'      => max( 0, $space_mobile ),
				'data-arrows'            => ( 'yes' === ( $settings['show_arrows'] ?? '' ) ) ? '1' : '0',
				'data-dots'              => ( 'yes' === ( $settings['show_dots'] ?? '' ) ) ? '1' : '0',
				'data-loop'              => ( 'yes' === ( $settings['loop'] ?? '' ) ) ? '1' : '0',
				'data-autoplay'          => ( 'yes' === ( $settings['autoplay'] ?? '' ) ) ? '1' : '0',
				'data-autoplay-delay'    => max( 1000, (int) ( $settings['autoplay_delay'] ?? 4000 ) ),
				'data-pause-on-hover'    => ( 'yes' === ( $settings['pause_on_hover'] ?? '' ) ) ? '1' : '0',
			]
		);
		?>
		<div <?php echo $this->get_render_attribute_string( 'carousel' ); ?>>
			<div class="swiper eco-member-carousel__swiper">
				<div class="swiper-wrapper">
					<?php foreach ( $members as $member ) : ?>
						<div class="swiper-slide">
							<?php $this->render_member( $member, $settings ); ?>
						</div>
					<?php endforeach; ?>
				</div>

				<?php if ( 'yes' === ( $settings['show_dots'] ?? '' ) ) : ?>
					<div class="swiper-pagination eco-member-carousel__pagination"></div>
				<?php endif; ?>
			</div>

			<?php if ( 'yes' === ( $settings['show_arrows'] ?? '' ) ) : ?>
				<button type="button" class="eco-member-carousel__prev" aria-label="<?php echo esc_attr__( 'Previous members', 'elementor-eco' ); ?>"></button>
				<button type="button" class="eco-member-carousel__next" aria-label="<?php echo esc_attr__( 'Next members', 'elementor-eco' ); ?>"></button>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_member( array $member, array $settings ) {
		$title   = $member['title'];
		$website = $member['website'];
		$image   = $member['image'];
		$is_link = ! empty( $website );
		$tag     = $is_link ? 'a' : 'div';
		$attrs   = 'class="eco-member-carousel__item"';

		if ( $is_link ) {
			$attrs .= ' href="' . esc_url( $website ) . '"';
			if ( 'yes' === ( $settings['open_new_tab'] ?? '' ) ) {
				$attrs .= ' target="_blank" rel="noopener"';
			}
			$attrs .= ' aria-label="' . esc_attr( $title ) . '"';
		}
		?>
		<<?php echo esc_html( $tag ); ?> <?php echo $attrs; ?>>
			<?php if ( $image ) : ?>
				<?php echo wp_get_attachment_image(
					$member['thumbnail_id'],
					'medium',
					false,
					[
						'class'   => 'eco-member-carousel__image',
						'alt'     => esc_attr( $title ),
						'loading' => 'lazy',
					]
				); ?>
			<?php else : ?>
				<span class="eco-member-carousel__name"><?php echo esc_html( $title ); ?></span>
			<?php endif; ?>
		</<?php echo esc_html( $tag ); ?>>
		<?php
	}

	private function get_members( array $settings ) {
		$tax_query  = [];
		$categories = array_filter( array_map( 'intval', (array) ( $settings['member_categories'] ?? [] ) ) );
		$tags       = array_filter( array_map( 'intval', (array) ( $settings['member_tags'] ?? [] ) ) );

		if ( $categories ) {
			$tax_query[] = [
				'taxonomy' => 'member-category',
				'field'    => 'term_id',
				'terms'    => $categories,
			];
		}

		if ( $tags ) {
			$tax_query[] = [
				'taxonomy' => 'member-tag',
				'field'    => 'term_id',
				'terms'    => $tags,
			];
		}

		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = ( 'OR' === ( $settings['taxonomy_relation'] ?? 'AND' ) ) ? 'OR' : 'AND';
		}

		$args = [
			'post_type'              => 'member',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		];

		if ( $tax_query ) {
			$args['tax_query'] = $tax_query;
		}

		$orderby = $settings['orderby'] ?? 'join_date';
		$order   = ( 'ASC' === ( $settings['order'] ?? 'DESC' ) ) ? 'ASC' : 'DESC';

		if ( 'rand' === $orderby ) {
			$args['orderby'] = 'rand';
		} elseif ( 'join_date' !== $orderby ) {
			$args['orderby'] = in_array( $orderby, [ 'date', 'title' ], true ) ? $orderby : 'date';
			$args['order']   = $order;
		}

		$query   = new \WP_Query( $args );
		$members = [];

		foreach ( $query->posts as $post ) {
			$thumbnail_id = get_post_thumbnail_id( $post->ID );
			$join_date    = function_exists( 'get_field' ) ? get_field( 'join_date', $post->ID ) : get_post_meta( $post->ID, 'join_date', true );
			$website      = function_exists( 'get_field' ) ? get_field( 'website', $post->ID ) : get_post_meta( $post->ID, 'website', true );

			$members[] = [
				'id'           => $post->ID,
				'title'        => get_the_title( $post ),
				'website'      => $website,
				'thumbnail_id' => $thumbnail_id,
				'image'        => $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'medium' ) : '',
				'join_time'    => $this->parse_join_date( $join_date, $post->post_date ),
				'post_time'    => strtotime( $post->post_date ),
			];
		}

		wp_reset_postdata();

		if ( 'join_date' === $orderby ) {
			usort(
				$members,
				static function ( $a, $b ) use ( $order ) {
					$result = $a['join_time'] <=> $b['join_time'];
					return 'ASC' === $order ? $result : -$result;
				}
			);
		}

		$limit = max( 1, (int) ( $settings['number_of_members'] ?? 12 ) );
		return array_slice( $members, 0, $limit );
	}

	private function parse_join_date( $date, $fallback ) {
		if ( is_string( $date ) && preg_match( '/^(\d{2})-(\d{2})-(\d{4})$/', $date, $matches ) ) {
			return mktime( 0, 0, 0, (int) $matches[2], (int) $matches[1], (int) $matches[3] );
		}

		$timestamp = is_string( $date ) ? strtotime( $date ) : false;
		return $timestamp ?: strtotime( $fallback );
	}

	private function get_term_options( $taxonomy ) {
		$options = [];
		$terms   = get_terms(
			[
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			]
		);

		if ( is_wp_error( $terms ) ) {
			return $options;
		}

		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}

		return $options;
	}
}
