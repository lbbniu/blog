<div id="container" class="clearfix layout_overall">
	<div id="main" class='layout_left'>
		<?php 
			$v = &$_GET['v'];
			global $query_string;
			//var_dump($query_string);
			parse_str($query_string);   //将请求解析到变量
			$paged = $paged ? $paged : 1;
			$tax_query = array( array('taxonomy' => 'post_format', 'field' => 'slug', 'terms' => array('post-format-image','post-format-video','post-format-aside'), 'operator' => 'IN') );
			$query_array = array('paged' => $paged, 'posts_per_page' => $posts_per_page, 'tax_query' => $tax_query);

			$sticky_posts = array();			//置顶的日志
			$sticky =  blueria_get_sticky(5);	//获取最新置顶的5篇日志
			if ( $paged == 1 && $sticky ) $sticky_posts = get_posts( array('post__in' => $sticky) );	//只在首页显示置顶日志

			if ($s) $query_array = array('paged' => $paged, 'posts_per_page' => $posts_per_page, 'tax_query' => $tax_query, 's' => $s);	//搜索请求(不受其他因素影响)
			if ( $v ) {
				if ($v == 'hot') $query_array = array_merge($query_array, array('orderby' => 'meta_value_num', 'meta_key' => 'views'));
				elseif ($v == 'top') $query_array = array_merge($query_array, array('orderby' => 'meta_value_num', 'meta_key' => 'blueria_vote','date_query' => array(array('after' => '1 week ago')) ));
				$blueria_posts = get_posts( $query_array );
			} elseif ( $tag ) {
				$query_array = array_merge($query_array, array('tag' => $tag));
				$blueria_posts = get_posts( $query_array );
			} elseif ( $category_name ) {
				$query_array = array_merge($query_array, array('category_name' => $category_name));
				$blueria_posts = get_posts( $query_array );
			} else {
				if ( $sticky ) $query_array['post__not_in'] = $sticky;			//要排除置顶的日志
				$blueria_posts = get_posts( $query_array );
				$blueria_posts = array_merge($sticky_posts, $blueria_posts);	//把置顶的放前面
			}
		?>
		<ul class="topbox">
			<li class="item all<?php if (!$tag && !$category_name) echo ' current-menu-item';?>"><a href="<?php echo home_url( '/' );?>">所有</a></li>
			<?php
				add_filter('nav_menu_css_class' , 'current_menu_item' , 10 , 2);
				function current_menu_item($classes, $item) {
					global $category_name, $tag;
					if ( $category_name ) {
						$term = get_term_by('id', $item->object_id, 'category');
						if ($term->slug == $category_name) $classes[] = "current-menu-item";
					}
					if ( $tag ) {
						$term = get_term_by('id', $item->object_id, 'post_tag');
						if ($term->slug == $tag) $classes[] = "current-menu-item";
					}
					return $classes;
				}
				wp_nav_menu( array('theme_location'=>'home_menu', 'container'=>false, 'items_wrap'=>'%3$s', 'depth'=>1, 'fallback_cb'=>false) );
			?>
		</ul>
		<?php
			$site_notice = blueria_get_option('site_notice');
			$site_notice = $site_notice ? $site_notice : '首页公告！！！';
			$site_notice = trim($site_notice);
			$notices = str_replace("\r\n", "\n", $site_notice);
			$notices = explode("\n", $notices);
		?>
		<div class="notice"><div class="noticebar">
			<div class="notice_box"><?php
				if (count($notices) > 1) {
					echo "<ul class='notice_content'>\n";
					foreach ($notices as $notice) echo "<li>{$notice}</li>\n";
					echo "</ul>";
				} else {
					echo $site_notice;
				}
			?></div>
		</div></div>
		
		<?php if ( $blueria_posts ) : foreach ( $blueria_posts as $post ) : setup_postdata( $post ); ?>
		<?php //$stickyHtml = in_array($post->ID, $sticky) ? "<div class='sticky'></div>" : ""; ?>
		<?php $stickyHtml = in_array($post->ID, $sticky) ? "[置顶] " : ""; ?>
		<div class="panel clearfix share_root" data-postid="<?php echo $post->ID;?>">
			<div class="panel_left">
				<h2 style='display:none;'><a class='url' href="<?php the_permalink(); ?>" target='_blank'><?php echo $stickyHtml; ?><?php the_title(); ?></a></h2>
				<?php $media_type = get_post_format(); ?>
				<!--文章内容-->
				<div class="content">
				     <?php the_content(); ?>
				</div>
				<!--视频-->
				<?php if ($media_type == 'video') : ?>
				<div class="videobox">
					<div class="videoplayer" src="<?php VideoUrlParser::checkVideo( get_field('videourl') );?>" autoplay="true">
						<div class='img_wrap'><img class='tu' src="<?php VideoUrlParser::checkImage( get_field('img') );?>" /></div>
					</div>
					<div class="playbtn"></div>
					<div class="video_close"><!--googleoff: all-->收起视频<!--googleon: all--></div>
				</div>
 				<!--图片-->
				<?php elseif ($media_type == 'image') : ?> 
				<div class="imagebox">
				<?php
					$imgurl = get_post_meta($post->ID, 'imgurl', true);
					if ( !empty($imgurl) ) {
						$img_type = get_post_meta($post->ID, 'img_type', true);
						if ( empty($img_type) ) $img_type = array_pop( getImgMeta($imgurl) );	//array_pop取出数组最后的那个值 即类型
					} else $img_type = getAttachmentType( get_field('pic') );

					if ($img_type != 'gif') :	//不是gif图的情况
						if ( !empty($imgurl) ) {
							$img = array();
							$img_w = get_post_meta($post->ID, 'img_w', true);
							$img_h = get_post_meta($post->ID, 'img_h', true);
							$img_w = $img_w?$img_w:auto;
							$img_h = $img_h?$img_h:auto;
							if ($img_w > 500) {
								$img[0] = get_bloginfo('template_url').'/TimThumb.php?w=500&src='.$imgurl;
								$img[1] = 500;
								$img[2] = (int)(500*$img_h/$img_w);
							} else {
								$img[0] = $imgurl;
								$img[1] = $img_w;
								$img[2] = $img_h;
							}
						} else {
							$img = wp_get_attachment_image_src(get_field('pic'), 'large');	//这里取大尺寸(默认600)
							if ($img[1] > 500) {
								$img[2] = (int)(500*$img[2]/$img[1]);
								$img[1] = 500;
							}
						}
						
						$home_image_cut = (boolean) blueria_get_option('home_image_cut');
						if ( !$home_image_cut ) {
							$img_h = $img[2];
						} else {
							$maxheight = (int) blueria_get_option('home_image_cut_height');
							$img_h = ($img[2] <= $maxheight) ? $img[2] : $maxheight;
						}
						$no_cuted = (boolean) ($home_image_cut && $img[2] > $maxheight);
					?>
						<div class='imageshow' style='height:<?php echo $img_h;?>px'>
							<a href="<?php the_permalink(); ?>" class="zoom<?php if ($no_cuted) echo 'cursor';?>" target='_blank'>
								<img class='tu' src='<?php echo $img[0];?>' width='<?php echo $img[1];?>' alt='<?php the_title(); ?>' />
							</a>
						</div>
					<?php if ($no_cuted) { ?><div class='image_more' style='width:<?php echo $img[1];?>px'></div><?php } ?>
					<?php else :	//如果是gif图
						$img = array();
						if ( !empty($imgurl) ) {	//网络图片
							$img_w = get_post_meta($post->ID, 'img_w', true);
							$img_h = get_post_meta($post->ID, 'img_h', true);
						} else {					//非网络图片
							$img = wp_get_attachment_image_src(get_field('pic'), 'full');	//只能取全图
							$imgurl = $img[0];
							$img_w  = $img[1];
							$img_h  = $img[2];
						}
						if ($img_w > 500) {
							$img[1] = 500;
							$img[2] = (int)(500*$img_h/$img_w);
							$img[0] = get_bloginfo('template_url')."/TimThumb.php?w=500&h=$img[2]&src={$imgurl}";
						} else {
							$img[1] = $img_w;
							$img[2] = $img_h;
							$img[0] = get_bloginfo('template_url')."/TimThumb.php?w={$img_w}&h={$img_h}&src={$imgurl}";
						}
					?>
						<div class='imageshow gif'>
							<span class='imagewrap' gif='<?php echo $imgurl;?>'>
								<img class='tu' src='<?php echo $img[0];?>' width='<?php echo $img[1];?>' height='<?php echo $img[2];?>' alt='<?php the_title(); ?>' />
							</span>
						</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php //echo $stickyHtml; //置顶标记 ?>
				<!--
				<div class="content">
					<?php //the_content(); ?>
				</div>
				-->
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
							
							<a href='<?php the_permalink(); ?>#comments' class='comments_count' title="查看更多评论" target='_blank'><span><?php echo $comments_count;?></span></a>
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
					<div class="float_btn comment_btn">吐槽</div>
					
					<div id="bdshare" class="bdshare_t bds_tools get-codes-bdshare bdshare">
					<span class="bds_more">分享</span></div>				
</div>
			</div>
			<?php if (get_the_tags()) :?>
			
			<?php endif; ?>
			<div class="comments comment_box" data-num="1">
				<ul class="new_commentlist"></ul>
				<form method="post" action="" class="comment_form clearfix">
					<input id="author" type="hidden" aria-required="true" value="" name="author">
					<input id="url" type="hidden" value="" name="url">
					<input id="avatar" type="hidden" value="" name="avatar">
					<?php comment_id_fields( $post->ID ); ?>
					<div class="comment_user"><img src="<?php bloginfo('template_url');?>/ui/userlogin_s.png" /></div>
					<div class="publish_bar">
						<textarea name="comment" class="comment_inputbox"><?php echo blueria_get_option('comment_notice');?></textarea>
						<div class="publish_btn"></div>
					</div>
				</form>
			</div>
		</div>
		<?php endforeach; wp_reset_postdata(); ?>
		<?php else: ?>
			<div class="panel">对不起,未找到任何相关内容!</div>
		<?php endif; ?>
		
	</div>
	<?php get_sidebar();?>
	<ul id='links' class='clearfix' <?php if (blueria_get_option('show_links') == 1) echo "style='display: block;margin-top: -15px;'";?>>
		<?php wp_list_bookmarks('title_li=&categorize=0&show_images=0&orderby=id&hide_invisible=0'); ?>
	</ul>
	<?php pagination($query_array); ?>
	<div id="pagination" class='layout_left'>
		<?php next_posts_link('加载更多笑料 ...'); ?>
		<div id="loading" class='layout_left'><img src="<?php bloginfo('template_url');?>/ui/loader.gif" align="top" />正在加载 ...</div>
	</div>
</div>

<script>
$("#pagination a").bind('click',function(){
	var _this = $(this);
	var next = _this.attr("href");
	var docH = $(document).height();
	$("#pagination a").hide();
	$("#loading").show();
	$.ajax({
		url: next,
		success: function (data) {
			var panel = $(data).find("#main > .panel");
			panel.appendTo("#main");												//追加内容
			updatePanel(panel);														//更新新面板的相关操作
			$(".pagination").html( $(data).find(".pagination").html() );			//更新分页导航
			if (typeof history.pushState == 'function') history.pushState({}, "", next);
			if (typeof DUOSHUO !== undefined) DUOSHUO.EmbedThread('.ds-thread');  //重新渲染多说评论
			$('html, body').animate({scrollTop: docH-160}, 500);	//上滚
			
			nextHref = $(data).find("#pagination a").attr("href");
			if ( nextHref != undefined ) {
				$("#pagination a").show();
				$("#loading").hide();
				$("#pagination a").attr("href", nextHref);
			} else {
				$("#pagination").html("没有更多了");  //最后一页
			}
		}
	});
	return false;
});
</script>