<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FeaturedSliderV2 extends Widget_Base {

	public function get_name() {
		return 'eco-featured-slider-v2';
	}

	public function get_title() {
		return __( 'eco Featured Slider v2', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'eco-featured-slider-v2-style' ];
	}

	public function get_script_depends() {
		return [ 'eco-featured-slider-v2-script' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'hero_section',
			[
				'label' => __( 'Hero Slide', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_hero_slide',
			[
				'label'        => __( 'Show Hero Slide', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'hero_kicker',
			[
				'label'       => __( 'Kicker', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Seit 1995 gestalten wir das Internet', 'elementor-eco' ),
				'label_block' => true,
				'condition'   => [ 'show_hero_slide' => 'yes' ],
			]
		);

		$this->add_control(
			'hero_title',
			[
				'label'       => __( 'Title', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Die Stimme der Internetwirtschaft', 'elementor-eco' ),
				'rows'        => 2,
				'condition'   => [ 'show_hero_slide' => 'yes' ],
			]
		);

		$this->add_control(
			'hero_lead',
			[
				'label'       => __( 'Lead Text', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Mit rund 1.000 Mitgliedsunternehmen aus über 70 Ländern ist eco der führende Verband der Internetwirtschaft in Europa.', 'elementor-eco' ),
				'rows'        => 3,
				'condition'   => [ 'show_hero_slide' => 'yes' ],
			]
		);

		$this->add_control(
			'hero_text',
			[
				'label'       => __( 'Text', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Wir fördern neue Technologien, Infrastrukturen und Märkte – und setzen uns für ein freies, sicheres und leistungsstarkes Internet ein.', 'elementor-eco' ),
				'rows'        => 4,
				'condition'   => [ 'show_hero_slide' => 'yes' ],
			]
		);

		$this->add_control(
			'hero_primary_text',
			[
				'label'       => __( 'Primary Button Text', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Mitglied werden', 'elementor-eco' ),
				'condition'   => [ 'show_hero_slide' => 'yes' ],
			]
		);

		$this->add_control(
			'hero_primary_link',
			[
				'label'       => __( 'Primary Button Link', 'elementor-eco' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://...',
				'condition'   => [ 'show_hero_slide' => 'yes' ],
			]
		);

		$this->add_control(
			'hero_secondary_text',
			[
				'label'       => __( 'Secondary Button Text', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Mehr erfahren', 'elementor-eco' ),
				'condition'   => [ 'show_hero_slide' => 'yes' ],
			]
		);

		$this->add_control(
			'hero_secondary_link',
			[
				'label'       => __( 'Secondary Button Link', 'elementor-eco' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://...',
				'condition'   => [ 'show_hero_slide' => 'yes' ],
			]
		);

		$this->add_control(
			'hero_image',
			[
				'label'     => __( 'Image', 'elementor-eco' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => [ 'show_hero_slide' => 'yes' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cards_section',
			[
				'label' => __( 'Card Slides', 'elementor-eco' ),
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
				'default'     => [ 'url' => '' ],
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
						'title'   => 'Data Center',
						'badge'   => 'Highlight',
						'eyebrow' => 'Award',
					],
					[
						'title'   => 'KI & Me – Chatbots im Fokus',
						'badge'   => 'Highlight',
						'eyebrow' => 'Workshop',
					],
					[
						'title'   => 'Digital Leaders Circle',
						'badge'   => '',
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
				'condition'   => [ 'autoplay' => 'yes' ],
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

		$this->add_responsive_control(
			'gap',
			[
				'label'      => __( 'Card Gap', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 'size' => 8, 'unit' => 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'selectors'  => [ '{{WRAPPER}} .eco-featured-slider-v2__panel--cards' => 'gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'slider_height',
			[
				'label'      => __( 'Desktop Height', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 'size' => 420, 'unit' => 'px' ],
				'range'      => [ 'px' => [ 'min' => 320, 'max' => 900 ] ],
				'selectors'  => [ '{{WRAPPER}} .eco-featured-slider-v2' => '--eco-featured-slider-v2-height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'card_main_width',
			[
				'label'      => __( 'Cards Main Column Width', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'default'    => [ 'size' => 60, 'unit' => '%' ],
				'range'      => [ '%' => [ 'min' => 45, 'max' => 75 ] ],
				'selectors'  => [ '{{WRAPPER}} .eco-featured-slider-v2' => '--eco-featured-slider-v2-card-main-width: {{SIZE}}%;' ],
			]
		);

		$this->add_control(
			'hero_image_width',
			[
				'label'      => __( 'Hero Image Width', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'default'    => [ 'size' => 52, 'unit' => '%' ],
				'range'      => [ '%' => [ 'min' => 38, 'max' => 65 ] ],
				'selectors'  => [ '{{WRAPPER}} .eco-featured-slider-v2' => '--eco-featured-slider-v2-hero-image-width: {{SIZE}}%;' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'hero_style_section',
			[
				'label' => __( 'Hero Style', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'hero_background_color',
			[
				'label'     => __( 'Background Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f2f3f4',
				'selectors' => [ '{{WRAPPER}} .eco-featured-slider-v2' => '--eco-featured-slider-v2-hero-bg: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'hero_accent_color',
			[
				'label'     => __( 'Accent Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e60012',
				'selectors' => [
					'{{WRAPPER}} .eco-featured-slider-v2' => '--eco-featured-slider-v2-accent: {{VALUE}};',
					'{{WRAPPER}} .eco-featured-slider-v2__hero-kicker, {{WRAPPER}} .eco-featured-slider-v2__hero-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hero_text_color',
			[
				'label'     => __( 'Text Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => [ '{{WRAPPER}} .eco-featured-slider-v2__hero-lead, {{WRAPPER}} .eco-featured-slider-v2__hero-text' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'hero_title_typography',
				'label'    => __( 'Hero Title Typography', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-featured-slider-v2__hero-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cards_style_section',
			[
				'label' => __( 'Cards Style', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label'     => __( 'Overlay Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(28, 8, 45, 0.58)',
				'selectors' => [ '{{WRAPPER}} .eco-featured-slider-v2__card::after' => 'background: linear-gradient(180deg, rgba(28, 8, 45, 0.05) 0%, {{VALUE}} 100%);' ],
			]
		);

		$this->add_control(
			'badge_background',
			[
				'label'     => __( 'Badge Background', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e6007e',
				'selectors' => [ '{{WRAPPER}} .eco-featured-slider-v2__badge' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label'     => __( 'Badge Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .eco-featured-slider-v2__badge' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'cards_text_color',
			[
				'label'     => __( 'Cards Text Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .eco-featured-slider-v2__card, {{WRAPPER}} .eco-featured-slider-v2__card:hover, {{WRAPPER}} .eco-featured-slider-v2__card:focus, {{WRAPPER}} .eco-featured-slider-v2__card-title, {{WRAPPER}} .eco-featured-slider-v2__card-text, {{WRAPPER}} .eco-featured-slider-v2__card-meta, {{WRAPPER}} .eco-featured-slider-v2__card-button, {{WRAPPER}} .eco-featured-slider-v2__eyebrow' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'main_title_typography',
				'label'    => __( 'Main Card Title Typography', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-featured-slider-v2__card--main .eco-featured-slider-v2__card-title',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'side_title_typography',
				'label'    => __( 'Side Card Title Typography', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-featured-slider-v2__card--side .eco-featured-slider-v2__card-title',
			]
		);

		$this->end_controls_section();
	}

	private function get_link_attrs( $link_settings ) {
		$attrs = '';

		if ( empty( $link_settings['url'] ) ) {
			return $attrs;
		}

		$attrs .= ' href="' . esc_url( $link_settings['url'] ) . '"';

		if ( ! empty( $link_settings['is_external'] ) ) {
			$attrs .= ' target="_blank"';
		}

		if ( ! empty( $link_settings['nofollow'] ) ) {
			$attrs .= ' rel="nofollow noopener"';
		} elseif ( ! empty( $link_settings['is_external'] ) ) {
			$attrs .= ' rel="noopener"';
		}

		return $attrs;
	}

	private function render_hero_slide( $settings, $is_active ) {
		$image_url = '';
		if ( ! empty( $settings['hero_image']['url'] ) ) {
			$image_url = $settings['hero_image']['url'];
		}

		$primary_link   = $settings['hero_primary_link'] ?? [];
		$secondary_link = $settings['hero_secondary_link'] ?? [];
		$primary_tag    = ! empty( $primary_link['url'] ) ? 'a' : 'span';
		$secondary_tag  = ! empty( $secondary_link['url'] ) ? 'a' : 'span';
		?>
		<div class="eco-featured-slider-v2__panel eco-featured-slider-v2__panel--hero <?php echo $is_active ? 'is-active' : ''; ?>" data-type="hero">
			<div class="eco-featured-slider-v2__hero-content">
				<div class="eco-featured-slider-v2__hero-inner">
					<?php if ( ! empty( $settings['hero_kicker'] ) ) : ?>
						<div class="eco-featured-slider-v2__hero-kicker"><?php echo esc_html( $settings['hero_kicker'] ); ?></div>
					<?php endif; ?>

					<?php if ( ! empty( $settings['hero_title'] ) ) : ?>
						<h1 class="eco-featured-slider-v2__hero-title"><?php echo nl2br( esc_html( $settings['hero_title'] ) ); ?></h1>
					<?php endif; ?>

					<?php if ( ! empty( $settings['hero_lead'] ) ) : ?>
						<div class="eco-featured-slider-v2__hero-lead"><?php echo wp_kses_post( wpautop( $settings['hero_lead'] ) ); ?></div>
					<?php endif; ?>

					<?php if ( ! empty( $settings['hero_text'] ) ) : ?>
						<div class="eco-featured-slider-v2__hero-text"><?php echo wp_kses_post( wpautop( $settings['hero_text'] ) ); ?></div>
					<?php endif; ?>

					<?php if ( ! empty( $settings['hero_primary_text'] ) || ! empty( $settings['hero_secondary_text'] ) ) : ?>
						<div class="eco-featured-slider-v2__hero-actions">
							<?php if ( ! empty( $settings['hero_primary_text'] ) ) : ?>
								<<?php echo esc_html( $primary_tag ); ?> class="eco-featured-slider-v2__hero-button eco-featured-slider-v2__hero-button--primary"<?php echo $this->get_link_attrs( $primary_link ); ?>>
									<span><?php echo esc_html( $settings['hero_primary_text'] ); ?></span>
									<span aria-hidden="true">→</span>
								</<?php echo esc_html( $primary_tag ); ?>>
							<?php endif; ?>

							<?php if ( ! empty( $settings['hero_secondary_text'] ) ) : ?>
								<<?php echo esc_html( $secondary_tag ); ?> class="eco-featured-slider-v2__hero-button eco-featured-slider-v2__hero-button--secondary"<?php echo $this->get_link_attrs( $secondary_link ); ?>>
									<span><?php echo esc_html( $settings['hero_secondary_text'] ); ?></span>
								</<?php echo esc_html( $secondary_tag ); ?>>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="eco-featured-slider-v2__hero-media">
				<?php if ( ! empty( $image_url ) ) : ?>
					<img class="eco-featured-slider-v2__hero-image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $settings['hero_title'] ?? '' ); ?>" loading="lazy">
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	private function render_card_item( $item, $index ) {
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
		$link        = $item['link'] ?? [];

		$tag = ! empty( $link['url'] ) ? 'a' : 'article';
		?>
		<<?php echo esc_html( $tag ); ?> class="eco-featured-slider-v2__card <?php echo $is_main ? 'eco-featured-slider-v2__card--main' : 'eco-featured-slider-v2__card--side'; ?>"<?php echo $this->get_link_attrs( $link ); ?>>
			<?php if ( ! empty( $image_url ) ) : ?>
				<img class="eco-featured-slider-v2__card-image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
			<?php endif; ?>

			<div class="eco-featured-slider-v2__card-content">
				<?php if ( ! empty( $badge ) || ! empty( $eyebrow ) ) : ?>
					<div class="eco-featured-slider-v2__labels">
						<?php if ( ! empty( $badge ) ) : ?>
							<span class="eco-featured-slider-v2__badge"><?php echo esc_html( $badge ); ?></span>
						<?php endif; ?>

						<?php if ( ! empty( $eyebrow ) ) : ?>
							<span class="eco-featured-slider-v2__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $title ) ) : ?>
					<h2 class="eco-featured-slider-v2__card-title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $text ) ) : ?>
					<div class="eco-featured-slider-v2__card-text"><?php echo esc_html( $text ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $date ) || ! empty( $location ) ) : ?>
					<div class="eco-featured-slider-v2__card-meta">
						<?php if ( ! empty( $date ) ) : ?>
							<span><?php echo esc_html( $date ); ?></span>
						<?php endif; ?>

						<?php if ( ! empty( $location ) ) : ?>
							<span><?php echo esc_html( $location ); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $is_main && ! empty( $button_text ) ) : ?>
					<div class="eco-featured-slider-v2__card-button"><?php echo esc_html( $button_text ); ?></div>
				<?php endif; ?>
			</div>
		</<?php echo esc_html( $tag ); ?>>
		<?php
	}

	protected function render() {
		$settings       = $this->get_settings_for_display();
		$items          = $settings['items'] ?? [];
		$show_hero      = ! empty( $settings['show_hero_slide'] ) && $settings['show_hero_slide'] === 'yes';
		$chunks         = ! empty( $items ) ? array_chunk( $items, 3 ) : [];
		$interval       = ! empty( $settings['interval'] ) ? absint( $settings['interval'] ) : 6000;
		$autoplay       = ! empty( $settings['autoplay'] ) && $settings['autoplay'] === 'yes';
		$show_dots      = ! empty( $settings['show_dots'] ) && $settings['show_dots'] === 'yes';
		$total_panels    = count( $chunks ) + ( $show_hero ? 1 : 0 );
		$current_index   = 0;

		if ( $total_panels < 1 ) {
			return;
		}
		?>
		<div class="eco-featured-slider-v2" data-autoplay="<?php echo esc_attr( $autoplay ? 'yes' : 'no' ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>">
			<div class="eco-featured-slider-v2__viewport">
				<?php if ( $show_hero ) : ?>
					<?php $this->render_hero_slide( $settings, $current_index === 0 ); ?>
					<?php $current_index++; ?>
				<?php endif; ?>

				<?php foreach ( $chunks as $chunk ) : ?>
					<div class="eco-featured-slider-v2__panel eco-featured-slider-v2__panel--cards <?php echo $current_index === 0 ? 'is-active' : ''; ?>" data-type="cards">
						<?php foreach ( $chunk as $item_index => $item ) : ?>
							<?php $this->render_card_item( $item, $item_index ); ?>
						<?php endforeach; ?>
					</div>
					<?php $current_index++; ?>
				<?php endforeach; ?>
			</div>

			<?php if ( $show_dots && $total_panels > 1 ) : ?>
				<div class="eco-featured-slider-v2__dots">
					<?php for ( $i = 0; $i < $total_panels; $i++ ) : ?>
						<button type="button" class="eco-featured-slider-v2__dot <?php echo $i === 0 ? 'is-active' : ''; ?>" data-index="<?php echo esc_attr( $i ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'elementor-eco' ), $i + 1 ) ); ?>"></button>
					<?php endfor; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
