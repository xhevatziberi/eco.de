<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EventCalendar extends Widget_Base {

	public function get_name() {
		return 'eco-event-calendar';
	}

	public function get_title() {
		return __( 'eco Event Calendar', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-calendar';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'eco-event-calendar-style' ];
	}

	public function get_script_depends() {
		return [ 'eco-event-calendar-script' ];
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
			'title',
			[
				'label'       => __( 'Title', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Veranstaltungskalender', 'elementor-eco' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'default_filter',
			[
				'label'   => __( 'Default Filter', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => [
					'all'           => __( 'All Events', 'elementor-eco' ),
					'eco-event'     => __( 'eco Events', 'elementor-eco' ),
					'partner-event' => __( 'Partner Events', 'elementor-eco' ),
					'past'          => __( 'Past Events', 'elementor-eco' ),
				],
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'   => __( 'Events Per Page', 'elementor-eco' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 2,
				'min'     => 1,
				'max'     => 12,
			]
		);

		$this->add_control(
			'show_filter',
			[
				'label'        => __( 'Show Filter', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-eco' ),
				'label_off'    => __( 'No', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_load_more',
			[
				'label'        => __( 'Show Load More', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-eco' ),
				'label_off'    => __( 'No', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label'        => __( 'Show Excerpt', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-eco' ),
				'label_off'    => __( 'No', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label'     => __( 'Excerpt Length', 'elementor-eco' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 16,
				'min'       => 5,
				'max'       => 60,
				'condition' => [
					'show_excerpt' => 'yes',
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
					'16-9' => '16:9',
					'4-3'  => '4:3',
					'3-2'  => '3:2',
					'1-1'  => '1:1',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'labels_section',
			[
				'label' => __( 'Labels', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control( 'filter_placeholder', [ 'label' => __( 'Filter Placeholder', 'elementor-eco' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Filter', 'elementor-eco' ) ] );
		$this->add_control( 'filter_all_label', [ 'label' => __( 'All Events Label', 'elementor-eco' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Alle Veranstaltungen', 'elementor-eco' ) ] );
		$this->add_control( 'filter_eco_label', [ 'label' => __( 'eco Events Label', 'elementor-eco' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'eco Events', 'elementor-eco' ) ] );
		$this->add_control( 'filter_partner_label', [ 'label' => __( 'Partner Events Label', 'elementor-eco' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Partner Events', 'elementor-eco' ) ] );
		$this->add_control( 'filter_past_label', [ 'label' => __( 'Past Events Label', 'elementor-eco' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Vergangene Veranstaltungen', 'elementor-eco' ) ] );
		$this->add_control( 'load_more_label', [ 'label' => __( 'Load More Label', 'elementor-eco' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Weitere Veranstaltungen laden', 'elementor-eco' ) ] );
		$this->add_control( 'empty_title', [ 'label' => __( 'Empty Title', 'elementor-eco' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Keine Veranstaltungen gefunden', 'elementor-eco' ) ] );
		$this->add_control( 'empty_text', [ 'label' => __( 'Empty Text', 'elementor-eco' ), 'type' => Controls_Manager::TEXTAREA, 'default' => __( 'Für diesen Zeitraum sind aktuell keine Veranstaltungen verfügbar.', 'elementor-eco' ) ] );

		$this->end_controls_section();

		$this->start_controls_section(
			'layout_style_section',
			[
				'label' => __( 'Layout', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'calendar_width',
			[
				'label' => __( 'Calendar Width', 'elementor-eco' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [ 'px' => [ 'min' => 180, 'max' => 500 ], '%' => [ 'min' => 20, 'max' => 50 ] ],
				'default' => [ 'size' => 340, 'unit' => 'px' ],
				'selectors' => [ '{{WRAPPER}} .eco-event-calendar' => '--eco-event-calendar-left: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label' => __( 'Gap', 'elementor-eco' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'default' => [ 'size' => 48, 'unit' => 'px' ],
				'selectors' => [ '{{WRAPPER}} .eco-event-calendar' => '--eco-event-calendar-gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'accent_color',
			[
				'label' => __( 'Accent Color', 'elementor-eco' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#c2cf00',
				'selectors' => [ '{{WRAPPER}} .eco-event-calendar' => '--eco-event-calendar-accent: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label' => __( 'Heading Color', 'elementor-eco' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1d1d1f',
				'selectors' => [ '{{WRAPPER}} .eco-event-calendar' => '--eco-event-calendar-heading: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'elementor-eco' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#6f6f75',
				'selectors' => [ '{{WRAPPER}} .eco-event-calendar' => '--eco-event-calendar-text: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style_section',
			[
				'label' => __( 'Title', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .eco-event-calendar__title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'elementor-eco' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .eco-event-calendar__title' => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'card_style_section',
			[
				'label' => __( 'Cards', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'cards_columns',
			[
				'label' => __( 'Columns', 'elementor-eco' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 2,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'min' => 1,
				'max' => 4,
				'selectors' => [ '{{WRAPPER}} .eco-event-calendar__cards' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'card_shadow',
				'selector' => '{{WRAPPER}} .eco-event-calendar-card',
			]
		);

		$this->add_control(
			'card_radius',
			[
				'label' => __( 'Border Radius', 'elementor-eco' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default' => [ 'size' => 4, 'unit' => 'px' ],
				'selectors' => [ '{{WRAPPER}} .eco-event-calendar-card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! class_exists( '\ElementorEco\EventCalendarAjax' ) ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		$year     = (int) current_time( 'Y' );
		$month    = (int) current_time( 'n' );
		$source   = $settings['default_filter'] ?? 'all';
		$data     = \ElementorEco\EventCalendarAjax::build_calendar_data( $settings, $year, $month, 0, $source, 1 );
		$ajax_settings = \ElementorEco\EventCalendarAjax::sanitize_settings( $settings );
		?>
		<div class="eco-event-calendar" data-year="<?php echo esc_attr( $year ); ?>" data-month="<?php echo esc_attr( $month ); ?>" data-day="0" data-source="<?php echo esc_attr( $source ); ?>" data-page="1" data-nonce="<?php echo esc_attr( wp_create_nonce( 'eco_event_calendar' ) ); ?>" data-settings="<?php echo esc_attr( wp_json_encode( $ajax_settings ) ); ?>">
			<div class="eco-event-calendar__header">
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<h2 class="eco-event-calendar__title"><?php echo esc_html( $settings['title'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $settings['show_filter'] ) && $settings['show_filter'] === 'yes' ) : ?>
					<div class="eco-event-calendar__filter">
						<select class="eco-event-calendar__filter-select" aria-label="<?php esc_attr_e( 'Filter events', 'elementor-eco' ); ?>">
							<option value="" disabled selected><?php echo esc_html( $settings['filter_placeholder'] ?? __( 'Filter', 'elementor-eco' ) ); ?></option>
							<option value="all" <?php selected( $source, 'all' ); ?>><?php echo esc_html( $settings['filter_all_label'] ?? __( 'Alle Veranstaltungen', 'elementor-eco' ) ); ?></option>
							<option value="eco-event" <?php selected( $source, 'eco-event' ); ?>><?php echo esc_html( $settings['filter_eco_label'] ?? __( 'eco Events', 'elementor-eco' ) ); ?></option>
							<option value="partner-event" <?php selected( $source, 'partner-event' ); ?>><?php echo esc_html( $settings['filter_partner_label'] ?? __( 'Partner Events', 'elementor-eco' ) ); ?></option>
							<option value="past" <?php selected( $source, 'past' ); ?>><?php echo esc_html( $settings['filter_past_label'] ?? __( 'Vergangene Veranstaltungen', 'elementor-eco' ) ); ?></option>
						</select>
					</div>
				<?php endif; ?>
			</div>

			<div class="eco-event-calendar__layout">
				<div class="eco-event-calendar__calendar" aria-live="polite">
					<?php echo $data['calendar_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>

				<div class="eco-event-calendar__results">
					<div class="eco-event-calendar__cards" aria-live="polite">
						<?php echo $data['cards_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>

					<?php if ( ! empty( $settings['show_load_more'] ) && $settings['show_load_more'] === 'yes' ) : ?>
						<div class="eco-event-calendar__load-more-wrap" <?php echo empty( $data['has_more'] ) ? 'hidden' : ''; ?>>
							<button type="button" class="eco-event-calendar__load-more">
								<?php echo esc_html( $settings['load_more_label'] ?? __( 'Weitere Veranstaltungen laden', 'elementor-eco' ) ); ?>
							</button>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
