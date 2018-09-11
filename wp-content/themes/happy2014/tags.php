<?php 
/* 
Template Name: Tags 
*/ 
?> 
<?php get_header(); ?> 

<div class="container global">
	<div class="row">
		<div class="col-md-9">
			<div class="tags">
			<div class="tit01">
				<h1>标签云</h1>
			</div>
			<div class="con">
				<?php wp_tag_cloud('smallest=20&largest=35&unit=px&number=0&order=rand&orderby=count');?> 
			</div>
			</div>

		</div>
		<div class="col-md-3 hidden-xs"><?php get_sidebar(); ?> </div>
	</div>
</div>


<?php get_footer(); ?> 
