<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = get_the_ID();
$faq     = eco_event_get_field( 'faq', $post_id, [] );
$heading = eco_event_get_section_heading( 'faq', $post_id );

if ( empty( $faq ) || ! is_array( $faq ) ) {
	return;
}
?>

<section class="eco-event-section eco-event-faq">
	<div class="eco-event-container">
		<div class="eco-event-section-head">
			<?php if ( $heading['eyebrow'] ) : ?>
				<span><?php echo esc_html( $heading['eyebrow'] ); ?></span>
			<?php endif; ?>
			<?php if ( $heading['title'] ) : ?>
				<h2><?php echo esc_html( $heading['title'] ); ?></h2>
			<?php endif; ?>
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
