<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FeaturedSlider extends Widget_Base {

	public function get_name() {
		return 'eco-featured-slider';
	}

	public function get_title() {
		return __( 'ECO Featured Slider', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'eco-featured-slider-style' ];
	}

	public function get_script_depends() {
		return [ 'eco-featured-slider-script' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Slides', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Image', 'elementor-eco' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);

		$repeater->add_control(
			'badge',
			[
				'label'       => __( 'Badge', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Highlight', 'elementor-eco' ),
				'placeholder' => __( 'Highlight', 'elementor-eco' ),
			]
		);

		$repeater->add_control(
			'eyebrow',
			[
				'label'       => __( 'Small Label', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Workshop, Award, Webinar...', 'elementor-eco' ),
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => __( 'Title', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Data Center', 'elementor-eco' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'text',
			[
				'label'       => __( 'Text', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '',
				'placeholder' => __( 'Short description', 'elementor-eco' ),
				'rows'        => 3,
			]
		);

		$repeater->add_control(
			'date',
			[
				'label'       => __( 'Date', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => '11.09.2026 – 12.09.2026',
			]
		);

		$repeater->add_control(
			'location',
			[
				'label'       => __( 'Location', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'Dreieich, Hessen',
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label'       => __( 'Button Text', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Jetzt anmelden', 'elementor-eco' ),
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => __( 'Link', 'elementor-eco' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://...',
				'default'     => [
					'url' => '',
				],
			]
		);

		$this->add_control(
			'items',
			[
				'label'       => __( 'Items', 'elementor-eco' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => [
					[
						'title' => 'Data Center',
						'badge' => 'Highlight',
						'eyebrow' => 'Award',
					],
					[
						'title' => 'KI & Me – Chatbots im Fokus',
						'badge' => 'Highlight',
						'eyebrow' => 'Workshop',
					],
					[
						'title' => 'Digital Leaders Circle',
						'badge' => '',
						'eyebrow' => '',
					],
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
			'autoplay',
			[
				'label'        => __( 'Autoplay', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'interval',
			[
				'label'       => __( 'Interval', 'elementor-eco' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 6000,
				'min'         => 1500,
				'max'         => 30000,
				'step'        => 500,
				'description' => __( 'In milliseconds.', 'elementor-eco' ),
				'condition'   => [
					'autoplay' => 'yes',
				],
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

		$this->add_responsive_control(
			'gap',
			[
				'label'      => __( 'Gap', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 8,
					'unit' => 'px',
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 40,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eco-featured-slider__panel' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'slider_height',
			[
				'label'      => __( 'Desktop Height', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 420,
					'unit' => 'px',
				],
				'range'      => [
					'px' => [
						'min' => 260,
						'max' => 800,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eco-featured-slider' => '--eco-featured-slider-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'main_width',
			[
				'label'      => __( 'Main Column Width', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'default'    => [
					'size' => 60,
					'unit' => '%',
				],
				'range'      => [
					'%' => [
						'min' => 45,
						'max' => 75,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eco-featured-slider' => '--eco-featured-slider-main-width: {{SIZE}}%;',
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
			'overlay_color',
			[
				'label'     => __( 'Overlay Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(28, 8, 45, 0.58)',
				'selectors' => [
					'{{WRAPPER}} .eco-featured-slider__card::after' => 'background: linear-gradient(180deg, rgba(28, 8, 45, 0.05) 0%, {{VALUE}} 100%);',
				],
			]
		);

		$this->add_control(
			'badge_background',
			[
				'label'     => __( 'Badge Background', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e6007e',
				'selectors' => [
					'{{WRAPPER}} .eco-featured-slider__badge' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .eco-featured-slider__badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eyebrow_color',
			[
				'label'     => __( 'Small Label Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eco-featured-slider__eyebrow' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Title Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eco-featured-slider__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Text Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eco-featured-slider__text, {{WRAPPER}} .eco-featured-slider__meta, {{WRAPPER}} .eco-featured-slider__button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'main_title_typography',
				'label'    => __( 'Main Title Typography', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-featured-slider__card--main .eco-featured-slider__title',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'side_title_typography',
				'label'    => __( 'Side Title Typography', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-featured-slider__card--side .eco-featured-slider__title',
			]
		);

		$this->end_controls_section();
	}

	private function render_item( $item, $index ) {
		$is_main = $index === 0;

		$image_url = '';
		if ( ! empty( $item['image']['url'] ) ) {
			$image_url = $item['image']['url'];
		}

		$title       = $item['title'] ?? '';
		$text        = $item['text'] ?? '';
		$badge       = $item['badge'] ?? '';
		$eyebrow     = $item['eyebrow'] ?? '';
		$date        = $item['date'] ?? '';
		$location    = $item['location'] ?? '';
		$button_text = $item['button_text'] ?? '';
		$link        = $item['link']['url'] ?? '';

		$tag = ! empty( $link ) ? 'a' : 'article';

		$target = '';
		$rel    = '';

		if ( ! empty( $item['link']['is_external'] ) ) {
			$target = ' target="_blank"';
			$rel    = ' rel="noopener"';
		}

		?>
		<<?php echo esc_html( $tag ); ?>
			class="eco-featured-slider__card <?php echo $is_main ? 'eco-featured-slider__card--main' : 'eco-featured-slider__card--side'; ?>"
			<?php if ( ! empty( $link ) ) : ?>
				href="<?php echo esc_url( $link ); ?>"<?php echo $target . $rel; ?>
			<?php endif; ?>
		>
			<?php if ( ! empty( $image_url ) ) : ?>
				<img class="eco-featured-slider__image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
			<?php endif; ?>

			<div class="eco-featured-slider__content">
				<?php if ( ! empty( $badge ) || ! empty( $eyebrow ) ) : ?>
					<div class="eco-featured-slider__labels">
						<?php if ( ! empty( $badge ) ) : ?>
							<span class="eco-featured-slider__badge"><?php echo esc_html( $badge ); ?></span>
						<?php endif; ?>

						<?php if ( ! empty( $eyebrow ) ) : ?>
							<span class="eco-featured-slider__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $title ) ) : ?>
					<h1 class="eco-featured-slider__title"><?php echo esc_html( $title ); ?></h1>
				<?php endif; ?>

				<?php if ( ! empty( $text ) ) : ?>
					<div class="eco-featured-slider__text"><?php echo esc_html( $text ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $date ) || ! empty( $location ) ) : ?>
					<div class="eco-featured-slider__meta">
						<?php if ( ! empty( $date ) ) : ?>
							<span><?php echo esc_html( $date ); ?></span>
						<?php endif; ?>

						<?php if ( ! empty( $location ) ) : ?>
							<span><?php echo esc_html( $location ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $is_main && ! empty( $button_text ) ) : ?>
					<div class="eco-featured-slider__button"><?php echo esc_html( $button_text ); ?></div>
				<?php endif; ?>
			</div>
		</<?php echo esc_html( $tag ); ?>>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = $settings['items'] ?? [];

		if ( empty( $items ) ) {
			return;
		}

		$chunks   = array_chunk( $items, 3 );
		$interval = ! empty( $settings['interval'] ) ? absint( $settings['interval'] ) : 6000;
		$autoplay = ! empty( $settings['autoplay'] ) && $settings['autoplay'] === 'yes';
		$show_dots = ! empty( $settings['show_dots'] ) && $settings['show_dots'] === 'yes';

		?>
		<div
			class="eco-featured-slider"
			data-autoplay="<?php echo esc_attr( $autoplay ? 'yes' : 'no' ); ?>"
			data-interval="<?php echo esc_attr( $interval ); ?>"
		>
			<div class="eco-featured-slider__viewport">
				<?php foreach ( $chunks as $chunk_index => $chunk ) : ?>
					<div class="eco-featured-slider__panel <?php echo $chunk_index === 0 ? 'is-active' : ''; ?>" data-index="<?php echo esc_attr( $chunk_index ); ?>">
						<?php foreach ( $chunk as $item_index => $item ) : ?>
							<?php $this->render_item( $item, $item_index ); ?>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( $show_dots && count( $chunks ) > 1 ) : ?>
				<div class="eco-featured-slider__dots">
					<?php foreach ( $chunks as $chunk_index => $chunk ) : ?>
						<button
							type="button"
							class="eco-featured-slider__dot <?php echo $chunk_index === 0 ? 'is-active' : ''; ?>"
							data-index="<?php echo esc_attr( $chunk_index ); ?>"
							aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'elementor-eco' ), $chunk_index + 1 ) ); ?>"
						></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}