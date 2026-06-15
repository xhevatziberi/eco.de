<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Downloads extends Widget_Base {

	public function get_name() {
		return 'eco-downloads';
	}

	public function get_title() {
		return __( 'Downloads', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-download-kit';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_style_depends() {
		return [ 'eco-downloads-style' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'elementor-eco' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Downloads', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'intro',
			[
				'label' => __( 'Intro Text', 'elementor-eco' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 4,
				'default' => '',
			]
		);

		$this->add_control(
			'show_filters',
			[
				'label' => __( 'Show Filter Bar', 'elementor-eco' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'elementor-eco' ),
				'label_off' => __( 'No', 'elementor-eco' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'allowed_categories',
			[
				'label' => __( 'Allowed Category Slugs', 'elementor-eco' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'description' => __( 'Comma-separated. Example: reports,studies,whitepapers', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => __( 'Downloads Per Page', 'elementor-eco' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 24,
				'min' => 1,
				'step' => 1,
			]
		);

		$this->add_control(
			'show_date',
			[
				'label' => __( 'Show Date', 'elementor-eco' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'elementor-eco' ),
				'label_off' => __( 'No', 'elementor-eco' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'date_label',
			[
				'label' => __( 'Date Label', 'elementor-eco' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Published', 'elementor-eco' ),
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_file_meta',
			[
				'label' => __( 'Show File Meta', 'elementor-eco' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'elementor-eco' ),
				'label_off' => __( 'No', 'elementor-eco' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => __( 'Shows filename, filetype, filesize and download count when available.', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => __( 'Order By', 'elementor-eco' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'title'      => __( 'Title', 'elementor-eco' ),
					'date'       => __( 'Date', 'elementor-eco' ),
					'menu_order' => __( 'Menu Order', 'elementor-eco' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __( 'Order', 'elementor-eco' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => [
					'ASC'  => __( 'ASC', 'elementor-eco' ),
					'DESC' => __( 'DESC', 'elementor-eco' ),
				],
			]
		);

		$this->end_controls_section();
	}

	protected function eco_parse_csv_slugs($value) {
		if (empty($value)) {
			return [];
		}

		$parts = array_map('trim', explode(',', (string) $value));
		$parts = array_map('sanitize_title', $parts);
		$parts = array_filter($parts);

		return array_values(array_unique($parts));
	}

	public function eco_filter_downloads_with_versions($clauses, $query) {
		if (!$query->get('eco_require_download_version')) {
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

	protected function eco_get_download_object($post_id) {
		if (!function_exists('download_monitor')) {
			return null;
		}

		$downloads = download_monitor()->service('download_repository')->retrieve([
			'p' => $post_id,
			'post_status' => [ 'publish' ],
		], 1, 0);

		if (!empty($downloads) && is_array($downloads)) {
			$download = reset($downloads);
			if (is_object($download)) {
				return $download;
			}
		}

		return null;
	}

	protected function eco_get_actual_download_url($download) {
		if (!$download || !is_object($download)) {
			return '';
		}

		if (method_exists($download, 'get_the_download_link')) {
			$url = $download->get_the_download_link();
			return is_string($url) ? trim($url) : '';
		}

		return '';
	}

	protected function eco_is_valid_download($download) {
		if (!$download || !is_object($download)) {
			return false;
		}

		if (method_exists($download, 'has_version') && !$download->has_version()) {
			return false;
		}

		$url = $this->eco_get_actual_download_url($download);
		if ($url === '') {
			return false;
		}

		if (method_exists($download, 'get_the_filename')) {
			$filename = trim((string) $download->get_the_filename());
			if ($filename === '') {
				return false;
			}
		}

		return true;
	}

	protected function eco_get_filetype($download) {
		if ($download && method_exists($download, 'get_the_filetype')) {
			return trim((string) $download->get_the_filetype());
		}
		return '';
	}

	protected function eco_get_filesize($download) {
		if ($download && method_exists($download, 'get_the_filesize')) {
			return trim((string) $download->get_the_filesize());
		}
		return '';
	}

	protected function eco_get_filename($download) {
		if ($download && method_exists($download, 'get_the_filename')) {
			return trim((string) $download->get_the_filename());
		}
		return '';
	}

	protected function eco_get_download_count($download, $post_id) {
		if ($download && method_exists($download, 'get_download_count')) {
			$count = $download->get_download_count();
			if ($count !== null && $count !== '') {
				return (int) $count;
			}
		}

		$count = get_post_meta($post_id, '_download_count', true);
		if ($count === '' || $count === null) {
			return '';
		}

		return (int) $count;
	}

	protected function eco_get_file_icon_class($filetype, $filename) {
		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		$type = strtolower((string) $filetype);

		if ($ext === 'pdf' || strpos($type, 'pdf') !== false) {
			return 'is-pdf';
		}
		if (in_array($ext, ['doc', 'docx'], true) || strpos($type, 'word') !== false) {
			return 'is-doc';
		}
		if (in_array($ext, ['xls', 'xlsx', 'csv'], true) || strpos($type, 'excel') !== false || strpos($type, 'sheet') !== false) {
			return 'is-xls';
		}
		if (in_array($ext, ['ppt', 'pptx'], true) || strpos($type, 'powerpoint') !== false || strpos($type, 'presentation') !== false) {
			return 'is-ppt';
		}
		if (in_array($ext, ['zip', 'rar', '7z'], true) || strpos($type, 'zip') !== false || strpos($type, 'archive') !== false) {
			return 'is-zip';
		}

		return 'is-file';
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$title            = isset($settings['title']) ? trim((string) $settings['title']) : '';
		$intro            = isset($settings['intro']) ? trim((string) $settings['intro']) : '';
		$show_filters     = !empty($settings['show_filters']) && $settings['show_filters'] === 'yes';
		$allowed_slugs    = $this->eco_parse_csv_slugs($settings['allowed_categories'] ?? '');
		$posts_per_page   = !empty($settings['posts_per_page']) ? max(1, intval($settings['posts_per_page'])) : 24;
		$show_date        = !empty($settings['show_date']) && $settings['show_date'] === 'yes';
		$date_label       = !empty($settings['date_label']) ? trim((string) $settings['date_label']) : __('Published', 'elementor-eco');
		$show_file_meta   = !empty($settings['show_file_meta']) && $settings['show_file_meta'] === 'yes';
		$orderby          = !empty($settings['orderby']) ? $settings['orderby'] : 'date';
		$order            = !empty($settings['order']) ? $settings['order'] : 'DESC';

		$active_category = isset($_GET['download_cat']) ? sanitize_title(wp_unslash($_GET['download_cat'])) : '';
		$paged = isset($_GET['eco_downloads_page']) ? max(1, intval($_GET['eco_downloads_page'])) : 1;

		$all_terms = get_terms([
			'taxonomy'   => 'dlm_download_category',
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'ASC',
		]);

		if (is_wp_error($all_terms)) {
			$all_terms = [];
		}

		$terms = [];

		if (!empty($allowed_slugs)) {
			foreach ($all_terms as $term) {
				if (in_array($term->slug, $allowed_slugs, true)) {
					$terms[] = $term;
				}
			}

			if ($active_category && !in_array($active_category, $allowed_slugs, true)) {
				$active_category = '';
			}

			if (count($allowed_slugs) === 1) {
				$active_category = $allowed_slugs[0];
				$show_filters = false;
			}
		} else {
			$terms = $all_terms;
		}

		$args = [
			'post_type'              => 'dlm_download',
			'post_status'            => 'publish',
			'posts_per_page'         => $posts_per_page,
			'paged'                  => $paged,
			'orderby'                => $orderby,
			'order'                  => $order,
			'eco_require_download_version' => 1,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => true,
		];

		if (!empty($active_category)) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'dlm_download_category',
					'field'    => 'slug',
					'terms'    => $active_category,
				]
			];
		} elseif (!empty($allowed_slugs)) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'dlm_download_category',
					'field'    => 'slug',
					'terms'    => $allowed_slugs,
					'operator' => 'IN',
				]
			];
		}

		add_filter('posts_clauses', [ $this, 'eco_filter_downloads_with_versions' ], 10, 2);
		$query = new \WP_Query($args);
		remove_filter('posts_clauses', [ $this, 'eco_filter_downloads_with_versions' ], 10);

		?>
		<div class="eco-downloads-widget">

			<?php if ($title !== '' || $intro !== '') : ?>
				<div class="eco-downloads-head">
					<?php if ($title !== '') : ?>
						<h2 class="eco-downloads-title"><?php echo esc_html($title); ?></h2>
					<?php endif; ?>

					<?php if ($intro !== '') : ?>
						<div class="eco-downloads-intro">
							<?php echo wp_kses_post(wpautop($intro)); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ($show_filters && !empty($terms)) : ?>
				<div class="eco-downloads-filters" aria-label="<?php echo esc_attr__('Download categories', 'elementor-eco'); ?>">
					<a class="eco-downloads-filter <?php echo $active_category === '' ? 'is-active' : ''; ?>"
					   href="<?php echo esc_url(remove_query_arg(['download_cat', 'eco_downloads_page'])); ?>">
						<?php esc_html_e('All', 'elementor-eco'); ?>
					</a>

					<?php foreach ($terms as $term) : ?>
						<a class="eco-downloads-filter <?php echo $active_category === $term->slug ? 'is-active' : ''; ?>"
						   href="<?php echo esc_url(add_query_arg([
							   'download_cat' => $term->slug,
							   'eco_downloads_page' => 1,
						   ])); ?>">
							<?php echo esc_html($term->name); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="eco-downloads-list">
				<?php if ($query->have_posts()) : ?>
					<?php while ($query->have_posts()) : $query->the_post(); ?>
						<?php
						$post_id      = get_the_ID();
						$excerpt      = get_the_excerpt();
						$item_terms   = get_the_terms($post_id, 'dlm_download_category');
						$download     = $this->eco_get_download_object($post_id);

						if (!$this->eco_is_valid_download($download)) {
							continue;
						}

						$download_url = $this->eco_get_actual_download_url($download);
						$filetype     = $this->eco_get_filetype($download);
						$filesize     = $this->eco_get_filesize($download);
						$filename     = $this->eco_get_filename($download);
						$download_cnt = $this->eco_get_download_count($download, $post_id);
						$icon_class   = $this->eco_get_file_icon_class($filetype, $filename);
						?>
						<article class="eco-download-card">
							<div class="eco-download-card-main">

								<?php if (!empty($item_terms) && !is_wp_error($item_terms)) : ?>
									<div class="eco-download-card-terms">
										<?php foreach ($item_terms as $term) : ?>
											<span class="eco-download-card-term"><?php echo esc_html($term->name); ?></span>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>

								<div class="eco-download-card-title-row">
									<span class="eco-download-card-file-icon <?php echo esc_attr($icon_class); ?>" aria-hidden="true"></span>
									<h4 class="eco-download-card-title"><?php the_title(); ?></h4>
								</div>

								<?php if ($show_date || $show_file_meta) : ?>
									<div class="eco-download-card-meta">
										<?php if ($show_date) : ?>
											<span class="eco-download-card-meta-item">
												<span class="eco-download-card-meta-text">
													<?php echo esc_html($date_label . ': ' . get_the_date()); ?>
												</span>
											</span>
										<?php endif; ?>

										<?php if ($show_file_meta && $filename !== '') : ?>
											<span class="eco-download-card-meta-item">
												<span class="eco-download-card-meta-text"><?php echo esc_html($filename); ?></span>
											</span>
										<?php endif; ?>

										<?php if ($show_file_meta && $filetype !== '') : ?>
											<span class="eco-download-card-meta-item">
												<span class="eco-download-card-meta-text"><?php echo esc_html($filetype); ?></span>
											</span>
										<?php endif; ?>

										<?php if ($show_file_meta && $filesize !== '') : ?>
											<span class="eco-download-card-meta-item">
												<span class="eco-download-card-meta-text"><?php echo esc_html($filesize); ?></span>
											</span>
										<?php endif; ?>

										<?php if ($show_file_meta && $download_cnt !== '') : ?>
											<span class="eco-download-card-meta-item">
												<span class="eco-download-card-meta-text">
													<?php
													echo esc_html(sprintf(
														_n('%s download', '%s downloads', $download_cnt, 'elementor-eco'),
														number_format_i18n($download_cnt)
													));
													?>
												</span>
											</span>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<?php if ($excerpt) : ?>
									<div class="eco-download-card-excerpt">
										<?php echo wp_kses_post(wpautop($excerpt)); ?>
									</div>
								<?php endif; ?>
							</div>

							<div class="eco-download-card-action">
								<a class="eco-download-card-button" href="<?php echo esc_url($download_url); ?>">
									<?php esc_html_e('Download', 'elementor-eco'); ?>
								</a>
							</div>
						</article>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				<?php else : ?>
					<div class="eco-downloads-empty">
						<?php esc_html_e('No downloads found.', 'elementor-eco'); ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ((int) $query->max_num_pages > 1) : ?>
				<div class="eco-downloads-pagination">
					<?php
					echo paginate_links([
						'base'      => esc_url(add_query_arg('eco_downloads_page', '%#%')),
						'format'    => '',
						'current'   => $paged,
						'total'     => (int) $query->max_num_pages,
						'prev_text' => __('« Prev', 'elementor-eco'),
						'next_text' => __('Next »', 'elementor-eco'),
						'type'      => 'list',
					]);
					?>
				</div>
			<?php endif; ?>

		</div>
		<?php
	}
}