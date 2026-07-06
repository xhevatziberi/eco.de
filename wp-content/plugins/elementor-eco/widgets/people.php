<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class People extends Widget_Base {

	public function get_name() {
		return 'people-list';
	}

	public function get_title() {
		return __( 'People', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-user-circle';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_script_depends() {
		return [ 'eco-people-script' ];
	}

	public function get_style_depends() {
		return [ 'eco-people-style' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'people',
			[
				'label' => __( 'Select People', 'elementor-eco' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => $this->get_people_options(),
				'default' => [],
			]
		);

		// --- NEW: ACF fallback (backward compatible) ---
		$this->add_control(
			'use_acf_fallback',
			[
				'label' => __( 'Auto-select from ACF when manual list is empty', 'elementor-eco' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'elementor-eco' ),
				'label_off' => __( 'No', 'elementor-eco' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'acf_field_name',
			[
				'label' => __( 'ACF field name', 'elementor-eco' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'responsible_persons',
				'placeholder' => 'responsible_persons',
				'condition' => [
					'use_acf_fallback' => 'yes',
				],
			]
		);

		$this->add_control(
			'acf_fallback_notice',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( '<strong>Note:</strong> Manual selection has priority. ACF is only used if no people are selected above.', 'elementor-eco' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => [
					'use_acf_fallback' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function get_people_options() {
		$options = [];

		$query = new \WP_Query([
			'post_type' => 'people',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		]);

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$options[get_the_ID()] = get_the_title();
			}
			wp_reset_postdata();
		}

		return $options;
	}

	/**
	 * NEW helper: Resolve people IDs from an ACF Post Object (multiple) field.
	 * Supports return values of:
	 * - array of WP_Post objects
	 * - array of IDs
	 * - single WP_Post
	 * - single ID
	 */
	protected function get_people_ids_from_acf_field(string $field_name, $context = null): array {
		if (!function_exists('get_field') || empty($field_name)) return [];

		$value = ($context !== null && $context !== '')
			? get_field($field_name, $context)
			: get_field($field_name);

		if (empty($value)) return [];

		$ids = [];

		if (is_array($value)) {
			foreach ($value as $item) {
				if (is_object($item) && isset($item->ID)) $ids[] = (int) $item->ID;
				elseif (is_numeric($item)) $ids[] = (int) $item;
			}
		} else {
			if (is_object($value) && isset($value->ID)) $ids[] = (int) $value->ID;
			elseif (is_numeric($value)) $ids[] = (int) $value;
		}

		return array_values(array_unique(array_filter($ids)));
	}


	protected function render() {
		$settings = $this->get_settings_for_display();

		// Manual selection (default behavior)
		$ids = isset($settings['people']) ? (array) $settings['people'] : [];
		$ids = array_values(array_filter(array_map('intval', $ids)));

		// If manual is empty AND fallback enabled → pull from ACF
		if (empty($ids) && !empty($settings['use_acf_fallback']) && $settings['use_acf_fallback'] === 'yes') {
			$field_name = isset($settings['acf_field_name']) ? trim((string) $settings['acf_field_name']) : 'responsible_persons';

			$qo = get_queried_object();
			$context = null;

			// ✅ If we are on a category/term archive, ACF stores values on "taxonomy_termId"
			if ($qo instanceof \WP_Term) {
				$context = $qo->taxonomy . '_' . $qo->term_id; // e.g. category_12
			} else {
				// Otherwise: try current post
				$post_id = (int) get_the_ID();
				if ($post_id > 0) {
					$context = $post_id;
				}
			}

			$acf_ids = $this->get_people_ids_from_acf_field($field_name, $context);




			if (!empty($acf_ids)) {
				$ids = $acf_ids;
			}
		}

		$args = [
			'post_type' => 'people',
			'posts_per_page' => -1,
			'post__in' => !empty($ids) ? $ids : [0],
			'orderby' => 'post__in'
		];

		$query = new \WP_Query($args);

		if ($query->have_posts()) :
			?>
			<div class="eco-people-list">
				<?php while ($query->have_posts()) : $query->the_post();

					$name = get_field('name') ? get_field('name') : get_the_title();
					$company = get_field('company');
					$position = get_field('position');
					$address = get_field('address');
					$phone = get_field('phone');
					$email = get_field('email');
					$biography = get_field('biography');
					$social = get_field('social_media');
					$photo = get_the_post_thumbnail_url(get_the_ID(), 'medium');

					$social_facebook = !empty($social['facebook']) ? esc_url($social['facebook']) : '';
					$social_twitter = !empty($social['twitter']) ? esc_url($social['twitter']) : '';
					$social_linkedin = !empty($social['linkedin']) ? esc_url($social['linkedin']) : '';
					$social_xing = !empty($social['xing']) ? esc_url($social['xing']) : '';

					?>
					<div class="eco-person">
						<div class="eco-person-photo">
							<?php if ($photo) : ?>
								<img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($name); ?>" />
							<?php endif; ?>
						</div>
						<div class="eco-person-content">
							<h3><?php echo esc_html($name); ?></h3>
							<?php if ($position) : ?>
								<p><strong><?php echo wp_kses_post((string) $position); ?></strong></p>
							<?php endif; ?>
							<?php if ($company) : ?>
								<p><?php echo esc_html($company); ?></p>
							<?php endif; ?>

							<?php if ($address || $phone || $email || $social || $biography) : ?>
								<p>
									<a href="#"
										class="eco-biography-link"
										data-name="<?php echo esc_attr($name); ?>"
										data-position="<?php echo esc_attr((string) $position); ?>"
										data-company="<?php echo esc_attr((string) $company); ?>"
										data-address="<?php echo esc_attr((string) $address); ?>"
										data-phone="<?php echo esc_attr((string) $phone); ?>"
										data-email="<?php echo esc_attr((string) $email); ?>"
										data-facebook="<?php echo esc_attr($social_facebook); ?>"
										data-twitter="<?php echo esc_attr($social_twitter); ?>"
										data-linkedin="<?php echo esc_attr($social_linkedin); ?>"
										data-xing="<?php echo esc_attr($social_xing); ?>"
										data-biography="<?php echo esc_attr(wp_strip_all_tags((string) $biography)); ?>">
										<i class="fas fa-info-circle"></i> <?php _e('More Information', 'elementor-eco'); ?>
									</a>
								</p>
							<?php endif; ?>
						</div>
					</div>

				<?php endwhile; ?>
			</div>

			<!-- Modal -->
			<div id="eco-biography-modal" class="eco-modal">
				<div class="eco-modal-content">
					<span class="eco-modal-close">&times;</span>
					<h3 class="eco-modal-title"></h3>
					<div class="eco-modal-body"></div>
				</div>
			</div>
			<?php
			wp_reset_postdata();
		endif;
	}

	protected function content_template() {
		?>
		<div class="eco-people-list">
			<# if ( settings.people && settings.people.length ) { #>
				<# _.each( settings.people, function( person ) { #>
					<div class="eco-person">
						<div class="eco-person-photo">
							<# if ( person.photo ) { #>
								<img src="{{ person.photo }}" alt="{{ person.name }}" />
							<# } #>
						</div>
						<div class="eco-person-content">
							<h3>{{ person.name }}</h3>
							<# if ( person.position ) { #>
								<p><strong>{{ person.position }}</strong></p>
							<# } #>
							<# if ( person.company ) { #>
								<p>{{ person.company }}</p>
							<# } #>
							<# if ( person.address || person.phone || person.email || person.social || person.biography ) { #>
								<p>
									<a href="#"
										class="eco-biography-link"
										data-name="{{ person.name }}"
										data-position="{{ person.position }}"
										data-company="{{ person.company }}"
										data-address="{{ person.address }}"
										data-phone="{{ person.phone }}"
										data-email="{{ person.email }}"
										data-facebook="{{ person.social.facebook }}"
										data-twitter="{{ person.social.twitter }}"
										data-linkedin="{{ person.social.linkedin }}"
										data-xing="{{ person.social.xing }}"
										data-biography="{{ person.biography }}">
										<i class="fas fa-info-circle"></i> <?php _e('More information', 'elementor-eco'); ?>
									</a>
								</p>
							<# } #>
						</div>
					</div>
				<# } ); #>
			<# } #>
		</div>
		<div id="eco-biography-modal" class="eco-modal">
			<div class="eco-modal-content">
				<span class="eco-modal-close">&times;</span>
				<h3 class="eco-modal-title"></h3>
				<div class="eco-modal-body"></div>
			</div>
		</div>
		<?php
	}

	public function render_people_by_ids($ids = []) {
		if (empty($ids)) return;

		$args = [
			'post_type' => 'people',
			'post__in' => $ids,
			'orderby' => 'post__in',
			'posts_per_page' => -1,
		];

		$query = new \WP_Query($args);

		if ($query->have_posts()) :
			ob_start();
			?>
			<div class="eco-people-list">
				<?php while ($query->have_posts()) : $query->the_post();

					$name = get_field('name') ?: get_the_title();
					$company = get_field('company');
					$position = get_field('position');
					$address = get_field('address');
					$phone = get_field('phone');
					$email = get_field('email');
					$biography = get_field('biography');
					$social = get_field('social_media');
					$photo = get_the_post_thumbnail_url(get_the_ID(), 'medium');

					$social_facebook = !empty($social['facebook']) ? esc_url($social['facebook']) : '';
					$social_twitter  = !empty($social['twitter']) ? esc_url($social['twitter']) : '';
					$social_linkedin = !empty($social['linkedin']) ? esc_url($social['linkedin']) : '';
					$social_xing     = !empty($social['xing']) ? esc_url($social['xing']) : '';
					?>
					<div class="eco-person">
						<div class="eco-person-photo">
							<?php if ($photo): ?>
								<img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($name); ?>" />
							<?php endif; ?>
						</div>
						<div class="eco-person-content">
							<h3><?php echo esc_html($name); ?></h3>
							<?php if ($position): ?>
								<p><strong><?php echo wp_kses_post((string) $position); ?></strong></p>
							<?php endif; ?>
							<?php if ($company): ?>
								<p><?php echo esc_html($company); ?></p>
							<?php endif; ?>
							<?php if ($address || $phone || $email || $social || $biography): ?>
								<p>
									<a href="#"
									class="eco-biography-link"
									data-name="<?php echo esc_attr($name); ?>"
									data-position="<?php echo esc_attr((string) $position); ?>"
									data-company="<?php echo esc_attr((string) $company); ?>"
									data-address="<?php echo esc_attr((string) $address); ?>"
									data-phone="<?php echo esc_attr((string) $phone); ?>"
									data-email="<?php echo esc_attr((string) $email); ?>"
									data-facebook="<?php echo esc_attr($social_facebook); ?>"
									data-twitter="<?php echo esc_attr($social_twitter); ?>"
									data-linkedin="<?php echo esc_attr($social_linkedin); ?>"
									data-xing="<?php echo esc_attr($social_xing); ?>"
									data-biography="<?php echo esc_attr(wp_strip_all_tags((string) $biography)); ?>">
										<i class="fas fa-info-circle"></i> <?php _e('More Information', 'elementor-eco'); ?>
									</a>
								</p>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
			<!-- Modal -->
			<div id="eco-biography-modal" class="eco-modal">
				<div class="eco-modal-content">
					<span class="eco-modal-close">&times;</span>
					<h3 class="eco-modal-title"></h3>
					<div class="eco-modal-body"></div>
				</div>
			</div>
			<?php
			wp_reset_postdata();
			return ob_get_clean();
		endif;
	}

}
