<div class="sidebar">
	<div class="tit01">
		<h1>相关文章</h1>
	</div>
		<div class="s1"><?php getRelatedPosts(); ?></div>
	
	<div class="tit01">
		<h1>广而告之</h1>
	</div>
		<div class="s2">
			<?php echo stripslashes(get_option('cnsecer_ads-250')); ?> 
		</div>
	<div class="tit01">
		<h1>标签云</h1>
	</div>
		<div class="s3">
			<div class="tagscloud">
			<?php wp_tag_cloud("smallest=15&largest=25&unit=px&number=30&orderby=count&order=DESC"); ?>
			</div>
		</div>
	<div class="tit01">
		<h1>随机文章</h1>
	</div>
		<div class="s4">
 
	    <?php query_posts('showposts=8&orderby=rand'); ?>  
	     
	    <?php while (have_posts()) : the_post(); ?>  
	        <dl> 
	        <dt><a title="<?php the_title(); ?>"  href="<?php the_permalink() ?>"><?php the_title(); ?></a></dt>  
	        </dl>
	    <?php endwhile;?>  	
		</div>
</div>