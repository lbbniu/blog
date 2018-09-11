<?php get_header(); ?>

<div class="container">
	<div class="search-page">
		
	<?php $posts = query_posts($query_string . '&orderby=date&showposts=6'); ?>         
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
				            <p><?php echo mb_strimwidth(strip_tags($post->post_content), 0,450,"..."); ?></p>
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
	<div class="navigation"><?php pagination($query_string);  ?></div>
	</div>
</div>

<?php get_footer(); ?>
