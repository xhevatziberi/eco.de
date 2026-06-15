<?php

namespace ElementorEco\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TestimonialShowcase extends Widget_Base {

	public function get_name(): string {
		return 'eco_testimonial_showcase';
	}

	public function get_title(): string {
		return __( 'ECO Testimonial Showcase', 'elementor-eco' );
	}

	public function get_icon(): string {
		return 'eicon-testimonial-carousel';
	}

	public function get_categories(): array {
		return [ 'eco-elements' ];
	}

	public function get_keywords(): array {
		return [ 'testimonial', 'quote', 'slider', 'swiper', 'member', 'eco' ];
	}

	public function get_style_depends(): array {
		return [ 'swiper', 'eco-testimonial-showcase' ];
	}

	public function get_script_depends(): array {
		return [ 'swiper', 'eco-testimonial-showcase' ];
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_slider_controls();
		$this->register_layout_controls();
		$this->register_card_style_controls();
		$this->register_content_style_controls();
		$this->register_navigation_style_controls();
	}

	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_testimonials',
			[
				'label' => __( 'Testimonials', 'elementor-eco' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'quote',
			[
				'label'       => __( 'Quote', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 8,
				'label_block' => true,
				'default'     => __(
					'Wir sind Mitglied bei eco, weil uns das Netzwerk mit relevanten Kontakten, Fachwissen und einer starken gemeinsamen Stimme verbindet.',
					'elementor-eco'
				),
			]
		);

		$repeater->add_control(
			'name',
			[
				'label'       => __( 'Name', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Oliver Süme', 'elementor-eco' ),
			]
		);

		$repeater->add_control(
			'position',
			[
				'label'       => __( 'Position', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Vorstandsvorsitzender', 'elementor-eco' ),
			]
		);

		$repeater->add_control(
			'company',
			[
				'label'       => __( 'Company', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'eco e.V.', 'elementor-eco' ),
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'   => __( 'Portrait', 'elementor-eco' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [ 'url' => '' ],
			]
		);

		$this->add_control(
			'testimonials',
			[
				'label'       => __( 'Testimonials', 'elementor-eco' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => [
					[
						'quote'    => __(
							'Wir sind Mitglied bei eco, weil uns das Netzwerk mit relevanten Kontakten, Fachwissen und einer starken gemeinsamen Stimme verbindet.',
							'elementor-eco'
						),
						'name'     => __( 'Oliver Süme', 'elementor-eco' ),
						'position' => __( 'Vorstandsvorsitzender', 'elementor-eco' ),
						'company'  => __( 'eco e.V.', 'elementor-eco' ),
					],
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_slider_controls(): void {
		$this->start_controls_section(
			'section_slider',
			[
				'label' => __( 'Slider', 'elementor-eco' ),
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
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'loop',
			[
				'label'        => __( 'Infinite Loop', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
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
				'min'       => 1000,
				'max'       => 20000,
				'step'      => 500,
				'default'   => 6000,
				'condition' => [ 'autoplay' => 'yes' ],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'        => __( 'Pause on Hover', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'autoplay' => 'yes' ],
			]
		);

		$this->add_control(
			'speed',
			[
				'label'   => __( 'Transition Speed', 'elementor-eco' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 100,
				'max'     => 3000,
				'step'    => 50,
				'default' => 550,
			]
		);

		$this->end_controls_section();
	}

	private function register_layout_controls(): void {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => __( 'Layout', 'elementor-eco' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'auto' => [
						'title' => __( 'Auto', 'elementor-eco' ),
						'icon'  => 'eicon-responsive',
					],
					'horizontal' => [
						'title' => __( 'Horizontal', 'elementor-eco' ),
						'icon'  => 'eicon-h-align-stretch',
					],
					'vertical' => [
						'title' => __( 'Vertical', 'elementor-eco' ),
						'icon'  => 'eicon-v-align-stretch',
					],
				],
				'default' => 'auto',
				'toggle'  => false,
			]
		);

		$this->add_control(
			'person_info_position',
			[
				'label'       => __( 'Person Information', 'elementor-eco' ),
				'description' => __( 'On tablet and mobile, the information is always displayed outside the visual panel.', 'elementor-eco' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'inside',
				'options'     => [
					'inside'  => __( 'Inside Visual Panel', 'elementor-eco' ),
					'outside' => __( 'Outside Visual Panel', 'elementor-eco' ),
				],
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label'      => __( 'Portrait Width', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'%' => [ 'min' => 25, 'max' => 50 ],
					'px' => [ 'min' => 160, 'max' => 500 ],
				],
				'default'    => [ 'unit' => '%', 'size' => 36 ],
				'selectors'  => [
					'{{WRAPPER}} .eco-testimonial-showcase' => '--eco-testimonial-image-column: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label'      => __( 'Column Gap', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 36 ],
				'selectors'  => [
					'{{WRAPPER}} .eco-testimonial-showcase' => '--eco-testimonial-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'min_height',
			[
				'label'      => __( 'Minimum Height', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 300, 'max' => 900 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 460 ],
				'selectors'  => [
					'{{WRAPPER}} .eco-testimonial-showcase' => '--eco-testimonial-min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_card_style_controls(): void {
		$this->start_controls_section(
			'section_card_style',
			[
				'label' => __( 'Card', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => __( 'Background', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f4f5f8',
				'selectors' => [
					'{{WRAPPER}} .eco-testimonial-showcase' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label'      => __( 'Padding', 'elementor-eco' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => 42,
					'right'    => 42,
					'bottom'   => 42,
					'left'     => 42,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .eco-testimonial-showcase' =>
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 10 ],
				'selectors'  => [
					'{{WRAPPER}} .eco-testimonial-showcase' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .eco-testimonial-showcase',
			]
		);

		$this->end_controls_section();
	}

	private function register_content_style_controls(): void {
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'elementor-eco' ),
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
					'{{WRAPPER}} .eco-testimonial-showcase' => '--eco-testimonial-accent: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'quote_color',
			[
				'label'     => __( 'Quote Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#222222',
				'selectors' => [
					'{{WRAPPER}} .eco-testimonial-showcase__quote' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'quote_typography',
				'selector' => '{{WRAPPER}} .eco-testimonial-showcase__quote',
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => __( 'Name Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => [
					'{{WRAPPER}} .eco-testimonial-showcase__name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .eco-testimonial-showcase__name',
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => __( 'Position / Company Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#666666',
				'selectors' => [
					'{{WRAPPER}} .eco-testimonial-showcase__meta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'selector' => '{{WRAPPER}} .eco-testimonial-showcase__meta',
			]
		);

		$this->end_controls_section();
	}

	private function register_navigation_style_controls(): void {
		$this->start_controls_section(
			'section_navigation_style',
			[
				'label' => __( 'Navigation', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		foreach (
			[
				'arrow_color'             => [ 'Arrow Color', '#e2001a', '--eco-testimonial-arrow-color' ],
				'arrow_background'        => [ 'Arrow Background', '#ffffff', '--eco-testimonial-arrow-background' ],
				'arrow_active_color'      => [ 'Next Arrow Color', '#ffffff', '--eco-testimonial-next-color' ],
				'arrow_active_background' => [ 'Next Arrow Background', '#e2001a', '--eco-testimonial-next-background' ],
				'dot_color'               => [ 'Dot Color', '#d6d8dc', '--eco-testimonial-dot-color' ],
				'dot_active_color'        => [ 'Active Dot Color', '#e2001a', '--eco-testimonial-dot-active' ],
			] as $control_id => $control
		) {
			$this->add_control(
				$control_id,
				[
					'label'     => __( $control[0], 'elementor-eco' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => $control[1],
					'selectors' => [
						'{{WRAPPER}} .eco-testimonial-showcase' => $control[2] . ': {{VALUE}};',
					],
				]
			);
		}

		$this->end_controls_section();
	}

	private function render_person_info( array $item, string $modifier ): void {
		if (
			empty( $item['name'] )
			&& empty( $item['position'] )
			&& empty( $item['company'] )
		) {
			return;
		}
		?>
		<div class="eco-testimonial-showcase__person eco-testimonial-showcase__person--<?php echo esc_attr( $modifier ); ?>">
			<?php if ( ! empty( $item['name'] ) ) : ?>
				<div class="eco-testimonial-showcase__name">
					<?php echo esc_html( $item['name'] ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $item['position'] ) || ! empty( $item['company'] ) ) : ?>
				<div class="eco-testimonial-showcase__meta">
					<?php
					$meta = array_filter(
						[
							$item['position'] ?? '',
							$item['company'] ?? '',
						]
					);

					echo esc_html( implode( ', ', $meta ) );
					?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function render(): void {
		$settings     = $this->get_settings_for_display();
		$testimonials = $settings['testimonials'] ?? [];

		if ( empty( $testimonials ) || ! is_array( $testimonials ) ) {
			return;
		}

		$layout = $settings['layout'] ?? 'auto';

		if ( ! in_array( $layout, [ 'auto', 'horizontal', 'vertical' ], true ) ) {
			$layout = 'auto';
		}

		$person_info_position = $settings['person_info_position'] ?? 'inside';

		if ( ! in_array( $person_info_position, [ 'inside', 'outside' ], true ) ) {
			$person_info_position = 'inside';
		}

		$slide_count = count( $testimonials );
		$show_arrows = 'yes' === ( $settings['show_arrows'] ?? '' ) && $slide_count > 1;
		$show_dots   = 'yes' === ( $settings['show_dots'] ?? '' ) && $slide_count > 1;

		$swiper_settings = [
			'loop'          => 'yes' === ( $settings['loop'] ?? '' ) && $slide_count > 1,
			'speed'         => max( 100, (int) ( $settings['speed'] ?? 550 ) ),
			'autoplay'      => 'yes' === ( $settings['autoplay'] ?? '' ),
			'autoplayDelay' => max( 1000, (int) ( $settings['autoplay_delay'] ?? 6000 ) ),
			'pauseOnHover'  => 'yes' === ( $settings['pause_on_hover'] ?? '' ),
			'showArrows'    => $show_arrows,
			'showDots'      => $show_dots,
		];
		?>
		<div
			class="eco-testimonial-showcase eco-testimonial-showcase--<?php echo esc_attr( $layout ); ?> eco-testimonial-showcase--person-<?php echo esc_attr( $person_info_position ); ?> swiper"
			data-eco-testimonial-settings="<?php echo esc_attr( wp_json_encode( $swiper_settings ) ); ?>"
		>
			<div class="swiper-wrapper">
				<?php foreach ( $testimonials as $item ) : ?>
					<?php
					$image     = is_array( $item['image'] ?? null ) ? $item['image'] : [];
					$image_url = $image['url'] ?? '';
					$image_alt = '';

					if ( ! empty( $image['id'] ) ) {
						$image_alt = (string) get_post_meta(
							(int) $image['id'],
							'_wp_attachment_image_alt',
							true
						);
					}

					if ( '' === $image_alt ) {
						$image_alt = (string) ( $item['name'] ?? '' );
					}
					?>
					<div class="swiper-slide eco-testimonial-showcase__slide">
						<article class="eco-testimonial-showcase__card">
							<div class="eco-testimonial-showcase__content">
								<div class="eco-testimonial-showcase__quote-mark" aria-hidden="true">
									<svg viewBox="0 0 52 40">
										<path d="M2 40V24C2 9 9 2 24 0v9c-8 2-11 6-11 13h11v18H2Zm28 0V24C30 9 37 2 52 0v9c-8 2-11 6-11 13h11v18H30Z"></path>
									</svg>
								</div>

								<?php if ( ! empty( $item['quote'] ) ) : ?>
									<div class="eco-testimonial-showcase__quote">
										<?php echo wpautop( wp_kses_post( $item['quote'] ) ); ?>
									</div>
								<?php endif; ?>

								<?php $this->render_person_info( $item, 'outside' ); ?>
							</div>

							<div class="eco-testimonial-showcase__visual">
								<div class="eco-testimonial-showcase__visual-bg" aria-hidden="true"></div>

								<?php if ( $image_url ) : ?>
									<img
										class="eco-testimonial-showcase__portrait"
										src="<?php echo esc_url( $image_url ); ?>"
										alt="<?php echo esc_attr( $image_alt ); ?>"
										loading="lazy"
									>
								<?php else : ?>
									<div class="eco-testimonial-showcase__placeholder" aria-hidden="true">
										<svg viewBox="0 0 64 64">
											<circle cx="32" cy="22" r="12"></circle>
											<path d="M12 58c2-14 10-21 20-21s18 7 20 21"></path>
										</svg>
									</div>
								<?php endif; ?>

								<?php $this->render_person_info( $item, 'inside' ); ?>
							</div>
						</article>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( $show_arrows || $show_dots ) : ?>
				<div class="eco-testimonial-showcase__footer">
					<?php if ( $show_arrows ) : ?>
						<div class="eco-testimonial-showcase__navigation">
							<button
								type="button"
								class="eco-testimonial-showcase__arrow eco-testimonial-showcase__prev"
								aria-label="<?php echo esc_attr__( 'Previous testimonial', 'elementor-eco' ); ?>"
							>
								<svg viewBox="0 0 24 24" aria-hidden="true">
									<path d="M15 18l-6-6 6-6"></path>
								</svg>
							</button>

							<button
								type="button"
								class="eco-testimonial-showcase__arrow eco-testimonial-showcase__next"
								aria-label="<?php echo esc_attr__( 'Next testimonial', 'elementor-eco' ); ?>"
							>
								<svg viewBox="0 0 24 24" aria-hidden="true">
									<path d="M9 18l6-6-6-6"></path>
								</svg>
							</button>
						</div>
					<?php endif; ?>

					<?php if ( $show_dots ) : ?>
						<div class="eco-testimonial-showcase__pagination"></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
