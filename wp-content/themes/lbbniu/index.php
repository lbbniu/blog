<?php
get_header();
?>
<div class="content-wrap">
	<div class="content">
	<div id="wowslider-container1">
	<div class="ws_images">
	<!--719X97-->
	<?php if (function_exists('get_lbb_most_viewed')): ?><ul><?php get_lbb_most_viewed('post',10); ?></ul><?php endif; ?>
	</div>

<div class="ws_thumbs">
		<div>	
		<?php if (function_exists('get_lbb_most_viewed1')):  get_lbb_most_viewed1('post',10);  endif; ?>
		</div>
	</div>
	<div class="ws_shadow">
	</div>
</div>
	
	<script type="text/javascript" src="<?php bloginfo('template_url');?>/js/slider.js"></script>	
<?php
	if ( have_posts() ) :
		// Start the Loop.
		while ( have_posts() ) : the_post();

			/*
			 * Include the post format-specific template for the content. If you want to
			 * use this in a child theme, then include a file called called content-___.php
			 * (where ___ is the post format) and that will be used instead.
			 */
			get_template_part( 'content', get_post_format() );

		endwhile;
		// Previous/next post navigation.
		lbbniu_paging_nav();

	else :
		// If no content, include the "No posts found" template.
		//get_template_part( 'content', 'none' );

	endif;
?>
	</div>
</div>

<?php 
get_sidebar();
get_footer();
?>