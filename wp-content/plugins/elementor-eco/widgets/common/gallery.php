<?php
namespace ElementorEco\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Gallery extends Widget_Base {

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
		return 'eco-gallery';
	}

	public function get_title() {
		return __( 'Eco Gallery', 'elementor-eco' );
	}

	public function get_icon() {
		return 'eicon-slider-full-screen';
	}

	public function get_categories() {
		return [ 'eco' ];
	}

	public function __construct($data = [], $args = null) {
      parent::__construct($data, $args);

      //
   }

	public function get_script_depends() {
		// return [ 'eco-heading-script' ];
		return [ 'eco' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Section Content', 'elementor-eco' ),
			]
		);

		$this->add_control(
			'gallery',
			[
				'label' => __( 'Add Images', 'elementor-eco' ),
				'type' => Controls_Manager::GALLERY,
				'default' => [],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<section class="wgl_cpt_section">
			<div class="wgl-portfolio">

			</div>
		</section>
		<div class="row">
			<div id="fullscreen_gallery">
				<div class="gallery_borders">
					<div class="top"></div>
					<div class="bottom"></div>
					<div class="left"></div>
					<div class="right"></div>
				</div>
				<div class="gallery_slider">
					<?php foreach ($settings['gallery'] as $key => $image): ?>
						<?php if ( $key == 0 ): ?>
							<div class="pic_big has_transition_1500 active">
								<img src="<?php echo $image['url']; ?>" />
							</div>
						<?php else: ?>
							<div class="pic_big has_transition_1500">
								<img data-src="<?php echo $image['url']; ?>" />
							</div>
						<?php endif; ?>

					<?php endforeach; ?>
				</div>
				<div class="gallery_controller">
					<div class="button_right has_transition_800">
						<div class="circle has_transition_1000"><img class="has_transition_1000" src="<?php echo ECO_ASSETS_ROOT; ?>/images/white_right_arrow.png" /></div>
					</div>
					<div class="button_left has_transition_800">
						<div class="circle has_transition_1000"><img class="has_transition_1000" src="<?php echo ECO_ASSETS_ROOT; ?>/images/white_left_arrow.png" /></div>
					</div>
					<div class="button_down has_transition_800">
						<div class="circle has_transition_1000"><img class="has_transition_1000" src="<?php echo ECO_ASSETS_ROOT; ?>/images/white_right_arrow.png" /></div>
					</div>
				</div>
				<div class="numbers">
					<p class="counter">1</p>
					<div class="gallery_diagonal"></div>
					<p class="total"><?php echo count($settings['gallery']); ?><p>
				</div>
				<div class="advice">
					<img width=20 src="<?php echo ECO_ASSETS_ROOT; ?>/images/rotate.png" />
					<p><?php _e('rotate for <br />fullscreen gallery', 'elementor-eco') ?></p>
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
