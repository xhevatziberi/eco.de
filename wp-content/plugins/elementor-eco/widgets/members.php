<?php
// plugins/elementor-eco/widgets/members.php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Members extends Widget_Base {

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
		return 'members';
	}

	public function get_title() {
		return __( 'Members', 'elementor-eco' );
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
		return [ 'eco-members-script' ];
	}

	public function get_style_depends() {
		return [ 'eco-members-style' ];
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
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		// Get all Members
		$args = [
			'post_type' => 'member',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		];
		$query = new \WP_Query($args);

		// Prepare member data
		$members = [];
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();

				$title = get_the_title();
				$first_letter = strtoupper(substr($title, 0, 1));
				if (!preg_match('/[A-Z]/i', $first_letter)) {
					$first_letter = '#';
				}

				$members[] = [
					'id' => get_the_ID(),
					'title' => $title,
					'first_letter' => $first_letter,
					'logo' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
					'website' => get_field('website'),
					'email' => get_field('email'),
					'phone' => get_field('phone'),
					'fax_number' => get_field('fax_number'),
					'line_1' => get_field('line_1'),
					'line_2' => get_field('line_2'),
					'line_3' => get_field('line_3'),
					'zip_code' => get_field('zip_code'),
					'city' => get_field('city'),
					'country' => get_field('country'),
					'description' => get_field('description'),
				];


			}
			wp_reset_postdata();
		}

		// Collect all used letters
		$letters = array_unique(array_column($members, 'first_letter'));
		sort($letters);
		?>

		<div class="eco-member-list">
			<h2><?php echo esc_html($settings['title']); ?></h2>

			<div class="member-filter">
				<?php
				// Print the filter bar
				foreach (array_merge(['#'], range('A', 'Z')) as $letter) {
					$is_active = in_array($letter, $letters);
					echo sprintf(
						'<span class="member-filter-item %s" data-letter="%s">%s</span>',
						$is_active ? '' : 'disabled',
						$letter,
						$letter
					);
				}
				?>
			</div>

			<div class="member-grid">
				<?php
				foreach ($members as $member) {
					$website = esc_url($member['website']);
					?>
					<!-- start -->
					 <div class="member-item" data-letter="<?php echo esc_attr($member['first_letter']); ?>">
						<a href="<?php echo esc_url($member['website'] ?: '#'); ?>" target="_blank" rel="noopener">
							<img src="<?php echo esc_url($member['logo']); ?>" alt="<?php echo esc_attr($member['title']); ?>" />
						</a>
						<h6><?php echo esc_html($member['title']); ?></h6>

						<?php if ($member['website']) : ?>
							<p><a href="<?php echo esc_url($member['website']); ?>" target="_blank" rel="noopener">Website besuchen</a></p>
						<?php endif; ?>

						<?php if (
							$member['line_1'] || $member['line_2'] || $member['line_3'] ||
							$member['zip_code'] || $member['city'] || $member['country'] ||
							$member['phone'] || $member['fax_number'] || $member['email'] || $member['description']
						) : ?>
							<p>
								<a href="#"
								class="member-description-link"
								data-title="<?php echo esc_attr($member['title']); ?>"
								data-website="<?php echo esc_url($member['website']); ?>"
								data-line1="<?php echo esc_attr($member['line_1']); ?>"
								data-line2="<?php echo esc_attr($member['line_2']); ?>"
								data-line3="<?php echo esc_attr($member['line_3']); ?>"
								data-zip="<?php echo esc_attr($member['zip_code']); ?>"
								data-city="<?php echo esc_attr($member['city']); ?>"
								data-country="<?php echo esc_attr($member['country']); ?>"
								data-phone="<?php echo esc_attr($member['phone']); ?>"
								data-fax="<?php echo esc_attr($member['fax_number']); ?>"
								data-email="<?php echo esc_attr($member['email']); ?>"
								data-description="<?php echo esc_attr(wp_strip_all_tags($member['description'])); ?>">
									<i class="fas fa-info-circle"></i> Mehr Informationen
								</a>
							</p>
						<?php endif; ?>
					</div>
					<!-- end -->
					<?php
				}
				?>
			</div> <!-- end member-grid -->

			<p style="text-align:center; margin-top: 20px;">
				<button id="load-more-btn" style="display:none;" class="eco-load-more-btn">
					Mehr anzeigen
				</button>
			</p>


			<div id="member-description-modal" class="eco-modal">
				<div class="eco-modal-content">
					<span class="eco-modal-close">&times;</span>
					<h5 class="eco-modal-title"></h5>
					<div class="eco-modal-body"></div>
				</div>
			</div>


		</div>
		<?php
	}


	protected function content_template() {
		?>
		
		
		<?php
	}
}
