<article class="excerpt">
	<header>
		<?php 
			$cate =  get_the_category();
			$cat =$cate[0];
			$category_link = get_category_link( $cat->term_id ); //分类地址
		/*	if( function_exists('catch_that_image')&&catch_that_image()!='' ) {
				//图片存在时做点什么！
				$first_img = catch_that_image();
			}else{
				//图片不存在时做点什么！
				$first_img = get_bloginfo('template_url').'/img/yyc.jpg';
			}*/
			//发布时间
			$show_time_age = human_time_diff( get_the_time('U'), current_time('timestamp') );
		?>
		<a class="label label-important" href="<?php echo $category_link; ?>"><?php echo $cat->name; ?><i class="label-arrow"></i></a>
		<h2>
		<a target="_blank" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?>
			<?php if(get_the_time('U')> (time()-86400)) : ?>
			<img src="<?php bloginfo('template_url');?>/img/new.gif" alt="24小时内最新"> 
			<?php endif; ?>
		</a>
		</h2>
	</header>
	<div class="focus">
	<a target="_blank" href="<?php the_permalink(); ?>">
		<?php //the_post_thumbnail('thumbnail', array( 'class' => 'thumb' )); ?>
		<img class="thumb" src="<?php bloginfo('wpurl');?>/wp-content/themes/lbbniu/timthumb.php?src=<?php post_thumbnail_src(); ?>&h=123&w=200&q=90&zc=1&ct=1" alt="<?php the_title(); ?>">
	</a></div>
			<span class="note"> 
				<?php the_excerpt(100); ?>
			</span>
<p class="auth-span">
		<span class="muted"><i class="fa fa-user"></i> <?php  the_author_posts_link(); ?></span>
		<span class="muted"><i class="fa fa-clock-o"></i> <?php echo $show_time_age; ?>前</span>	<span class="muted"><i class="fa fa-eye"></i> <?php if(function_exists('the_views')) { the_views(); } ?></span>
		<span class="muted"><span class="muted"><i class="fa fa-comments-o"></i><a href="<?php the_permalink(); ?>#comments"> <?php comments_number(' 0', ' 1', ' %' );?></a></span>
			</span><span class="muted">
	<a href="javascript:;" data-action="ding" data-id="<?php the_ID(); ?>" id="Addlike" class="action <?php if(isset($_COOKIE['bigfa_ding_'.$post->ID])) echo ' actived';?>"><i class="fa fa-heart-o"></i>
	<span class="count"><?php if( get_post_meta($post->ID,'bigfa_ding',true) ){            
                    echo get_post_meta($post->ID,'bigfa_ding',true);
         } else {
            echo '0';
         }?></span>喜欢</a></span></p>

</article>
