<?php get_header(); ?>
<div class="container global">
	<div class="search">
		<!-- search form begin -->
		<div class="top">
			<form class="navbar-form navbar-left" action="<?php echo home_url( '/' ); ?>" method="post"  role="search">
			  <div class="input-group">
		        <input type="text" name="s" class="form-control" x-webkit-speech placeholder="input your question here">
		        <span class="input-group-btn">
		        	<button class="btn btn-success" type="button">Search</button>
		        </span>
		      </div>
		     </form>			
		</div>
		<!-- search form end -->
		<div class="search-page">	
		<?php $posts = query_posts($query_string . '&orderby=date&showposts=5'); ?>         
		<?php if( $posts ) : ?>                                      
			<?php foreach( $posts as $post ) : setup_postdata( $post ); ?> 
				<div class="main">
		        <div class="row border">				
					    <div class="row">
					      <!--thumb begin-->
					      <div class="col-md-3 hidden-xs">
					        <div class="search-thumb">
					          <a href="#"><img src="<?php post_thumbnail_src();?>" alt="<?php the_title(); ?>" class="img-rounded img-responsive"></a>
					        </div>
					      </div>
					      <!--thumb end-->
					      <!--content begin-->
					      <div class="col-md-9 ">
					        <div class="search-content">
					          <div class="hcontent">
					            <a href="<?php the_permalink() ?>" target="_blank"><?php  the_title();/*echo mb_strimwidth(get_the_title(), 0, 43,"...")*/ ?></a>
					          </div>
					          <div class="fcontent">
					            <p><?php echo mb_strimwidth(strip_tags($post->post_content), 0,450,"..."); ?></p>
					          </div>
					          <div class="flink">
					          	<a href="<?php the_permalink() ?>" title="<?php  the_title();?>"><?php the_permalink() ?></a> <span><?php the_time('20y-m-d')?></span>
					          </div>
					        </div>
					      </div>
					      <!--content end-->
					    </div>
				</div>
				</div>
			<?php endforeach; ?>                                          
		<?php endif; ?>
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