<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id      = get_the_ID();
$agenda_days  = eco_event_get_field( 'agenda_days', $post_id, [] );
$legacy_items = eco_event_get_field( 'agenda', $post_id, [] );

if ( ( empty( $agenda_days ) || ! is_array( $agenda_days ) ) && ! empty( $legacy_items ) && is_array( $legacy_items ) ) {
	$agenda_days = [
		[
			'day_label'    => __( 'Day 1', 'eco-theme' ),
			'day_date'     => eco_event_get_field( 'start_date', $post_id, '' ),
			'agenda_items' => $legacy_items,
		],
	];
}

if ( empty( $agenda_days ) || ! is_array( $agenda_days ) ) {
	return;
}

$valid_days = [];
foreach ( $agenda_days as $day_index => $day ) {
	$items = $day['agenda_items'] ?? [];
	if ( ! empty( $items ) && is_array( $items ) ) {
		$valid_days[] = [
			'index' => $day_index,
			'day'   => $day,
		];
	}
}

if ( empty( $valid_days ) ) {
	return;
}
?>

<section class="eco-event-section eco-event-agenda">
	<div class="eco-event-container eco-event-agenda__container">
		<div class="eco-event-section-head eco-event-section-head--row">
			<div>
				<span><?php esc_html_e( 'Program', 'eco-theme' ); ?></span>
				<h2><?php esc_html_e( 'Agenda', 'eco-theme' ); ?></h2>
			</div>
			<a class="eco-event-ical-link eco-icon eco-icon-calendar-check" href="<?php echo esc_url( eco_event_get_ical_url( $post_id ) ); ?>"><?php esc_html_e( 'Download iCal', 'eco-theme' ); ?></a>
		</div>

		<?php if ( count( $valid_days ) > 1 ) : ?>
			<div class="eco-event-agenda-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Agenda days', 'eco-theme' ); ?>">
				<?php foreach ( $valid_days as $tab_index => $entry ) : ?>
					<?php
					$day       = $entry['day'];
					$tab_id    = 'eco-event-agenda-tab-' . $post_id . '-' . $tab_index;
					$panel_id  = 'eco-event-agenda-panel-' . $post_id . '-' . $tab_index;
					$day_label = $day['day_label'] ?? sprintf( __( 'Day %d', 'eco-theme' ), $tab_index + 1 );
					$day_date  = ! empty( $day['day_date'] ) ? eco_event_format_date( $day['day_date'] ) : '';
					?>
					<button class="eco-event-agenda-tabs__button<?php echo $tab_index === 0 ? ' is-active' : ''; ?>" id="<?php echo esc_attr( $tab_id ); ?>" type="button" role="tab" aria-selected="<?php echo $tab_index === 0 ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $panel_id ); ?>" data-eco-agenda-tab="<?php echo esc_attr( $panel_id ); ?>">
						<span><?php echo esc_html( $day_label ); ?></span>
						<?php if ( $day_date ) : ?><small><?php echo esc_html( $day_date ); ?></small><?php endif; ?>
					</button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php foreach ( $valid_days as $panel_index => $entry ) : ?>
			<?php
			$day       = $entry['day'];
			$items     = $day['agenda_items'] ?? [];
			$panel_id  = 'eco-event-agenda-panel-' . $post_id . '-' . $panel_index;
			$tab_id    = 'eco-event-agenda-tab-' . $post_id . '-' . $panel_index;
			$day_label = $day['day_label'] ?? sprintf( __( 'Day %d', 'eco-theme' ), $panel_index + 1 );
			$day_date  = ! empty( $day['day_date'] ) ? eco_event_format_date( $day['day_date'] ) : '';
			?>
			<div class="eco-event-agenda-panel<?php echo $panel_index === 0 ? ' is-active' : ''; ?>" id="<?php echo esc_attr( $panel_id ); ?>" role="tabpanel" aria-labelledby="<?php echo esc_attr( $tab_id ); ?>" <?php echo $panel_index === 0 ? '' : 'hidden'; ?>>
				<?php if ( count( $valid_days ) > 1 ) : ?>
					<div class="eco-event-agenda-day-title">
						<strong><?php echo esc_html( $day_label ); ?></strong>
						<?php if ( $day_date ) : ?><span><?php echo esc_html( $day_date ); ?></span><?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="eco-event-agenda__list">
					<?php foreach ( $items as $item ) : ?>
						<?php
						$title = $item['title'] ?? '';
						if ( ! $title ) {
							continue;
						}
						$time     = $item['time'] ?? '';
						$end_time = $item['end_time'] ?? '';
						$desc     = $item['description'] ?? '';
						$style    = $item['style'] ?? 'default';
						$location = $item['location'] ?? '';
						$color    = ! empty( $item['color'] ) && eco_event_is_valid_hex( $item['color'] ) ? $item['color'] : '';
						$speakers = eco_event_normalize_posts( $item['speakers'] ?? [] );
						?>
						<article class="eco-event-agenda-item eco-event-agenda-item--<?php echo esc_attr( $style ); ?>" <?php echo $color ? 'style="--eco-event-accent:' . esc_attr( $color ) . ';"' : ''; ?>>
							<div class="eco-event-agenda-item__time eco-icon eco-icon-clock">
								<?php echo esc_html( $time && $end_time ? $time . ' – ' . $end_time : $time ); ?>
							</div>
							<div class="eco-event-agenda-item__content">
								<h3><?php echo esc_html( $title ); ?></h3>
								<?php if ( $desc ) : ?><div class="eco-event-richtext"><?php echo wp_kses_post( wpautop( $desc ) ); ?></div><?php endif; ?>
								<?php if ( ! empty( $speakers ) || $location ) : ?>
									<div class="eco-event-agenda-meta">
										<?php foreach ( $speakers as $speaker_id ) : ?>
											<span class="eco-icon eco-icon-user"><?php echo esc_html( get_the_title( $speaker_id ) ); ?></span>
										<?php endforeach; ?>
										<?php if ( $location ) : ?>
											<span class="eco-icon eco-icon-map-pin"><?php echo esc_html( $location ); ?></span>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
