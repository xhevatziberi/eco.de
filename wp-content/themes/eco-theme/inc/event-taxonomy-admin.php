<?php
/**
 * Event taxonomy admin UI.
 *
 * Event Source, Event Series, Event Label and Event Format are edited through
 * SCF fields in the Event Options panel. Hide the duplicate native WordPress taxonomy
 * controls so editors do not have two competing inputs for the same terms.
 *
 * Quick Edit and the taxonomy management screens remain available.
 *
 * @package eco-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove the classic-editor/native taxonomy meta boxes from Event edit screens.
 */
function eco_hide_duplicate_event_taxonomy_meta_boxes(): void {
	remove_meta_box( 'event-sourcediv', 'event', 'side' );
	remove_meta_box( 'event-seriesdiv', 'event', 'side' );
	remove_meta_box( 'event-labeldiv', 'event', 'side' );
	remove_meta_box( 'tagsdiv-event-label', 'event', 'side' );
	remove_meta_box( 'event-formatdiv', 'event', 'side' );
	remove_meta_box( 'tagsdiv-event-format', 'event', 'side' );
}
add_action(
	'add_meta_boxes_event',
	'eco_hide_duplicate_event_taxonomy_meta_boxes',
	100
);

/**
 * Remove the equivalent taxonomy panels from the block editor sidebar.
 *
 * The taxonomies stay registered in REST so WPML, Quick Edit and other
 * integrations can continue to use them normally.
 */
function eco_hide_duplicate_event_taxonomy_block_editor_panels(): void {
	$screen = get_current_screen();

	if ( ! $screen || 'event' !== $screen->post_type ) {
		return;
	}

	$script = <<<'JS'
wp.domReady(function () {
	if (!window.wp || !wp.data || typeof wp.data.dispatch !== 'function') {
		return;
	}

	var editPostStore = wp.data.dispatch('core/edit-post');

	if (!editPostStore || typeof editPostStore.removeEditorPanel !== 'function') {
		return;
	}

	editPostStore.removeEditorPanel('taxonomy-panel-event-source');
	editPostStore.removeEditorPanel('taxonomy-panel-event-series');
	editPostStore.removeEditorPanel('taxonomy-panel-event-label');
	editPostStore.removeEditorPanel('taxonomy-panel-event-format');
});
JS;

	wp_add_inline_script( 'wp-edit-post', $script, 'after' );
}
add_action(
	'enqueue_block_editor_assets',
	'eco_hide_duplicate_event_taxonomy_block_editor_panels',
	100
);
