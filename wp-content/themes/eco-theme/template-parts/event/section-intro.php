<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$intro       = eco_event_get_field( 'intro', get_the_ID(), '' );
$benefits    = eco_event_get_field( 'benefits', get_the_ID(), [] );
$impressions = eco_event_get_field( 'impressions', get_the_ID(), [] );

if ( ! $intro && empty( $benefits ) && empty( $impressions ) ) {
	return;
}
?>

<section class="eco-event-section eco-event-intro">
	<div class="eco-event-container eco-event-two-col">
		<?php if ( $intro ) : ?>
			<div class="eco-event-richtext">
				<h2><?php esc_html_e( 'Über das Event', 'eco-theme' ); ?></h2>
				<?php echo wp_kses_post( $intro ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $benefits ) && is_array( $benefits ) ) : ?>
			<div class="eco-event-benefits">
				<h2><?php esc_html_e( 'Ihre Vorteile', 'eco-theme' ); ?></h2>
				<ul>
					<?php foreach ( $benefits as $benefit ) : ?>
						<?php if ( empty( $benefit['text'] ) ) { continue; } ?>
						<li>
							<span aria-hidden="true"></span>
							<?php echo esc_html( $benefit['text'] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $impressions ) && is_array( $impressions ) ) : ?>
		<div class="eco-event-container eco-event-gallery">
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
</section>
