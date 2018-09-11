<?php get_header(); ?>

<!-- 内容开始 -->
<div class="container">
<div class="category">

  <div class="row">
    <div class="col-md-12">
      <div class="cate-title"><span>分类目录</span></div>
    </div>    
  </div>

  <div class="row">
    <div class="col-md-9">

		<?php $posts = query_posts($query_string . '&orderby=date&showposts=7'); ?>         
		<?php if( $posts ) : ?>                                      
			<?php foreach( $posts as $post ) : setup_postdata( $post ); ?> 
		      <!--cate-main begin-->
		      <div class="cate-main">
		        <div class="row">				
					  <!--cate-article begin-->
					  <div class="cate-article">
					    <div class="row">
					      <!--thumb begin-->
					      <div class="col-md-3 hidden-xs">
					        <div class="thumb">
					          <a href="#"><img src="<?php post_thumbnail_src();?>" alt="缩略图" class="img-rounded"></a>
					        </div>
					      </div>
					      <!--thumb end-->
					      <!--content begin-->
					      <div class="col-md-9">
					        <div class="cate-article-content">
					          <div class="hcontent">
					            <a href="<?php the_permalink() ?>" target="_blank"><?php echo mb_strimwidth(get_the_title(), 0, 43,"...") ?></a>
					          </div>
					          <div class="fcontent">
					            <p><?php echo mb_strimwidth(strip_tags($post->post_content), 0,550,"..."); ?></p>
					          </div>
					        </div>
					      </div>
					      <!--content end-->
					    </div>
					  </div>
					  <!--cate-article end-->  
				</div>
		      </div>
		      <!--cate-main end-->
			<?php endforeach; ?>                                          
		<?php endif; ?>

  	</div>
	<div class="col-md-3 hidden-sm hidden-xs">
		<?php get_sidebar(); ?>
	</div>
  </div>
</div>
<div class="navigation"><?php pagination($query_string);  ?></div>
</div>
<?php get_footer(); ?>