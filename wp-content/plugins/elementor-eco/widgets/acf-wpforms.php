<?php
namespace ElementorEco\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AcfWpforms extends Widget_Base {

	public function get_name() {
		return 'eco-acf-wpforms';
	}

	public function get_title() {
		return __( 'ACF WPForms', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'form_section',
			[
				'label' => __( 'Form', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'acf_field_name',
			[
				'label'       => __( 'ACF Field Name', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'wp_forms_form',
				'placeholder' => 'wp_forms_form',
				'description' => __( 'Enter the ACF/SCF field name that stores the selected WPForms form.', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'        => __( 'Show Form Title', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'show_description',
			[
				'label'        => __( 'Show Form Description', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'fallback_form_id',
			[
				'label'       => __( 'Fallback Form ID', 'elementor-eco' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'step'        => 1,
				'description' => __( 'Optional. Used only when the ACF field is empty, which is useful for Elementor template previews.', 'elementor-eco' ),
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$form_id  = $this->get_form_id( $settings );

		if ( ! $form_id || ! shortcode_exists( 'wpforms' ) ) {
			return;
		}

		$show_title       = 'yes' === ( $settings['show_title'] ?? '' ) ? 'true' : 'false';
		$show_description = 'yes' === ( $settings['show_description'] ?? '' ) ? 'true' : 'false';

		$shortcode = sprintf(
			'[wpforms id="%d" title="%s" description="%s"]',
			$form_id,
			$show_title,
			$show_description
		);

		echo '<div class="eco-acf-wpforms">';
		echo do_shortcode( $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}

	private function get_form_id( array $settings ) {
		$field_name = sanitize_key( $settings['acf_field_name'] ?? '' );
		$form_value = null;

		if ( $field_name ) {
			$post_id = $this->get_context_post_id();

			if ( function_exists( 'get_field' ) ) {
				$form_value = get_field( $field_name, $post_id ?: false );
			} elseif ( $post_id ) {
				$form_value = get_post_meta( $post_id, $field_name, true );
			}
		}

		$form_id = $this->normalize_form_id( $form_value );

		if ( ! $form_id ) {
			$form_id = absint( $settings['fallback_form_id'] ?? 0 );
		}

		if ( ! $form_id ) {
			return 0;
		}

		// WPForms forms normally use the "wpforms" post type. Avoid rendering
		// unrelated posts if an invalid relationship value is stored.
		$post_type = get_post_type( $form_id );
		if ( $post_type && 'wpforms' !== $post_type ) {
			return 0;
		}

		return $form_id;
	}

	private function normalize_form_id( $value ) {
		if ( empty( $value ) ) {
			return 0;
		}

		if ( $value instanceof \WP_Post ) {
			return absint( $value->ID );
		}

		if ( is_object( $value ) && isset( $value->ID ) ) {
			return absint( $value->ID );
		}

		if ( is_array( $value ) ) {
			if ( isset( $value['ID'] ) ) {
				return absint( $value['ID'] );
			}

			$first = reset( $value );

			return $this->normalize_form_id( $first );
		}

		return absint( $value );
	}

	private function get_context_post_id() {
		$post_id = get_the_ID();

		if ( $post_id ) {
			return absint( $post_id );
		}

		if ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return absint( $_GET['post'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		return 0;
	}
}
