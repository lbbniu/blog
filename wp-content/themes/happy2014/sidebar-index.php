<div class="sidebar">
	<div class="tit01">
		<h1>热评文章</h1>
	</div>
		<div class="s1">
			<?php popular_posts(); ?>
		</div>
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
		<h1>最新评论</h1>
	</div>
		<div class="s3">
			<?php get_recent_comments(); ?>
		</div>

</div>