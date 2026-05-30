<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$type     = $args['type'] ?? 'both';
$post_id  = get_the_ID();
$speakers = eco_event_normalize_posts( eco_event_get_field( 'speakers', $post_id, [] ) );
$contacts = eco_event_normalize_posts( eco_event_get_field( 'contact_people', $post_id, [] ) );

$render_people = static function ( array $people, string $variant = 'speaker' ) {
	if ( empty( $people ) ) {
		return;
	}
	?>
	<div class="eco-event-people-grid eco-event-people-grid--<?php echo esc_attr( $variant ); ?>">
		<?php foreach ( $people as $person_id ) : ?>
			<?php
			$image    = get_the_post_thumbnail_url( $person_id, $variant === 'speaker' ? 'medium_large' : 'medium' );
			$position = function_exists( 'get_field' ) ? get_field( 'position', $person_id ) : '';
			$company  = function_exists( 'get_field' ) ? get_field( 'company', $person_id ) : '';
			$email    = function_exists( 'get_field' ) ? get_field( 'email', $person_id ) : '';
			$linkedin = function_exists( 'get_field' ) ? get_field( 'linkedin', $person_id ) : '';
			?>
			<article class="eco-event-person eco-event-person--<?php echo esc_attr( $variant ); ?>">
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

<?php if ( ( $type === 'speakers' || $type === 'both' ) && ! empty( $speakers ) ) : ?>
	<section class="eco-event-section eco-event-people eco-event-people--speakers">
		<div class="eco-event-container">
			<div class="eco-event-section-head">
				<span><?php esc_html_e( 'Menschen', 'eco-theme' ); ?></span>
				<h2><?php esc_html_e( 'Speaker', 'eco-theme' ); ?></h2>
			</div>
			<?php $render_people( $speakers, 'speaker' ); ?>
		</div>
	</section>
<?php endif; ?>

<?php if ( ( $type === 'contacts' || $type === 'both' ) && ! empty( $contacts ) ) : ?>
	<section class="eco-event-section eco-event-contacts">
		<div class="eco-event-container">
			<div class="eco-event-section-head">
				<span><?php esc_html_e( 'Kontakt', 'eco-theme' ); ?></span>
				<h2><?php esc_html_e( 'Ihre Ansprechpartner', 'eco-theme' ); ?></h2>
			</div>
			<?php $render_people( $contacts, 'contact' ); ?>
		</div>
	</section>
<?php endif; ?>
