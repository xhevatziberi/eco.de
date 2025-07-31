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



	protected function render() {
		$settings = $this->get_settings_for_display();

		$ids = $settings['people'];

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
								<p><strong><?php echo esc_html($position); ?></strong></p>
							<?php endif; ?>
							<?php if ($company) : ?>
								<p><?php echo esc_html($company); ?></p>
							<?php endif; ?>

							<?php if (
								$address || $phone || $email || $social || $biography
							) : ?>
								<p>
									<a href="#"
										class="eco-biography-link"
										data-name="<?php echo esc_attr($name); ?>"
										data-position="<?php echo esc_attr($position); ?>"
										data-company="<?php echo esc_attr($company); ?>"
										data-address="<?php echo esc_attr($address); ?>"
										data-phone="<?php echo esc_attr($phone); ?>"
										data-email="<?php echo esc_attr($email); ?>"
										data-facebook="<?php echo $social_facebook; ?>"
										data-twitter="<?php echo $social_twitter; ?>"
										data-linkedin="<?php echo $social_linkedin; ?>"
										data-xing="<?php echo $social_xing; ?>"
										data-biography="<?php echo esc_attr(wp_strip_all_tags($biography)); ?>">
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
										<i class="fas fa-info-circle"></i> Mehr Informationen
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
}
