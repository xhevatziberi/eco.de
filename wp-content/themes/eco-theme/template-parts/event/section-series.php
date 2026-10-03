<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = get_the_ID();
$events  = eco_event_get_series_events( $post_id );

if ( empty( $events ) ) {
	return;
}

$terms       = eco_event_get_series_terms( $post_id );
$series_name = ! empty( $terms ) && $terms[0] instanceof WP_Term ? $terms[0]->name : '';
$heading     = eco_event_get_section_heading( 'series', $post_id );
$title       = $heading['title'] ?: $series_name;
$eyebrow     = $heading['eyebrow'];

if ( ! $eyebrow && $series_name && $title !== $series_name ) {
	$eyebrow = $series_name;
}

if ( ! $title ) {
	return;
}
?>

<section class="eco-event-section eco-event-series">
	<div class="eco-event-container">
		<div class="eco-event-section-head">
			<?php if ( $eyebrow ) : ?>
				<span><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<h2><?php echo esc_html( $title ); ?></h2>
		</div>

		<div class="eco-event-series__grid">
			<?php foreach ( $events as $event_id ) : ?>
				<?php
				$event_title    = eco_event_get_title( $event_id );
				$image          = eco_event_get_image_url( 'card_image', $event_id, 'large' );
				$label          = eco_event_get_label( $event_id );
				$format         = eco_event_get_format( $event_id );
				$date_line      = eco_event_get_date_line( $event_id );
				$location       = eco_event_get_location_line( $event_id );
				$is_past        = eco_event_is_past( $event_id );
				$members_only   = eco_event_is_members_only( $event_id );
				$layout         = eco_event_get_field( 'event_layout', $event_id, 'small' );
				$accent         = eco_event_get_color( $event_id, $layout );
				$contrast       = eco_event_get_contrast_color( $event_id, $accent );
				?>
				<a
					class="eco-event-series-card<?php echo $is_past ? ' eco-event-series-card--past' : ''; ?>"
					href="<?php echo esc_url( get_permalink( $event_id ) ); ?>"
					style="--eco-event-series-accent: <?php echo esc_attr( $accent ); ?>; --eco-event-series-contrast: <?php echo esc_attr( $contrast ); ?>;"
				>
					<?php if ( $image ) : ?>
						<div class="eco-event-series-card__media">
							<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $event_title ); ?>" loading="lazy">
						</div>
					<?php endif; ?>

					<div class="eco-event-series-card__body">
						<?php if ( $label || $format || $members_only || $is_past ) : ?>
							<div class="eco-event-series-card__badges">
								<?php if ( $label ) : ?>
									<span class="eco-event-series-card__badge"><?php echo esc_html( $label ); ?></span>
								<?php endif; ?>
								<?php if ( $format ) : ?>
									<span class="eco-event-series-card__badge eco-event-series-card__badge--format"><?php echo esc_html( $format ); ?></span>
								<?php endif; ?>
								<?php if ( $members_only ) : ?>
									<span class="eco-event-series-card__badge"><?php echo esc_html( eco_event_get_members_only_label() ); ?></span>
								<?php endif; ?>
								<?php if ( $is_past ) : ?>
									<span class="eco-event-series-card__badge eco-event-series-card__badge--muted"><?php esc_html_e( 'Past Event', 'eco-theme' ); ?></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<h3 class="eco-event-series-card__title"><?php echo esc_html( $event_title ); ?></h3>

						<?php if ( $date_line || $location ) : ?>
							<div class="eco-event-series-card__meta">
								<?php if ( $date_line ) : ?>
									<span class="eco-icon eco-icon-calendar"><?php echo esc_html( $date_line ); ?></span>
								<?php endif; ?>
								<?php if ( $location ) : ?>
									<span class="eco-icon eco-icon-map-pin"><?php echo esc_html( $location ); ?></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
