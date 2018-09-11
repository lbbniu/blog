<?php
get_header(); ?>
			
<div class="content-wrap">
	<div class="content">		
		<?php
			// Start the Loop.
			if (have_posts()) : the_post(); update_post_caches($posts);
				//get_template_part( 'content', get_post_format() );
				$cate =  get_the_category();
				$cat =$cate[0];
				$category_link = get_category_link( $cat->term_id ); //分类地址
		?>
		<header class="article-header">
			<h1 class="article-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
			<div class="meta">
				<span class="muted single_cat"><i class="fa fa-bookmark-o"></i><a href="<?php echo $category_link;?>"> <?php echo $cat->name; ?></a></span>
				<span class="muted single_auth"><i class="fa fa-user"></i><a href="<?php the_author_link(); ?>"> <?php the_author(); ?></a></span>
				<span class="muted single_comment"><i class="fa fa-comments-o"></i><a href="<?php the_permalink(); ?>#comments"><?php comments_number(' 0条评论', ' 1条评论', ' %条评论' );?></a></span>					
				<span class="muted"><i class="fa fa-calendar"></i> <?php the_time('Y年n月j日'); ?></span>
				<span class="muted"><i class="fa fa-eye"></i> <?php if(function_exists('the_views')) { the_views(); } ?></span>
			</div>
		</header>		
		<div class="banner banner-post">
			<script src="http://www.cloudad.asia/page/s.php?s=29921&w=728&h=90"></script>
		</div>		
		<article class="article-content">
		<?php the_content(); ?>
		<p>转载请注明：<?php the_category(',');?> » <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>     
		<div class="article-social">
			<a href="javascript:;" data-action="ding" data-id="<?php the_ID(); ?>" id="Addlike" class="action <?php if(isset($_COOKIE['bigfa_ding_'.$post->ID])) echo ' actived';?>"><i class="fa fa-heart-o"></i>喜欢 (<span class="count"><?php if( get_post_meta($post->ID,'bigfa_ding',true) ){ echo get_post_meta($post->ID,'bigfa_ding',true);} else {echo '0';}?></span>)</a><span class="or">or</span>
			<span class="action action-share bdsharebuttonbox bdshare-button-style0-16" data-bd-bind="1409454860534">
				<i class="fa fa-share-alt"></i>分享 (<span class="bds_count" data-cmd="count" title="累计分享0次">0</span>)
				<div class="action-popover">
					<div class="popover top in">
						<div class="arrow"></div>
						<div class="popover-content">
							<a href="#" class="sinaweibo fa fa-weibo" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_qzone fa fa-star" data-cmd="qzone" title="分享到QQ空间"></a><a href="#" class="tencentweibo fa fa-tencent-weibo" data-cmd="tqq" title="分享到腾讯微博"></a><a href="#" class="qq fa fa-qq" data-cmd="sqq" title="分享到QQ好友"></a><a href="#" class="bds_renren fa fa-renren" data-cmd="renren" title="分享到人人网"></a><a href="#" class="bds_weixin fa fa-weixin" data-cmd="weixin" title="分享到微信"></a><a href="#" class="bds_more fa fa-ellipsis-h" data-cmd="more"></a>
					    </div>
					</div>		
				</div>
			</span>			
		</div>
		</article>
        <footer class="article-footer">
			<div class="article-tags"><?php the_tags('<i class="fa fa-tags"></i>',' ',''); ?><!--<a href="tag/新生.html" rel="tag">新生</a>--></div>
		</footer>
		<nav class="article-nav">
			
			<span class="article-nav-prev">
				<?php previous_post_link('<i class="fa fa-angle-double-left"></i> %link') ?>
		   </span>
			<span class="article-nav-next">
				<?php next_post_link('%link <i class="fa fa-angle-double-right"></i> ') ?> 
			</span>
		</nav>
		<?php
			// Previous/next post navigation.
			//lbbniu_post_nav();
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) {
				//comments_template();
			}
			endif;
		?>	
		<div class="related_top">
			<div class="related_posts">
				<ul class="related_img">
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
						<li class="related_box">
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" target="_blank">
						<img src="<?php bloginfo('template_url');?>/timthumb.php?src=<?php post_thumbnail_src(); ?>&h=110&w=185&q=90&zc=1&ct=1" alt="<?php the_title(); ?>">	
						<br><span class="r_title"><?php the_title(); ?></span></a>
						</li>	
					<?php $i++;
					} wp_reset_query();
					wp_reset_postdata();
				}
				if ( $i  == 0 )  echo '<div class="r_title">没有相关文章!</div>';
				?>		
				</ul>

				<div class="relates">
					<ul>
					<?php
					$post_num = 8;
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
						<?php if($i == 0): ?>
							<li><i class="fa fa-minus"></i><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
					    <?php else: ?>
							<li><i class="fa fa-angle-right"></i><a target="_blank" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
						<?php endif; ?>
						<?php $i++;
						} wp_reset_query();
						wp_reset_postdata();
					}
					?>		
					</ul>
				</div>
			</div>
		</div>
		<div id="comment-ad" class="banner banner-related">
			<script src="http://www.cloudad.asia/page/s.php?s=29920&w=728&h=90"></script>
		</div>		
		<div id="respond" class="no_webshot">
				<form action="http://blog.lbbniu.com/wp-comments-post.php" method="post" id="commentform">
					<div class="comt-title">
						<div class="comt-avatar pull-left">
							<img alt="" src="avatar/.png" class="avatar avatar-54 photo avatar-default" height="54" width="54">			
						</div>
						<div class="comt-author pull-left">发表我的评论</div>
						<a id="cancel-comment-reply-link" class="pull-right" href="javascript:;">取消评论</a>
					</div>
					
					<div class="comt">
						<div class="comt-box">
							<textarea placeholder="写点什么..." class="input-block-level comt-area" name="comment" id="comment" cols="100%" rows="3" tabindex="1" onkeydown="if(event.ctrlKey&&event.keyCode==13){document.getElementById('submit').click();return false};"></textarea>
							<div class="comt-ctrl">
								<button class="btn btn-primary pull-right" type="submit" name="submit" id="submit" tabindex="5"><i class="fa fa-check-square-o"></i> 提交评论</button>
								<div class="comt-tips pull-right"><input type="hidden" name="comment_post_ID" value="2347" id="comment_post_ID">
								<input type="hidden" name="comment_parent" id="comment_parent" value="0">
								</div>
								<span data-type="comment-insert-smilie" class="muted comt-smilie"><i class="fa fa-smile-o"></i> 表情</span>
								<span class="muted comt-mailme"><label for="comment_mail_notify" class="checkbox inline" style="padding-top:0"><input type="checkbox" name="comment_mail_notify" id="comment_mail_notify" value="comment_mail_notify" checked="checked">有人回复时邮件通知我</label></span>
							</div>
						</div>
					</div>		
				</form>
		</div>
	</div>
</div>

<?php
//get_sidebar( 'content' );
get_sidebar();
get_footer();
