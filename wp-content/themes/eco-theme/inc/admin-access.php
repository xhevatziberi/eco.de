<?php
/**
 * Administrative access helpers.
 *
 * @package eco-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether a user has special access to site-structure editing.
 *
 * User ID 1 (eco_admin) always has access. Additional users are selected in:
 * Settings > eco Settings > Users with Special Access.
 *
 * @param int|null $user_id Optional user ID. Defaults to the current user.
 * @return bool
 */
function eco_user_has_special_access( $user_id = null ): bool {
	if ( null === $user_id ) {
		$user_id = get_current_user_id();
	}

	$user_id = absint( $user_id );

	if ( ! $user_id ) {
		return false;
	}

	// eco_admin. Keep this user permanently allowed even if the SCF field is empty.
	if ( 1 === $user_id ) {
		return true;
	}

	if ( ! function_exists( 'get_field' ) ) {
		return false;
	}

	$allowed_users = get_field( 'users_with_special_access', 'option' );

	if ( empty( $allowed_users ) || ! is_array( $allowed_users ) ) {
		return false;
	}

	$allowed_user_ids = array_filter(
		array_map(
			static function ( $user ) {
				if ( $user instanceof WP_User ) {
					return absint( $user->ID );
				}

				if ( is_array( $user ) && ! empty( $user['ID'] ) ) {
					return absint( $user['ID'] );
				}

				return absint( $user );
			},
			$allowed_users
		)
	);

	return in_array( $user_id, $allowed_user_ids, true );
}
