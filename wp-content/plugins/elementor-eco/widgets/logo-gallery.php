<?php
namespace ElementorEco\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LogoGallery extends Widget_Base {

	public function get_name() {
		return 'eco-logo-gallery';
	}

	public function get_title() {
		return __( 'Logo Gallery', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'eco-logo-gallery-style' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'header_section',
			[
				'label' => __( 'Header', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'header_title',
			[
				'label'       => __( 'Title', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Eine Studie von', 'elementor-eco' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'header_icon',
			[
				'label'   => __( 'Icon', 'elementor-eco' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-book-open',
					'library' => 'fa-solid',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Images', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'image_source',
			[
				'label'   => __( 'Image Source', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => [
					'manual' => __( 'Elementor Gallery', 'elementor-eco' ),
					'acf'    => __( 'ACF Gallery Field', 'elementor-eco' ),
				],
			]
		);

		$this->add_control(
			'manual_gallery',
			[
				'label'     => __( 'Choose Images', 'elementor-eco' ),
				'type'      => Controls_Manager::GALLERY,
				'default'   => [],
				'condition' => [
					'image_source' => 'manual',
				],
			]
		);

		$this->add_control(
			'acf_field_name',
			[
				'label'       => __( 'ACF Gallery Field Name', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'partner_logos',
				'description' => __( 'Enter the ACF field name, not the field label. The gallery is read from the current post used by the Elementor template.', 'elementor-eco' ),
				'condition'   => [
					'image_source' => 'acf',
				],
			]
		);

		$this->add_control(
			'image_size',
			[
				'label'   => __( 'WordPress Image Size', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'medium_large',
				'options' => [
					'thumbnail'    => __( 'Thumbnail', 'elementor-eco' ),
					'medium'       => __( 'Medium', 'elementor-eco' ),
					'medium_large' => __( 'Medium Large', 'elementor-eco' ),
					'large'        => __( 'Large', 'elementor-eco' ),
					'full'         => __( 'Full', 'elementor-eco' ),
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
				'label'          => __( 'Grid Columns', 'elementor-eco' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 3,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'min'            => 1,
				'max'            => 10,
				'selectors'      => [
					'{{WRAPPER}} .eco-logo-gallery' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'header_style_section',
			[
				'label' => __( 'Header', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'header_gap',
			[
				'label'      => __( 'Icon and Title Gap', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'rem' ],
				'range'      => [
					'px'  => [ 'min' => 0, 'max' => 60 ],
					'rem' => [ 'min' => 0, 'max' => 4, 'step' => 0.1 ],
				],
				'default'    => [ 'size' => 16, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-logo-gallery__header' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_bottom_spacing',
			[
				'label'      => __( 'Bottom Spacing', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'rem' ],
				'range'      => [
					'px'  => [ 'min' => 0, 'max' => 100 ],
					'rem' => [ 'min' => 0, 'max' => 6, 'step' => 0.1 ],
				],
				'default'    => [ 'size' => 24, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-logo-gallery__header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_icon_size',
			[
				'label'      => __( 'Icon Size', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'rem' ],
				'range'      => [
					'px'  => [ 'min' => 8, 'max' => 100 ],
					'rem' => [ 'min' => 0.5, 'max' => 6, 'step' => 0.1 ],
				],
				'default'    => [ 'size' => 32, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-logo-gallery__header-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eco-logo-gallery__header-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'header_color',
			[
				'label'     => __( 'Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => [
					'{{WRAPPER}} .eco-logo-gallery__header' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'header_typography',
				'selector' => '{{WRAPPER}} .eco-logo-gallery__header-title',
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
				'size_units' => [ 'px', 'rem' ],
				'range'      => [
					'px'  => [ 'min' => 0, 'max' => 80 ],
					'rem' => [ 'min' => 0, 'max' => 5, 'step' => 0.1 ],
				],
				'default'    => [ 'size' => 16, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-logo-gallery' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label'      => __( 'Padding', 'elementor-eco' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem' ],
				'default'    => [
					'top'      => 24,
					'right'    => 24,
					'bottom'   => 24,
					'left'     => 24,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .eco-logo-gallery__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .eco-logo-gallery__item' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .eco-logo-gallery__item' => 'border-color: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'top_border_width',
			[
				'label'      => __( 'Top Border Width', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 20 ],
				],
				'default'    => [ 'size' => 0, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-logo-gallery__item' => 'border-top-width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .eco-logo-gallery__item' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'elementor-eco' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem' ],
				'default'    => [
					'top'      => 7,
					'right'    => 7,
					'bottom'   => 7,
					'left'     => 7,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .eco-logo-gallery__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .eco-logo-gallery__item',
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
			'logo_max_width',
			[
				'label'      => __( 'Maximum Width', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'%'  => [ 'min' => 10, 'max' => 100 ],
					'px' => [ 'min' => 20, 'max' => 600 ],
				],
				'default'    => [ 'size' => 82, 'unit' => '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-logo-gallery__image' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'logo_max_height',
			[
				'label'      => __( 'Maximum Height', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [
					'%'  => [ 'min' => 10, 'max' => 100 ],
					'px' => [ 'min' => 20, 'max' => 600 ],
				],
				'default'    => [ 'size' => 70, 'unit' => '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-logo-gallery__image' => 'max-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$images   = $this->get_images( $settings );

		if ( empty( $images ) ) {
			return;
		}

		$image_size   = $settings['image_size'] ?? 'medium_large';
		$header_title = trim( (string) ( $settings['header_title'] ?? '' ) );
		$header_icon  = $settings['header_icon'] ?? [];
		?>
		<div class="eco-logo-gallery-widget">
			<?php if ( $header_title || ! empty( $header_icon['value'] ) ) : ?>
				<div class="eco-logo-gallery__header">
					<?php if ( ! empty( $header_icon['value'] ) ) : ?>
						<span class="eco-logo-gallery__header-icon" aria-hidden="true">
							<?php Icons_Manager::render_icon( $header_icon, [ 'aria-hidden' => 'true' ] ); ?>
						</span>
					<?php endif; ?>

					<?php if ( $header_title ) : ?>
						<div class="eco-logo-gallery__header-title"><?php echo esc_html( $header_title ); ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="eco-logo-gallery" role="list">
			<?php foreach ( $images as $image ) : ?>
				<div class="eco-logo-gallery__item" role="listitem">
					<?php echo $this->render_image( $image, $image_size ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	private function get_images( array $settings ) {
		if ( 'acf' === ( $settings['image_source'] ?? 'manual' ) ) {
			return $this->get_acf_images( $settings['acf_field_name'] ?? '' );
		}

		return $this->normalize_images( $settings['manual_gallery'] ?? [] );
	}

	private function get_acf_images( $field_name ) {
		$field_name = sanitize_key( $field_name );

		if ( ! $field_name || ! function_exists( 'get_field' ) ) {
			return [];
		}

		$object_id = get_queried_object_id();

		if ( ! $object_id ) {
			$object_id = get_the_ID();
		}

		$value = get_field( $field_name, $object_id ?: false );

		return $this->normalize_images( is_array( $value ) ? $value : [] );
	}

	private function normalize_images( array $images ) {
		$normalized = [];

		foreach ( $images as $image ) {
			if ( is_numeric( $image ) ) {
				$normalized[] = [ 'id' => (int) $image ];
				continue;
			}

			if ( is_string( $image ) ) {
				$normalized[] = [ 'url' => esc_url_raw( $image ), 'alt' => '' ];
				continue;
			}

			if ( ! is_array( $image ) ) {
				continue;
			}

			$id  = isset( $image['ID'] ) ? (int) $image['ID'] : (int) ( $image['id'] ?? 0 );
			$url = isset( $image['url'] ) ? esc_url_raw( $image['url'] ) : '';
			$alt = isset( $image['alt'] ) ? sanitize_text_field( $image['alt'] ) : '';

			if ( $id || $url ) {
				$normalized[] = [
					'id'  => $id,
					'url' => $url,
					'alt' => $alt,
				];
			}
		}

		return $normalized;
	}

	private function render_image( array $image, $image_size ) {
		$attachment_id = (int) ( $image['id'] ?? 0 );

		if ( $attachment_id ) {
			return wp_get_attachment_image(
				$attachment_id,
				$image_size,
				false,
				[
					'class'   => 'eco-logo-gallery__image',
					'loading' => 'lazy',
					'decoding' => 'async',
				]
			);
		}

		$url = $image['url'] ?? '';

		if ( ! $url ) {
			return '';
		}

		return sprintf(
			'<img class="eco-logo-gallery__image" src="%1$s" alt="%2$s" loading="lazy" decoding="async">',
			esc_url( $url ),
			esc_attr( $image['alt'] ?? '' )
		);
	}
}
