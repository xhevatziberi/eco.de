<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if a URL points to an SVG file.
 */
function eco_theme_is_svg_url( $url ) {
	if ( empty( $url ) || ! is_string( $url ) ) {
		return false;
	}

	$path = wp_parse_url( $url, PHP_URL_PATH );

	return is_string( $path ) && strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) === 'svg';
}

/**
 * Convert a local WordPress URL to an absolute filesystem path.
 */
function eco_theme_get_local_path_from_url( $url ) {
	if ( empty( $url ) || ! is_string( $url ) ) {
		return '';
	}

	$uploads = wp_get_upload_dir();

	if ( ! empty( $uploads['baseurl'] ) && strpos( $url, $uploads['baseurl'] ) === 0 ) {
		$relative = ltrim( str_replace( $uploads['baseurl'], '', $url ), '/' );
		$path     = trailingslashit( $uploads['basedir'] ) . $relative;

		return file_exists( $path ) ? $path : '';
	}

	$site_url = site_url();

	if ( strpos( $url, $site_url ) === 0 ) {
		$relative = ltrim( str_replace( $site_url, '', $url ), '/' );
		$path     = ABSPATH . $relative;

		return file_exists( $path ) ? $path : '';
	}

	return '';
}

/**
 * Shared SVG allowlist.
 */
function eco_theme_svg_allowed_html() {
	return array(
		'svg' => array(
			'xmlns'           => true,
			'width'           => true,
			'height'          => true,
			'viewBox'         => true,
			'viewbox'         => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'class'           => true,
			'aria-hidden'     => true,
			'focusable'       => true,
			'role'            => true,
		),
		'g' => array(
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'clip-path'       => true,
			'transform'       => true,
		),
		'path' => array(
			'd'               => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
		),
		'rect' => array(
			'x'               => true,
			'y'               => true,
			'rx'              => true,
			'ry'              => true,
			'width'           => true,
			'height'          => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
		),
		'circle' => array(
			'cx'              => true,
			'cy'              => true,
			'r'               => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
		),
		'line' => array(
			'x1'              => true,
			'y1'              => true,
			'x2'              => true,
			'y2'              => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
		),
		'polyline' => array(
			'points'          => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
		),
		'polygon' => array(
			'points'          => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
		),
	);
}

/**
 * Inline a local SVG URL.
 *
 * @param string $url SVG URL.
 * @param string $extra_class Extra class added to the SVG.
 *
 * @return string Safe inline SVG markup.
 */
function eco_theme_get_inline_svg_from_url( $url, $extra_class = '' ) {
	if ( ! eco_theme_is_svg_url( $url ) ) {
		return '';
	}

	$path = eco_theme_get_local_path_from_url( $url );

	if ( empty( $path ) || ! file_exists( $path ) ) {
		return '';
	}

	$svg = file_get_contents( $path );

	if ( empty( $svg ) || stripos( $svg, '<svg' ) === false ) {
		return '';
	}

	$extra_class = trim( sanitize_html_class( $extra_class ) );

	$svg = preg_replace_callback(
		'/<svg\b([^>]*)>/i',
		function( $matches ) use ( $extra_class ) {
			$attrs = $matches[1];

			if ( preg_match( '/\sclass=("|\')(.*?)\1/i', $attrs, $class_match ) ) {
				$classes = trim( $class_match[2] . ' ' . $extra_class );

				$attrs = preg_replace(
					'/\sclass=("|\')(.*?)\1/i',
					' class="' . esc_attr( $classes ) . '"',
					$attrs,
					1
				);
			} elseif ( ! empty( $extra_class ) ) {
				$attrs .= ' class="' . esc_attr( $extra_class ) . '"';
			}

			if ( stripos( $attrs, 'aria-hidden=' ) === false ) {
				$attrs .= ' aria-hidden="true"';
			}

			if ( stripos( $attrs, 'focusable=' ) === false ) {
				$attrs .= ' focusable="false"';
			}

			return '<svg' . $attrs . '>';
		},
		$svg,
		1
	);

	return wp_kses( $svg, eco_theme_svg_allowed_html() );
}

/**
 * Render icon URL.
 *
 * If SVG, output inline SVG.
 * If image, output normal img.
 */
function eco_theme_render_icon_url( $url, $svg_class = '', $img_class = '', $echo = true ) {
	if ( empty( $url ) || ! is_string( $url ) ) {
		return '';
	}

	$output = '';

	if ( eco_theme_is_svg_url( $url ) ) {
		$svg = eco_theme_get_inline_svg_from_url( $url, $svg_class );

		if ( ! empty( $svg ) ) {
			$output = $svg;
		}
	}

	if ( empty( $output ) ) {
		$output = sprintf(
			'<img class="%s" src="%s" alt="" loading="lazy">',
			esc_attr( $img_class ),
			esc_url( $url )
		);
	}

	if ( $echo ) {
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	return $output;
}