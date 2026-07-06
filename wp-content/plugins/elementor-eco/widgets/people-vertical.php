<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) exit;

class PeopleVertical extends Widget_Base {

	public function get_name() {
		return 'people-vertical';
	}

	public function get_title() {
		return __( 'People vertical', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_script_depends() {
		return [ 'eco-people-vertical-script' ];
	}

	public function get_style_depends() {
		return [ 'eco-people-vertical-style' ];
	}

	protected function register_controls() {
		$this->start_controls_section('section_content', [
			'label' => __( 'Content', 'elementor-eco' ),
		]);

		$this->add_control('info_display', [
			'label' => __( 'Info display', 'elementor-eco' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'modal',
			'options' => [
				'modal'  => __( 'Popup / Modal', 'elementor-eco' ),
				'expand' => __( 'Click to expand', 'elementor-eco' ),
			],
		]);

		$this->add_responsive_control('columns', [
			'label' => __( 'Columns', 'elementor-eco' ),
			'type' => Controls_Manager::NUMBER,
			'min' => 1,
			'max' => 6,
			'step' => 1,
			'default' => 2,
			'tablet_default' => 2,
			'mobile_default' => 1,
			'selectors' => [
				'{{WRAPPER}} .eco-people-vertical' => '--eco-pv-cols: {{VALUE}};',
			],
		]);

		$this->add_control('info_label', [
			'label' => __( 'Info button label', 'elementor-eco' ),
			'type' => Controls_Manager::TEXT,
			'default' => __( 'Mehr anzeigen', 'elementor-eco' ),
		]);

		$repeater = new Repeater();

		$repeater->add_control('person_id', [
			'label' => __( 'Person', 'elementor-eco' ),
			'type' => Controls_Manager::SELECT2,
			'label_block' => true,
			'multiple' => false,
			'options' => $this->get_people_options(),
		]);

		$this->add_control('items', [
			'label' => __( 'People', 'elementor-eco' ),
			'type' => Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			// show the selected ID; if you want a name here we can do it with a small JS in editor, but keep it simple
			'title_field' => __( 'Person ID:', 'elementor-eco' ) . ' {{{ person_id }}}',
			'default' => [],
		]);

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

    protected function format_biography($bio): string {
        $bio = (string) $bio;
        if ($bio === '') return '';

        // If it already contains common block-level tags, assume it's well-formatted HTML
        if (preg_match('~<(p|ul|ol|li|div|br|h[1-6])\b~i', $bio)) {
            return wp_kses_post($bio);
        }

        // Otherwise, convert newlines to paragraphs (handles bullets/newlines gracefully)
        return wp_kses_post(wpautop($bio));
    }


	protected function render() {
		$settings = $this->get_settings_for_display();

		$cols = isset($settings['columns']) ? max(1, min(6, (int) $settings['columns'])) : 2;

		$items = isset($settings['items']) ? (array) $settings['items'] : [];
		if (empty($items)) return;

		$info_display = !empty($settings['info_display']) ? $settings['info_display'] : 'modal';
		$info_label   = !empty($settings['info_label']) ? $settings['info_label'] : __('Show more', 'elementor-eco');

		$ids = [];
		foreach ($items as $it) {
			if (!empty($it['person_id'])) $ids[] = (int) $it['person_id'];
		}
		$ids = array_values(array_filter(array_unique($ids)));
		if (empty($ids)) return;

		$q = new \WP_Query([
			'post_type' => 'people',
			'posts_per_page' => -1,
			'post__in' => $ids,
			'orderby' => 'post__in',
		]);

		$by_id = [];
		if ($q->have_posts()) {
			while ($q->have_posts()) {
				$q->the_post();
				$by_id[get_the_ID()] = get_post();
			}
			wp_reset_postdata();
		}

		$widget_id = 'eco-people-vertical-' . $this->get_id();
		?>
		<div id="<?php echo esc_attr($widget_id); ?>"
		     class="eco-people-vertical"
		     data-cols="<?php echo esc_attr($cols); ?>"
		     data-info-display="<?php echo esc_attr($info_display); ?>">
			<?php
			foreach ($items as $it) {
				$pid = !empty($it['person_id']) ? (int) $it['person_id'] : 0;
				if (!$pid || empty($by_id[$pid])) continue;

				$post_obj = $by_id[$pid];
				setup_postdata($post_obj);

				$name     = get_field('name', $pid) ?: get_the_title($pid);
				$company  = get_field('company', $pid);
				$position = get_field('position', $pid);
				$address  = get_field('address', $pid);
				$phone    = get_field('phone', $pid);
				$email    = get_field('email', $pid);
				$bio      = get_field('biography', $pid);
				$social   = get_field('social_media', $pid);
				$photo    = get_the_post_thumbnail_url($pid, 'large');

				$facebook = !empty($social['facebook']) ? esc_url($social['facebook']) : '';
				$twitter  = !empty($social['twitter']) ? esc_url($social['twitter']) : '';
				$linkedin = !empty($social['linkedin']) ? esc_url($social['linkedin']) : '';
				$xing     = !empty($social['xing']) ? esc_url($social['xing']) : '';

				$has_info = ($address || $phone || $email || $bio || $facebook || $twitter || $linkedin || $xing);
				?>
				<article class="eco-pv-item">
					<div class="eco-pv-left">
						<?php if ($photo): ?>
							<img class="eco-pv-photo" src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($name); ?>">
						<?php endif; ?>
					</div>

					<div class="eco-pv-right">
						<h5 class="eco-pv-name"><?php echo esc_html($name); ?></h5>

						<?php if ($position): ?>
							<div class="eco-pv-position"><?php echo wp_kses_post((string) $position); ?></div>
						<?php endif; ?>

						<?php if ($company): ?>
							<div class="eco-pv-company"><?php echo esc_html($company); ?></div>
						<?php endif; ?>

						<?php if ($has_info): ?>
							<button type="button" class="eco-pv-toggle" aria-expanded="false">
								<?php echo esc_html($info_label); ?>
							</button>

							<div class="eco-pv-info" hidden>
								<?php if ($address): ?>
									<div class="eco-pv-meta"><strong><?php _e('Address', 'elementor-eco'); ?>:</strong> <?php echo wp_kses_post((string) $address); ?></div>
								<?php endif; ?>

								<?php if ($phone): ?>
									<div class="eco-pv-meta"><strong><?php _e('Phone', 'elementor-eco'); ?>:</strong> <?php echo esc_html($phone); ?></div>
								<?php endif; ?>

								<?php if ($email): ?>
									<div class="eco-pv-meta"><strong><?php _e('Email', 'elementor-eco'); ?>:</strong> <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></div>
								<?php endif; ?>

								<?php if ($facebook || $twitter || $linkedin || $xing): ?>
									<div class="eco-pv-social">
										<?php if ($linkedin): ?><a href="<?php echo esc_url($linkedin); ?>" target="_blank" rel="noopener">LinkedIn</a><?php endif; ?>
										<?php if ($xing): ?><a href="<?php echo esc_url($xing); ?>" target="_blank" rel="noopener">Xing</a><?php endif; ?>
										<?php if ($twitter): ?><a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener">X</a><?php endif; ?>
										<?php if ($facebook): ?><a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
									</div>
								<?php endif; ?>

								<?php if ($bio): ?>
									<div class="eco-pv-bio">
										<?php echo $this->format_biography($bio); ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</article>
				<?php
			}
			wp_reset_postdata();
			?>
		</div>

		<?php if ($info_display === 'modal'): ?>
			<div class="eco-pv-modal" aria-hidden="true" hidden>
				<div class="eco-pv-modal__backdrop"></div>
				<div class="eco-pv-modal__dialog" role="dialog" aria-modal="true">
					<button type="button" class="eco-pv-modal__close" aria-label="<?php esc_attr_e('Close', 'elementor-eco'); ?>">×</button>
					<div class="eco-pv-modal__content"></div>
				</div>
			</div>
		<?php endif; ?>
		<?php
	}
}
