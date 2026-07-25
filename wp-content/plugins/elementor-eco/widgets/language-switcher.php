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

class LanguageSwitcher extends Widget_Base {

	public function get_name() {
		return 'eco-language-switcher';
	}

	public function get_title() {
		return __( 'eco Language Switcher', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-global-settings';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'eco-language-switcher-style' ];
	}

	public function get_script_depends() {
		return [ 'eco-language-switcher-script' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Language Switcher', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'trigger_icon',
			[
				'label'   => __( 'Trigger Icon', 'elementor-eco' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-globe',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'show_current_name',
			[
				'label'        => __( 'Show Current Language Name', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-eco' ),
				'label_off'    => __( 'Hide', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'show_current_flag',
			[
				'label'        => __( 'Show Current Language Flag', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-eco' ),
				'label_off'    => __( 'Hide', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'show_chevron',
			[
				'label'        => __( 'Show Dropdown Arrow', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-eco' ),
				'label_off'    => __( 'Hide', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'name_format',
			[
				'label'   => __( 'Language Name Format', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'native_name',
				'options' => [
					'native_name'     => __( 'Native name (Deutsch / English)', 'elementor-eco' ),
					'translated_name' => __( 'Translated name', 'elementor-eco' ),
					'language_code'   => __( 'Language code (DE / EN)', 'elementor-eco' ),
				],
			]
		);

		$this->add_control(
			'uppercase_code',
			[
				'label'        => __( 'Uppercase Language Code', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'name_format' => 'language_code',
				],
			]
		);

		$this->add_control(
			'show_flags',
			[
				'label'        => __( 'Show Flags in Dropdown', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-eco' ),
				'label_off'    => __( 'Hide', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'hide_missing',
			[
				'label'        => __( 'Hide Missing Translations', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-eco' ),
				'label_off'    => __( 'No', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Languages without a translation for the current page will not be listed.', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'hide_current',
			[
				'label'        => __( 'Hide Current Language from Dropdown', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-eco' ),
				'label_off'    => __( 'No', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'dropdown_position',
			[
				'label'   => __( 'Dropdown Position', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom',
				'options' => [
					'bottom' => __( 'Below', 'elementor-eco' ),
					'top'    => __( 'Above', 'elementor-eco' ),
				],
			]
		);

		$this->add_control(
			'dropdown_alignment',
			[
				'label'   => __( 'Dropdown Alignment', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'  => __( 'Left', 'elementor-eco' ),
					'right' => __( 'Right', 'elementor-eco' ),
				],
			]
		);

		$this->add_control(
			'mobile_breakpoint',
			[
				'label'       => __( 'Native Select Breakpoint', 'elementor-eco' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 767,
				'min'         => 320,
				'max'         => 1200,
				'step'        => 1,
				'description' => __( 'At or below this viewport width, the trigger opens the device’s native language select.', 'elementor-eco' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'trigger_style_section',
			[
				'label' => __( 'Trigger', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label'   => __( 'Alignment', 'elementor-eco' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'right',
				'options' => [
					'left' => [ 'title' => __( 'Left', 'elementor-eco' ), 'icon' => 'eicon-h-align-left' ],
					'center' => [ 'title' => __( 'Center', 'elementor-eco' ), 'icon' => 'eicon-h-align-center' ],
					'right' => [ 'title' => __( 'Right', 'elementor-eco' ), 'icon' => 'eicon-h-align-right' ],
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
				'selectors' => [
					'{{WRAPPER}} .eco-language-switcher-wrap' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'trigger_size',
			[
				'label'      => __( 'Minimum Size', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 'size' => 44, 'unit' => 'px' ],
				'range'      => [ 'px' => [ 'min' => 28, 'max' => 100 ] ],
				'selectors'  => [
					'{{WRAPPER}} .eco-language-switcher__trigger' => 'min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => __( 'Icon Size', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 'size' => 20, 'unit' => 'px' ],
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 60 ] ],
				'selectors'  => [
					'{{WRAPPER}} .eco-language-switcher__icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eco-language-switcher__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'trigger_gap',
			[
				'label'      => __( 'Content Gap', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 'size' => 8, 'unit' => 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
				'selectors'  => [
					'{{WRAPPER}} .eco-language-switcher__trigger' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'trigger_padding',
			[
				'label'      => __( 'Padding', 'elementor-eco' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => 8, 'right' => 10, 'bottom' => 8, 'left' => 10, 'unit' => 'px', 'isLinked' => false ],
				'selectors'  => [
					'{{WRAPPER}} .eco-language-switcher__trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'trigger_typography',
				'selector' => '{{WRAPPER}} .eco-language-switcher__current-name',
			]
		);

		$this->start_controls_tabs( 'trigger_state_tabs' );
		$this->start_controls_tab( 'trigger_normal_tab', [ 'label' => __( 'Normal', 'elementor-eco' ) ] );
		$this->add_control( 'trigger_color', [
			'label' => __( 'Color', 'elementor-eco' ), 'type' => Controls_Manager::COLOR, 'default' => '#223fa1',
			'selectors' => [ '{{WRAPPER}} .eco-language-switcher__trigger' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'trigger_background', [
			'label' => __( 'Background', 'elementor-eco' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .eco-language-switcher__trigger' => 'background-color: {{VALUE}};' ],
		] );
		$this->end_controls_tab();
		$this->start_controls_tab( 'trigger_hover_tab', [ 'label' => __( 'Hover / Open', 'elementor-eco' ) ] );
		$this->add_control( 'trigger_hover_color', [
			'label' => __( 'Color', 'elementor-eco' ), 'type' => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .eco-language-switcher__trigger:hover, {{WRAPPER}} .eco-language-switcher.is-open .eco-language-switcher__trigger' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'trigger_hover_background', [
			'label' => __( 'Background', 'elementor-eco' ), 'type' => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .eco-language-switcher__trigger:hover, {{WRAPPER}} .eco-language-switcher.is-open .eco-language-switcher__trigger' => 'background-color: {{VALUE}};' ],
		] );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'trigger_border',
				'selector' => '{{WRAPPER}} .eco-language-switcher__trigger',
			]
		);

		$this->add_responsive_control(
			'trigger_radius',
			[
				'label'      => __( 'Border Radius', 'elementor-eco' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ '{{WRAPPER}} .eco-language-switcher__trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'dropdown_style_section',
			[
				'label' => __( 'Dropdown', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control( 'dropdown_width', [
			'label' => __( 'Minimum Width', 'elementor-eco' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px' ],
			'default' => [ 'size' => 180, 'unit' => 'px' ], 'range' => [ 'px' => [ 'min' => 100, 'max' => 400 ] ],
			'selectors' => [ '{{WRAPPER}} .eco-language-switcher__dropdown' => 'min-width: {{SIZE}}{{UNIT}};' ],
		] );
		$this->add_control( 'dropdown_background', [
			'label' => __( 'Background', 'elementor-eco' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .eco-language-switcher__dropdown' => 'background-color: {{VALUE}};' ],
		] );
		$this->add_control( 'item_color', [
			'label' => __( 'Text Color', 'elementor-eco' ), 'type' => Controls_Manager::COLOR, 'default' => '#1f2937',
			'selectors' => [ '{{WRAPPER}} .eco-language-switcher__link' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'item_hover_color', [
			'label' => __( 'Hover Text Color', 'elementor-eco' ), 'type' => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .eco-language-switcher__link:hover, {{WRAPPER}} .eco-language-switcher__link:focus-visible' => 'color: {{VALUE}};' ],
		] );
		$this->add_control( 'item_hover_background', [
			'label' => __( 'Hover Background', 'elementor-eco' ), 'type' => Controls_Manager::COLOR, 'default' => '#f3f4f6',
			'selectors' => [ '{{WRAPPER}} .eco-language-switcher__link:hover, {{WRAPPER}} .eco-language-switcher__link:focus-visible' => 'background-color: {{VALUE}};' ],
		] );
		$this->add_control( 'active_color', [
			'label' => __( 'Current Language Color', 'elementor-eco' ), 'type' => Controls_Manager::COLOR, 'default' => '#223fa1',
			'selectors' => [ '{{WRAPPER}} .eco-language-switcher__link.is-active' => 'color: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'item_typography', 'selector' => '{{WRAPPER}} .eco-language-switcher__link' ] );
		$this->add_responsive_control( 'item_padding', [
			'label' => __( 'Item Padding', 'elementor-eco' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px', 'em' ],
			'default' => [ 'top' => 10, 'right' => 14, 'bottom' => 10, 'left' => 14, 'unit' => 'px', 'isLinked' => false ],
			'selectors' => [ '{{WRAPPER}} .eco-language-switcher__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );
		$this->add_responsive_control( 'flag_width', [
			'label' => __( 'Flag Width', 'elementor-eco' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px' ],
			'default' => [ 'size' => 20, 'unit' => 'px' ], 'range' => [ 'px' => [ 'min' => 10, 'max' => 60 ] ],
			'selectors' => [ '{{WRAPPER}} .eco-language-switcher__flag' => 'width: {{SIZE}}{{UNIT}};' ],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'dropdown_border', 'selector' => '{{WRAPPER}} .eco-language-switcher__dropdown' ] );
		$this->add_responsive_control( 'dropdown_radius', [
			'label' => __( 'Border Radius', 'elementor-eco' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px' ],
			'selectors' => [ '{{WRAPPER}} .eco-language-switcher__dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'dropdown_shadow', 'selector' => '{{WRAPPER}} .eco-language-switcher__dropdown' ] );
		$this->end_controls_section();
	}

	private function get_language_label( $language, $settings ) {
		$format = isset( $settings['name_format'] ) ? $settings['name_format'] : 'native_name';

		if ( 'language_code' === $format ) {
			$label = isset( $language['language_code'] ) ? $language['language_code'] : '';
			return 'yes' === $settings['uppercase_code'] ? strtoupper( $label ) : strtolower( $label );
		}

		if ( 'translated_name' === $format ) {
			return isset( $language['translated_name'] ) ? $language['translated_name'] : '';
		}

		return isset( $language['native_name'] ) ? $language['native_name'] : '';
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! has_filter( 'wpml_active_languages' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="eco-language-switcher__notice">' . esc_html__( 'WPML must be active to display the language switcher.', 'elementor-eco' ) . '</div>';
			}
			return;
		}

		$args = [
			'skip_missing' => 'yes' === $settings['hide_missing'] ? 1 : 0,
			'orderby'      => 'id',
			'order'        => 'asc',
		];

		$languages = apply_filters( 'wpml_active_languages', null, $args );
		if ( empty( $languages ) || ! is_array( $languages ) ) {
			return;
		}

		$current_language = null;
		$visible_languages = [];
		foreach ( $languages as $language ) {
			if ( ! empty( $language['active'] ) ) {
				$current_language = $language;
			}
			if ( 'yes' === $settings['hide_current'] && ! empty( $language['active'] ) ) {
				continue;
			}
			$visible_languages[] = $language;
		}

		if ( ! $current_language ) {
			$current_language = reset( $languages );
		}

		$widget_id = 'eco-language-switcher-' . $this->get_id();
		$dropdown_id = $widget_id . '-dropdown';
		$breakpoint = max( 320, min( 1200, absint( $settings['mobile_breakpoint'] ) ) );
		$classes = [
			'eco-language-switcher',
			'eco-language-switcher--' . sanitize_html_class( $settings['dropdown_position'] ),
			'eco-language-switcher--align-' . sanitize_html_class( $settings['dropdown_alignment'] ),
		];
		?>
		<div class="eco-language-switcher-wrap">
			<div id="<?php echo esc_attr( $widget_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-mobile-breakpoint="<?php echo esc_attr( $breakpoint ); ?>">
				<button class="eco-language-switcher__trigger" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $dropdown_id ); ?>" aria-haspopup="listbox" aria-label="<?php echo esc_attr__( 'Choose language', 'elementor-eco' ); ?>">
					<?php if ( ! empty( $settings['trigger_icon']['value'] ) ) : ?>
						<span class="eco-language-switcher__icon" aria-hidden="true"><?php Icons_Manager::render_icon( $settings['trigger_icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
					<?php endif; ?>
					<?php if ( 'yes' === $settings['show_current_flag'] && ! empty( $current_language['country_flag_url'] ) ) : ?>
						<img class="eco-language-switcher__flag eco-language-switcher__current-flag" src="<?php echo esc_url( $current_language['country_flag_url'] ); ?>" alt="" loading="lazy">
					<?php endif; ?>
					<?php if ( 'yes' === $settings['show_current_name'] ) : ?>
						<span class="eco-language-switcher__current-name"><?php echo esc_html( $this->get_language_label( $current_language, $settings ) ); ?></span>
					<?php endif; ?>
					<?php if ( 'yes' === $settings['show_chevron'] ) : ?>
						<span class="eco-language-switcher__chevron" aria-hidden="true"><svg viewBox="0 0 12 8" focusable="false"><path d="M1 1.5 6 6.5l5-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/></svg></span>
					<?php endif; ?>
				</button>

				<div id="<?php echo esc_attr( $dropdown_id ); ?>" class="eco-language-switcher__dropdown" role="listbox" hidden>
					<?php foreach ( $visible_languages as $language ) : ?>
						<a class="eco-language-switcher__link<?php echo ! empty( $language['active'] ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( $language['url'] ); ?>" hreflang="<?php echo esc_attr( $language['language_code'] ); ?>" lang="<?php echo esc_attr( $language['default_locale'] ?? $language['language_code'] ); ?>" role="option" aria-selected="<?php echo ! empty( $language['active'] ) ? 'true' : 'false'; ?>">
							<?php if ( 'yes' === $settings['show_flags'] && ! empty( $language['country_flag_url'] ) ) : ?>
								<img class="eco-language-switcher__flag" src="<?php echo esc_url( $language['country_flag_url'] ); ?>" alt="" loading="lazy">
							<?php endif; ?>
							<span><?php echo esc_html( $this->get_language_label( $language, $settings ) ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>

				<select class="eco-language-switcher__native-select" aria-label="<?php echo esc_attr__( 'Choose language', 'elementor-eco' ); ?>">
					<?php foreach ( $languages as $language ) : ?>
						<option value="<?php echo esc_url( $language['url'] ); ?>"<?php selected( ! empty( $language['active'] ) ); ?>><?php echo esc_html( $this->get_language_label( $language, $settings ) ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<?php
	}
}
