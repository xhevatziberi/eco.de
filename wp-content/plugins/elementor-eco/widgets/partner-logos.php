<?php
namespace ElementorEco\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PartnerLogos extends Widget_Base {

	public function get_name() {
		return 'eco-partner-logos';
	}

	public function get_title() {
		return __( 'Partner Logos', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'eco-partner-logos-style' ];
	}

	public function get_script_depends() {
		return [ 'eco-partner-logos-script' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'partners_section',
			[
				'label' => __( 'Partners', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'name',
			[
				'label'       => __( 'Name', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Partner name', 'elementor-eco' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'logo',
			[
				'label'   => __( 'Logo', 'elementor-eco' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => __( 'Link', 'elementor-eco' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'options'     => [ 'url', 'is_external', 'nofollow' ],
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'partners',
			[
				'label'       => __( 'Partner Logos', 'elementor-eco' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => [
					[ 'name' => __( 'Partner 1', 'elementor-eco' ) ],
					[ 'name' => __( 'Partner 2', 'elementor-eco' ) ],
					[ 'name' => __( 'Partner 3', 'elementor-eco' ) ],
					[ 'name' => __( 'Partner 4', 'elementor-eco' ) ],
					[ 'name' => __( 'Partner 5', 'elementor-eco' ) ],
					[ 'name' => __( 'Partner 6', 'elementor-eco' ) ],
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
				'label'          => __( 'Columns', 'elementor-eco' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 6,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'min'            => 1,
				'max'            => 10,
				'selectors'      => [
					'{{WRAPPER}} .eco-partner-logos' => '--eco-partner-columns: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'open_new_tab',
			[
				'label'        => __( 'Open Links in New Tab', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'enable_read_more',
			[
				'label'        => __( 'Show More on Tablet/Mobile', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'On', 'elementor-eco' ),
				'label_off'    => __( 'Off', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'On tablet and mobile, initially show two rows when more items are available.', 'elementor-eco' ),
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
			'gap',
			[
				'label'      => __( 'Gap', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 60 ],
				],
				'default'    => [ 'size' => 16, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-partner-logos' => 'gap: {{SIZE}}{{UNIT}};',
				],
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
				'default'    => [ 'size' => 2, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-partner-logos__item' => 'padding: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .eco-partner-logos__item' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .eco-partner-logos__item' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .eco-partner-logos__item' => 'border-top-color: {{VALUE}};',
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
					'{{WRAPPER}} .eco-partner-logos__item' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .eco-partner-logos__item',
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
				'default'    => [ 'size' => 80, 'unit' => '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-partner-logos__image' => 'max-width: {{SIZE}}{{UNIT}};',
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
				'default'    => [ 'size' => 65, 'unit' => '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eco-partner-logos__image' => 'max-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$partners = $settings['partners'] ?? [];

		if ( empty( $partners ) ) {
			return;
		}
		?>
		<div
			class="eco-partner-logos"
			data-read-more="<?php echo esc_attr( 'yes' === ( $settings['enable_read_more'] ?? 'yes' ) ? 'yes' : 'no' ); ?>"
			data-read-more-label="<?php echo esc_attr__( 'Show more', 'elementor-eco' ); ?>"
			data-show-less-label="<?php echo esc_attr__( 'Show less', 'elementor-eco' ); ?>"
		>
			<?php foreach ( $partners as $index => $partner ) :
				$image_url = $partner['logo']['url'] ?? '';
				$name      = trim( (string) ( $partner['name'] ?? '' ) );
				$link_url  = $partner['link']['url'] ?? '';

				if ( ! $image_url ) {
					continue;
				}

				$tag = $link_url ? 'a' : 'div';
				$this->set_render_attribute( 'partner-' . $index, 'class', 'eco-partner-logos__item' );

				if ( $link_url ) {
					$this->set_render_attribute( 'partner-' . $index, 'href', esc_url( $link_url ) );

					if ( 'yes' === $settings['open_new_tab'] || ! empty( $partner['link']['is_external'] ) ) {
						$this->set_render_attribute( 'partner-' . $index, 'target', '_blank' );
						$this->set_render_attribute( 'partner-' . $index, 'rel', 'noopener' );
					}

					if ( ! empty( $partner['link']['nofollow'] ) ) {
						$this->set_render_attribute( 'partner-' . $index, 'rel', 'noopener nofollow' );
					}

					if ( $name ) {
						$this->set_render_attribute( 'partner-' . $index, 'aria-label', $name );
					}
				}
				?>
				<<?php echo esc_html( $tag ); ?> <?php echo $this->get_render_attribute_string( 'partner-' . $index ); ?>>
					<img
						class="eco-partner-logos__image"
						src="<?php echo esc_url( $image_url ); ?>"
						alt="<?php echo esc_attr( $name ); ?>"
						loading="lazy"
					>
				</<?php echo esc_html( $tag ); ?>>
			<?php endforeach; ?>
		</div>
		<?php if ( 'yes' === ( $settings['enable_read_more'] ?? 'yes' ) ) : ?>
			<button class="eco-partner-logos__toggle" type="button" hidden aria-expanded="false">
				<?php echo esc_html__( 'Show more', 'elementor-eco' ); ?>
			</button>
		<?php endif; ?>
		<?php
	}
}

