<?php get_header(); ?>


<div id="container" class="clearfix layout_overall">
	<div id="main" class="layout_left">
        <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
        <div class="topbox"></div>
        
		<div class="panel clearfix">
            <?php the_content(); ?>
		</div>
		<?php endwhile;?>
        
	</div>
	<?php get_sidebar();?>
    <div id="pagination"  class='layout_left'></div>   
    <?php wp_reset_query(); ?>
</div>

<?php get_footer(); ?>