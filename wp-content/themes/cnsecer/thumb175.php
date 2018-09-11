<?php $posts = query_posts($query_string . '&orderby=date&showposts=16'); ?>   
<?php if( $posts ) : ?>    
                               
	<?php foreach( $posts as $post ) : setup_postdata( $post ); ?> 
	<!-- 输出开始-->
    <div class="col-md-3">
  		<div class="project">
			<div class="thumb ">
				<a href="<?php the_permalink() ?>" target="_blank"><img src="<?php post_thumbnail_src();?>" alt="缩略图" class="img-rounded"></a>
			</div>
			<div class="ftitle">
				<?php echo $str;?>
				<a href="<?php the_permalink() ?>" target="_blank"><?php echo mb_strimwidth(get_the_title(), 0, 23,"...") ?></a>
			</div>
		</div>
    </div>
    <!-- 输出结束 -->

	<?php endforeach; ?>                                          
<?php endif; ?>