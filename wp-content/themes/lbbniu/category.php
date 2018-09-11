<?php
get_header(); ?>

		<div class="content-wrap">
	<div class="content">
		<header class="archive-header"> 
			<h1><i class="fa fa-folder-open"></i><?php printf( __( ' &nbsp;分类： %s', 'lbbniu' ), single_cat_title( '', false ) ); ?> 
				<!--<a title="订阅<?php echo single_cat_title( '', false ); ?>" target="_blank" href="<?php echo get_category_feed_link(); ?>"><i class="rss fa fa-rss"></i></a>-->
				<?php 
				$category = get_category( get_query_var('cat') ); 
				if ( ! empty( $category ) ) 
				echo '<a title="订阅'.single_cat_title( '', false ).'" href="' . get_category_feed_link( $category->cat_ID ) . '" target="_blank" rel="nofollow"><i class="rss fa fa-rss"></i></a>'; 
				?>
			
			</h1>
		</header>

		<?php if ( have_posts() ) : ?>
		<?php
			// Start the Loop.
			while ( have_posts() ) : the_post();

			//get_template_part( 'content', get_post_format() );
			//发布时间
			$show_time_age = human_time_diff( get_the_time('U'), current_time('timestamp') );
		?>
		<article class="excerpt">
			<header><h2><a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>" target="_blank"><?php the_title(); ?>  
			<?php if(get_the_time('U')> (time()-86400)) : ?>
			<img src="<?php bloginfo('template_url');?>/img/new.gif" alt="24小时内最新"> 
			<?php endif; ?>
			</a></h2>
			</header>
			<div class="focus"><a href="<?php the_permalink(); ?>" target="_blank"><img alt="<?php the_title(); ?>" src="<?php bloginfo('template_url');?>/timthumb.php?src=<?php post_thumbnail_src(); ?>&h=123&w=200&q=90&zc=1&ct=1" class="thumb"></a></div>
					<span class="note">
						<?php the_excerpt(); ?>
					</span>
		<p class="auth-span">
				<span class="muted"><i class="fa fa-user"></i> <?php  the_author_posts_link(); ?></span>
				<span class="muted"><i class="fa fa-clock-o"></i> <?php echo $show_time_age; ?>前</span>	<span class="muted"><i class="fa fa-eye"></i> <?php if(function_exists('the_views')) { the_views(); } ?></span>	<span class="muted"><span class="muted"><i class="fa fa-comments-o"></i><a href="<?php the_permalink(); ?>#comments"> <?php comments_number(' 0', ' 1', ' %' );?></a></span>
					</span><span class="muted">
		<a class="action <?php if(isset($_COOKIE['bigfa_ding_'.$post->ID])) echo ' actived';?>" id="Addlike" data-id="<?php the_ID(); ?>" data-action="ding" href="javascript:;"><i class="fa fa-heart-o"></i><span class="count"><?php if( get_post_meta($post->ID,'bigfa_ding',true) ){            
                    echo get_post_meta($post->ID,'bigfa_ding',true);
         } else {
            echo '0';
         }?></span>喜欢</a></span></p>
		</article>
		
	 <?php

			endwhile;
			lbbniu_paging_nav();
		else :
			// If no content, include the "No posts found" template.
			//get_template_part( 'content', 'none' );

		endif;
	?>

	</div>
	</div>
<?php
get_sidebar();
get_footer();
?>
