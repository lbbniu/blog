<?php get_header(); ?>
<style>
	#container #ds-thread #ds-reset .ds-comments-info {display: inherit;}
	#container #ds-thread #ds-reset .ds-paginator {display: inherit !important;border-bottom:none;padding-bottom:5px;}
	#container #ds-thread #ds-reset .ds-paginator div.ds-border {margin-bottom:0;}
	#container #ds-thread #ds-reset .ds-comments .ds-post-placeholder {display: inherit;background-color:#F4F4F4;border-radius:5px;margin-top:0;padding: 20px 0;}
</style>

<div id="container" class="clearfix">
	<div id="main">
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		<div class="topbox">
			<div class="titlebox clearfix">
				<?php
					$author = get_post_meta($post->ID, 'username', true);
					if ( !empty($author) ) $avatar = "<img src='".get_post_meta($post->ID, 'avatar', true)."' width='24' height='24' class='avatar' />";
					else $avatar = get_avatar(get_the_author_meta('ID'),24);
					echo $avatar;
				?>
				<h1><?php the_title();?></h1>
				<div class="pagenav clearfix">
					<span class='hotkeys robots-nocontent'><!--googleoff: all-->查看键盘快捷键？<!--googleon: all--></span>
					<span class="prev" title="上一篇"><?php previous_post_link('%link'); ?></span>
					<span class="next" title="下一篇"><?php next_post_link('%link'); ?></span>
				</div>
			</div>
		</div>
		
		<ul class="post_nav clearfix">
			<li><a href="<?php echo home_url('/');?>">首页</a></li>
			<?php wp_nav_menu( array('theme_location'=>'home_menu', 'container'=>false, 'items_wrap'=>'%3$s', 'depth'=>1, 'fallback_cb'=>false) ); ?>
		</ul>
		
		<div class="panel clearfix share_root">
			<div class="panel_left">
				<?php $media_type = get_post_format(); ?>
				<?php if ($media_type == 'video') : ?>
				<div class="videobox">
					<div class="videoplayer" src="<?php VideoUrlParser::checkVideo( get_field('videourl') );?>" autoplay="true">
						<div class='img_wrap'><img class='tu' src="<?php VideoUrlParser::checkImage( get_field('img') );?>" /></div>
					</div>
					<div class="playbtn"></div>
					<div class="video_close"><!--googleoff: all-->收起视频<!--googleon: all--></div>
				</div>
				<?php elseif ($media_type == 'aside') : ?>
				<div class="asidebox">
					<?php the_content(); ?>
				</div>
				<?php elseif ($media_type == 'image') : ?>	
				<div class="imagebox">
				<?php
					$imgurl = get_post_meta($post->ID, 'imgurl', true);
					if ( !empty($imgurl) ) {
						$img_type = get_post_meta($post->ID, 'img_type', true);
						if ( empty($img_type) ) $img_type = array_pop( getImgMeta($imgurl) );	//array_pop取出数组最后的那个值 即类型
					} else $img_type = getAttachmentType( get_field('pic') );

					if ( !empty($imgurl) ) {
						$img = array();
						$img_w = get_post_meta($post->ID, 'img_w', true);
						$img_h = get_post_meta($post->ID, 'img_h', true);
						$img_w = $img_w?$img_w:440;
						$img_h = $img_h?$img_h:330;
						if ($img_w > 440) {
							if ($img_type == 'gif') $img[0] = $imgurl;	//如果是gif图 直接引用原图
							else $img[0] = get_bloginfo('template_url').'/TimThumb.php?w=440&src='.$imgurl;
							$img[1] = 440;
							$img[2] = (int)(440*$img_h/$img_w);
						} else {
							$img[0] = $imgurl;
							$img[1] = $img_w;
							$img[2] = $img_h;
						}
					} else {
						if ($img_type == 'gif') {
							$img = wp_get_attachment_image_src(get_field('pic'), 'full');
							if ($img[1] > 440) {
								$img[2] = (int)(440*$img[2]/$img[1]);
								$img[1] = 440;
							}
						} else $img = wp_get_attachment_image_src(get_field('pic'), 'medium');
					}
				?>
					<img class='tu' src='<?php echo $img[0];?>' width='<?php echo $img[1];?>' height='<?php echo $img[2];?>' alt='<?php the_title(); ?>' />
				</div>
				<?php endif; ?>
				
				<?php if ( is_active_sidebar('post_left_column') ) :?>	 
				<ul class='column_left column'>
					<?php dynamic_sidebar('post_left_column'); ?>
				</ul>
				<?php endif; ?>
				
				<div id="wumiiDisplayDiv"></div>
				<div class="comments">
					<div class="ds-thread" data-thread-key="<?php the_ID();?>" data-title="<?php the_title();?>" data-url="<?php the_permalink();?>" data-author-key="<?php global $authordata;echo $authordata?$authordata->ID:0;?>"></div>
					<a name="comments"></a>
				</div>
			</div>
			<div class='panel_right'>
				<div class="infobox">
					<?php
						//$author = get_post_meta($post->ID, 'username', true);
						if ( !empty($author) ) {
							$avatar = "<img src='".get_post_meta($post->ID, 'avatar', true)."' />";
							$author_url = get_post_meta($post->ID, 'url', true);
						} else {
							$author = get_the_author_meta('nickname');
							$avatar = get_avatar(get_the_author_meta('ID'),32);
							$author_url = get_the_author_meta('user_url');
						}
					?>
					<?php if ($media_type == 'aside') :?>
					<div class="content">
						<table class="author author_aside">
							<tr>
								<td class="avatar"><a href="<?php echo $author_url;?>" target="_blank" rel="nofollow"><?php echo $avatar; ?></a></td>
								<td><ul>
								<li>由 <a href="<?php echo $author_url;?>" target="_blank" rel="nofollow author"><?php echo $author; ?></a> 发布</li>
								<li><?php the_time('Y年m月j日 G:i');?></li>
								</ul></td>
							</tr>
						</table>
					</div>
					<?php else : ?>
					<div class="content arrow">
						<a class='url hidden' href="<?php the_permalink(); ?>"><?php the_title();?></a>			
						<?php
							the_content();
							wp_link_pages('before=<div class="post_pagination">&after=&link_before=<span>&link_after=</span>');
							wp_link_pages('before=&after=</div>&next_or_number=next&nextpagelink=下一页&previouspagelink=上一页');
						?>
						<table class="author">
							<tr>
								<td class="avatar"><a href="<?php echo $author_url;?>" target="_blank" rel="nofollow"><?php echo $avatar; ?></a></td>
								<td><ul>
								<li>由 <a href="<?php echo $author_url;?>" target="_blank" rel="nofollow author"><?php echo $author; ?></a> 发布</li>
								<li><?php the_time('Y年m月j日 G:i');?></li>
								</ul></td>
							</tr>
						</table>
					</div>
					<?php endif; ?>
					<div class="share">
						<span class="sharelabel">分享到：</span>
						<span class="sharebtn share_sinawb" title="分享到新浪微博"></span>
						<span class="sharebtn share_qqwb" title="分享到腾讯微博"></span>
						<span class="sharebtn share_qzone" title="分享到QQ空间"></span>
						<span class="sharebtn share_qq" title="分享给QQ好友"></span>
						<span class="sharebtn share_renren" title="分享到人人网"></span>
						<a class='comments_count' href='#comments' title="查看更多评论"><span class="ds-thread-count" data-thread-key="<?php the_ID();?>"></span></a>
					</div>
					<div class='infobar clearfix'>
						<?php $vote = get_post_meta($post->ID, "blueria_vote", true);$vote = $vote ? $vote : 0; ?>
						<?php $view = get_post_meta($post->ID, "views", true);$view = $view ? $view : 0; ?>
						<div class='votebtn vote_up' title="赞 +1" onclick="blueria_vote(<?php the_ID();?>,'up',this);"></div>
						<div class='vote_down' title="踩 -1" onclick="blueria_vote(<?php the_ID();?>,'down',this);" style='display:none;'></div>
						<ul class='databar'>
							<li class='vote'>至少 <span class='num'><?php echo $vote; ?></span> 人赞过</li>
							<li class='view'>已被浏览过 <?php echo $view; ?> 次</li>
						</ul>
					</div>
					<?php if (get_the_tags()) :?>
					<div class='tags'>
						<?php the_tags('','');?>
						<div class='tagicon'></div>
					</div>
					<?php endif; ?>
					<div class="pagenav2 clearfix">
						<span class="prev"><?php previous_post_link('前一篇：%link'); ?></span>
						<span class="next"><?php next_post_link('后一篇：%link'); ?></span>
					</div>
					<?php if ( is_active_sidebar('post_right_column') ) :?>	 
					<ul class='column_right column'>
						<?php dynamic_sidebar('post_right_column'); ?>
					</ul>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php endwhile;?>
		
	</div>
	<?php get_sidebar();?>
	<div id="pagination"></div>	 
	<?php wp_reset_query(); ?>
</div>

<script>

// var ooo = $(".infobox:first");
// //var rrr = ooo.parent().prev().children(":first");
// var rrr = ooo.parent().prev();
// ooo.blueriaFloat({marginTop: 65, refer: rrr});
$(document).keydown(function(e){
	var key = e.keyCode;
	var prev_url = $(".prev a").attr("href");
	var next_url = $(".next a").attr("href");
	switch (key) {
		case 65:
			if (prev_url) window.location.href = prev_url;
			break;
		case 68:
			if (next_url) window.location.href = next_url;
			break;
		case 87:
			$(".vote_up").click();
			break;
		case 83:
			$(".vote_down").click();
			break;
		// case 67:
		//	 $(".ds-textarea-wrapper textarea").focus();
		//	 break;
	}
});
</script>
<?php get_footer(); ?>