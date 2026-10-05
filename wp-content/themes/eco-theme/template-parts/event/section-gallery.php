<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id      = get_the_ID();
$impressions  = eco_event_get_field( 'impressions', $post_id, [] );
$flickr_url   = trim( (string) eco_event_get_field( 'impressions_flickr_url', $post_id, '' ) );
$flickr_label = trim( (string) eco_event_get_field( 'impressions_flickr_label', $post_id, '' ) );
$heading      = eco_event_get_section_heading( 'impressions', $post_id );

if ( ! is_array( $impressions ) ) {
	$impressions = [];
}

if ( empty( $impressions ) && ! $flickr_url ) {
	return;
}

$flickr_label = $flickr_label ?: __( 'View Flickr album', 'eco-theme' );
?>

<section class="eco-event-section eco-event-gallery-section">
	<div class="eco-event-container">
		<div class="eco-event-section-head">
			<?php if ( $heading['eyebrow'] ) : ?>
				<span><?php echo esc_html( $heading['eyebrow'] ); ?></span>
			<?php endif; ?>
			<?php if ( $heading['title'] ) : ?>
				<h2><?php echo esc_html( $heading['title'] ); ?></h2>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $impressions ) ) : ?>
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
		<?php endif; ?>

		<?php if ( $flickr_url ) : ?>
			<div class="eco-event-gallery__actions">
				<a class="eco-event-button eco-icon eco-icon-arrow-right" href="<?php echo esc_url( $flickr_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php echo esc_html( $flickr_label ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
