<?php
/**
 * Single Event template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_stylesheet_directory() . '/inc/event-helpers.php';

get_header();

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
