<?php
/**
 * Event Label and Event Format taxonomies.
 *
 * These replace the old fixed event_label select so editors can maintain
 * visible labels/formats centrally and translate the taxonomy terms in WPML.
 *
 * @package eco-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Event Label and Event Format.
 */
function eco_register_event_display_taxonomies(): void {
	$common = [
		'public'             => true,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_nav_menus'  => false,
		'show_in_rest'       => true,
		'show_tagcloud'      => false,
		'show_in_quick_edit' => true,
		'show_admin_column'  => true,
		'hierarchical'       => false,
		'rewrite'            => false,
		'query_var'          => false,
		'capabilities'       => [
			'manage_terms' => 'manage_categories',
			'edit_terms'   => 'manage_categories',
			'delete_terms' => 'manage_categories',
			'assign_terms' => 'edit_posts',
		],
	];

	register_taxonomy(
		'event-label',
		[ 'event' ],
		array_merge(
			$common,
			[
				'labels' => [
					'name'          => __( 'Event Labels', 'eco-theme' ),
					'singular_name' => __( 'Event Label', 'eco-theme' ),
					'menu_name'     => __( 'Event Labels', 'eco-theme' ),
					'all_items'     => __( 'All Event Labels', 'eco-theme' ),
					'edit_item'     => __( 'Edit Event Label', 'eco-theme' ),
					'update_item'   => __( 'Update Event Label', 'eco-theme' ),
					'add_new_item'  => __( 'Add New Event Label', 'eco-theme' ),
					'new_item_name' => __( 'New Event Label Name', 'eco-theme' ),
					'search_items'  => __( 'Search Event Labels', 'eco-theme' ),
				],
			]
		)
	);

	register_taxonomy(
		'event-format',
		[ 'event' ],
		array_merge(
			$common,
			[
				'labels' => [
					'name'          => __( 'Event Formats', 'eco-theme' ),
					'singular_name' => __( 'Event Format', 'eco-theme' ),
					'menu_name'     => __( 'Event Formats', 'eco-theme' ),
					'all_items'     => __( 'All Event Formats', 'eco-theme' ),
					'edit_item'     => __( 'Edit Event Format', 'eco-theme' ),
					'update_item'   => __( 'Update Event Format', 'eco-theme' ),
					'add_new_item'  => __( 'Add New Event Format', 'eco-theme' ),
					'new_item_name' => __( 'New Event Format Name', 'eco-theme' ),
					'search_items'  => __( 'Search Event Formats', 'eco-theme' ),
				],
			]
		)
	);
}
add_action( 'init', 'eco_register_event_display_taxonomies', 9 );

/**
 * Initial taxonomy terms.
 *
 * Keys are stable slugs; editors may translate the displayed names in WPML.
 */
function eco_get_initial_event_label_terms(): array {
	return [
		'event'               => 'Event',
		'eco-event'           => 'eco Event',
		'networker-nrw-event' => 'networker NRW Event',
		'nrw-units-event'     => 'nrw.uniTS Event',
		'de-cix-event'        => 'DE-CIX Event',
		'eco-akademie'        => 'eco AKADEMIE',
		'membersplus'         => 'Events+',
		'politalk'            => 'PoliTalk',
		'nettalk'             => 'NetTalk',
		'localtalk'           => 'LocalTalk',
		'bxltalk'             => 'BxlTalk',
		'kg-sitzung'          => 'KG-Sitzung',
		'partner-event'       => 'Partner-Event',
		'highlight'           => 'Highlight',
	];
}

function eco_get_initial_event_format_terms(): array {
	return [
		'webinar'      => 'Webinar',
		'workshop'     => 'Workshop',
		'conference'   => 'Conference',
		'training'     => 'Training',
		'coaching'     => 'Coaching',
		'roundtable'   => 'Roundtable',
		'masterclass'  => 'Masterclass',
		'seminar'      => 'Seminar',
		'seminar-zert' => 'Seminar mit Zertifizierung',
		'award'        => 'Award',
		'on-premises'  => 'On-Premises',
		'online'       => 'Online',
		'hybrid'       => 'Hybrid',
	];
}

/**
 * Insert the initial terms once and migrate values from the legacy event_label meta.
 */
function eco_migrate_event_display_taxonomies(): void {
	$migration_version = 1;

	if ( (int) get_option( 'eco_event_display_taxonomy_migration', 0 ) >= $migration_version ) {
		return;
	}

	if ( ! taxonomy_exists( 'event-label' ) || ! taxonomy_exists( 'event-format' ) ) {
		return;
	}

	foreach ( eco_get_initial_event_label_terms() as $slug => $name ) {
		if ( ! term_exists( $slug, 'event-label' ) ) {
			wp_insert_term( $name, 'event-label', [ 'slug' => $slug ] );
		}
	}

	foreach ( eco_get_initial_event_format_terms() as $slug => $name ) {
		if ( ! term_exists( $slug, 'event-format' ) ) {
			wp_insert_term( $name, 'event-format', [ 'slug' => $slug ] );
		}
	}

	// Keep the existing Event Source slug, only update the requested visible spelling.
	if ( taxonomy_exists( 'event-source' ) ) {
		$source = get_term_by( 'slug', 'partner-event', 'event-source' );

		if ( $source instanceof WP_Term && 'Partnerevents' !== $source->name ) {
			wp_update_term( $source->term_id, 'event-source', [ 'name' => 'Partnerevents' ] );
		}
	}

	$legacy_label_map = [
		'event'               => 'event',
		'eco_event'           => 'eco-event',
		'networker_nrw_event' => 'networker-nrw-event',
		'nrw_units_event'     => 'nrw-units-event',
		'de_cix_event'        => 'de-cix-event',
		'eco_akademie'        => 'eco-akademie',
		'membersplus'         => 'membersplus',
		'politalk'            => 'politalk',
		'nettalk'             => 'nettalk',
		'localtalk'           => 'localtalk',
		'bxltalk'             => 'bxltalk',
		'kg_sitzung'          => 'kg-sitzung',
		'partner_event'       => 'partner-event',
		'highlight'           => 'highlight',
	];

	$legacy_format_map = [
		'webinar'      => 'webinar',
		'workshop'     => 'workshop',
		'conference'   => 'conference',
		'training'     => 'training',
		'coaching'     => 'coaching',
		'roundtable'   => 'roundtable',
		'masterclass'  => 'masterclass',
		'seminar'      => 'seminar',
		'seminar_zert' => 'seminar-zert',
		'award'        => 'award',
		'on_premises'  => 'on-premises',
		'online'       => 'online',
		'hybrid'       => 'hybrid',
	];

	$event_ids = get_posts(
		[
			'post_type'        => 'event',
			'post_status'      => 'any',
			'posts_per_page'   => -1,
			'fields'           => 'ids',
			'suppress_filters' => true,
		]
	);

	foreach ( $event_ids as $event_id ) {
		$legacy_value = (string) get_post_meta( $event_id, 'event_label', true );

		if ( '' === $legacy_value ) {
			continue;
		}

		$current_labels = wp_get_object_terms( $event_id, 'event-label', [ 'fields' => 'ids' ] );
		$current_format = wp_get_object_terms( $event_id, 'event-format', [ 'fields' => 'ids' ] );

		if ( empty( $current_labels ) && isset( $legacy_label_map[ $legacy_value ] ) ) {
			wp_set_object_terms( $event_id, $legacy_label_map[ $legacy_value ], 'event-label', false );
		}

		if ( empty( $current_format ) && isset( $legacy_format_map[ $legacy_value ] ) ) {
			wp_set_object_terms( $event_id, $legacy_format_map[ $legacy_value ], 'event-format', false );

			/*
			 * Legacy event_label could only store one value. When that value was
			 * actually a format (for example Webinar), derive a sensible visible
			 * Event Label from Event Source so existing events gain the new two-badge
			 * structure without manual cleanup.
			 */
			if ( empty( $current_labels ) && taxonomy_exists( 'event-source' ) ) {
				$source_slugs = wp_get_object_terms( $event_id, 'event-source', [ 'fields' => 'slugs' ] );

				if ( ! is_wp_error( $source_slugs ) ) {
					if ( in_array( 'eco-event', $source_slugs, true ) ) {
						wp_set_object_terms( $event_id, 'eco-event', 'event-label', false );
					} elseif ( in_array( 'partner-event', $source_slugs, true ) ) {
						wp_set_object_terms( $event_id, 'partner-event', 'event-label', false );
					}
				}
			}
		}
	}

	update_option( 'eco_event_display_taxonomy_migration', $migration_version, false );
}
add_action( 'admin_init', 'eco_migrate_event_display_taxonomies', 20 );
