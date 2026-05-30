<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id     = get_the_ID();
$image       = eco_event_get_image_url( 'hero_image', $post_id, 'full' );
$label       = eco_event_get_label( $post_id );
$date_line   = eco_event_get_date_line( $post_id );
$location    = eco_event_get_location_line( $post_id );
$teaser      = eco_event_get_field( 'teaser_text', $post_id, '' );
$button      = eco_event_get_registration_button( $post_id );
$is_past     = eco_event_is_past( $post_id );
?>

<section class="eco-event-hero eco-event-hero--big">
	<?php if ( $image ) : ?>
		<div class="eco-event-hero__bg" style="background-image: url('<?php echo esc_url( $image ); ?>');"></div>
	<?php endif; ?>

	<div class="eco-event-hero__overlay"></div>

	<div class="eco-event-container eco-event-hero__inner">
		<div class="eco-event-hero__content">
			<div class="eco-event-eyebrow">
				<?php if ( $label ) : ?>
					<span class="eco-event-badge"><?php echo esc_html( $label ); ?></span>
				<?php endif; ?>
				<?php if ( $is_past ) : ?>
					<span class="eco-event-badge eco-event-badge--muted"><?php esc_html_e( 'Past Event', 'eco-theme' ); ?></span>
				<?php endif; ?>
			</div>

			<h1 class="eco-event-title"><?php echo esc_html( eco_event_get_title( $post_id ) ); ?></h1>

			<?php if ( $teaser ) : ?>
				<p class="eco-event-teaser"><?php echo esc_html( $teaser ); ?></p>
			<?php endif; ?>

			<div class="eco-event-meta eco-event-meta--hero">
				<?php if ( $date_line ) : ?>
					<span><?php echo esc_html( $date_line ); ?></span>
				<?php endif; ?>
				<?php if ( $location ) : ?>
					<span><?php echo esc_html( $location ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( $button ) : ?>
				<a class="eco-event-button" href="<?php echo esc_url( $button['url'] ); ?>" <?php echo ! empty( $button['target'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
					<?php echo esc_html( $button['label'] ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php eco_event_the_content_area(); ?>

<?php get_template_part( 'template-parts/event/section', 'intro' ); ?>
<?php get_template_part( 'template-parts/event/section', 'agenda' ); ?>
<?php get_template_part( 'template-parts/event/section', 'people' ); ?>
<?php get_template_part( 'template-parts/event/section', 'location' ); ?>
<?php get_template_part( 'template-parts/event/section', 'registration' ); ?>
<?php get_template_part( 'template-parts/event/section', 'partners' ); ?>
<?php get_template_part( 'template-parts/event/section', 'faq' ); ?>
