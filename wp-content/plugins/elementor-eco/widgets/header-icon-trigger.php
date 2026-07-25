<?php
namespace ElementorEco\Widgets;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HeaderIconTrigger extends Widget_Base {

	public function get_name() {
		return 'eco-header-icon-trigger';
	}

	public function get_title() {
		return __( 'eco Header Icon Trigger', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-menu-bar';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'eco-header-actions-style' ];
	}

	public function get_html_wrapper_class() {
		return parent::get_html_wrapper_class() . ' the-menu-trigger eco-header-icon-trigger-widget';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Trigger', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'icon',
			[
				'label'   => __( 'Icon', 'elementor-eco' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-bars',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'aria_label',
			[
				'label'       => __( 'Accessible Label', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Open menu', 'elementor-eco' ),
				'placeholder' => __( 'Open menu', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'additional_class',
			[
				'label'       => __( 'Additional Trigger Class', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __( 'The class “the-menu-trigger” is always included.', 'elementor-eco' ),
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

		$this->add_responsive_control(
			'button_size',
			[
				'label'      => __( 'Button Size', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 'size' => 36, 'unit' => 'px' ],
				'range'      => [ 'px' => [ 'min' => 28, 'max' => 80 ] ],
				'selectors'  => [
					'{{WRAPPER}} .eco-header-icon-trigger' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => __( 'Icon Size', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 'size' => 22, 'unit' => 'px' ],
				'range'      => [ 'px' => [ 'min' => 12, 'max' => 50 ] ],
				'selectors'  => [
					'{{WRAPPER}} .eco-header-icon-trigger__icon'     => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eco-header-icon-trigger__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'color_tabs' );

		$this->start_controls_tab(
			'normal_tab',
			[ 'label' => __( 'Normal', 'elementor-eco' ) ]
		);

		$this->add_control(
			'color',
			[
				'label'     => __( 'Icon Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .eco-header-icon-trigger' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => __( 'Background', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eco-header-icon-trigger' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover_tab',
			[ 'label' => __( 'Hover', 'elementor-eco' ) ]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => __( 'Icon Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eco-header-icon-trigger:hover, {{WRAPPER}} .eco-header-icon-trigger:focus-visible' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_background_color',
			[
				'label'     => __( 'Background', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eco-header-icon-trigger:hover, {{WRAPPER}} .eco-header-icon-trigger:focus-visible' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$classes  = [ 'eco-header-icon-trigger', 'the-menu-trigger' ];

		if ( ! empty( $settings['additional_class'] ) ) {
			$extra = preg_split( '/\s+/', trim( $settings['additional_class'] ) );
			foreach ( $extra as $class ) {
				$class = sanitize_html_class( $class );
				if ( $class ) {
					$classes[] = $class;
				}
			}
		}

		$this->add_render_attribute( 'trigger', 'type', 'button' );
		$this->add_render_attribute( 'trigger', 'class', $classes );
		$this->add_render_attribute( 'trigger', 'aria-label', $settings['aria_label'] ?: __( 'Open menu', 'elementor-eco' ) );
		$this->add_render_attribute( 'trigger', 'aria-expanded', 'false' );
		?>
		<button <?php echo $this->get_render_attribute_string( 'trigger' ); ?>>
			<span class="eco-header-icon-trigger__icon" aria-hidden="true">
				<?php Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); ?>
			</span>
		</button>
		<?php
	}
}
