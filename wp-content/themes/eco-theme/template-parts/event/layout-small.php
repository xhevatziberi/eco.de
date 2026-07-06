<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id   = get_the_ID();
$image     = eco_event_get_image_url( 'hero_image', $post_id, 'large' );
$label     = eco_event_get_label( $post_id );
$date_line = eco_event_get_date_line( $post_id );
$location  = eco_event_get_location_line( $post_id );
$teaser    = eco_event_get_field( 'teaser_text', $post_id, '' );
$button    = eco_event_get_registration_button( $post_id );
$is_past   = eco_event_is_past( $post_id );
?>

<section class="eco-event-hero eco-event-hero--small">
	<div class="eco-event-container eco-event-back-wrap">
		<a class="eco-event-back" href="<?php echo esc_url( home_url( '/events/' ) ); ?>"><?php esc_html_e( 'Back to all events', 'eco-theme' ); ?></a>
	</div>

	<div class="eco-event-container eco-event-hero__grid">
		<?php if ( $image ) : ?>
			<div class="eco-event-hero__image">
				<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
			</div>
		<?php endif; ?>

		<div class="eco-event-hero__content">
			<?php if ( $label || $is_past ) : ?>
				<div class="eco-event-eyebrow">
					<?php if ( $label ) : ?>
						<span class="eco-event-badge"><?php echo esc_html( $label ); ?></span>
					<?php endif; ?>
					<?php if ( $is_past ) : ?>
						<span class="eco-event-badge eco-event-badge--muted"><?php esc_html_e( 'Past Event', 'eco-theme' ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<h1 class="eco-event-title"><?php echo esc_html( eco_event_get_title( $post_id ) ); ?></h1>

			<?php if ( $teaser ) : ?>
				<p class="eco-event-teaser"><?php echo esc_html( $teaser ); ?></p>
			<?php endif; ?>

			<div class="eco-event-info-box">
				<?php if ( $date_line ) : ?><span class="eco-event-info-box__item eco-icon-calendar"><?php echo esc_html( $date_line ); ?></span><?php endif; ?>
				<?php if ( eco_event_get_field( 'start_time', $post_id, '' ) || eco_event_get_field( 'end_time', $post_id, '' ) ) : ?>
					<span class="eco-event-info-box__item eco-icon-clock"><?php echo esc_html( trim( eco_event_get_field( 'start_time', $post_id, '' ) . ( eco_event_get_field( 'end_time', $post_id, '' ) ? ' – ' . eco_event_get_field( 'end_time', $post_id, '' ) : '' ) ) ); ?></span>
				<?php endif; ?>
				<?php if ( $location ) : ?><span class="eco-event-info-box__item eco-icon-location"><?php echo esc_html( $location ); ?></span><?php endif; ?>
				<?php if ( eco_event_get_field( 'max_participants', $post_id, '' ) ) : ?><span class="eco-event-info-box__item eco-icon-users"><?php printf( esc_html__( 'Max. %s participants', 'eco-theme' ), esc_html( eco_event_get_field( 'max_participants', $post_id, '' ) ) ); ?></span><?php endif; ?>
			</div>

			<div class="eco-event-hero__buttons">
				<?php if ( $button ) : ?>
					<a class="eco-event-button eco-event-button--wide" href="<?php echo esc_url( $button['url'] ); ?>" <?php echo ! empty( $button['target'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $button['label'] ); ?></a>
				<?php endif; ?>
				<a class="eco-event-button eco-event-button--wide" href="<?php echo esc_url( eco_event_get_ical_url( $post_id ) ); ?>"><?php esc_html_e( 'iCal', 'eco-theme' ); ?></a>
			</div>
		</div>
	</div>
</section>

<?php eco_event_the_content_area(); ?>
<?php get_template_part( 'template-parts/event/section', 'intro' ); ?>
<?php get_template_part( 'template-parts/event/section', 'agenda' ); ?>
<?php get_template_part( 'template-parts/event/section', 'people', [ 'type' => 'speakers' ] ); ?>
<?php get_template_part( 'template-parts/event/section', 'people', [ 'type' => 'contacts' ] ); ?>
<?php get_template_part( 'template-parts/event/section', 'partners' ); ?>
<?php get_template_part( 'template-parts/event/section', 'registration' ); ?>
