<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id   = get_the_ID();
$type      = eco_event_get_field( 'registration_type', $post_id, 'external' );
$intro     = eco_event_get_field( 'registration_intro', $post_id, '' );
$pretix    = eco_event_get_pretix_shortcode( $post_id );
$price     = eco_event_get_field( 'price_info', $post_id, '' );
$max       = eco_event_get_field( 'max_participants', $post_id, '' );
$is_past   = eco_event_is_past( $post_id );
$heading   = eco_event_get_section_heading( 'registration', $post_id );
$show_area = ! $is_past && ( $intro || $price || $max || ( 'pretix' === $type && $pretix ) );

if ( ! $show_area ) {
	return;
}
?>

<section id="registration" class="eco-event-section eco-event-registration">
	<div class="eco-event-container">
		<div class="eco-event-registration__box">
			<div class="eco-event-section-head">
				<?php if ( $heading['eyebrow'] ) : ?>
					<span><?php echo esc_html( $heading['eyebrow'] ); ?></span>
				<?php endif; ?>
				<?php if ( $heading['title'] ) : ?>
					<h2><?php echo esc_html( $heading['title'] ); ?></h2>
				<?php endif; ?>
			</div>

			<?php if ( $intro ) : ?>
				<div class="eco-event-richtext"><?php echo wp_kses_post( $intro ); ?></div>
			<?php endif; ?>

			<?php if ( $price || $max ) : ?>
				<div class="eco-event-registration__facts">
					<?php if ( $price ) : ?><span><?php echo esc_html( $price ); ?></span><?php endif; ?>
					<?php if ( $max ) : ?><span><?php printf( esc_html__( 'Max. %s participants', 'eco-theme' ), esc_html( $max ) ); ?></span><?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( 'pretix' === $type && $pretix ) : ?>
				<div class="eco-event-pretix">
					<?php echo do_shortcode( $pretix ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
