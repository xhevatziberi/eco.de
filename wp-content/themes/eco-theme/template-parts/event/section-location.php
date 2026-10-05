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
$show_map     = eco_event_get_boolean_field( 'show_google_map', $post_id, false );
$map_embed    = $show_map && in_array( $mode, [ 'onsite', 'hybrid' ], true ) ? eco_event_get_google_maps_embed_url( $post_id ) : '';
$heading      = eco_event_get_section_heading( 'location', $post_id );
$has_location = $name || $street || $zip || $city || $country || $platform || $description || $image || $map_embed;

if ( ! $has_location ) {
	return;
}

$media_columns = ( $image ? 1 : 0 ) + ( $map_embed ? 1 : 0 );
$grid_columns  = 1 + $media_columns;
?>

<section class="eco-event-section eco-event-location">
	<div class="eco-event-container eco-event-location__grid eco-event-location__grid--columns-<?php echo esc_attr( $grid_columns ); ?>">
		<div>
			<div class="eco-event-section-head">
				<?php if ( $heading['eyebrow'] ) : ?>
					<span><?php echo esc_html( $heading['eyebrow'] ); ?></span>
				<?php endif; ?>
				<?php if ( $heading['title'] ) : ?>
					<h2><?php echo esc_html( $heading['title'] ); ?></h2>
				<?php endif; ?>
			</div>

			<div class="eco-event-location__box">
				<?php if ( in_array( $mode, [ 'online', 'hybrid' ], true ) ) : ?>
					<h3><?php echo esc_html( 'hybrid' === $mode ? __( 'Online Participation', 'eco-theme' ) : __( 'Online Event', 'eco-theme' ) ); ?></h3>
					<?php if ( $platform ) : ?>
						<p><?php echo esc_html( $platform ); ?></p>
					<?php endif; ?>
					<?php if ( $online_url ) : ?>
						<a href="<?php echo esc_url( $online_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open online link', 'eco-theme' ); ?></a>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( in_array( $mode, [ 'onsite', 'hybrid' ], true ) && ( $name || $city || $street || $zip ) ) : ?>
					<?php if ( $name || $city ) : ?>
						<h3><?php echo esc_html( $name ?: $city ); ?></h3>
					<?php endif; ?>
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

		<?php if ( $map_embed ) : ?>
			<div class="eco-event-location__map">
				<iframe
					src="<?php echo esc_url( $map_embed ); ?>"
					title="<?php echo esc_attr__( 'Google Map', 'eco-theme' ); ?>"
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"
					allowfullscreen
				></iframe>
			</div>
		<?php endif; ?>
	</div>
</section>
