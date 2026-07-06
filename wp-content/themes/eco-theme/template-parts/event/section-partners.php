<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id        = get_the_ID();
$partner_groups = eco_event_get_partner_groups( $post_id );

if ( empty( $partner_groups ) ) {
	return;
}
?>

<section class="eco-event-section eco-event-partners">
	<div class="eco-event-container">
		<div class="eco-event-section-head">
			<span><?php esc_html_e( 'Network', 'eco-theme' ); ?></span>
			<h2><?php esc_html_e( 'Partners', 'eco-theme' ); ?></h2>
		</div>

		<div class="eco-event-partner-groups">
			<?php foreach ( $partner_groups as $group ) : ?>
				<?php
				$heading  = $group['heading'] ?? '';
				$color    = $group['color'] ?? '#000000';
				$partners = $group['partners'] ?? [];

				if ( empty( $partners ) ) {
					continue;
				}
				?>

				<div class="eco-event-partner-group" style="--eco-partner-tier-color: <?php echo esc_attr( $color ); ?>;">
					<?php if ( $heading ) : ?>
						<div class="eco-event-partner-group__head">
							<span class="eco-event-partner-group__line" aria-hidden="true"></span>
							<h3><?php echo esc_html( $heading ); ?></h3>
						</div>
					<?php endif; ?>

					<div class="eco-event-logo-grid">
						<?php foreach ( $partners as $partner ) : ?>
							<?php
							$name = $partner['name'] ?? '';
							$logo = $partner['logo'] ?? '';
							$url  = $partner['url'] ?? '';
							$tag  = $url ? 'a' : 'div';

							if ( ! $name && ! $logo ) {
								continue;
							}
							?>

							<<?php echo esc_html( $tag ); ?>
								class="eco-event-logo"
								<?php echo $url ? 'href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer"' : ''; ?>
								aria-label="<?php echo esc_attr( $name ); ?>"
							>
								<?php if ( $logo ) : ?>
									<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy">
								<?php else : ?>
									<span><?php echo esc_html( $name ); ?></span>
								<?php endif; ?>
							</<?php echo esc_html( $tag ); ?>>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
