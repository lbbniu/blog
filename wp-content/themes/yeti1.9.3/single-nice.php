<?php get_header(); ?>

<style>

	#container #ds-thread #ds-reset .ds-comments-info {display: inherit;}

	#container #ds-thread #ds-reset .ds-paginator {display: inherit !important;border-bottom:none;padding-bottom:5px;}

	#container #ds-thread #ds-reset .ds-paginator div.ds-border {margin-bottom:0;}

	#container #ds-thread #ds-reset .ds-comments .ds-post-placeholder {display: inherit;background-color:#F4F4F4;border-radius:5px;margin-top:0;padding: 20px 0;}

</style>



<div id="container" class="clearfix layout_overall">

	<div id="main" class='layout_left'>

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

					<span class="prev" title="上一条"><?php next_post_link('%link'); ?></span>

					<span class="next" title="下一条"><?php previous_post_link('%link'); ?></span>

				</div>

			</div>

		</div>

		

		<ul class="post_nav clearfix">

			

			<?php wp_nav_menu( array('theme_location'=>'home_menu', 'container'=>false, 'items_wrap'=>'%3$s', 'depth'=>1, 'fallback_cb'=>false) ); ?>

		</ul>

		

		<div class="panel clearfix share_root" style="margin-bottom: -14px;">

			<div class="panel_left">

				<div class="content">

					<a class='url hidden' href="<?php the_permalink(); ?>" target='_blank'></a>

					<?php 

						the_content();

						wp_link_pages('before=<div class="post_pagination">&after=&link_before=<span>&link_after=</span>');

						wp_link_pages('before=&after=</div>&next_or_number=next&nextpagelink=下一页&previouspagelink=上一页'); 

					?>

				</div>



				<?php $media_type = get_post_format(); ?>

				<?php if ($media_type == 'video') : ?>

				<div class="videobox">

					<div class="videoplayer" src="<?php VideoUrlParser::checkVideo( get_field('videourl') );?>" autoplay="true">

						<div class='img_wrap'><img class='tu' src="<?php VideoUrlParser::checkImage( get_field('img') );?>" /></div>

					</div>

					<div class="playbtn"></div>

					<div class="video_close"><!--googleoff: all-->收起视频<!--googleon: all--></div>

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

						$img_w = $img_w?$img_w:auto;

						$img_h = $img_h?$img_h:auto;

						if ($img_w > 500) {

							if ($img_type == 'gif') $img[0] = $imgurl;	//如果是gif图 直接引用原图

							else $img[0] = get_bloginfo('template_url').'/TimThumb.php?w=500&src='.$imgurl;

							$img[1] = 500;

							$img[2] = (int)(500*$img_h/$img_w);

						} else {

							$img[0] = $imgurl;

							$img[1] = $img_w;

							$img[2] = $img_h;

						}

					} else {

						if ($img_type == 'gif') {

							$img = wp_get_attachment_image_src(get_field('pic'), 'full');

							if ($img[1] > 500) {

								$img[2] = (int)(500*$img[2]/$img[1]);

								$img[1] = 500;

							}

						} else {

							$img = wp_get_attachment_image_src(get_field('pic'), 'large');

							if ($img[1] > 500) {

								$img[2] = (int)(500*$img[2]/$img[1]);

								$img[1] = 500;

							}

						}

					}

				?>

					<img class='tu' src='<?php echo $img[0];?>' width='<?php echo $img[1];?>' height='<?php echo $img[2];?>' alt='<?php the_title(); ?>' />

				</div>

				<?php endif; ?>

				<?php

					$author = get_post_meta($post->ID, 'username', true);

					if ( !empty($author) ) {

						$avatar = "<img src='".get_post_meta($post->ID, 'avatar', true)."' />";

						$author_url = get_post_meta($post->ID, 'url', true);

					} else {

						$author = get_the_author_meta('nickname');

						$avatar = get_avatar(get_the_author_meta('ID'),32);

						$author_url = get_the_author_meta('user_url');

					}

					$view = get_post_meta($post->ID, "views", true);

					$view = $view ? $view : 0;

					$comments_count = $post->comment_count;

					$comments_count = $comments_count ? "评论({$comments_count})" : "暂无评论";

				?>

				<table class="author">

					<tr>

						<td class="avatar"><a href="<?php echo $author_url;?>" target="_blank" rel="nofollow"><?php echo $avatar; ?></a></td>

						<td><ul>

							<li>由 <a href="<?php echo $author_url;?>" target="_blank" rel="nofollow author"><?php echo $author; ?></a> 发布</li>

							<li><?php the_time('Y-m-j G:i');?></li>

						</ul></td>

						<td class='postinfo'>

							

							<a href='#comments' class='comments_count' title="查看更多评论"><span><?php echo $comments_count;?></span></a>

						</td>

					</tr>

				</table>

			</div>

			<div class='panel_right'>

				<div class="floatbar">

					<?php

						$vote = get_post_meta($post->ID, "blueria_vote", true);

						$vote = $vote ? $vote : 0;

						$vote2 = get_post_meta($post->ID, "blueria_vote2", true);

						$vote2 = $vote2 ? $vote2 : 0; 

					?>

					<ul class="vote">

						<li><a class="vote_up" onclick="blueria_vote(<?php the_ID();?>,'up',this);" href="javascript:;" title="赞 +1"><?php echo $vote; ?></a></li>

						<li><a class="vote_down" onclick="blueria_vote(<?php the_ID();?>,'down',this);" href="javascript:;" title="踩 -1"><?php echo $vote2; ?></a></li>

					</ul>

					<!-- <div class="float_btn comment_btn">吐槽</div> -->


<!-- bshare button start-->
<style>
#bsPanel {position: fixed;_position: absolute;_top: expression(eval(document.documentElement.scrollTop+106));}
</style>
  <div class="bshare-custom">
   <a style="vertical-align:baseline !important; height: 22px; width: 67px; text-decoration: none; opacity: 1;" class="bshare-more"><span class="bds_more">分享</span></a>			
  </div>
<script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/buttonLite.js#uuid=a40880a8-b6e5-4f71-b5ba-77f2a6f979cc&style=-1"></script>
<script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/bshareC0.js"></script><script type="text/javascript" charset="utf-8">
   bShare.addEntry({pic:"<?php echo $img[0];?>"});
</script>	

<!-- bshare button end-->

</div>

			</div>

			<?php if (get_the_tags()) :?>

			

			<?php endif; ?>

			<?php if ( is_active_sidebar('post_left_column') ) :?>   

				<ul class='column_left column'>

					<?php dynamic_sidebar('post_left_column'); ?>

				</ul>

			<?php endif; ?>

			<div id="wumiiDisplayDiv"></div>

			<div class="comments">

				<?php remove_all_filters('comments_template', 0);comments_template();?>

			</div>

		</div>

		<?php endwhile;?>

		

	</div>

	<?php get_sidebar();?>

	<div id="pagination" class='layout_left'></div>   

	<?php wp_reset_query(); ?>

</div>



<script>

$(document).keydown(function(e){

	var key = e.keyCode;

	var prev_url = $(".prev a").attr("href");

	var next_url = $(".next a").attr("href");

	if ($(":focus").length != 0) return null;

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

var commnet_paged = [1];	//默认第一页是已经加载的

$(".page-numbers").live('click', function(){

	var _this = $(this);

	// var page_num = _this.text();

	// var cur_page = parseInt( _this.siblings(".current").text() );

	// if (page_num == '下一页') {

	// 	page_num = cur_page + 1;

	// } else if (page_num == '上一页') {

	// 	page_num = cur_page - 1;

	// } else {

	// 	page_num = parseInt(page_num);

	// }

	// if ($.inArray(page_num, commnet_paged) != -1) {	//加载过了

	// 	alert("第"+page_num+"页已载入 无需再次加载 ...");

	// 	return false;

	// }



	var href = _this.attr("href");

	_this.siblings(".loading").css('display', 'inline-block');

	$.ajax({

		url: href,

		success: function (data) {

			var comment_bar = $(data).find(".comment_bar");

			// comment_bar.appendTo(".commentlist");

			$(".commentlist").html( comment_bar );

			$("#comments-navi").html( $(data).find("#comments-navi").html() );

			// commnet_paged.push(page_num);	//加入到已浏览数组

		}

	});

	return false;

});

function ds(s) {

	if (s == 0) {

		$(".commenter a, #comment_submit").on('click',function(e){ e.stopPropagation(); moreLogin(); });

		$(".commenter img").attr('src', tpl_url+'ui/userlogin.png');

		$("#commentform").off("submit").on("submit", false);

	} else if (s == 1) {

		$(".commenter a").attr({'href': DUOSHUO.visitor.data.url, 'target': '_blank', 'rel': 'nofollow external'});

		$(".commenter img").attr('src', DUOSHUO.visitor.data.avatar_url);

		$("input[name='author']").val(DUOSHUO.visitor.data.name);

		$("input[name='url']").val(DUOSHUO.visitor.data.url);

		$("input[name='avatar']").val(DUOSHUO.visitor.data.avatar_url);

	}

}

</script>

<?php get_footer(); ?>