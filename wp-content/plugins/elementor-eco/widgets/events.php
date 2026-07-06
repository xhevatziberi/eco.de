<?php
// plugins/elementor-eco/widgets/events.php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EventList extends Widget_Base {

	/**
	 * Retrieve the widgset name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'event-list';
	}

	public function get_title() {
		return __( 'Event List', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-ellipsis-h';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
   	}

	public function get_script_depends() {
		return [ 'eco-events-script' ];
	}

	public function get_style_depends() {
		return [ 'eco-events-style' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Section Content', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'elementor-eco' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Title' , 'elementor-eco' ),
			]
		);

		$this->end_controls_section();

		/*
		background-color
		padding
		margin
		border-radius
		border-color
		border-width
		border-style
		*/
		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Section Style', 'elementor-eco' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'color',
			[
				'label' => __( 'Color', 'elementor-eco' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .eco-event-list h2' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'elementor-eco' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eco-event-list' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'padding',
			[
				'label' => __( 'Padding', 'elementor-eco' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '20',
					'bottom' => '20',
					'left' => '20',
					'right' => '20',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .eco-event-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'margin',
			[
				'label' => __( 'Margin', 'elementor-eco' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '0',
					'bottom' => '0',
					'left' => '0',
					'right' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .eco-event-list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'elementor-eco' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '0',
					'bottom' => '0',
					'left' => '0',
					'right' => '0',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .eco-event-list' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'border_color',
			[
				'label' => __( 'Border Color', 'elementor-eco' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#cccccc',
				'selectors' => [
					'{{WRAPPER}} .eco-event-list' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'border_width',
			[
				'label' => __( 'Border Width', 'elementor-eco' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => '1',
					'bottom' => '1',
					'left' => '1',
					'right' => '1',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .eco-event-list' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>

		<div class="eco-event-list">
			<h2><?php echo $settings['title']; ?></h2>
			
			<div id="calendar">
				<div class="calendar-header">
					<span id="prev-month"><i aria-hidden="true" class="fas fa-caret-left"></i></span>
					<span id="calendar-month-year"></span>
					<span id="next-month"><i aria-hidden="true" class="fas fa-caret-right"></i></span>
				</div>

				<div class="calendar-filters">
					<select id="category-select">
						<option value=""><?php echo esc_html__( 'All', 'elementor-eco' ); ?></option>
						<?php
						$categories = get_terms( array(
							'taxonomy' => 'event-category',
							'hide_empty' => false,
						) );
						foreach ( $categories as $category ) {
							echo '<option value="' . esc_attr( $category->slug ) . '">' . esc_html( $category->name ) . '</option>';
						}
						?>
					</select>
				</div>

				<div class="calendar-tags">
					<div class="event-tags">
						<?php
						$tags = get_terms( array(
							'taxonomy' => 'event-tag',
							'hide_empty' => false,
						) );
						echo '<span class="event-tag" data-tag="">' . esc_html__( 'All', 'elementor-eco' ) . '</span>';
						foreach ( $tags as $tag ) {
							echo '<span class="event-tag" data-tag="' . esc_attr( $tag->slug ) . '">' . esc_html( $tag->name ) . '</span>';
						}
						?>
					</div>
				</div>

				<div id="calendar-days"></div>

				<div class="calendar-this-month">
					<button id="this-month-button" class="event-this-month active"><?php _e('This Month', 'elementor-eco'); ?></button>
				</div>

				<div id="events"></div>

			</div>

		</div>

		<?php
	}

	protected function content_template() {
		?>
		<div class="eco-event-list">
			<h2>{{{ settings.title }}}</h2>
			<div id="calendar">
				<div class="calendar-header">
				<button id="prev-month">&lt;</button>
				<span id="calendar-month-year"></span>
				<button id="next-month">&gt;</button>
				</div>

				<div class="calendar-filters">
				<label for="category-select"><?php esc_html_e( 'Filter by Category:', 'elementor-eco' ); ?></label>
				<select id="category-select">
					<option value=""><?php esc_html_e( 'All Categories', 'elementor-eco' ); ?></option>
					<!-- Add categories dynamically or hardcoded -->
					<option value="category-1"><?php esc_html_e( 'Category 1', 'elementor-eco' ); ?></option>
					<option value="category-2"><?php esc_html_e( 'Category 2', 'elementor-eco' ); ?></option>
				</select>
				</div>

				<div id="calendar-days"></div>
				<div id="events"></div>
		</div>
		
		<?php
	}
}
