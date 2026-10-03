<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id  = get_the_ID();
$intro    = eco_event_get_field( 'intro', $post_id, '' );
$benefits = eco_event_get_field( 'benefits', $post_id, [] );
$heading  = eco_event_get_section_heading( 'intro', $post_id );

if ( is_array( $benefits ) ) {
	$benefits = array_values(
		array_filter(
			$benefits,
			static function ( $benefit ) {
				return ! empty( trim( (string) ( $benefit['text'] ?? '' ) ) );
			}
		)
	);
} else {
	$benefits = [];
}

if ( ! $intro && empty( $benefits ) ) {
	return;
}

$single_column = ! $intro || empty( $benefits );
?>

<section class="eco-event-section eco-event-intro">
	<div class="eco-event-container eco-event-two-col<?php echo $single_column ? ' eco-event-two-col--single' : ''; ?>">
		<?php if ( $intro ) : ?>
			<div class="eco-event-intro__text">
				<div class="eco-event-section-head eco-event-section-head--intro">
					<?php if ( $heading['eyebrow'] ) : ?>
						<span><?php echo esc_html( $heading['eyebrow'] ); ?></span>
					<?php endif; ?>
					<?php if ( $heading['title'] ) : ?>
						<h2 class="eco-event-intro__title"><?php echo esc_html( $heading['title'] ); ?></h2>
					<?php endif; ?>
				</div>
				<div class="eco-event-richtext"><?php echo wp_kses_post( $intro ); ?></div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $benefits ) ) : ?>
			<aside class="eco-event-benefits">
				<h2><?php esc_html_e( 'Your Benefits', 'eco-theme' ); ?></h2>
				<ul>
					<?php foreach ( $benefits as $benefit ) : ?>
						<li><span aria-hidden="true">✓</span><?php echo esc_html( $benefit['text'] ); ?></li>
					<?php endforeach; ?>
				</ul>
			</aside>
		<?php endif; ?>
	</div>
</section>
