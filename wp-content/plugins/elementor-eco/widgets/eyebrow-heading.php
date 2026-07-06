<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EyebrowHeading extends Widget_Base {

	public function get_name() {
		return 'eco-eyebrow-heading';
	}

	public function get_title() {
		return __( 'eco Eyebrow Heading', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-heading';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'dashicons', 'eco-eyebrow-heading-style' ];
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
			'icon',
			[
				'label'       => __( 'Icon', 'elementor-eco' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => true,
				'description' => __( 'If empty, the widget will try to use the current post ACF field: icon.', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'label',
			[
				'label'       => __( 'Text', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Wir gestalten Netzpolitik', 'elementor-eco' ),
				'description' => __( 'If empty, the widget will try to use the current post ACF field: eyebrow_label.', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'fallback_to_acf',
			[
				'label'        => __( 'Fallback to ACF', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'elementor-eco' ),
				'label_off'    => __( 'No', 'elementor-eco' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'acf_icon_field',
			[
				'label'       => __( 'ACF Icon Field', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'icon',
				'label_block' => true,
				'condition'   => [
					'fallback_to_acf' => 'yes',
				],
			]
		);

		$this->add_control(
			'acf_label_field',
			[
				'label'       => __( 'ACF Text Field', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'eyebrow_label',
				'label_block' => true,
				'condition'   => [
					'fallback_to_acf' => 'yes',
				],
			]
		);

		$this->add_control(
			'acf_color_field',
			[
				'label'       => __( 'ACF Color Field', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'color',
				'label_block' => true,
				'condition'   => [
					'fallback_to_acf' => 'yes',
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
            'color',
            [
                'label'       => __( 'Color', 'elementor-eco' ),
                'type'        => Controls_Manager::COLOR,
                'default'     => '',
                'selectors'   => [
                    '{{WRAPPER}} .eco-eyebrow-heading' => '--eco-eyebrow-color: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .eco-eyebrow-heading__icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eco-eyebrow-heading__icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eco-eyebrow-heading__icon svg' => 'color: {{VALUE}}; fill: {{VALUE}};',
                    '{{WRAPPER}} .eco-eyebrow-heading__icon svg *' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
                    '{{WRAPPER}} .eco-eyebrow-heading__line' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .eco-eyebrow-heading__text' => 'color: {{VALUE}};',
                ],
                'description' => __( 'If empty, the widget will try to use the current post ACF color field. Final fallback is #009fe3.', 'elementor-eco' ),
            ]
        );

		$this->add_responsive_control(
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
					'right' => [
						'title' => __( 'Right', 'elementor-eco' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
				'selectors' => [
					'{{WRAPPER}} .eco-eyebrow-heading' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label'      => __( 'Gap', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 6,
					'unit' => 'px',
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 40,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eco-eyebrow-heading' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => __( 'Icon Size', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 22,
					'unit' => 'px',
				],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eco-eyebrow-heading__icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eco-eyebrow-heading__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eco-eyebrow-heading__icon img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'line_width',
			[
				'label'      => __( 'Line Width', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eco-eyebrow-heading__line' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'line_height',
			[
				'label'      => __( 'Line Height', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 1,
					'unit' => 'px',
				],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 8,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eco-eyebrow-heading__line' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'line_gap',
			[
				'label'      => __( 'Line Gap', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 2,
					'unit' => 'px',
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 40,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eco-eyebrow-heading__line' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Text Color Override', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eco-eyebrow-heading__text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => __( 'Typography', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-eyebrow-heading__text',
			]
		);

		$this->end_controls_section();
	}

	private function is_valid_hex_color( $color ) {
		return is_string( $color ) && preg_match( '/^#([A-Fa-f0-9]{3}){1,2}$/', trim( $color ) );
	}

	private function get_context_post_id() {
		$post_id = get_queried_object_id();

		if ( $post_id ) {
			return $post_id;
		}

		return get_the_ID();
	}

	private function get_acf_value( $field_name, $post_id ) {
		if ( empty( $field_name ) || ! function_exists( 'get_field' ) ) {
			return '';
		}

		return get_field( sanitize_key( $field_name ), $post_id );
	}

	private function get_label( $settings, $post_id ) {
		$label = trim( (string) ( $settings['label'] ?? '' ) );

		if ( ! empty( $label ) ) {
			return $label;
		}

		if ( empty( $settings['fallback_to_acf'] ) || $settings['fallback_to_acf'] !== 'yes' ) {
			return '';
		}

		$field_name = ! empty( $settings['acf_label_field'] ) ? $settings['acf_label_field'] : 'eyebrow_label';
		$value      = $this->get_acf_value( $field_name, $post_id );

		if ( is_string( $value ) ) {
			return trim( $value );
		}

		return '';
	}

	private function get_color( $settings, $post_id ) {
        /*
        * If Elementor color control is filled, do not print inline color.
        * Elementor will output the CSS variable itself through the selector.
        */
        if ( ! empty( $settings['color'] ) ) {
            return '';
        }

        if ( ! empty( $settings['fallback_to_acf'] ) && $settings['fallback_to_acf'] === 'yes' ) {
            $field_name = ! empty( $settings['acf_color_field'] ) ? $settings['acf_color_field'] : 'color';
            $value      = $this->get_acf_value( $field_name, $post_id );

            if ( $this->is_valid_hex_color( $value ) ) {
                return trim( $value );
            }
        }

        return '#009fe3';
    }

	private function get_icon_from_acf( $settings, $post_id ) {
		if ( empty( $settings['fallback_to_acf'] ) || $settings['fallback_to_acf'] !== 'yes' ) {
			return '';
		}

		$field_name = ! empty( $settings['acf_icon_field'] ) ? $settings['acf_icon_field'] : 'icon';

		return $this->get_acf_value( $field_name, $post_id );
	}

	private function render_acf_icon( $icon ) {
        if ( empty( $icon ) ) {
            return;
        }

        $type  = '';
        $value = '';

        if ( is_string( $icon ) ) {
            $value = trim( $icon );
        } elseif ( is_array( $icon ) ) {
            $type = ! empty( $icon['type'] ) && is_string( $icon['type'] )
                ? sanitize_key( $icon['type'] )
                : '';

            if ( ! empty( $icon['value'] ) ) {
                $value = $icon['value'];
            } elseif ( ! empty( $icon['url'] ) ) {
                $value = $icon['url'];
            } elseif ( ! empty( $icon['class'] ) ) {
                $value = $icon['class'];
            }
        }

        if ( empty( $value ) ) {
            return;
        }

        /**
         * Image attachment ID.
         */
        if ( is_numeric( $value ) ) {
            $image_url = wp_get_attachment_image_url( absint( $value ), 'thumbnail' );

            if ( $image_url ) {
                ?>
                <img src="<?php echo esc_url( $image_url ); ?>" alt="" loading="lazy">
                <?php
            }

            return;
        }

        if ( is_array( $value ) ) {
            if ( ! empty( $value['url'] ) ) {
                ?>
                <img src="<?php echo esc_url( $value['url'] ); ?>" alt="" loading="lazy">
                <?php
                return;
            }

            if ( ! empty( $value['ID'] ) ) {
                $image_url = wp_get_attachment_image_url( absint( $value['ID'] ), 'thumbnail' );

                if ( $image_url ) {
                    ?>
                    <img src="<?php echo esc_url( $image_url ); ?>" alt="" loading="lazy">
                    <?php
                }

                return;
            }
        }

        $value = trim( (string) $value );

        if ( empty( $value ) ) {
            return;
        }

        /**
         * Image URL.
         */
        if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
            ?>
            <img src="<?php echo esc_url( $value ); ?>" alt="" loading="lazy">
            <?php
            return;
        }

        /**
         * Dashicons.
         * ACF may return "dashicons-admin-home" instead of "dashicons dashicons-admin-home".
         */
        if ( strpos( $value, 'dashicons-' ) === 0 ) {
            $value = 'dashicons ' . $value;
        }

        /**
         * Font Awesome / Elementor / custom icon class.
         */
        ?>
        <i class="<?php echo esc_attr( $value ); ?>" aria-hidden="true"></i>
        <?php
    }

	private function has_elementor_icon( $settings ) {
		return ! empty( $settings['icon'] ) && is_array( $settings['icon'] ) && ! empty( $settings['icon']['value'] );
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$post_id  = $this->get_context_post_id();

		$label = $this->get_label( $settings, $post_id );
		$color = $this->get_color( $settings, $post_id );

		$has_elementor_icon = $this->has_elementor_icon( $settings );
		$acf_icon           = $has_elementor_icon ? '' : $this->get_icon_from_acf( $settings, $post_id );

		if ( empty( $label ) && ! $has_elementor_icon && empty( $acf_icon ) ) {
			return;
		}

		?>
		<div class="eco-eyebrow-heading"<?php echo ! empty( $color ) ? ' style="--eco-eyebrow-color: ' . esc_attr( $color ) . ';"' : ''; ?>>
			<?php if ( $has_elementor_icon || ! empty( $acf_icon ) ) : ?>
				<span class="eco-eyebrow-heading__icon" aria-hidden="true">
					<?php
					if ( $has_elementor_icon ) {
						Icons_Manager::render_icon(
							$settings['icon'],
							[
								'aria-hidden' => 'true',
							]
						);
					} else {
						$this->render_acf_icon( $acf_icon );
					}
					?>
				</span>
			<?php endif; ?>

			<span class="eco-eyebrow-heading__line" aria-hidden="true"></span>

			<?php if ( ! empty( $label ) ) : ?>
				<span class="eco-eyebrow-heading__text">
					<?php echo esc_html( $label ); ?>
				</span>
			<?php endif; ?>
		</div>
		<?php
	}
}