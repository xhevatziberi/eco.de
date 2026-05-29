<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LogoGrid extends Widget_Base {

	public function get_name() {
		return 'eco-logo-grid';
	}

	public function get_title() {
		return __( 'Member Logo Grid', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_script_depends() {
		return [ 'eco-logo-grid-script' ];
	}

	public function get_style_depends() {
		return [ 'eco-logo-grid-style' ];
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
			'max_logos',
			[
				'label'   => __( 'Maximum Logos to Load', 'elementor-eco' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 64,
				'min'     => 1,
				'max'     => 300,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __( 'Order By', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'rand',
				'options' => [
					'rand'       => __( 'Random', 'elementor-eco' ),
					'title'      => __( 'Title', 'elementor-eco' ),
					'date'       => __( 'Date', 'elementor-eco' ),
					'menu_order' => __( 'Menu Order', 'elementor-eco' ),
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
				'condition' => [
					'orderby!' => 'rand',
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

		$this->add_responsive_control(
			'columns',
			[
				'label'   => __( 'Columns', 'elementor-eco' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 2,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'min'     => 1,
				'max'     => 8,
				'selectors' => [
					'{{WRAPPER}} .eco-logo-grid' => '--eco-logo-grid-columns: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'rows',
			[
				'label'   => __( 'Rows', 'elementor-eco' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3,
				'min'     => 1,
				'max'     => 10,
			]
		);

		$this->add_control(
			'change_interval',
			[
				'label'       => __( 'Change Interval', 'elementor-eco' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 6000,
				'min'         => 1500,
				'max'         => 30000,
				'step'        => 500,
				'description' => __( 'In milliseconds. Example: 6000 = 6 seconds.', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'animation_duration',
			[
				'label'       => __( 'Animation Duration', 'elementor-eco' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 550,
				'min'         => 100,
				'max'         => 3000,
				'step'        => 50,
				'description' => __( 'In milliseconds.', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'open_new_tab',
			[
				'label'        => __( 'Open Links in New Tab', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-eco' ),
				'label_off'    => __( 'No', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Grid Style', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label'      => __( 'Gap', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
				],
				'default' => [
					'size' => 24,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .eco-logo-grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cell_height',
			[
				'label'      => __( 'Cell Height', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 60,
						'max' => 260,
					],
				],
				'default' => [
					'size' => 80,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .eco-logo-grid__item' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cell_padding',
			[
				'label'      => __( 'Cell Padding', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
				],
				'default' => [
					'size' => 18,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .eco-logo-grid__item' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cell_background',
			[
				'label'     => __( 'Cell Background', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eco-logo-grid__item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cell_border_color',
			[
				'label'     => __( 'Border Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eeeeee',
				'selectors' => [
					'{{WRAPPER}} .eco-logo-grid__item' => 'border-color: {{VALUE}};',
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
					'px' => [
						'min' => 0,
						'max' => 40,
					],
				],
				'default' => [
					'size' => 2,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .eco-logo-grid__item' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'cell_shadow',
				'label'    => __( 'Box Shadow', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-logo-grid__item',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'logo_style_section',
			[
				'label' => __( 'Logo Style', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'logo_max_width',
			[
				'label'      => __( 'Logo Max Width', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 40,
						'max' => 300,
					],
					'%' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 160,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .eco-logo-grid__logo' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'logo_max_height',
			[
				'label'      => __( 'Logo Max Height', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 20,
						'max' => 160,
					],
				],
				'default' => [
					'size' => 45,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .eco-logo-grid__logo' => 'max-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'logo_opacity',
			[
				'label' => __( 'Logo Opacity', 'elementor-eco' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0.1,
						'max'  => 1,
						'step' => 0.05,
					],
				],
				'default' => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .eco-logo-grid__logo' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function get_member_logos( $settings ) {
		$orderby_allowed = [ 'rand', 'title', 'date', 'menu_order' ];
		$orderby         = ! empty( $settings['orderby'] ) && in_array( $settings['orderby'], $orderby_allowed, true )
			? $settings['orderby']
			: 'rand';

		$args = [
			'post_type'      => 'member',
			'post_status'    => 'publish',
			'posts_per_page' => ! empty( $settings['max_logos'] ) ? absint( $settings['max_logos'] ) : 64,
			'orderby'        => $orderby,
		];

		if ( $orderby !== 'rand' ) {
			$args['order'] = ! empty( $settings['order'] ) && $settings['order'] === 'DESC' ? 'DESC' : 'ASC';
		}

		$query = new \WP_Query( $args );
		$logos = [];

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$post_id = get_the_ID();
				$logo    = get_the_post_thumbnail_url( $post_id, 'full' );

				if ( ! $logo ) {
					continue;
				}

				$website = function_exists( 'get_field' ) ? get_field( 'website', $post_id ) : '';

				$logos[] = [
					'logo'  => esc_url_raw( $logo ),
					'url'   => is_string( $website ) ? esc_url_raw( $website ) : '',
					'title' => html_entity_decode( get_the_title( $post_id ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
				];
			}

			wp_reset_postdata();
		}

		return $logos;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$logos    = $this->get_member_logos( $settings );

		if ( empty( $logos ) ) {
			return;
		}

		$columns       = ! empty( $settings['columns'] ) ? absint( $settings['columns'] ) : 2;
        $rows          = ! empty( $settings['rows'] ) ? absint( $settings['rows'] ) : 3;
        $display_count = max( 1, $columns * $rows );
		$change_interval    = ! empty( $settings['change_interval'] ) ? absint( $settings['change_interval'] ) : 6000;
		$animation_duration = ! empty( $settings['animation_duration'] ) ? absint( $settings['animation_duration'] ) : 550;
		$open_new_tab       = ! empty( $settings['open_new_tab'] ) && $settings['open_new_tab'] === 'yes';

		?>
		<div
            class="eco-logo-grid"
            data-logos="<?php echo esc_attr( wp_json_encode( $logos ) ); ?>"
            data-display-count="<?php echo esc_attr( $display_count ); ?>"
            data-rows="<?php echo esc_attr( $rows ); ?>"
            data-interval="<?php echo esc_attr( $change_interval ); ?>"
            data-duration="<?php echo esc_attr( $animation_duration ); ?>"
            data-open-new-tab="<?php echo esc_attr( $open_new_tab ? 'yes' : 'no' ); ?>"
        ></div>
		<?php
	}
}