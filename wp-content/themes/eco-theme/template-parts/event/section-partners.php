<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id        = get_the_ID();
$partner_groups = eco_event_get_partner_groups( $post_id );
$heading        = eco_event_get_section_heading( 'partners', $post_id );

if ( empty( $partner_groups ) ) {
	return;
}
?>

<section class="eco-event-section eco-event-partners">
	<div class="eco-event-container">
		<div class="eco-event-section-head">
			<?php if ( $heading['eyebrow'] ) : ?>
				<span><?php echo esc_html( $heading['eyebrow'] ); ?></span>
			<?php endif; ?>
			<?php if ( $heading['title'] ) : ?>
				<h2><?php echo esc_html( $heading['title'] ); ?></h2>
			<?php endif; ?>
		</div>

		<div class="eco-event-partner-groups">
			<?php foreach ( $partner_groups as $group ) : ?>
				<?php
				$group_heading = $group['heading'] ?? '';
				$color         = $group['color'] ?? '';
				$logo_size     = $group['logo_size'] ?? 'normal';
				$partners      = $group['partners'] ?? [];

				if ( empty( $partners ) ) {
					continue;
				}
				?>

				<div
					class="eco-event-partner-group eco-event-partner-group--<?php echo esc_attr( $logo_size ); ?>"
					<?php echo $color ? 'style="--eco-partner-tier-color: ' . esc_attr( $color ) . ';"' : ''; ?>
				>
					<?php if ( $group_heading ) : ?>
						<div class="eco-event-partner-group__head">
							<span class="eco-event-partner-group__line" aria-hidden="true"></span>
							<h3><?php echo esc_html( $group_heading ); ?></h3>
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
