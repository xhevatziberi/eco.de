<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ContentCards extends Widget_Base {

	public function get_name() {
		return 'eco-content-cards';
	}

	public function get_title() {
		return __( 'eco Content Cards', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'eco-content-cards-style' ];
	}

	public function get_script_depends() {
		return [ 'eco-content-cards-script' ];
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
			'post_types',
			[
				'label'       => __( 'Post Types', 'elementor-eco' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'default'     => [ 'post' ],
				'options'     => $this->get_available_post_types(),
			]
		);

		$this->add_control(
			'query_source',
			[
				'label'   => __( 'Query Source', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => [
					'manual'            => __( 'Manual / Widget Terms', 'elementor-eco' ),
					'current_acf_terms' => __( 'Current ACF Terms', 'elementor-eco' ),
					'current_taxonomy'  => __( 'Current Taxonomy Term', 'elementor-eco' ),
				],
			]
		);

		$this->add_control(
			'include_terms',
			[
				'label'       => __( 'Include Terms', 'elementor-eco' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->get_taxonomy_term_options(),
				'description' => __( 'Optional. Works across posts, events, podcasts and press.', 'elementor-eco' ),
				'condition'   => [
					'query_source' => 'manual',
				],
			]
		);

		$this->add_control(
			'acf_term_fields',
			[
				'label'       => __( 'ACF Field Name(s)', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => 'include_terms',
				'description' => __( 'Comma-separated ACF taxonomy field names from the current post/CPT. Example: event_categories,event_tags', 'elementor-eco' ),
				'condition'   => [
					'query_source' => 'current_acf_terms',
				],
			]
		);

		$this->add_control(
			'manual_ids',
			[
				'label'       => __( 'Manual IDs', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '12, 24, 35',
				'description' => __( 'Optional. If filled, only these posts are shown.', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'   => __( 'Posts Per Page', 'elementor-eco' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3,
				'min'     => 1,
				'max'     => 24,
			]
		);

		$this->add_control(
			'offset',
			[
				'label'   => __( 'Offset', 'elementor-eco' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'max'     => 100,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __( 'Order By', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'date'        => __( 'Date', 'elementor-eco' ),
					'title'       => __( 'Title', 'elementor-eco' ),
					'menu_order'  => __( 'Menu Order', 'elementor-eco' ),
					'rand'        => __( 'Random', 'elementor-eco' ),
					'event_start' => __( 'Event Start Date', 'elementor-eco' ),
					'post__in'    => __( 'Manual Order', 'elementor-eco' ),
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
					'ASC'  => 'ASC',
					'DESC' => 'DESC',
				],
			]
		);

		$this->add_control(
			'event_filter',
			[
				'label'   => __( 'Event Filter', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => [
					'all'    => __( 'All Events', 'elementor-eco' ),
					'future' => __( 'Future Events Only', 'elementor-eco' ),
					'past'   => __( 'Past Events Only', 'elementor-eco' ),
				],
				'description' => __( 'Only applies when querying events.', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'highlight_filter',
			[
				'label'       => __( 'Highlight Filter', 'elementor-eco' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'all',
				'options'     => [
					'all'              => __( 'All', 'elementor-eco' ),
					'highlighted_only' => __( 'Highlighted only', 'elementor-eco' ),
					'skip_first'       => __( 'Skip first highlight', 'elementor-eco' ),
				],
				'description' => __( 'Uses the ACF/SCF true/false field is_highlight. “Skip first highlight” excludes only the newest highlighted item matching this widget query.', 'elementor-eco' ),
			],
		);

		$this->add_control(
			'exclude_current',
			[
				'label'        => __( 'Exclude Current Post', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'display_section',
			[
				'label' => __( 'Display', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'card_style',
			[
				'label'   => __( 'Card Style', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => __( 'Default', 'elementor-eco' ),
					'event'    => __( 'Event', 'elementor-eco' ),
					'overlay'  => __( 'Overlay / Highlight', 'elementor-eco' ),
					'featured' => __( 'Featured Horizontal', 'elementor-eco' ),
				],
			]
		);


		$this->add_control(
			'featured_image_position',
			[
				'label'   => __( 'Image Position', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => __( 'Left', 'elementor-eco' ),
					'right' => __( 'Right', 'elementor-eco' ),
				],
				'condition' => [
					'card_style' => 'featured',
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'           => __( 'Columns', 'elementor-eco' ),
				'type'            => Controls_Manager::NUMBER,
				'default'         => 3,
				'tablet_default'  => 2,
				'mobile_default'  => 1,
				'min'             => 1,
				'max'             => 6,
				'selectors'       => [
					'{{WRAPPER}} .eco-content-cards__grid' => '--eco-content-cards-columns: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'image_ratio',
			[
				'label'   => __( 'Image Ratio', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '16-9',
				'options' => [
					'16-9'  => '16:9',
					'4-3'   => '4:3',
					'3-2'   => '3:2',
					'1-1'   => '1:1',
					'21-9'  => '21:9',
				],
			]
		);

		$this->add_control(
			'badge_source',
			[
				'label'   => __( 'Badge Source', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto'      => __( 'Auto', 'elementor-eco' ),
					'post_type' => __( 'Post Type', 'elementor-eco' ),
					'term'      => __( 'First Term', 'elementor-eco' ),
					'acf'       => __( 'ACF Field', 'elementor-eco' ),
					'custom'    => __( 'Custom Text', 'elementor-eco' ),
					'hide'      => __( 'Hide', 'elementor-eco' ),
				],
			]
		);

		$this->add_control(
			'badge_custom_text',
			[
				'label'     => __( 'Custom Badge Text', 'elementor-eco' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'News',
				'condition' => [
					'badge_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'acf_badge_field',
			[
				'label'       => __( 'ACF Badge Field', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'event_label',
				'placeholder' => 'event_label',
				'condition'   => [
					'badge_source' => 'acf',
				],
			]
		);

		$this->add_control(
			'show_image',
			[
				'label'        => __( 'Show Image', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_category',
			[
				'label'        => __( 'Show Category', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label'        => __( 'Show Excerpt', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label'   => __( 'Excerpt Length', 'elementor-eco' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 22,
				'min'     => 5,
				'max'     => 80,
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_date',
			[
				'label'        => __( 'Show Date', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_location',
			[
				'label'        => __( 'Show Event Location', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'link_full_card',
			[
				'label'        => __( 'Link Full Card', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'load_more_section',
			[
				'label' => __( 'Ajax Load More', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'enable_load_more',
			[
				'label'        => __( 'Enable Load More', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'load_more_text',
			[
				'label'   => __( 'Button Text', 'elementor-eco' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Weitere laden', 'elementor-eco' ),
				'condition' => [
					'enable_load_more' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'card_style_section',
			[
				'label' => __( 'Card Style', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'grid_gap',
			[
				'label'      => __( 'Gap', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 'size' => 24, 'unit' => 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'selectors'  => [
					'{{WRAPPER}} .eco-content-cards__grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_background',
			[
				'label'     => __( 'Card Background', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eco-content-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_border_color',
			[
				'label'     => __( 'Border Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8e8e8',
				'selectors' => [
					'{{WRAPPER}} .eco-content-card' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_radius',
			[
				'label'      => __( 'Border Radius', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 'size' => 3, 'unit' => 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'selectors'  => [
					'{{WRAPPER}} .eco-content-card' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eco-content-card__image' => 'border-top-left-radius: {{SIZE}}{{UNIT}}; border-top-right-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => __( 'Content Padding', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 'size' => 22, 'unit' => 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'selectors'  => [
					'{{WRAPPER}} .eco-content-card__body' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eco-content-card',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'text_style_section',
			[
				'label' => __( 'Text Style', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'badge_background',
			[
				'label'     => __( 'Badge Background', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffb000',
				'selectors' => [
					'{{WRAPPER}} .eco-content-card__badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label'     => __( 'Badge Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eco-content-card__badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_color',
			[
				'label'     => __( 'Category Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff9d00',
				'selectors' => [
					'{{WRAPPER}} .eco-content-card__category' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .eco-content-card__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label'     => __( 'Excerpt Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6f6f6f',
				'selectors' => [
					'{{WRAPPER}} .eco-content-card__excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => __( 'Meta Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#777777',
				'selectors' => [
					'{{WRAPPER}} .eco-content-card__meta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __( 'Title Typography', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-content-card__title',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'excerpt_typography',
				'label'    => __( 'Excerpt Typography', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-content-card__excerpt',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_style_section',
			[
				'label' => __( 'Load More Button', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_load_more' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => __( 'Text Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff9d00',
				'selectors' => [
					'{{WRAPPER}} .eco-content-cards__load-more' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color',
			[
				'label'     => __( 'Border Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff9d00',
				'selectors' => [
					'{{WRAPPER}} .eco-content-cards__load-more' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background',
			[
				'label'     => __( 'Background', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eco-content-cards__load-more' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function get_available_post_types() {
		$options = [];

		foreach ( [ 'post', 'event', 'podcast', 'press' ] as $post_type ) {
			$obj = get_post_type_object( $post_type );

			if ( $obj ) {
				$options[ $post_type ] = $obj->labels->singular_name ?: $post_type;
			}
		}

		return $options;
	}

	private function get_manual_post_options() {
		$options = [];

		$query = new \WP_Query(
			[
				'post_type'      => [ 'post', 'event', 'podcast', 'press' ],
				'post_status'    => 'publish',
				'posts_per_page' => 150,
				'orderby'        => 'date',
				'order'          => 'DESC',
			]
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$post_type = get_post_type();
				$obj       = get_post_type_object( $post_type );
				$label     = $obj ? $obj->labels->singular_name : $post_type;

				$options[ get_the_ID() ] = '[' . $label . '] ' . get_the_title();
			}

			wp_reset_postdata();
		}

		return $options;
	}

	private function get_taxonomy_term_options() {
		$options = [];

		$taxonomies = [
			'category'         => __( 'Post Categories', 'elementor-eco' ),
			'post_tag'         => __( 'Post Tags', 'elementor-eco' ),
			'event-category'   => __( 'Event Categories', 'elementor-eco' ),
			'event-tag'        => __( 'Event Tags', 'elementor-eco' ),
			'event-type'       => __( 'Event Types', 'elementor-eco' ),
			'podcast-category' => __( 'Podcast Categories', 'elementor-eco' ),
			'press-category'   => __( 'Press Categories', 'elementor-eco' ),
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

	public static function build_query_args( $settings, $page = 1 ) {
		$post_types = ! empty( $settings['post_types'] ) && is_array( $settings['post_types'] )
			? array_map( 'sanitize_key', $settings['post_types'] )
			: [ 'post' ];

		$allowed_post_types = [ 'post', 'event', 'podcast', 'press' ];
		$post_types         = array_values( array_intersect( $post_types, $allowed_post_types ) );

		if ( empty( $post_types ) ) {
			$post_types = [ 'post' ];
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

		$query_source = $settings['query_source'] ?? 'manual';

		if ( $query_source === 'current_acf_terms' ) {
			$tax_query = self::build_tax_query_from_current_acf_terms( $settings );

			if ( isset( $tax_query['__eco_empty_acf_terms'] ) ) {
				$args['post__in'] = [ 0 ];
			} elseif ( ! empty( $tax_query ) ) {
				$args['tax_query'] = $tax_query;
			}
		} elseif ( $query_source === 'current_taxonomy' ) {
			$tax_query = self::build_tax_query_from_current_taxonomy( $settings );

			if ( isset( $tax_query['__eco_empty_current_taxonomy'] ) ) {
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

		$args = self::apply_highlight_filter( $args, $settings );

		return $args;
	}

	private static function parse_ids( $ids_string ) {
		if ( empty( $ids_string ) || ! is_string( $ids_string ) ) {
			return [];
		}

		$ids = array_filter( array_map( 'absint', explode( ',', $ids_string ) ) );

		return array_values( array_unique( $ids ) );
	}

	private static function get_highlight_meta_clause() {
		return [
			'key'     => 'is_highlight',
			'value'   => '1',
			'compare' => '=',
		];
	}

	private static function append_meta_query_clause( $args, $clause ) {
		if ( empty( $clause ) || ! is_array( $clause ) ) {
			return $args;
		}

		if ( empty( $args['meta_query'] ) || ! is_array( $args['meta_query'] ) ) {
			$args['meta_query'] = [ $clause ];

			return $args;
		}

		$meta_query = $args['meta_query'];

		if ( isset( $meta_query['relation'] ) ) {
			$meta_query[] = $clause;
		} else {
			$normalized_meta_query = [
				'relation' => 'AND',
			];

			foreach ( $meta_query as $existing_clause ) {
				if ( is_array( $existing_clause ) ) {
					$normalized_meta_query[] = $existing_clause;
				}
			}

			$normalized_meta_query[] = $clause;
			$meta_query               = $normalized_meta_query;
		}

		$args['meta_query'] = $meta_query;

		return $args;
	}

	private static function apply_highlight_filter( $args, $settings ) {
		$highlight_filter = ! empty( $settings['highlight_filter'] )
			? sanitize_key( $settings['highlight_filter'] )
			: 'all';

		if ( ! in_array( $highlight_filter, [ 'all', 'highlighted_only', 'skip_first' ], true ) ) {
			$highlight_filter = 'all';
		}

		if ( 'all' === $highlight_filter ) {
			return $args;
		}

		if ( 'highlighted_only' === $highlight_filter ) {
			return self::append_meta_query_clause( $args, self::get_highlight_meta_clause() );
		}

		$first_highlight_id = self::get_first_highlight_id_for_query( $args );

		if ( $first_highlight_id ) {
			$post__not_in = ! empty( $args['post__not_in'] ) && is_array( $args['post__not_in'] )
				? array_map( 'absint', $args['post__not_in'] )
				: [];

			$post__not_in[] = $first_highlight_id;

			$args['post__not_in'] = array_values( array_unique( array_filter( $post__not_in ) ) );
		}

		return $args;
	}

	private static function get_first_highlight_id_for_query( $args ) {
		if ( ! empty( $args['post__in'] ) && is_array( $args['post__in'] ) && in_array( 0, array_map( 'absint', $args['post__in'] ), true ) ) {
			return 0;
		}

		$highlight_args = $args;

		unset( $highlight_args['offset'] );
		unset( $highlight_args['paged'] );
		unset( $highlight_args['meta_key'] );

		$highlight_args['posts_per_page']         = 1;
		$highlight_args['fields']                 = 'ids';
		$highlight_args['no_found_rows']          = true;
		$highlight_args['ignore_sticky_posts']    = true;
		$highlight_args['update_post_meta_cache'] = false;
		$highlight_args['update_post_term_cache'] = false;
		$highlight_args['orderby']                = 'date';
		$highlight_args['order']                  = 'DESC';

		$highlight_args = self::append_meta_query_clause( $highlight_args, self::get_highlight_meta_clause() );

		$query = new \WP_Query( $highlight_args );

		if ( empty( $query->posts ) ) {
			return 0;
		}

		return absint( $query->posts[0] );
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


	private static function build_tax_query_from_current_taxonomy( $settings ) {
		$query_source = $settings['query_source'] ?? 'manual';

		if ( $query_source !== 'current_taxonomy' ) {
			return [];
		}

		$term_id  = ! empty( $settings['context_term_id'] ) ? absint( $settings['context_term_id'] ) : 0;
		$taxonomy = ! empty( $settings['context_taxonomy'] ) ? sanitize_key( $settings['context_taxonomy'] ) : '';

		if ( $term_id && ! empty( $taxonomy ) && taxonomy_exists( $taxonomy ) ) {
			$term = get_term( $term_id, $taxonomy );
		} else {
			$term = get_queried_object();
		}

		if ( ! $term || is_wp_error( $term ) || empty( $term->term_id ) || empty( $term->taxonomy ) ) {
			return [ '__eco_empty_current_taxonomy' => true ];
		}

		$taxonomy = sanitize_key( $term->taxonomy );

		if ( ! taxonomy_exists( $taxonomy ) ) {
			return [ '__eco_empty_current_taxonomy' => true ];
		}

		return [
			[
				'taxonomy'         => $taxonomy,
				'field'            => 'term_id',
				'terms'            => [ absint( $term->term_id ) ],
				'include_children' => true,
			],
		];
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
		// }

		if ( $post_type === 'event' ) {
			$big_image = self::get_field_value( 'big_image', $post_id );

			if ( is_array( $big_image ) && ! empty( $big_image['url'] ) ) {
				return $big_image['url'];
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
		if ( preg_match( '/^\d{8}$/', $date ) ) {
			$dt = \DateTime::createFromFormat( 'Ymd', $date );

			if ( $dt ) {
				return $dt->format( 'd.m.Y' );
			}
		}

		return esc_html( $date );
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

					<h3 class="eco-content-card__title">
						<?php if ( ! $link_full_card ) : ?>
							<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
						<?php else : ?>
							<?php echo esc_html( get_the_title() ); ?>
						<?php endif; ?>
					</h3>

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

	private static function render_empty_state( $settings = [] ) {
		$post_types = $settings['post_types'] ?? [];

		if ( ! is_array( $post_types ) ) {
			$post_types = [ $post_types ];
		}

		$accent_color = in_array( 'event', $post_types, true ) ? '#c2cf00' : '#F9B000';
		?>
		<div class="eco-empty-state eco-content-cards__empty" style="--eco-empty-state-accent: <?php echo esc_attr( $accent_color ); ?>;">
			<div class="eco-empty-state__icon" aria-hidden="true">
				<svg viewBox="0 0 24 24">
					<path d="M4 6h16"></path>
					<path d="M4 12h10"></path>
					<path d="M4 18h7"></path>
				</svg>
			</div>

			<h3 class="eco-empty-state__title">
				<?php esc_html_e( 'No content found', 'elementor-eco' ); ?>
			</h3>

			<p class="eco-empty-state__text">
				<?php esc_html_e( 'No matching content is currently available for this section.', 'elementor-eco' ); ?>
			</p>
		</div>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$page     = 1;
		$args     = self::build_query_args( $settings, $page );
		$query    = new \WP_Query( $args );

		if ( ! $query->have_posts() ) {
			self::render_empty_state( $settings );
			return;
		}

		$widget_id = 'eco-content-cards-' . $this->get_id();

		$queried_object = get_queried_object();
		$context_taxonomy = ( $queried_object instanceof \WP_Term ) ? $queried_object->taxonomy : '';
		$context_term_id  = ( $queried_object instanceof \WP_Term ) ? absint( $queried_object->term_id ) : 0;

		$ajax_settings = [
			'query_source'     => $settings['query_source'] ?? 'manual',
			'acf_term_fields'  => $settings['acf_term_fields'] ?? '',
			'context_post_id'  => get_queried_object_id(),
			'context_taxonomy' => $context_taxonomy,
			'context_term_id'  => $context_term_id,
			'post_types'       => $settings['post_types'] ?? [ 'post' ],
			'include_terms'    => $settings['include_terms'] ?? [],
			'manual_ids'       => $settings['manual_ids'] ?? '',
			'posts_per_page'   => $settings['posts_per_page'] ?? 3,
			'offset'           => $settings['offset'] ?? 0,
			'orderby'          => $settings['orderby'] ?? 'date',
			'order'            => $settings['order'] ?? 'DESC',
			'event_filter'     => $settings['event_filter'] ?? 'all',
			'highlight_filter' => $settings['highlight_filter'] ?? 'all',
			'exclude_current'  => $settings['exclude_current'] ?? '',
			'card_style'              => $settings['card_style'] ?? 'default',
			'featured_image_position' => $settings['featured_image_position'] ?? 'left',
			'image_ratio'             => $settings['image_ratio'] ?? '16-9',
			'badge_source'     => $settings['badge_source'] ?? 'auto',
			'badge_custom_text'=> $settings['badge_custom_text'] ?? '',
			'acf_badge_field'  => $settings['acf_badge_field'] ?? 'event_label',
			'show_image'       => $settings['show_image'] ?? 'yes',
			'show_category'    => $settings['show_category'] ?? 'yes',
			'show_excerpt'     => $settings['show_excerpt'] ?? '',
			'excerpt_length'   => $settings['excerpt_length'] ?? 22,
			'show_date'        => $settings['show_date'] ?? 'yes',
			'show_location'    => $settings['show_location'] ?? 'yes',
			'link_full_card'   => $settings['link_full_card'] ?? 'yes',
		];

		$has_more = $query->found_posts > ( ( ! empty( $settings['offset'] ) ? absint( $settings['offset'] ) : 0 ) + $query->post_count );

		?>
		<div
			id="<?php echo esc_attr( $widget_id ); ?>"
			class="eco-content-cards eco-content-cards--style-<?php echo esc_attr( $ajax_settings['card_style'] ); ?>"
			data-settings="<?php echo esc_attr( wp_json_encode( $ajax_settings ) ); ?>"
			data-page="1"
		>
			<div class="eco-content-cards__grid">
				<?php echo self::render_cards_html( $query, $ajax_settings ); ?>
			</div>

			<?php if ( $ajax_settings['card_style'] !== 'featured' && ! empty( $settings['enable_load_more'] ) && $settings['enable_load_more'] === 'yes' && $has_more ) : ?>
				<div class="eco-content-cards__load-more-wrap">
					<button type="button" class="eco-content-cards__load-more">
						<span class="eco-content-cards__load-more-text">
							<?php echo esc_html( $settings['load_more_text'] ?? __( 'Load more', 'elementor-eco' ) ); ?>
						</span>
						<span class="eco-content-cards__load-more-loading">
							<?php esc_html_e( 'Loading...', 'elementor-eco' ); ?>
						</span>
					</button>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

}
