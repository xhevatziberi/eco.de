<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$agenda = eco_event_get_field( 'agenda', get_the_ID(), [] );

if ( empty( $agenda ) || ! is_array( $agenda ) ) {
	return;
}
?>

<section class="eco-event-section eco-event-agenda">
	<div class="eco-event-container">
		<div class="eco-event-section-head">
			<span><?php esc_html_e( 'Programm', 'eco-theme' ); ?></span>
			<h2><?php esc_html_e( 'Agenda', 'eco-theme' ); ?></h2>
		</div>

		<div class="eco-event-agenda__list">
			<?php foreach ( $agenda as $item ) : ?>
				<?php
				$title = $item['title'] ?? '';
				if ( ! $title ) {
					continue;
				}
				$time     = $item['time'] ?? '';
				$end_time = $item['end_time'] ?? '';
				$desc     = $item['description'] ?? '';
				$style    = $item['style'] ?? 'default';
				$color    = ! empty( $item['color'] ) && eco_event_is_valid_hex( $item['color'] ) ? $item['color'] : '';
				$speakers = $item['speakers'] ?? [];
				?>
				<article class="eco-event-agenda-item eco-event-agenda-item--<?php echo esc_attr( $style ); ?>" <?php echo $color ? 'style="--eco-event-accent:' . esc_attr( $color ) . ';"' : ''; ?>>
					<div class="eco-event-agenda-item__time">
						<?php if ( $time && $end_time ) : ?>
							<?php echo esc_html( $time . ' – ' . $end_time ); ?>
						<?php elseif ( $time ) : ?>
							<?php echo esc_html( $time ); ?>
						<?php endif; ?>
					</div>
					<div class="eco-event-agenda-item__content">
						<h3><?php echo esc_html( $title ); ?></h3>
						<?php if ( $desc ) : ?>
							<div class="eco-event-richtext"><?php echo wp_kses_post( $desc ); ?></div>
						<?php endif; ?>
						<?php if ( ! empty( $speakers ) && is_array( $speakers ) ) : ?>
							<div class="eco-event-agenda-speakers">
								<?php foreach ( $speakers as $speaker ) : ?>
									<?php $speaker_id = is_object( $speaker ) ? $speaker->ID : (int) $speaker; ?>
									<span><?php echo esc_html( get_the_title( $speaker_id ) ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
