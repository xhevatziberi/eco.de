<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$type     = $args['type'] ?? 'both';
$post_id  = get_the_ID();
$speakers = eco_event_normalize_posts( eco_event_get_field( 'speakers', $post_id, [] ) );
$contacts = eco_event_normalize_posts( eco_event_get_field( 'contact_people', $post_id, [] ) );

$render_people = static function ( array $people, string $variant = 'speaker', int $visible_limit = 0 ) {
	if ( empty( $people ) ) {
		return;
	}
	?>
	<div class="eco-event-people-grid eco-event-people-grid--<?php echo esc_attr( $variant ); ?>">
		<?php foreach ( $people as $index => $person_id ) : ?>
			<?php
			$image    = get_the_post_thumbnail_url( $person_id, 'speaker' === $variant ? 'medium_large' : 'medium' );
			$position = function_exists( 'get_field' ) ? get_field( 'position', $person_id ) : '';
			$company  = function_exists( 'get_field' ) ? get_field( 'company', $person_id ) : '';
			$email    = function_exists( 'get_field' ) ? get_field( 'email', $person_id ) : '';
			$social   = function_exists( 'get_field' ) ? get_field( 'social_media', $person_id ) : [];
			$linkedin = is_array( $social ) ? ( $social['linkedin'] ?? '' ) : '';
			$is_extra = $visible_limit > 0 && $index >= $visible_limit;
			?>
			<article
				class="eco-event-person eco-event-person--<?php echo esc_attr( $variant ); ?><?php echo $is_extra ? ' eco-event-person--extra' : ''; ?>"
				<?php echo $is_extra ? 'hidden' : ''; ?>
			>
				<?php if ( $image ) : ?>
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title( $person_id ) ); ?>" loading="lazy">
				<?php endif; ?>
				<div class="eco-event-person__body">
					<h3><?php echo esc_html( get_the_title( $person_id ) ); ?></h3>
					<?php if ( $position || $company ) : ?>
						<p><?php echo esc_html( trim( $position . ( $position && $company ? ', ' : '' ) . $company ) ); ?></p>
					<?php endif; ?>
					<div class="eco-event-person__links">
						<?php if ( $email ) : ?><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a><?php endif; ?>
						<?php if ( $linkedin ) : ?><a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'LinkedIn', 'eco-theme' ); ?></a><?php endif; ?>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
};
?>

<?php if ( ( 'speakers' === $type || 'both' === $type ) && ! empty( $speakers ) ) : ?>
	<?php $heading = eco_event_get_section_heading( 'speakers', $post_id ); ?>
	<section class="eco-event-section eco-event-people eco-event-people--speakers">
		<div class="eco-event-container">
			<div class="eco-event-section-head">
				<?php if ( $heading['eyebrow'] ) : ?>
					<span><?php echo esc_html( $heading['eyebrow'] ); ?></span>
				<?php endif; ?>
				<?php if ( $heading['title'] ) : ?>
					<h2><?php echo esc_html( $heading['title'] ); ?></h2>
				<?php endif; ?>
			</div>

			<?php $render_people( $speakers, 'speaker', 8 ); ?>

			<?php if ( count( $speakers ) > 8 ) : ?>
				<div class="eco-event-people__toggle-wrap">
					<button
						type="button"
						class="eco-event-people__toggle"
						data-eco-speakers-toggle
						data-more-label="<?php echo esc_attr__( 'More', 'eco-theme' ); ?>"
						data-less-label="<?php echo esc_attr__( 'Show less', 'eco-theme' ); ?>"
						aria-expanded="false"
					>
						<?php esc_html_e( 'More', 'eco-theme' ); ?>
					</button>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php if ( ( 'contacts' === $type || 'both' === $type ) && ! empty( $contacts ) ) : ?>
	<?php $heading = eco_event_get_section_heading( 'contacts', $post_id ); ?>
	<section class="eco-event-section eco-event-contacts">
		<div class="eco-event-container">
			<div class="eco-event-section-head">
				<?php if ( $heading['eyebrow'] ) : ?>
					<span><?php echo esc_html( $heading['eyebrow'] ); ?></span>
				<?php endif; ?>
				<?php if ( $heading['title'] ) : ?>
					<h2><?php echo esc_html( $heading['title'] ); ?></h2>
				<?php endif; ?>
			</div>
			<?php $render_people( $contacts, 'contact' ); ?>
		</div>
	</section>
<?php endif; ?>
