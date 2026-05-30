<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$speakers = eco_event_get_field( 'speakers', get_the_ID(), [] );
$contacts = eco_event_get_field( 'contact_people', get_the_ID(), [] );

if ( empty( $speakers ) && empty( $contacts ) ) {
	return;
}

$render_people = static function ( $people ) {
	if ( empty( $people ) || ! is_array( $people ) ) {
		return;
	}
	?>
	<div class="eco-event-people-grid">
		<?php foreach ( $people as $person ) : ?>
			<?php
			$person_id = is_object( $person ) ? $person->ID : (int) $person;
			$image     = get_the_post_thumbnail_url( $person_id, 'medium' );
			$position  = function_exists( 'get_field' ) ? get_field( 'position', $person_id ) : '';
			$company   = function_exists( 'get_field' ) ? get_field( 'company', $person_id ) : '';
			$email     = function_exists( 'get_field' ) ? get_field( 'email', $person_id ) : '';
			?>
			<article class="eco-event-person">
				<?php if ( $image ) : ?>
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title( $person_id ) ); ?>" loading="lazy">
				<?php endif; ?>
				<div>
					<h3><?php echo esc_html( get_the_title( $person_id ) ); ?></h3>
					<?php if ( $position || $company ) : ?>
						<p><?php echo esc_html( trim( $position . ( $position && $company ? ', ' : '' ) . $company ) ); ?></p>
					<?php endif; ?>
					<?php if ( $email ) : ?>
						<a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
};
?>

<?php if ( ! empty( $speakers ) ) : ?>
	<section class="eco-event-section eco-event-people">
		<div class="eco-event-container">
			<div class="eco-event-section-head">
				<span><?php esc_html_e( 'Menschen', 'eco-theme' ); ?></span>
				<h2><?php esc_html_e( 'Speaker', 'eco-theme' ); ?></h2>
			</div>
			<?php $render_people( $speakers ); ?>
		</div>
	</section>
<?php endif; ?>

<?php if ( ! empty( $contacts ) ) : ?>
	<section class="eco-event-section eco-event-contacts">
		<div class="eco-event-container">
			<div class="eco-event-section-head">
				<span><?php esc_html_e( 'Kontakt', 'eco-theme' ); ?></span>
				<h2><?php esc_html_e( 'Ihre Ansprechpartner', 'eco-theme' ); ?></h2>
			</div>
			<?php $render_people( $contacts ); ?>
		</div>
	</section>
<?php endif; ?>
