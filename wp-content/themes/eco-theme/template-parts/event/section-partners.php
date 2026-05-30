<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$partners = eco_event_get_field( 'partners', get_the_ID(), [] );
$sponsors = eco_event_get_field( 'sponsors', get_the_ID(), [] );

if ( empty( $partners ) && empty( $sponsors ) ) {
	return;
}

$render_members = static function ( $members ) {
	if ( empty( $members ) || ! is_array( $members ) ) {
		return;
	}
	?>
	<div class="eco-event-logo-grid">
		<?php foreach ( $members as $member ) : ?>
			<?php
			$member_id = is_object( $member ) ? $member->ID : (int) $member;
			$image     = get_the_post_thumbnail_url( $member_id, 'medium' );
			$website   = function_exists( 'get_field' ) ? get_field( 'website', $member_id ) : '';
			$tag       = $website ? 'a' : 'div';
			?>
			<<?php echo esc_html( $tag ); ?> class="eco-event-logo" <?php echo $website ? 'href="' . esc_url( $website ) . '" target="_blank" rel="noopener noreferrer"' : ''; ?>>
				<?php if ( $image ) : ?>
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title( $member_id ) ); ?>" loading="lazy">
				<?php else : ?>
					<span><?php echo esc_html( get_the_title( $member_id ) ); ?></span>
				<?php endif; ?>
			</<?php echo esc_html( $tag ); ?>>
		<?php endforeach; ?>
	</div>
	<?php
};
?>

<?php if ( ! empty( $sponsors ) ) : ?>
	<section class="eco-event-section eco-event-sponsors">
		<div class="eco-event-container">
			<div class="eco-event-section-head">
				<span><?php esc_html_e( 'Unterstützer', 'eco-theme' ); ?></span>
				<h2><?php esc_html_e( 'Sponsoren', 'eco-theme' ); ?></h2>
			</div>
			<?php $render_members( $sponsors ); ?>
		</div>
	</section>
<?php endif; ?>

<?php if ( ! empty( $partners ) ) : ?>
	<section class="eco-event-section eco-event-partners">
		<div class="eco-event-container">
			<div class="eco-event-section-head">
				<span><?php esc_html_e( 'Netzwerk', 'eco-theme' ); ?></span>
				<h2><?php esc_html_e( 'Partner', 'eco-theme' ); ?></h2>
			</div>
			<?php $render_members( $partners ); ?>
		</div>
	</section>
<?php endif; ?>
