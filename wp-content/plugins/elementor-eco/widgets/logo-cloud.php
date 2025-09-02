<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class LogoCloud extends Widget_Base {

	public function get_name() {
		return 'logo-cloud';
	}

	public function get_title() {
		return __( 'Member Logo Cloud', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function get_script_depends() {
		return [ 'eco-logo-cloud-script' ];
	}

	public function get_style_depends() {
		return [ 'eco-logo-cloud-style' ];
	}

	protected function render() {
		// Get all member logos
		$args = [
			'post_type' => 'member',
			'posts_per_page' => -1,
			'orderby' => 'rand',
			'post_status' => 'publish',
			'posts_per_page' => 64, // Limit to 64 logos
		];
		$query = new \WP_Query($args);

		$logos = [];

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$logo = get_the_post_thumbnail_url(get_the_ID(), 'full');
				if ($logo) {
					$logos[] = [
						'logo' => esc_url($logo),
						'url' => esc_url(get_field('website'))
					];
				}
			}
			wp_reset_postdata();
		}
		?>

		<div class="eco-logo-cloud" data-logos='<?php echo json_encode($logos); ?>'>
			<!-- JS will inject logos -->
			 
		</div>
		<?php
	}
}
