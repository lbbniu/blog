
<?php
	$slug = stripslashes(get_option(''));
	$cat=get_category_by_slug($slug); //获取分类别名为 wordpress 的分类数据
	$cat_links=get_category_link($cat->term_id); // 通过$cat数组里面的分类id获取分类链接
?>
<?php $posts = get_posts( "category=($cat->term_id&numberposts=4" ); ?>      
<?php if( $posts ) : ?>                                      
	<?php foreach( $posts as $post ) : setup_postdata( $post ); ?> 
	<!-- 输出开始-->
    <div class="col-md-3 ">
    <div class="project">
		<div class="thumb ">
			<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>" target="_blank"><img src="<?php post_thumbnail_src();?>" alt="<?php the_title(); ?>" class="img-responsive img-rounded"></a>
		</div>
		<div class="ftitle">
			<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>" target="_blank"><?php echo mb_strimwidth(get_the_title(), 0, 33,"...") ?></a>
		</div>
    </div>
    </div>
    <!-- 输出结束 -->
	<?php endforeach; ?>                                          
<?php endif; ?>

