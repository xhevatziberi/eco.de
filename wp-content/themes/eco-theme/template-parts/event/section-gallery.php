<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$impressions = eco_event_get_field( 'impressions', get_the_ID(), [] );

if ( empty( $impressions ) || ! is_array( $impressions ) ) {
	return;
}
?>

<section class="eco-event-section eco-event-gallery-section">
	<div class="eco-event-container">
		<h2 class="eco-event-simple-title"><?php esc_html_e( 'Impressionen', 'eco-theme' ); ?></h2>
		<div class="eco-event-gallery">
			<?php foreach ( array_slice( $impressions, 0, 6 ) as $image ) : ?>
				<?php
				$url = '';
				$alt = '';
				if ( is_array( $image ) ) {
					$url = ! empty( $image['sizes']['medium_large'] ) ? $image['sizes']['medium_large'] : ( $image['url'] ?? '' );
					$alt = $image['alt'] ?? '';
				}
				?>
				<?php if ( $url ) : ?>
					<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy">
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
