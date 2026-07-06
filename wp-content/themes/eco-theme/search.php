<?php
/**
 * Search results override (eco Search UI)
 */

get_header();
?>

<div class="page-wrapper eco-search-scope">
	<div class="elementor-section elementor-section-boxed">
		<div class="elementor-container">
			<article id="eco-search-results" class="page type-page status-publish">
				<div class="page-content clearfix">
					<?php echo do_shortcode('[eco_search_page engine="default" per_page="10"]'); ?>
				</div>
			</article>
		</div>
	</div>
</div>

<?php get_footer(); ?>
