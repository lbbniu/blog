<div class="related_title"><h3>相关文章</h3></div>
<div class="related_posts">
<div class="row">
<?php
$post_num = 4;
$exclude_id = $post->ID;
$posttags = get_the_tags(); $i = 0;
if ( $i < $post_num ) {
	$cats = ''; foreach ( get_the_category() as $cat ) $cats .= $cat->cat_ID . ',';
	$args = array(
		'category__in' => explode(',', $cats),
		'post__not_in' => explode(',', $exclude_id),
		'caller_get_posts' => 1,
		'orderby' => 'comment_date',
		'posts_per_page' => $post_num - $i
	);
	query_posts($args);
	while( have_posts() ) { the_post(); ?>
		<div class="col-md-3 col-xs-3">
			<div class="r_pic">
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" target="_blank">
			<img src="<?php echo post_thumbnail_src(); ?>" alt="<?php the_title(); ?>" class="img-responsive img-rounded" />
			</a>
			</div>
			<div class="r_title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" target="_blank" rel="bookmark"><?php the_title(); ?></a></div>
		</div>
	</li>
	<?php $i++;
	} wp_reset_query();
}
if ( $i  == 0 )  echo '<div class="r_title">没有相关文章!</div>';
?>
</div>
</div>