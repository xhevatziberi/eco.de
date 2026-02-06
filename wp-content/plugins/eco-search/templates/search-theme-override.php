<?php
/**
 * Theme override for ALL WP searches (?s=...)
 * Uses Elementor boxed container structure.
 */
if (!defined('ABSPATH')) exit;

get_header();
?>

<div class="page-wrapper" id="eco-search-scope">
	<div class="elementor-section elementor-section-boxed">
		<div class="elementor-container">
			<main id="primary" class="site-main">
				<?php echo do_shortcode('[eco_search_page engine="default" per_page="10"]'); ?>
			</main>
		</div>
	</div>
</div>

<?php
get_footer();
