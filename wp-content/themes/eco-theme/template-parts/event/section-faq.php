<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$faq = eco_event_get_field( 'faq', get_the_ID(), [] );

if ( empty( $faq ) || ! is_array( $faq ) ) {
	return;
}
?>

<section class="eco-event-section eco-event-faq">
	<div class="eco-event-container">
		<div class="eco-event-section-head">
			<span><?php esc_html_e( 'Questions', 'eco-theme' ); ?></span>
			<h2><?php esc_html_e( 'Frequently asked questions', 'eco-theme' ); ?></h2>
		</div>

		<div class="eco-event-faq__list">
			<?php foreach ( $faq as $item ) : ?>
				<?php if ( empty( $item['question'] ) ) { continue; } ?>
				<details class="eco-event-faq__item">
					<summary><?php echo esc_html( $item['question'] ); ?></summary>
					<?php if ( ! empty( $item['answer'] ) ) : ?>
						<div class="eco-event-richtext"><?php echo wp_kses_post( $item['answer'] ); ?></div>
					<?php endif; ?>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
