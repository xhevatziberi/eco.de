<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id  = get_the_ID();
$intro    = eco_event_get_field( 'intro', $post_id, '' );
$benefits = eco_event_get_field( 'benefits', $post_id, [] );

if ( ! $intro && empty( $benefits ) ) {
	return;
}
?>

<section class="eco-event-section eco-event-intro">
	<div class="eco-event-container eco-event-two-col">
		<?php if ( $intro ) : ?>
			<div class="eco-event-richtext eco-event-intro__text">
				<h2><?php esc_html_e( 'About the event', 'eco-theme' ); ?></h2>
				<?php echo wp_kses_post( $intro ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $benefits ) && is_array( $benefits ) ) : ?>
			<aside class="eco-event-benefits">
				<h2><?php esc_html_e( 'Your Benefits', 'eco-theme' ); ?></h2>
				<ul>
					<?php foreach ( $benefits as $benefit ) : ?>
						<?php if ( empty( $benefit['text'] ) ) { continue; } ?>
						<li><span aria-hidden="true">✓</span><?php echo esc_html( $benefit['text'] ); ?></li>
					<?php endforeach; ?>
				</ul>
			</aside>
		<?php endif; ?>
	</div>
</section>
