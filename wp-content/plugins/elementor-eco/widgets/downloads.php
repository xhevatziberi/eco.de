<?php

namespace ElementorEco\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Downloads extends Widget_Base {

	public function get_name(): string {
		return 'eco-downloads';
	}

	public function get_title(): string {
		return __( 'eco Downloads', 'elementor-eco' );
	}

	public function get_icon(): string {
		return 'eicon-download-kit';
	}

	public function get_categories(): array {
		return [ 'eco' ];
	}

	public function get_keywords(): array {
		return [ 'download', 'downloads', 'document', 'file', 'dlm', 'eco' ];
	}

	public function get_style_depends(): array {
		return [ 'eco-downloads-style' ];
	}

	public function get_script_depends(): array {
		return [ 'eco-downloads-script' ];
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_query_controls();
		$this->register_display_controls();
		$this->register_style_controls();
	}

	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => __( 'Title', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Downloads', 'elementor-eco' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'intro',
			[
				'label'       => __( 'Intro Text', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => '',
				'label_block' => true,
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => __( 'Button Text', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Download', 'elementor-eco' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();
	}

	private function register_query_controls(): void {
		$this->start_controls_section(
			'section_query',
			[
				'label' => __( 'Query', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'query_source',
			[
				'label'   => __( 'Query Source', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => [
					'manual'      => __( 'Manual Terms', 'elementor-eco' ),
					'current_acf' => __( 'Current ACF Terms', 'elementor-eco' ),
				],
			]
		);

		$this->add_control(
			'manual_categories',
			[
				'label'       => __( 'Download Categories', 'elementor-eco' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->get_taxonomy_options( 'dlm_download_category' ),
				'multiple'    => true,
				'label_block' => true,
				'condition'   => [
					'query_source' => 'manual',
				],
			]
		);

		$this->add_control(
			'manual_tags',
			[
				'label'       => __( 'Download Tags', 'elementor-eco' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->get_taxonomy_options( 'dlm_download_tag' ),
				'multiple'    => true,
				'label_block' => true,
				'condition'   => [
					'query_source' => 'manual',
				],
			]
		);

		$this->add_control(
			'acf_category_field',
			[
				'label'       => __( 'ACF Category Field Name', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'download_categories',
				'placeholder' => 'download_categories',
				'label_block' => true,
				'condition'   => [
					'query_source' => 'current_acf',
				],
			]
		);

		$this->add_control(
			'acf_tag_field',
			[
				'label'       => __( 'ACF Tag Field Name', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'download_tags',
				'placeholder' => 'download_tags',
				'label_block' => true,
				'condition'   => [
					'query_source' => 'current_acf',
				],
			]
		);

		$this->add_control(
			'tax_relation',
			[
				'label'       => __( 'Category / Tag Relation', 'elementor-eco' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'AND',
				'options'     => [
					'AND' => __( 'AND — match both', 'elementor-eco' ),
					'OR'  => __( 'OR — match either', 'elementor-eco' ),
				],
				'description' => __( 'Within each taxonomy, any selected term can match.', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'empty_terms_behavior',
			[
				'label'       => __( 'When No Terms Are Selected', 'elementor-eco' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'none',
				'options'     => [
					'none' => __( 'Show no downloads', 'elementor-eco' ),
					'all'  => __( 'Show all downloads', 'elementor-eco' ),
				],
				'description' => __( 'For template usage, “Show no downloads” prevents an unconfigured Tile from showing every download.', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'   => __( 'Downloads Per Page', 'elementor-eco' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 12,
				'min'     => 1,
				'max'     => 100,
				'step'    => 1,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __( 'Order By', 'elementor-eco' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'date'       => __( 'Date', 'elementor-eco' ),
					'title'      => __( 'Title', 'elementor-eco' ),
					'menu_order' => __( 'Menu Order', 'elementor-eco' ),
					'rand'       => __( 'Random', 'elementor-eco' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'     => __( 'Order', 'elementor-eco' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => [
					'ASC'  => __( 'Ascending', 'elementor-eco' ),
					'DESC' => __( 'Descending', 'elementor-eco' ),
				],
				'condition' => [
					'orderby!' => 'rand',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_display_controls(): void {
		$this->start_controls_section(
			'section_display',
			[
				'label' => __( 'Display', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'show_filters',
			[
				'label'        => __( 'Show Filter Bar', 'elementor-eco' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'filter_taxonomies',
			[
				'label'     => __( 'Filter Terms', 'elementor-eco' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'both',
				'options'   => [
					'categories' => __( 'Categories only', 'elementor-eco' ),
					'tags'       => __( 'Tags only', 'elementor-eco' ),
					'both'       => __( 'Categories and tags', 'elementor-eco' ),
				],
				'condition' => [
					'show_filters' => 'yes',
				],
			]
		);

		foreach ( [
			'show_date' => [ 'Show Date', 'yes' ],
			'show_file_meta' => [ 'Show File Information', 'yes' ],
			'show_excerpt' => [ 'Show Description', 'yes' ],
			'show_terms' => [ 'Show Term Badges', 'yes' ],
			'show_pagination' => [ 'Show Pagination', 'yes' ],
		] as $id => $data ) {
			$this->add_control(
				$id,
				[
					'label'        => __( $data[0], 'elementor-eco' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => $data[1],
				]
			);
		}

		$this->add_control(
			'date_label',
			[
				'label'     => __( 'Date Label', 'elementor-eco' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Published', 'elementor-eco' ),
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_controls(): void {
		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Style', 'elementor-eco' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'accent_color',
			[
				'label'       => __( 'Accent Color', 'elementor-eco' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '',
				'description' => __( 'When empty, the current post ACF “color” field is used. Fallback: #e2001a.', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'card_background',
			[
				'label'     => __( 'Card Background', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eco-download-card' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => __( 'Border Color', 'elementor-eco' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e6e7ea',
				'selectors' => [
					'{{WRAPPER}} .eco-download-card' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => __( 'Card Padding', 'elementor-eco' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => 22,
					'right'    => 22,
					'bottom'   => 22,
					'left'     => 22,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .eco-download-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_radius',
			[
				'label'      => __( 'Border Radius', 'elementor-eco' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 40 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => 10 ],
				'selectors'  => [
					'{{WRAPPER}} .eco-download-card' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .eco-download-card',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __( 'Download Title Typography', 'elementor-eco' ),
				'selector' => '{{WRAPPER}} .eco-download-card-title',
			]
		);

		$this->end_controls_section();
	}

	private function get_taxonomy_options( string $taxonomy ): array {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return [];
		}

		$terms = get_terms(
			[
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			]
		);

		if ( is_wp_error( $terms ) ) {
			return [];
		}

		$options = [];

		foreach ( $terms as $term ) {
			$options[ (string) $term->term_id ] = $term->name;
		}

		return $options;
	}

	private function get_context_post_id_from_settings( array $settings = [] ): int {
		if ( ! empty( $settings['context_post_id'] ) ) {
			return absint( $settings['context_post_id'] );
		}

		$post_id = get_the_ID();

		if ( $post_id ) {
			return (int) $post_id;
		}

		$queried_id = get_queried_object_id();

		return $queried_id ? (int) $queried_id : 0;
	}

	private function normalize_term_ids( $value ): array {
		if ( empty( $value ) ) {
			return [];
		}

		if ( ! is_array( $value ) ) {
			$value = [ $value ];
		}

		$ids = [];

		foreach ( $value as $item ) {
			if ( is_object( $item ) && isset( $item->term_id ) ) {
				$ids[] = (int) $item->term_id;
				continue;
			}

			if ( is_array( $item ) && isset( $item['term_id'] ) ) {
				$ids[] = (int) $item['term_id'];
				continue;
			}

			if ( is_numeric( $item ) ) {
				$ids[] = (int) $item;
			}
		}

		return array_values( array_unique( array_filter( $ids ) ) );
	}

	private function get_selected_terms( array $settings ): array {
		$categories = [];
		$tags       = [];

		if ( 'current_acf' === ( $settings['query_source'] ?? 'manual' ) ) {
			$post_id = $this->get_context_post_id_from_settings( $settings );

			if ( $post_id && function_exists( 'get_field' ) ) {
				$category_field = sanitize_key( $settings['acf_category_field'] ?? 'download_categories' );
				$tag_field      = sanitize_key( $settings['acf_tag_field'] ?? 'download_tags' );

				if ( $category_field ) {
					$categories = $this->normalize_term_ids( get_field( $category_field, $post_id ) );
				}

				if ( $tag_field ) {
					$tags = $this->normalize_term_ids( get_field( $tag_field, $post_id ) );
				}
			}
		} else {
			$categories = $this->normalize_term_ids( $settings['manual_categories'] ?? [] );
			$tags       = $this->normalize_term_ids( $settings['manual_tags'] ?? [] );
		}

		return [
			'categories' => $categories,
			'tags'       => $tags,
		];
	}

	private function build_base_tax_query( array $selected, string $relation ): array {
		$clauses = [];

		if ( ! empty( $selected['categories'] ) ) {
			$clauses[] = [
				'taxonomy' => 'dlm_download_category',
				'field'    => 'term_id',
				'terms'    => $selected['categories'],
				'operator' => 'IN',
			];
		}

		if ( ! empty( $selected['tags'] ) ) {
			$clauses[] = [
				'taxonomy' => 'dlm_download_tag',
				'field'    => 'term_id',
				'terms'    => $selected['tags'],
				'operator' => 'IN',
			];
		}

		if ( count( $clauses ) > 1 ) {
			$clauses['relation'] = 'OR' === $relation ? 'OR' : 'AND';
		}

		return $clauses;
	}

	private function get_widget_query_keys( array $settings = [] ): array {
		$suffix = '';

		if ( ! empty( $settings['_widget_id'] ) ) {
			$suffix = (string) $settings['_widget_id'];
		} elseif ( method_exists( $this, 'get_id' ) ) {
			$suffix = (string) $this->get_id();
		}

		$suffix = sanitize_key( $suffix );

		if ( '' === $suffix ) {
			$suffix = 'downloads';
		}

		return [
			'filter' => 'eco_dl_filter_' . $suffix,
			'page'   => 'eco_dl_page_' . $suffix,
		];
	}

	private function get_filter_terms( array $selected, string $mode, bool $include_all_when_empty = false ): array {
		$terms = [];

		if ( in_array( $mode, [ 'categories', 'both' ], true ) && ( ! empty( $selected['categories'] ) || $include_all_when_empty ) ) {
			$category_args = [
				'taxonomy'   => 'dlm_download_category',
				'hide_empty' => true,
				'orderby'    => 'name',
				'order'      => 'ASC',
			];

			if ( ! empty( $selected['categories'] ) ) {
				$category_args['include'] = $selected['categories'];
			}

			$category_terms = get_terms( $category_args );

			if ( ! is_wp_error( $category_terms ) ) {
				$terms = array_merge( $terms, $category_terms );
			}
		}

		if ( in_array( $mode, [ 'tags', 'both' ], true ) && ( ! empty( $selected['tags'] ) || $include_all_when_empty ) ) {
			$tag_args = [
				'taxonomy'   => 'dlm_download_tag',
				'hide_empty' => true,
				'orderby'    => 'name',
				'order'      => 'ASC',
			];

			if ( ! empty( $selected['tags'] ) ) {
				$tag_args['include'] = $selected['tags'];
			}

			$tag_terms = get_terms( $tag_args );

			if ( ! is_wp_error( $tag_terms ) ) {
				$terms = array_merge( $terms, $tag_terms );
			}
		}

		usort( $terms, static fn( $a, $b ) => strcasecmp( $a->name, $b->name ) );

		return $terms;
	}

	public function filter_downloads_with_versions( array $clauses, \WP_Query $query ): array {
		if ( ! $query->get( 'eco_require_download_version' ) ) {
			return $clauses;
		}

		global $wpdb;

		$clauses['where'] .= " AND EXISTS (
			SELECT 1
			FROM {$wpdb->posts} dlmv
			WHERE dlmv.post_parent = {$wpdb->posts}.ID
			  AND dlmv.post_type = 'dlm_download_version'
			  AND dlmv.post_status NOT IN ('trash', 'auto-draft')
		)";

		return $clauses;
	}

	private function get_download_object( int $post_id ) {
		if ( ! function_exists( 'download_monitor' ) ) {
			return null;
		}

		$downloads = download_monitor()->service( 'download_repository' )->retrieve(
			[
				'p'           => $post_id,
				'post_status' => [ 'publish' ],
			],
			1,
			0
		);

		if ( ! empty( $downloads ) && is_array( $downloads ) ) {
			$download = reset( $downloads );
			return is_object( $download ) ? $download : null;
		}

		return null;
	}

	private function get_download_url( $download ): string {
		if ( ! is_object( $download ) || ! method_exists( $download, 'get_the_download_link' ) ) {
			return '';
		}

		$url = $download->get_the_download_link();
		return is_string( $url ) ? trim( $url ) : '';
	}

	private function is_valid_download( $download ): bool {
		if ( ! is_object( $download ) ) {
			return false;
		}

		if ( method_exists( $download, 'has_version' ) && ! $download->has_version() ) {
			return false;
		}

		return '' !== $this->get_download_url( $download );
	}

	private function call_download_method( $download, string $method ): string {
		if ( is_object( $download ) && method_exists( $download, $method ) ) {
			return trim( (string) $download->{$method}() );
		}

		return '';
	}

	private function get_download_count( $download, int $post_id ) {
		if ( is_object( $download ) && method_exists( $download, 'get_download_count' ) ) {
			$count = $download->get_download_count();
			if ( '' !== $count && null !== $count ) {
				return (int) $count;
			}
		}

		$count = get_post_meta( $post_id, '_download_count', true );
		return ( '' === $count || null === $count ) ? '' : (int) $count;
	}

	private function get_file_icon_class( string $filetype, string $filename ): string {
		$ext  = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
		$type = strtolower( $filetype );

		if ( 'pdf' === $ext || str_contains( $type, 'pdf' ) ) {
			return 'is-pdf';
		}
		if ( in_array( $ext, [ 'doc', 'docx' ], true ) || str_contains( $type, 'word' ) ) {
			return 'is-doc';
		}
		if ( in_array( $ext, [ 'xls', 'xlsx', 'csv' ], true ) || str_contains( $type, 'excel' ) || str_contains( $type, 'sheet' ) ) {
			return 'is-xls';
		}
		if ( in_array( $ext, [ 'ppt', 'pptx' ], true ) || str_contains( $type, 'powerpoint' ) || str_contains( $type, 'presentation' ) ) {
			return 'is-ppt';
		}
		if ( in_array( $ext, [ 'zip', 'rar', '7z' ], true ) || str_contains( $type, 'zip' ) || str_contains( $type, 'archive' ) ) {
			return 'is-zip';
		}

		return 'is-file';
	}

	private function get_accent_color( array $settings ): string {
		$color = trim( (string) ( $settings['accent_color'] ?? '' ) );

		if ( '' === $color && ! empty( $settings['resolved_accent_color'] ) ) {
			$color = trim( (string) $settings['resolved_accent_color'] );
		}

		if ( '' === $color && function_exists( 'get_field' ) ) {
			$post_id = $this->get_context_post_id_from_settings( $settings );
			if ( $post_id ) {
				$color = trim( (string) get_field( 'color', $post_id ) );
			}
		}

		return sanitize_hex_color( $color ) ?: '#e2001a';
	}

	private function render_empty_state( string $accent_color ): void {
		?>
		<div class="eco-downloads-empty" style="--eco-downloads-accent: <?php echo esc_attr( $accent_color ); ?>;">
			<span class="eco-downloads-empty__icon" aria-hidden="true">
				<svg viewBox="0 0 24 24">
					<path d="M12 3v11"></path>
					<path d="m8 10 4 4 4-4"></path>
					<path d="M5 19h14"></path>
				</svg>
			</span>
			<div>
				<h3><?php esc_html_e( 'No downloads found', 'elementor-eco' ); ?></h3>
				<p><?php esc_html_e( 'No matching downloads are currently available for this section.', 'elementor-eco' ); ?></p>
			</div>
		</div>
		<?php
	}

	private function prepare_ajax_settings( array $settings ): array {
		$settings['context_post_id']       = $this->get_context_post_id_from_settings( $settings );
		$settings['resolved_accent_color'] = $this->get_accent_color( $settings );
		$settings['_widget_id']            = sanitize_key( $this->get_id() ?: wp_unique_id( 'downloads_' ) );

		return $settings;
	}

	private function normalize_ajax_settings( $raw ): array {
		if ( is_string( $raw ) ) {
			$decoded = json_decode( wp_unslash( $raw ), true );
			$raw     = is_array( $decoded ) ? $decoded : [];
		}

		if ( ! is_array( $raw ) ) {
			return [];
		}

		$settings = [];
		$keys = [
			'query_source', 'manual_categories', 'manual_tags', 'acf_category_field', 'acf_tag_field',
			'tax_relation', 'empty_terms_behavior', 'posts_per_page', 'orderby', 'order',
			'show_filters', 'filter_taxonomies', 'show_date', 'date_label', 'show_file_meta',
			'show_excerpt', 'show_terms', 'show_pagination', 'button_text', 'accent_color',
			'context_post_id', 'resolved_accent_color', '_widget_id',
		];

		foreach ( $keys as $key ) {
			if ( array_key_exists( $key, $raw ) ) {
				$settings[ $key ] = $raw[ $key ];
			}
		}

		$settings['manual_categories'] = $this->normalize_term_ids( $settings['manual_categories'] ?? [] );
		$settings['manual_tags']       = $this->normalize_term_ids( $settings['manual_tags'] ?? [] );
		$settings['context_post_id']   = absint( $settings['context_post_id'] ?? 0 );
		$settings['posts_per_page']    = max( 1, min( 100, absint( $settings['posts_per_page'] ?? 12 ) ) );

		return $settings;
	}

	private function get_query_state( array $settings, int $page = 1, string $active_filter = '' ): array {
		$selected     = $this->get_selected_terms( $settings );
		$has_terms    = ! empty( $selected['categories'] ) || ! empty( $selected['tags'] );
		$relation     = 'OR' === ( $settings['tax_relation'] ?? 'AND' ) ? 'OR' : 'AND';
		$order        = 'ASC' === ( $settings['order'] ?? 'DESC' ) ? 'ASC' : 'DESC';
		$orderby      = in_array( $settings['orderby'] ?? 'date', [ 'date', 'title', 'menu_order', 'rand' ], true ) ? $settings['orderby'] : 'date';
		$per_page     = max( 1, min( 100, (int) ( $settings['posts_per_page'] ?? 12 ) ) );
		$page         = max( 1, $page );
		$active_filter = sanitize_text_field( $active_filter );
		$tax_query    = $this->build_base_tax_query( $selected, $relation );

		if ( $active_filter && str_contains( $active_filter, ':' ) ) {
			[ $filter_taxonomy, $filter_term_id ] = array_pad( explode( ':', $active_filter, 2 ), 2, '' );

			if ( in_array( $filter_taxonomy, [ 'dlm_download_category', 'dlm_download_tag' ], true ) && absint( $filter_term_id ) ) {
				$tax_query = [
					[
						'taxonomy' => $filter_taxonomy,
						'field'    => 'term_id',
						'terms'    => [ absint( $filter_term_id ) ],
					],
				];
			}
		}

		$args = [
			'post_type'                    => 'dlm_download',
			'post_status'                  => 'publish',
			'posts_per_page'               => $per_page,
			'paged'                        => $page,
			'orderby'                      => $orderby,
			'order'                        => $order,
			'eco_require_download_version' => 1,
			'update_post_meta_cache'       => false,
			'update_post_term_cache'       => true,
			'ignore_sticky_posts'          => true,
		];

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		} elseif ( 'none' === ( $settings['empty_terms_behavior'] ?? 'none' ) ) {
			$args['post__in'] = [ 0 ];
		}

		return [
			'args'          => $args,
			'selected'      => $selected,
			'has_terms'     => $has_terms,
			'active_filter' => $active_filter,
		];
	}

	private function run_download_query( array $args ): \WP_Query {
		add_filter( 'posts_clauses', [ $this, 'filter_downloads_with_versions' ], 10, 2 );
		$query = new \WP_Query( $args );
		remove_filter( 'posts_clauses', [ $this, 'filter_downloads_with_versions' ], 10 );

		return $query;
	}

	private function render_download_items( \WP_Query $query, array $settings, string $accent_color, ?int &$rendered = null ): void {
		$rendered = 0;
		$button_text  = trim( (string) ( $settings['button_text'] ?? __( 'Download', 'elementor-eco' ) ) );
		$show_date    = 'yes' === ( $settings['show_date'] ?? '' );
		$show_meta    = 'yes' === ( $settings['show_file_meta'] ?? '' );
		$show_excerpt = 'yes' === ( $settings['show_excerpt'] ?? '' );
		$show_terms   = 'yes' === ( $settings['show_terms'] ?? '' );
		$date_label   = trim( (string) ( $settings['date_label'] ?? __( 'Published', 'elementor-eco' ) ) );

		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();

				$post_id  = get_the_ID();
				$download = $this->get_download_object( $post_id );

				if ( ! $this->is_valid_download( $download ) ) {
					continue;
				}

				++$rendered;

				$url          = $this->get_download_url( $download );
				$filetype     = $this->call_download_method( $download, 'get_the_filetype' );
				$filesize     = $this->call_download_method( $download, 'get_the_filesize' );
				$filename     = $this->call_download_method( $download, 'get_the_filename' );
				$icon_class   = $this->get_file_icon_class( $filetype, $filename );
				$excerpt      = get_the_excerpt();
				$category_terms = wp_get_post_terms( $post_id, 'dlm_download_category' );
				$tag_terms      = wp_get_post_terms( $post_id, 'dlm_download_tag' );
				$item_terms     = array_merge(
					is_wp_error( $category_terms ) ? [] : $category_terms,
					is_wp_error( $tag_terms ) ? [] : $tag_terms
				);
				?>
				<article class="eco-download-card">
					<div class="eco-download-card-main">
						<?php if ( $show_terms && ! empty( $item_terms ) && ! is_wp_error( $item_terms ) ) : ?>
							<div class="eco-download-card-terms">
								<?php foreach ( $item_terms as $term ) : ?>
									<span class="eco-download-card-term"><?php echo esc_html( $term->name ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<div class="eco-download-card-title-row">
							<span class="eco-download-card-file-icon <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></span>
							<h3 class="eco-download-card-title"><?php the_title(); ?></h3>
						</div>

						<?php if ( $show_date || $show_meta ) : ?>
							<div class="eco-download-card-meta">
								<?php if ( $show_date ) : ?>
									<span><?php echo esc_html( $date_label . ': ' . get_the_date() ); ?></span>
								<?php endif; ?>
								<?php if ( $show_meta && '' !== $filesize ) : ?><span><?php echo esc_html( $filesize ); ?></span><?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( $show_excerpt && $excerpt ) : ?>
							<div class="eco-download-card-excerpt"><?php echo wp_kses_post( wpautop( $excerpt ) ); ?></div>
						<?php endif; ?>
					</div>

					<div class="eco-download-card-action">
						<a class="eco-download-card-button" href="<?php echo esc_url( $url ); ?>">
							<span><?php echo esc_html( $button_text ); ?></span>
							<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v11"></path><path d="m8 10 4 4 4-4"></path><path d="M5 19h14"></path></svg>
						</a>
					</div>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>

		<?php if ( 0 === $rendered ) : ?>
			<?php $this->render_empty_state( $accent_color ); ?>
		<?php endif;
	}

	private function render_pagination( \WP_Query $query, array $settings, int $paged, string $active_filter = '' ): void {
		$show_pages = 'yes' === ( $settings['show_pagination'] ?? '' );

		if ( ! $show_pages || (int) $query->max_num_pages <= 1 ) {
			return;
		}

		$query_keys = $this->get_widget_query_keys( $settings );
		$args      = [ $query_keys['page'] => '%#%' ];

		if ( '' !== $active_filter ) {
			$args[ $query_keys['filter'] ] = $active_filter;
		}
		?>
		<nav class="eco-downloads-pagination" aria-label="<?php echo esc_attr__( 'Downloads pagination', 'elementor-eco' ); ?>">
			<?php
			echo wp_kses_post(
				paginate_links(
					[
						'base'      => esc_url_raw( add_query_arg( $args ) ),
						'format'    => '',
						'current'   => $paged,
						'total'     => (int) $query->max_num_pages,
						'prev_text' => __( 'Previous', 'elementor-eco' ),
						'next_text' => __( 'Next', 'elementor-eco' ),
						'type'      => 'list',
					]
				)
			);
			?>
		</nav>
		<?php
	}

	private function render_filters( array $filter_terms, string $active_filter, array $settings ): void {
		if ( empty( $filter_terms ) ) {
			return;
		}

		$query_keys = $this->get_widget_query_keys( $settings );
		?>
		<nav class="eco-downloads-filters" aria-label="<?php echo esc_attr__( 'Download filters', 'elementor-eco' ); ?>">
			<a class="eco-downloads-filter <?php echo '' === $active_filter ? 'is-active' : ''; ?>" href="<?php echo esc_url( remove_query_arg( [ $query_keys['filter'], $query_keys['page'] ] ) ); ?>" data-filter="" data-page="1">
				<?php esc_html_e( 'All', 'elementor-eco' ); ?>
			</a>
			<?php foreach ( $filter_terms as $term ) : ?>
				<?php $filter_value = $term->taxonomy . ':' . $term->term_id; ?>
				<a class="eco-downloads-filter <?php echo $filter_value === $active_filter ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( [ $query_keys['filter'] => $filter_value, $query_keys['page'] => 1 ] ) ); ?>" data-filter="<?php echo esc_attr( $filter_value ); ?>" data-page="1">
					<?php echo esc_html( $term->name ); ?>
				</a>
			<?php endforeach; ?>
		</nav>
		<?php
	}

	private function get_render_fragments( array $settings, int $page, string $active_filter ): array {
		$accent_color = $this->get_accent_color( $settings );
		$state        = $this->get_query_state( $settings, $page, $active_filter );
		$query        = $this->run_download_query( $state['args'] );

		ob_start();
		$rendered = 0;
		$this->render_download_items( $query, $settings, $accent_color, $rendered );
		$list_html = ob_get_clean();

		ob_start();
		if ( $rendered > 0 ) {
			$this->render_pagination( $query, $settings, $page, $state['active_filter'] );
		}
		$pagination_html = ob_get_clean();

		return [
			'list_html'       => $list_html,
			'pagination_html' => $pagination_html,
			'page'            => $page,
			'max_pages'       => (int) $query->max_num_pages,
			'active_filter'   => $state['active_filter'],
		];
	}

	public static function ajax_render(): void {
		$instance = new self();
		$settings = $instance->normalize_ajax_settings( $_POST['settings'] ?? [] );
		$page     = isset( $_POST['page'] ) ? max( 1, absint( $_POST['page'] ) ) : 1;
		$filter   = isset( $_POST['filter'] ) ? sanitize_text_field( wp_unslash( $_POST['filter'] ) ) : '';

		wp_send_json_success( $instance->get_render_fragments( $settings, $page, $filter ) );
	}

	protected function render(): void {
		$settings     = $this->prepare_ajax_settings( $this->get_settings_for_display() );
		$title        = trim( (string) ( $settings['title'] ?? '' ) );
		$intro        = trim( (string) ( $settings['intro'] ?? '' ) );
		$selected     = $this->get_selected_terms( $settings );
		$has_terms    = ! empty( $selected['categories'] ) || ! empty( $selected['tags'] );
		$accent_color = $this->get_accent_color( $settings );
		$query_keys   = $this->get_widget_query_keys( $settings );
		$show_filters = 'yes' === ( $settings['show_filters'] ?? '' );
		$paged        = isset( $_GET[ $query_keys['page'] ] ) ? max( 1, (int) $_GET[ $query_keys['page'] ] ) : 1;
		$active_filter = isset( $_GET[ $query_keys['filter'] ] ) ? sanitize_text_field( wp_unslash( $_GET[ $query_keys['filter'] ] ) ) : '';
		$state        = $this->get_query_state( $settings, $paged, $active_filter );
		$query        = $this->run_download_query( $state['args'] );
		$filter_terms = $show_filters && ( $has_terms || 'all' === ( $settings['empty_terms_behavior'] ?? 'none' ) )
			? $this->get_filter_terms( $selected, $settings['filter_taxonomies'] ?? 'both', ! $has_terms )
			: [];
		?>
		<div
			class="eco-downloads-widget"
			style="--eco-downloads-accent: <?php echo esc_attr( $accent_color ); ?>;"
			data-settings="<?php echo esc_attr( wp_json_encode( $settings ) ); ?>"
			data-page="<?php echo esc_attr( $paged ); ?>"
			data-filter="<?php echo esc_attr( $state['active_filter'] ); ?>"
		>
			<?php if ( '' !== $title || '' !== $intro ) : ?>
				<header class="eco-downloads-head">
					<?php if ( '' !== $title ) : ?>
						<h2 class="eco-downloads-title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $intro ) : ?>
						<div class="eco-downloads-intro"><?php echo wp_kses_post( wpautop( $intro ) ); ?></div>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<?php $this->render_filters( $filter_terms, $state['active_filter'], $settings ); ?>

			<div class="eco-downloads-results" aria-live="polite">
				<div class="eco-downloads-list">
					<?php
					$rendered = 0;
					$this->render_download_items( $query, $settings, $accent_color, $rendered );
					?>
				</div>

				<?php if ( $rendered > 0 ) : ?>
					<?php $this->render_pagination( $query, $settings, $paged, $state['active_filter'] ); ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
