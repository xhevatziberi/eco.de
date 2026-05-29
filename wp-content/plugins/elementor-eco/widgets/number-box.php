<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NumberBox extends Widget_Base {

	public function get_name() {
		return 'eco-number-box';
	}

	public function get_title() {
		return __( 'Number Box', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-number-field';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'eco-number-box-style' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'number',
			[
				'label'       => __( 'Number', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '01',
				'placeholder' => '01',
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => __( 'Title', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Neutrale Plattform', 'elementor-eco' ),
				'placeholder' => __( 'Enter title', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'text',
			[
				'label'       => __( 'Text', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Wir bringen Mitglieder und Stakeholder der Wirtschaft in Gespräch mit Wissenschaft, Gesellschaft und Politik – auf Augenhöhe.', 'elementor-eco' ),
				'placeholder' => __( 'Enter text', 'elementor-eco' ),
				'rows'        => 4,
			]
		);

		$this->add_control(
			'alignment',
			[
				'label'   => __( 'Alignment', 'elementor-eco' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor-eco' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor-eco' ),
						'icon'  => 'eicon-text-align-center',
					],
				],
				'toggle' => false,
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
			'number_color',
			[
				'label'     => __( 'Number Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fde7f3',
				'selectors' => [
					'{{WRAPPER}} .eco-number-box__number' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .eco-number-box__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Text Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6f6f6f',
				'selectors' => [
					'{{WRAPPER}} .eco-number-box__text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'gap',
			[
				'label'      => __( 'Gap', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 8,
						'max' => 80,
					],
				],
				'default' => [
					'size' => 18,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .eco-number-box' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_spacing',
			[
				'label'      => __( 'Title/Text Spacing', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 4,
						'max' => 40,
					],
				],
				'default' => [
					'size' => 8,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .eco-number-box__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'number_typography',
				'label'    => __( 'Number Typography', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-number-box__number',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __( 'Title Typography', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-number-box__title',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => __( 'Text Typography', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-number-box__text',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$alignment = ! empty( $settings['alignment'] ) ? $settings['alignment'] : 'left';

		$this->add_render_attribute(
			'wrapper',
			'class',
			[
				'eco-number-box',
				'eco-number-box--' . sanitize_html_class( $alignment ),
			]
		);

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( ! empty( $settings['number'] ) ) : ?>
				<div class="eco-number-box__number">
					<?php echo esc_html( $settings['number'] ); ?>
				</div>
			<?php endif; ?>

			<div class="eco-number-box__content">
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<h5 class="eco-number-box__title">
						<?php echo esc_html( $settings['title'] ); ?>
					</h5>
				<?php endif; ?>

				<?php if ( ! empty( $settings['text'] ) ) : ?>
					<div class="eco-number-box__text">
						<?php echo wp_kses_post( nl2br( $settings['text'] ) ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}