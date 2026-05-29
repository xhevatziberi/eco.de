<?php
/**
 * Hide Elementor LOCAL templates tagged "internal" from the library modal
 * for non-admins by filtering the REST response.
 */
add_filter(
	'rest_post_dispatch',
	function ( $response, WP_REST_Server $server, WP_REST_Request $request ) {

		// Only touch Elementor's local templates endpoint
		if ( $request->get_route() !== '/elementor/v1/template-library/templates' ) {
			return $response;
		}

		// Only when source=local
		if ( $request->get_param( 'source' ) !== 'local' ) {
			return $response;
		}

		// Let admins see everything
		if ( current_user_can( 'administrator' ) ) {
			// return $response;
		}

		// Safety: bail on errors and non-response objects
		if ( is_wp_error( $response ) || ! ( $response instanceof WP_REST_Response ) ) {
			return $response;
		}

		$data = $response->get_data();

		if ( isset( $data['templates'] ) && is_array( $data['templates'] ) ) {
			foreach ( $data['templates'] as $i => $t ) {
				$post_id = isset( $t['template_id'] ) ? (int) $t['template_id'] : 0;
				if ( $post_id && has_term( 'internal', 'elementor_library_category', $post_id ) ) {
					unset( $data['templates'][ $i ] );
				}
			}
			// Re-index so the JSON is clean
			$data['templates'] = array_values( $data['templates'] );
			$response->set_data( $data );
		}

		return $response;
	},
	10,
	3
);

