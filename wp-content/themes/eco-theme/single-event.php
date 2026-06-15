<?php
/**
 * Single Event template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_stylesheet_directory() . '/inc/event-helpers.php';

eco_event_maybe_serve_ical();

$css_file = get_stylesheet_directory() . '/assets/css/event-single.css';
$js_file  = get_stylesheet_directory() . '/assets/js/event-single.js';

wp_enqueue_style(
	'eco-event-single',
	get_stylesheet_directory_uri() . '/assets/css/event-single.css',
	[],
	file_exists( $css_file ) ? filemtime( $css_file ) : null
);

wp_enqueue_script(
	'eco-event-single',
	get_stylesheet_directory_uri() . '/assets/js/event-single.js',
	[],
	file_exists( $js_file ) ? filemtime( $js_file ) : null,
	true
);

get_header();

/**
 * Elementor editor fix.
 *
 * Elementor needs the_content() to work, but we don't want to use it for rendering the event content in the editor,
 * because it would add unwanted markup and styles.
 * So we check if we're in Elementor preview mode, and if so, we just output the content without the header and footer.
 */
if ( isset( $_GET['elementor-preview'] ) ) {
	while ( have_posts() ) {
		the_post();
		the_content();
	}

	get_footer();
	return;
}

while ( have_posts() ) :
	the_post();

	$layout = eco_event_get_field( 'event_layout', get_the_ID(), 'small' );
	$layout = $layout === 'big' ? 'big' : 'small';
	?>
	<main id="primary" class="eco-event-single eco-event-single--<?php echo esc_attr( $layout ); ?>" style="--eco-event-accent: <?php echo esc_attr( eco_event_get_color() ); ?>;">
		<?php get_template_part( 'template-parts/event/layout', $layout ); ?>
	</main>
	<?php
endwhile;

get_footer();