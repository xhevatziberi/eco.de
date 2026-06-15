<?php
namespace ElementorEco\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BenefitCard extends Widget_Base {

	public function get_name() {
		return 'eco-benefit-card';
	}

	public function get_title() {
		return __( 'Benefit Card', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-icon-box';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'eco-benefit-card-style' ];
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_card_style_controls();
		$this->register_icon_style_controls();
		$this->register_content_style_controls();
		$this->register_link_style_controls();
	}

	private function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'icon',
			[
				'label'   => __( 'Icon', 'elementor-eco' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-sitemap',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'number',
			[
				'label'       => __( 'Number', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '01',
				'label_block' => false,
			]
		);

		$this->add_control(
			'label',
			[
				'label'       => __( 'Label', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Kompetenzgruppen', 'elementor-eco' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => __( 'Title', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Mitwirkung & fachlicher Austausch', 'elementor-eco' ),
				'rows'        => 3,
				'label_block' => true,
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => __( 'Title HTML Tag', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'div' => 'DIV',
				],
			]
		);

		$this->add_control(
			'description',
			[
				'label'   => __( 'Description', 'elementor-eco' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => __( 'Bringen Sie Ihre Expertise ein, gestalten Sie relevante Themen aktiv mit und tauschen Sie sich mit führenden Fachleuten der Internetwirtschaft aus.', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'link_text',
			[
				'label'       => __( 'Link Text', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Kompetenzgruppen entdecken', 'elementor-eco' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'link',
			[
				'label'       => __( 'Link', 'elementor-eco' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://eco.de/',
				'options'     => [ 'url', 'is_external', 'nofollow' ],
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->end_controls_section();
	}

	private function register_card_style_controls() {
		$this->start_controls_section(
			'section_card_style',
			[
				'label' => __( 'Card', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'min_height',
			[
				'label'      => __( 'Minimum Height', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [ 'min' => 200, 'max' => 800 ],
					'vh' => [ 'min' => 20, 'max' => 100 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => 400 ],
				'selectors'  => [
					'{{WRAPPER}} .eco-benefit-card' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => __( 'Padding', 'elementor-eco' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top' => 30, 'right' => 30, 'bottom' => 30, 'left' => 30,
					'unit' => 'px', 'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .eco-benefit-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .eco-benefit-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'card_border',
				'selector'  => '{{WRAPPER}} .eco-benefit-card',
				'fields_options' => [
					'border' => [ 'default' => 'solid' ],
					'width'  => [ 'default' => [ 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1, 'isLinked' => true ] ],
					'color'  => [ 'default' => '#dedfe3' ],
				],
			]
		);

		$this->add_control(
			'top_border_color',
			[
				'label'     => __( 'Top Border Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#85898d',
				'selectors' => [
					'{{WRAPPER}} .eco-benefit-card' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'top_border_width',
			[
				'label'      => __( 'Top Border Width', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 12 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 4 ],
				'selectors'  => [
					'{{WRAPPER}} .eco-benefit-card' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'elementor-eco' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [ 'top' => 7, 'right' => 7, 'bottom' => 7, 'left' => 7, 'unit' => 'px', 'isLinked' => true ],
				'selectors'  => [
					'{{WRAPPER}} .eco-benefit-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eco-benefit-card',
			]
		);

		$this->add_control(
			'hover_heading',
			[
				'label'     => __( 'Hover', 'elementor-eco' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'hover_top_border_color',
			[
				'label'     => __( 'Top Border Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ed0016',
				'selectors' => [
					'{{WRAPPER}} .eco-benefit-card:hover' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_translate',
			[
				'label'      => __( 'Move Up', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 5 ],
				'selectors'  => [
					'{{WRAPPER}} .eco-benefit-card:hover' => 'transform: translateY(-{{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_icon_style_controls() {
		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => __( 'Icon & Number', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'top_spacing',
			[
				'label'      => __( 'Space Below Icon Row', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 42 ],
				'selectors'  => [
					'{{WRAPPER}} .eco-benefit-card__top' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_box_size',
			[
				'label'      => __( 'Icon Box Size', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 30, 'max' => 100 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 54 ],
				'selectors'  => [
					'{{WRAPPER}} .eco-benefit-card__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; flex-basis: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label'      => __( 'Icon Size', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 60 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 27 ],
				'selectors'  => [
					'{{WRAPPER}} .eco-benefit-card__icon i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eco-benefit-card__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Icon Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ed0016',
				'selectors' => [
					'{{WRAPPER}} .eco-benefit-card__icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_background',
			[
				'label'     => __( 'Icon Background', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fdebed',
				'selectors' => [
					'{{WRAPPER}} .eco-benefit-card__icon' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_radius',
			[
				'label'      => __( 'Icon Border Radius', 'elementor-eco' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [ 'top' => 6, 'right' => 6, 'bottom' => 6, 'left' => 6, 'unit' => 'px', 'isLinked' => true ],
				'selectors'  => [
					'{{WRAPPER}} .eco-benefit-card__icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'number_color',
			[
				'label'     => __( 'Number Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d2d4d8',
				'selectors' => [
					'{{WRAPPER}} .eco-benefit-card__number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'number_typography',
				'selector' => '{{WRAPPER}} .eco-benefit-card__number',
			]
		);

		$this->end_controls_section();
	}

	private function register_content_style_controls() {
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Text', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => __( 'Label Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ed0016',
				'selectors' => [
					'{{WRAPPER}} .eco-benefit-card__label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .eco-benefit-card__label',
			]
		);

		$this->add_responsive_control(
			'label_spacing',
			[
				'label'      => __( 'Label Bottom Spacing', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 10 ],
				'selectors'  => [
					'{{WRAPPER}} .eco-benefit-card__label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Title Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#171719',
				'selectors' => [
					'{{WRAPPER}} .eco-benefit-card__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eco-benefit-card__title',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'      => __( 'Title Bottom Spacing', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 15 ],
				'selectors'  => [
					'{{WRAPPER}} .eco-benefit-card__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => __( 'Description Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#686b72',
				'selectors' => [
					'{{WRAPPER}} .eco-benefit-card__description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .eco-benefit-card__description',
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label'      => __( 'Description Bottom Spacing', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 28 ],
				'selectors'  => [
					'{{WRAPPER}} .eco-benefit-card__description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_link_style_controls() {
		$this->start_controls_section(
			'section_link_style',
			[
				'label' => __( 'Link', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'link_color',
			[
				'label'     => __( 'Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#171719',
				'selectors' => [
					'{{WRAPPER}} .eco-benefit-card__link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label'     => __( 'Hover Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ed0016',
				'selectors' => [
					'{{WRAPPER}} .eco-benefit-card__link:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label'     => __( 'Arrow Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ed0016',
				'selectors' => [
					'{{WRAPPER}} .eco-benefit-card__link-arrow' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'link_typography',
				'selector' => '{{WRAPPER}} .eco-benefit-card__link',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$title_tag = in_array( $settings['title_tag'], [ 'h2', 'h3', 'h4', 'h5', 'h6', 'div' ], true ) ? $settings['title_tag'] : 'h3';

		$this->add_render_attribute( 'card', 'class', 'eco-benefit-card' );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );
		}
		$this->add_render_attribute( 'link', 'class', 'eco-benefit-card__link' );
		?>
		<article <?php echo $this->get_render_attribute_string( 'card' ); ?>>
			<div class="eco-benefit-card__top">
				<?php if ( ! empty( $settings['icon']['value'] ) ) : ?>
					<div class="eco-benefit-card__icon" aria-hidden="true">
						<?php Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); ?>
					</div>
				<?php endif; ?>

				<?php if ( '' !== trim( (string) $settings['number'] ) ) : ?>
					<span class="eco-benefit-card__number"><?php echo esc_html( $settings['number'] ); ?></span>
				<?php endif; ?>
			</div>

			<div class="eco-benefit-card__content">
				<?php if ( '' !== trim( (string) $settings['label'] ) ) : ?>
					<div class="eco-benefit-card__label"><?php echo esc_html( $settings['label'] ); ?></div>
				<?php endif; ?>

				<?php if ( '' !== trim( (string) $settings['title'] ) ) : ?>
					<<?php echo esc_html( $title_tag ); ?> class="eco-benefit-card__title">
						<?php echo esc_html( $settings['title'] ); ?>
					</<?php echo esc_html( $title_tag ); ?>>
				<?php endif; ?>

				<?php if ( '' !== trim( wp_strip_all_tags( (string) $settings['description'] ) ) ) : ?>
					<div class="eco-benefit-card__description">
						<?php echo wp_kses_post( $settings['description'] ); ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( '' !== trim( (string) $settings['link_text'] ) ) : ?>
				<?php if ( ! empty( $settings['link']['url'] ) ) : ?>
					<a <?php echo $this->get_render_attribute_string( 'link' ); ?>>
						<span><?php echo esc_html( $settings['link_text'] ); ?></span>
						<span class="eco-benefit-card__link-arrow" aria-hidden="true">→</span>
					</a>
				<?php else : ?>
					<div class="eco-benefit-card__link eco-benefit-card__link--disabled">
						<span><?php echo esc_html( $settings['link_text'] ); ?></span>
						<span class="eco-benefit-card__link-arrow" aria-hidden="true">→</span>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</article>
		<?php
	}
}
