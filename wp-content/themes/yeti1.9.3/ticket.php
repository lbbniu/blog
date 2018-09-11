<?php
/*
*Template Name:审贴页
*/
?>

<?php get_header(); ?>


<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
	'post_status' => 'pending',
	'posts_per_page' => '1',
	'paged' => $paged,
);

$the_query = new WP_Query( $args );
if ( $the_query->have_posts() ) :
while ( $the_query->have_posts() ) : $the_query->the_post();
?>
<div id="container" class="clearfix layout_overall">
	<div id="main" class="layout_left" style="width: 640px;">
	
<div class="panel clearfix share_root">

<div class="panel_left" style="width: 460px; min-height: 350px; margin-bottom: 5px;">
				<div class="content" style="font-size: 14px;">
					<a class='url hidden' href="<?php the_permalink(); ?>" target='_blank'></a>
					
					<?php 

						the_content(); 					
					?>
					
				</div>

				<?php $media_type = get_post_format(); ?>
				<?php if ($media_type == 'video') : ?>
				<div class="videobox" style="max-width: 460px;">
					<div class="videoplayer" style="max-width: 460px;" src="<?php VideoUrlParser::checkVideo( get_field('videourl') );?>" autoplay="true">
						<div class='img_wrap'><img class='tu' src="<?php VideoUrlParser::checkImage( get_field('img') );?>" /></div>
					</div>
					<div class="playbtn"></div>
					<div class="video_close"><!--googleoff: all-->收起视频<!--googleon: all--></div>
				</div>
				<?php elseif ($media_type == 'image') : ?>  
				<div class="imagebox" style="max-width: 460px;">
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
						if ($img_w > 460) {
							if ($img_type == 'gif') $img[0] = $imgurl;	//如果是gif图 直接引用原图
							else $img[0] = get_bloginfo('template_url').'/TimThumb.php?w=460&src='.$imgurl;
							$img[1] = 460;
							$img[2] = (int)(460*$img_h/$img_w);
						} else {
							$img[0] = $imgurl;
							$img[1] = $img_w;
							$img[2] = $img_h;
						}
					} else {
						if ($img_type == 'gif') {
							$img = wp_get_attachment_image_src(get_field('pic'), 'full');
							if ($img[1] > 460) {
								$img[2] = (int)(500*$img[2]/$img[1]);
								$img[1] = 460;
							}
						} else {
							$img = wp_get_attachment_image_src(get_field('pic'), 'large');
							if ($img[1] > 460) {
								$img[2] = (int)(460*$img[2]/$img[1]);
								$img[1] = 460;
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
					
				?>
				
			</div>
<div class="panel_right" style="margin-right: 15px;">
				<div class="floatbar">
					<?php
						$vote = get_post_meta($post->ID, "blueria_vote", true);
						$vote = $vote ? $vote : 0;
						$vote2 = get_post_meta($post->ID, "blueria_vote2", true);
						$vote2 = $vote2 ? $vote2 : 0; 
					?>
					<ul class="vote" id="vote_<?php the_ID();?>" style="margin-top: 30px;">
					<!--<?php echo $vote;?>-->
						<li><a class="vote_button vote_up" onclick="blueria_vote(<?php the_ID();?>,'up',this);" href="javascript:;" title="赞 +1">好笑</a></li>
						<li><a class="vote_button vote_down" onclick="blueria_vote(<?php the_ID();?>,'down',this);" href="javascript:;" title="踩 -1" style="margin-top: 30px;">不好笑</a></li>
						<li><a class="vote_button vote_skip" href="javascript:;" title="跳过" style="margin-top: 30px; background: url('http://t.heminjie.com/wp-content/themes/woaixiao/ui/skip.png') no-repeat scroll 0px 0px transparent;">跳过</a></li>
					</ul>

				</div>
			</div>
</div>

	</div>
<?php get_sidebar(); ?>
<?php
endwhile;

else :
	?>
	<div style="text-align: center; max-width: 440px; margin-left: 240px; margin-top: 15px; font-size: 14px;border-radius: 3px; padding:20px 10px 0px 0px; background-color: rgb(255, 255, 255); border: 1px solid rgb(221, 221, 221);">哎哟，肿么木有待审的新笑料了！我要<a href="http://t.heminjie.com/add-post" style="color: rgb(250, 125, 15); font-size: 16px;">来一发发发！！</a></br></br></br><img src="http://t.heminjie.com/wp-content/themes/woaixiao/ui/3g.png"></div>

	<?php
endif;

$pages = $the_query->max_num_pages;
if ( $pages >= 2 ):
$big = 999999999;
$paginate = paginate_links( array(
	'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	'format' => '?paged=%#%',
	'current' => max( 1, get_query_var('paged') ),
	'total' => $the_query->max_num_pages,
	'end_size' => 13,//最多显示*个页码
	'type' => 'array'
) );
echo '<ul class="pagination" style="display: none;">';
foreach ($paginate as $value) {
    echo '<li style="float:left;">'.$value.'</li>';
}
echo '<li style="clear:both;"></li></ul><div style="clear:both;"></div>';
endif;
?>
</div><!--#container -->
<script>
$(".vote_button").live('click',function(){												//用bind()执行一次后会失效，所以用live()
	var _this = $(this);
	var next = $(".next").attr("href");
	if(next){
		$.ajax({
			url: next,
			success: function (data) {
				var panel = $(data).find("#main > .panel");
				/*var vote = $(data).find("#main > .panel > .panel_right > .floatbar > .vote");
				var vote_id = $(vote).attr("id");
				if (null != getCookie("blueria_" + vote_id)){
					$(".vote_skip").trigger("click");
					$("#notify").jGrowl("\u8fd9\u6761\u7b11\u6599\u6295\u8fc7\u7968\u4e86\uff0c\u81ea\u52a8\u8df3\u8fc7\uff01", {theme: "smoke"});
				}*/
				$("#main").html(panel);													//更新内容
				updatePanel(panel);														//更新新面板的相关操作
				$(".pagination").html( $(data).find(".pagination").html() );			//更新分页导航
				
			}
		});
	}else{
		alert('哎呀，已经是最后一条笑料了~~~');
	}
	return false;
});
</script>
<?php wp_reset_postdata();?>
<?php get_footer(); ?>