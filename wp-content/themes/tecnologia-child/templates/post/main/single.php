<?php if ( ! VamtamElementorBridge::is_build_with_elementor() ) : ?>
	<div class="limit-wrapper single-post-meta-wrapper">
		<div class="meta-top">
			<div class="meta-left has-author">
				<div class="meta-left-top with-separator">
				</div>
			</div>

			<?php if ( function_exists( 'sharing_display' ) ) : ?>
				<div class="meta-right">
					<?php get_template_part( 'templates/share' ); ?>
				</div>
			<?php endif ?>
		</div>
	</div>
<?php endif; ?>

<div class="post-content-outer single-post">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-media post-media-image">
			<div class='media-inner'>
				<?php the_post_thumbnail( 'full' ) ?>
			</div>
		</div>
	<?php endif; ?>

	<?php include locate_template( 'templates/post/content.php' ); ?>
	<div class="single-post-meta-bottom limit-wrapper">
		<?php get_template_part( 'templates/post/meta/tags' ); ?>
	</div>
</div>

