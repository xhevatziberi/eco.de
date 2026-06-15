<?php
/**
 * Elementor Members widget.
 *
 * Path:
 * plugins/elementor-eco/widgets/members.php
 */

namespace ElementorEco\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Members extends Widget_Base {

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
				'label'       => __( 'Title', 'elementor-eco' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Unsere Mitglieder', 'elementor-eco' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$widget_id = 'eco-members-' . esc_attr( $this->get_id() );

		/*
		 * Do not run real member queries inside Elementor editor.
		 */
		$is_editor = (
			class_exists( '\Elementor\Plugin' ) &&
			\Elementor\Plugin::$instance->editor->is_edit_mode()
		);

		if ( $is_editor ) {
			$this->render_editor_preview( $settings );
			return;
		}

		$letters = class_exists( '\ElementorEco\Members_Ajax' )
			? \ElementorEco\Members_Ajax::get_available_letters()
			: [];

		$first_letter = ! empty( $letters ) ? reset( $letters ) : '';
		?>
		<section
			id="<?php echo esc_attr( $widget_id ); ?>"
			class="eco-member-list"
			data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
			data-nonce="<?php echo esc_attr( wp_create_nonce( \ElementorEco\Members_Ajax::NONCE_ACTION ) ); ?>"
			data-initial-letter="<?php echo esc_attr( $first_letter ); ?>"
		>
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<h2 class="eco-member-list__title">
					<?php echo esc_html( $settings['title'] ); ?>
				</h2>
			<?php endif; ?>

			<div
				class="member-filter"
				role="group"
				aria-label="<?php esc_attr_e( 'Mitglieder alphabetisch filtern', 'elementor-eco' ); ?>"
			>
				<?php foreach ( array_merge( [ '#' ], range( 'A', 'Z' ) ) as $letter ) : ?>
					<?php
					$is_enabled = in_array( $letter, $letters, true );
					$is_active  = $is_enabled && $letter === $first_letter;
					?>
					<button
						type="button"
						class="member-filter-item<?php echo $is_active ? ' active' : ''; ?>"
						data-letter="<?php echo esc_attr( $letter ); ?>"
						<?php disabled( ! $is_enabled ); ?>
						aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>"
					>
						<?php echo esc_html( $letter ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<div
				class="eco-members-status"
				role="status"
				aria-live="polite"
			></div>

			<div class="member-grid" aria-busy="true">
				<?php echo $this->get_loading_skeletons(); ?>
			</div>

			<div class="eco-members-load-more">
				<button
					type="button"
					class="eco-load-more-btn"
					hidden
				>
					<?php esc_html_e( 'Mehr anzeigen', 'elementor-eco' ); ?>
				</button>
			</div>

			<div
				class="eco-modal"
				role="dialog"
				aria-modal="true"
				aria-hidden="true"
				aria-labelledby="<?php echo esc_attr( $widget_id ); ?>-modal-title"
			>
				<div class="eco-modal__backdrop" data-modal-close></div>

				<div class="eco-modal-content" role="document">
					<button
						type="button"
						class="eco-modal-close"
						data-modal-close
						aria-label="<?php esc_attr_e( 'Dialog schließen', 'elementor-eco' ); ?>"
					>
						<span aria-hidden="true">&times;</span>
					</button>

					<h3
						id="<?php echo esc_attr( $widget_id ); ?>-modal-title"
						class="eco-modal-title"
					></h3>

					<div class="eco-modal-body"></div>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Lightweight editor representation.
	 */
	private function render_editor_preview( $settings ) {
		?>
		<div class="eco-member-list eco-member-list--editor">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<h2 class="eco-member-list__title">
					<?php echo esc_html( $settings['title'] ); ?>
				</h2>
			<?php endif; ?>

			<div class="member-filter member-filter--preview">
				<?php foreach ( [ '#', 'A', 'B', 'C', 'D', 'E', 'F', 'G' ] as $index => $letter ) : ?>
					<span class="member-filter-item<?php echo 0 === $index ? ' active' : ''; ?>">
						<?php echo esc_html( $letter ); ?>
					</span>
				<?php endforeach; ?>

				<span class="member-filter-item member-filter-item--more">…</span>
			</div>

			<div class="member-grid">
				<?php echo $this->get_loading_skeletons( 4 ); ?>
			</div>

			<p class="eco-members-editor-note">
				<?php
				esc_html_e(
					'Die Mitglieder werden auf der Website dynamisch geladen.',
					'elementor-eco'
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Generate loading placeholders.
	 */
	private function get_loading_skeletons( $count = 8 ) {
		ob_start();

		for ( $i = 0; $i < $count; $i++ ) {
			?>
			<div class="eco-member-skeleton" aria-hidden="true">
				<div class="eco-member-skeleton__visual"></div>
				<div class="eco-member-skeleton__line eco-member-skeleton__line--title"></div>
				<div class="eco-member-skeleton__line"></div>
			</div>
			<?php
		}

		return ob_get_clean();
	}

	protected function content_template() {
		/*
		 * Deliberately empty.
		 *
		 * Elementor uses the PHP editor preview rendered above, avoiding
		 * an expensive client-side preview containing all members.
		 */
	}
}