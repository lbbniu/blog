<?php get_header(); ?>

<!-- 内容开始 -->
<div class="container global">
<div class="row">

	<!--main begin-->
	<div class="col-md-9">

		<!--幻灯开始-->
		<div class="tit">
			<h1>热门推荐</h1>
		</div>
		<?php include(TEMPLATEPATH . '/slider.php'); ?>
		<!--幻灯结束-->

		<!-- 最新文章开始 -->
		<div class="tit">
			<h1>最新文章</h1>
		</div>
		<div class="main">
		<?php $posts = get_posts( "numberposts=8" ); ?>      
		<?php if( $posts ) : ?>                                      
			<?php foreach( $posts as $post ) : setup_postdata( $post ); ?> 
			<div class="content">
			<div class="row">
				<div class="col-md-4 hidden-xs">
					<div class="thumb">
						<a href="<?php the_permalink() ?>" target="_blank"><img src="<?php post_thumbnail_src();?>" alt='<?php echo mb_strimwidth(get_the_title(), 0, 28,"...") ?>' class="img-responsive img-rounded"></a>
					</div>
				</div>
				<div class="col-md-8">
					<dl>
						<dt>
							<a href="<?php the_permalink() ?>" target="_blank"><?php the_title(); ?></a>
						</dt>
						<dd>
							<p><?php echo mb_strimwidth(strip_tags($post->post_content), 0,350,"..."); ?></p>
						</dd>
						<div class="info hidden-xs">
					          	<a href="<?php the_permalink() ?>" title="<?php  the_title();?>"><?php the_permalink() ?></a> <span><?php the_time('20y-m-d')?></span>
						 </div>
					</dl>
				</div>
			</div>
			</div>
			<?php endforeach; ?>                                          
		<?php endif; ?>		
		</div>		

		<div class="navigation ">
			<ul class="pager">
			  <li class="previous"><?php previous_posts_link( __( '&larr; Previous ', 0 ) ) ?></li>
			  <li class="next"><?php next_posts_link( __( 'Next  &rarr;', 0 ) ) ?></li>
			</ul>
		</div>
		
		<!-- 最新文章结束 -->


	</div>
	<!--main end-->
	
	<!--sidebar begin-->
	<div class="col-md-3 hidden-xs">
		<?php get_sidebar("index"); ?>
	</div>
	<!-- sidebar end -->
</div>

</div>
<!-- 内容结束 -->

<?php get_footer(); ?>