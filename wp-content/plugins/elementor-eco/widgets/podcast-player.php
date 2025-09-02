<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

class PodcastPlayer extends Widget_Base {

	public function get_name() {
		return 'podcast-player';
	}

	public function get_title() {
		return __('Podcast Player', 'elementor-eco');
	}

	public function get_icon() {
		return 'eicon-play';
	}

	public function get_categories() {
		return ['eco'];
	}

	public function get_script_depends() {
		return ['eco-podcast-script'];
	}

	public function get_style_depends() {
		return ['eco-podcast-style'];
	}

	protected function register_controls() {
		$this->start_controls_section('content_section', [
			'label' => __('Content', 'elementor-eco')
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$audio = get_field('audio_url');
		if (!$audio) {
			return;
		}
		$title = get_the_title();
		$description = get_field('short_description');
		$image = get_field('cover_image') ? get_field('cover_image')['sizes']['medium_large'] : 'https://placehold.co/300x300.png?text=Podcast';
		

		?>

		<div id="eco-podcast-player" class="eco-podcast-player" data-audio="<?php echo esc_url($audio); ?>">
			<div class="eco-podcast-left">
				<img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
			</div>
			<div class="eco-podcast-right">
				<h3><?php echo esc_html($title); ?></h3>
				<?php if (!empty($description)): ?>
					<p class="podcast-description"><?php echo esc_html($description); ?></p>
				<?php endif; ?>

				<div class="progress-bar"><span class="progress"></span></div>

				<div class="eco-podcast-controls">
					<div>
						<span class="time current">0:00</span> /
						<span class="time duration">0:00</span>
					</div>
					<button class="play-btn"><i class="fas fa-play"></i></button>
				</div>
			</div>
		</div>

		<?php
	}
}
