<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id      = get_the_ID();
$image        = eco_event_get_image_url( 'hero_image', $post_id, 'large' );
$label        = eco_event_get_label( $post_id );
$format       = eco_event_get_format( $post_id );
$occurrences  = eco_event_get_occurrence_display_items( $post_id );
$location     = eco_event_get_location_line( $post_id );
$teaser       = eco_event_get_field( 'teaser_text', $post_id, '' );
$button       = eco_event_get_registration_button( $post_id );
$is_past      = eco_event_is_past( $post_id );
$members_only = eco_event_is_members_only( $post_id );
?>

<section class="eco-event-hero eco-event-hero--small">
	<div class="eco-event-container eco-event-back-wrap">
		<a class="eco-event-back eco-icon eco-icon-arrow-left" href="<?php echo esc_url( home_url( '/events/' ) ); ?>">
			<?php esc_html_e( 'Back to all events', 'eco-theme' ); ?>
		</a>
	</div>

	<div class="eco-event-container eco-event-hero__grid">
		<?php if ( $image ) : ?>
			<div class="eco-event-hero__image">
				<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
			</div>
		<?php endif; ?>

		<div class="eco-event-hero__content">
			<?php if ( $label || $format || $members_only || $is_past ) : ?>
				<div class="eco-event-eyebrow">
					<?php if ( $label ) : ?>
						<span class="eco-event-badge"><?php echo esc_html( $label ); ?></span>
					<?php endif; ?>
					<?php if ( $format ) : ?>
						<span class="eco-event-badge eco-event-badge--format"><?php echo esc_html( $format ); ?></span>
					<?php endif; ?>
					<?php if ( $members_only ) : ?>
						<span class="eco-event-badge eco-event-badge--members"><?php echo esc_html( eco_event_get_members_only_label() ); ?></span>
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
				<?php if ( ! empty( $occurrences ) ) : ?>
					<?php $primary = array_shift( $occurrences ); ?>
					<?php if ( $primary['date'] ) : ?>
						<span class="eco-event-info-box__item eco-icon eco-icon-calendar"><?php echo esc_html( $primary['date'] ); ?></span>
					<?php endif; ?>
					<?php if ( $primary['time'] ) : ?>
						<span class="eco-event-info-box__item eco-icon eco-icon-clock"><?php echo esc_html( $primary['time'] ); ?></span>
					<?php endif; ?>

					<?php foreach ( $occurrences as $occurrence ) : ?>
						<div class="eco-event-info-box__occurrence">
							<?php if ( $occurrence['label'] ) : ?>
								<strong class="eco-event-info-box__occurrence-label"><?php echo esc_html( $occurrence['label'] ); ?></strong>
							<?php endif; ?>
							<?php if ( $occurrence['date'] ) : ?>
								<span class="eco-event-info-box__item eco-icon eco-icon-calendar"><?php echo esc_html( $occurrence['date'] ); ?></span>
							<?php endif; ?>
							<?php if ( $occurrence['time'] ) : ?>
								<span class="eco-event-info-box__item eco-icon eco-icon-clock"><?php echo esc_html( $occurrence['time'] ); ?></span>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php if ( $location ) : ?><span class="eco-event-info-box__item eco-icon eco-icon-map-pin"><?php echo esc_html( $location ); ?></span><?php endif; ?>
				<?php if ( eco_event_get_field( 'max_participants', $post_id, '' ) ) : ?><span class="eco-event-info-box__item eco-icon eco-icon-users"><?php printf( esc_html__( 'Max. %s participants', 'eco-theme' ), esc_html( eco_event_get_field( 'max_participants', $post_id, '' ) ) ); ?></span><?php endif; ?>
			</div>

			<div class="eco-event-hero__buttons">
				<?php if ( $button ) : ?>
					<a class="eco-event-button eco-event-button--wide eco-icon eco-icon-arrow-right" href="<?php echo esc_url( $button['url'] ); ?>" <?php echo ! empty( $button['target'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $button['label'] ); ?></a>
				<?php endif; ?>
				<a class="eco-event-button eco-event-button--wide eco-icon eco-icon-calendar-check" href="<?php echo esc_url( eco_event_get_ical_url( $post_id ) ); ?>"><?php esc_html_e( 'iCal', 'eco-theme' ); ?></a>
			</div>
		</div>
	</div>
</section>

<?php eco_event_render_elementor_content_at( 'after_hero', $post_id ); ?>
<?php get_template_part( 'template-parts/event/section', 'intro' ); ?>
<?php eco_event_render_elementor_content_at( 'after_intro', $post_id ); ?>
<?php get_template_part( 'template-parts/event/section', 'partners' ); ?>
<?php get_template_part( 'template-parts/event/section', 'gallery' ); ?>
<?php get_template_part( 'template-parts/event/section', 'people', [ 'type' => 'speakers' ] ); ?>
<?php get_template_part( 'template-parts/event/section', 'agenda' ); ?>
<?php eco_event_render_elementor_content_at( 'after_agenda', $post_id ); ?>
<?php get_template_part( 'template-parts/event/section', 'location' ); ?>
<?php eco_event_render_elementor_content_at( 'before_registration', $post_id ); ?>
<?php get_template_part( 'template-parts/event/section', 'registration' ); ?>
<?php eco_event_render_elementor_content_at( 'after_registration', $post_id ); ?>
<?php get_template_part( 'template-parts/event/section', 'series' ); ?>
<?php get_template_part( 'template-parts/event/section', 'faq' ); ?>
<?php get_template_part( 'template-parts/event/section', 'people', [ 'type' => 'contacts' ] ); ?>
