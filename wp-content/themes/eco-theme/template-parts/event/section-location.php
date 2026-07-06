<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id      = get_the_ID();
$mode         = eco_event_get_field( 'event_mode', $post_id, 'onsite' );
$name         = eco_event_get_field( 'location_name', $post_id, '' );
$street       = eco_event_get_field( 'street', $post_id, '' );
$zip          = eco_event_get_field( 'zip_plz', $post_id, '' );
$city         = eco_event_get_field( 'city', $post_id, '' );
$country      = eco_event_get_field( 'country', $post_id, '' );
$maps_url     = eco_event_get_field( 'maps_url', $post_id, '' );
$image        = eco_event_get_image_url( 'location_image', $post_id, 'large' );
$description  = eco_event_get_field( 'location_description', $post_id, '' );
$platform     = eco_event_get_field( 'online_platform', $post_id, '' );
$online_url   = eco_event_get_field( 'online_url', $post_id, '' );
$has_location = $name || $street || $zip || $city || $country || $platform || $description || $image;

if ( ! $has_location ) {
	return;
}
?>

<section class="eco-event-section eco-event-location">
	<div class="eco-event-container eco-event-location__grid">
		<div>
			<div class="eco-event-section-head">
				<span><?php esc_html_e( 'Place', 'eco-theme' ); ?></span>
				<h2><?php esc_html_e( 'Location', 'eco-theme' ); ?></h2>
			</div>

			<div class="eco-event-location__box">
				<?php if ( in_array( $mode, [ 'online', 'hybrid' ], true ) ) : ?>
					<h3><?php echo esc_html( $mode === 'hybrid' ? __( 'Online Participation', 'eco-theme' ) : __( 'Online Event', 'eco-theme' ) ); ?></h3>
					<?php if ( $platform ) : ?>
						<p><?php echo esc_html( $platform ); ?></p>
					<?php endif; ?>
					<?php if ( $online_url ) : ?>
						<a href="<?php echo esc_url( $online_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open online link', 'eco-theme' ); ?></a>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( in_array( $mode, [ 'onsite', 'hybrid' ], true ) && ( $name || $city ) ) : ?>
					<h3><?php echo esc_html( $name ?: $city ); ?></h3>
					<p>
						<?php echo esc_html( $street ); ?><?php echo $street ? '<br>' : ''; ?>
						<?php echo esc_html( trim( $zip . ' ' . $city ) ); ?><?php echo ( $zip || $city ) && $country ? '<br>' : ''; ?>
						<?php echo esc_html( $country ); ?>
					</p>
					<?php if ( $maps_url ) : ?>
						<a href="<?php echo esc_url( $maps_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Plan route', 'eco-theme' ); ?></a>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<div class="eco-event-richtext"><?php echo wp_kses_post( $description ); ?></div>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $image ) : ?>
			<div class="eco-event-location__image">
				<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $name ?: __( 'Location', 'eco-theme' ) ); ?>" loading="lazy">
			</div>
		<?php endif; ?>
	</div>
</section>
