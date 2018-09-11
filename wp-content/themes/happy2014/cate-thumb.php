<?php get_header(); ?>
<style>
	body{background-color:#777;}
</style>

<div class="cate-thumb">
	
	<div class="container">

	<div class="main">
		<div class="title">
			<span>原创主题</span> 安全者博客原创模板,兼容各大浏览器,响应布局
		</div>
		<div class="row">
			<?php include(TEMPLATEPATH . '/thumb250-a.php'); ?>
		</div>
	</div>


	<div class="main">
		<div class="title">
			<span>推荐主题</span> 精品wordpress主题推荐
		</div>
		<div class="row">
			<?php include(TEMPLATEPATH . '/thumb250-b.php'); ?>
		</div>
		<div class="navigation ">
			<ul class="pager">
			  <li class="previous"><?php previous_posts_link( __( '&larr; Previous ', 0 ) ) ?></li>
			  <li class="next"><?php next_posts_link( __( 'Next  &rarr;', 0 ) ) ?></li>
			</ul>
		</div>		
	</div>

	</div>
</div>


<?php get_footer(); ?>	