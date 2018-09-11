<?php get_header(); ?>

<!-- 内容开始 -->

<div class="container global">
<div class="category">
<div class="row">

	<!--main begin-->
	<div class="col-md-9">

		<!-- 最新文章开始 -->
		<div class="tit">
			<h1>
			<?php if (function_exists('cate_nav')) cate_nav(); ?>
			</h1>
		</div>

		
		<div class="main">
		<?php $posts = query_posts($query_string . '&orderby=date&showposts=9'); ?>       
		<?php if( $posts ) : ?>                                      
			<?php foreach( $posts as $post ) : setup_postdata( $post ); ?> 
			<div class="content">
			<div class="row">
				<div class="col-md-4 hidden-xs">
					<div class="thumb">
						<a href="<?php the_permalink() ?>" target="_blank"><img src="<?php post_thumbnail_src();?>" alt='<?php the_title(); ?>' class="img-responsive img-rounded"></a>
					</div>
				</div>
				<div class="col-md-8 ">
					<dl>
						<dt>
							<a href="<?php the_permalink() ?>" target="_blank"><?php the_title(); ?></a>
						</dt>
						<dd>
							<p><?php echo mb_strimwidth(strip_tags($post->post_content), 0,350,"..."); ?></p>
						</dd>
						<div class="info hidden-xs">
							<p><a href="" title=""><?php the_author();?></a>@<?php the_category(', '); ?>  <span class="glyphicon glyphicon-calendar"><?php the_time('20y-m-d')?></span> </p>
							 <div class="tags"><?php the_tags('<span class="glyphicon glyphicon-tags"></span>', ', ', ','); ?></div> 
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
		<?php get_sidebar(); ?>
	</div>
	<!-- sidebar end -->
</div>
</div>

</div>

<?php get_footer(); ?>