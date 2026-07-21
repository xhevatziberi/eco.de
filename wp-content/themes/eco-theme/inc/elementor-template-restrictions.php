<?php
/**
 * Elementor template restrictions.
 *
 * Only the user with the username "eco_admin" may edit or delete
 * Elementor templates.
 *
 * Other users may still edit regular posts and pages with Elementor.
 *
 * @package eco-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether a user may manage Elementor templates.
 *
 * @param int|null $user_id Optional user ID. Defaults to the current user.
 *
 * @return bool
 */
function eco_user_can_manage_elementor_templates( $user_id = null ) {
	if ( null === $user_id ) {
		$user_id = get_current_user_id();
	}

	$user = get_userdata( $user_id );

	return $user instanceof WP_User
		&& 'eco_admin' === $user->user_login;
}

/**
 * Prevent every user except "eco_admin" from editing or deleting
 * Elementor templates.
 *
 * Elementor templates use the "elementor_library" post type.
 *
 * @param string[] $caps    Required primitive capabilities.
 * @param string   $cap     Requested capability.
 * @param int      $user_id User ID.
 * @param mixed[]  $args    Additional capability arguments.
 *
 * @return string[]
 */
function eco_restrict_elementor_template_capabilities( $caps, $cap, $user_id, $args ) {
	$restricted_capabilities = array(
		'edit_post',
		'delete_post',
	);

	if ( ! in_array( $cap, $restricted_capabilities, true ) ) {
		return $caps;
	}

	$post_id = isset( $args[0] ) ? absint( $args[0] ) : 0;

	if ( ! $post_id ) {
		return $caps;
	}

	$post = get_post( $post_id );

	if ( ! $post || 'elementor_library' !== $post->post_type ) {
		return $caps;
	}

	if ( ! eco_user_can_manage_elementor_templates( $user_id ) ) {
		return array( 'do_not_allow' );
	}

	return $caps;
}
add_filter(
	'map_meta_cap',
	'eco_restrict_elementor_template_capabilities',
	10,
	4
);

/**
 * Hide Elementor Theme Builder edit handles inside the preview iframe.
 *
 * Removes controls such as:
 * - Edit Single Post
 * - Edit Header
 * - Edit Footer
 * - Edit Archive
 */
function eco_hide_elementor_template_edit_handles() {
	if ( eco_user_can_manage_elementor_templates() ) {
		return;
	}

	wp_register_style(
		'eco-elementor-template-restrictions',
		false,
		array(),
		null
	);

	wp_enqueue_style( 'eco-elementor-template-restrictions' );

	wp_add_inline_style(
		'eco-elementor-template-restrictions',
		'
		/* Elementor Theme Builder document edit handles. */
		.elementor-document-handle,
		.elementor-document-handle *,
		.elementor-document-handle:hover,
		.elementor-document-handle:focus,
		.elementor-document-handle:focus-within {
			display: none !important;
			visibility: hidden !important;
			opacity: 0 !important;
			pointer-events: none !important;
		}

		/* Older Elementor edit-handle selectors. */
		.elementor-editor-element-edit,
		.elementor-editor-element-edit-mode {
			display: none !important;
			visibility: hidden !important;
			opacity: 0 !important;
			pointer-events: none !important;
		}
		'
	);
}
add_action(
	'elementor/preview/enqueue_styles',
	'eco_hide_elementor_template_edit_handles'
);

/**
 * Hide the Elementor Templates menu for every user except "eco_admin".
 */
function eco_hide_elementor_templates_admin_menu() {
	if ( eco_user_can_manage_elementor_templates() ) {
		return;
	}

	remove_menu_page( 'edit.php?post_type=elementor_library' );
}
add_action(
	'admin_menu',
	'eco_hide_elementor_templates_admin_menu',
	999
);

/**
 * Remove Elementor template row actions for every user except "eco_admin".
 *
 * @param array   $actions Post row actions.
 * @param WP_Post $post    Current post.
 *
 * @return array
 */
function eco_remove_elementor_template_row_actions( $actions, $post ) {
	if (
		! $post instanceof WP_Post
		|| 'elementor_library' !== $post->post_type
		|| eco_user_can_manage_elementor_templates()
	) {
		return $actions;
	}

	unset(
		$actions['edit'],
		$actions['inline hide-if-no-js'],
		$actions['elementor'],
		$actions['trash']
	);

	return $actions;
}
add_filter(
	'post_row_actions',
	'eco_remove_elementor_template_row_actions',
	10,
	2
);