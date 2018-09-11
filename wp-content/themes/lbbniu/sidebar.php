<?php
/**
 * The Sidebar containing the main widget area
 *
 * @package WordPress
 * @subpackage lbbniu
 * @since lbbniu
 */
?>
<aside class="sidebar">
		
<div class="widget widget_text"><div class="textwidget"><div class="social">
<a href="http://weibo.com/2102315265/" rel="external nofollow" title="新浪微博" target="_blank"><i class="sinaweibo fa fa-weibo"></i></a>
<a href="http://t.qq.com/lbbniu/" rel="external nofollow" title="腾讯微博" target="_blank"><i class="tencentweibo fa fa-tencent-weibo"></i></a>
<a class="weixin"><i class="weixins fa fa-weixin"></i>
	<div class="weixin-popover">
		<div class="popover bottom in">
			<div class="arrow"></div>
			<div class="popover-title">订阅号“<?php bloginfo('name'); ?>”</div>
			<div class="popover-content"><img src="<?php bloginfo('template_url'); ?>/img/weixin.gif" ></div>
		</div>
	</div>
</a>
<a href="mailto:lbbniu@qq.com" rel="external nofollow" title="Email" target="_blank"><i class="email fa fa-envelope-o"></i></a>
<a href="http://wpa.qq.com/msgrd?V=1&Uin=75397273&Site=blog.lbbniu.com&Menu=yes" rel="external nofollow" title="联系QQ" target="_blank"><i class="qq fa fa-qq"></i></a>
<a href="/feed" rel="external nofollow" target="_blank"  title="订阅本站"><i class="rss fa fa-rss"></i></a>

</div></div></div>

<div class="widget d_postlist"><div class="title"><h2>热门</h2></div>
<ul>
	<?php
	$post_num = 5; // 设置调用条数
	$args = array(
	'post_password' => '',
	'post_status' => 'publish', // 只选公开的文章.
	'post__not_in' => array($post->ID),//排除当前文章
	'caller_get_posts' => 1, // 排除置頂文章.
	'orderby' => 'comment_count', // 依評論數排序.
	'posts_per_page' => $post_num
	);
	$query_posts = new WP_Query();
	$query_posts->query($args);
	while( $query_posts->have_posts() ) { $query_posts->the_post(); ?>
	<li>
		<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" >
			<span class="thumbnail">
				<img src="<?php bloginfo('wpurl');?>/wp-content/themes/lbbniu/timthumb.php?src=<?php post_thumbnail_src(); ?>&h=123&w=200&q=90&zc=1&ct=1" alt="<?php the_title(); ?>" /></span>
				<span class="text"><?php the_title(); ?></span>
				<span class="muted"><?php the_time('Y-m-d'); ?></span><span class="muted"><?php comments_number(' 0 评论', ' 1 评论', ' % 评论' );?></span>
		</a>
	</li>
<?php } wp_reset_query();?>

</ul>
</div>
<div class="widget d_banner">
	<div class="d_banner_inner">
		<script src="http://www.cloudad.asia/page/s.php?s=29918&w=300&h=300"></script>
	</div>
</div>
<?php if( !function_exists('dynamic_sidebar') 
					|| !dynamic_sidebar('Third_sidebar')) : ?>
<div class="widget d_tag">
	<div class="title"><h2>标签云</h2></div>
	<div class="d_tags">	
		<?php 
		wp_tag_cloud('smallest=&largest='); 
		//	wp_tag_cloud( array('topic_count_text_callback' => 'my_tag_text_callback' ) ); 
		
		//	function my_tag_text_callback( $count ) {
		//		echo sprintf( _n( '(%s)', '(%s)', $count ), number_format_i18n( $count ) );
		//		return sprintf( _n( '(%s)', '(%s)', $count ), number_format_i18n( $count ) );
		//	}
		?>
	</div>
</div>
<?php endif; ?>
<div class="widget widget_text">	
	<div class="title"><h2>友情链接</h2></div>			
	<div class="textwidget"><br>
<a style='margin-left:40px;'  href='http://www.cloudad.asia/aff/index.php/track/19030_144' target='_blank'>爱之谷商城</a>
<a style='margin-left:40px;' href='http://www.cloudad.asia/aff/index.php/track/19030_143' target='_blank'>桔色成人用品网</a>
<a style='margin-left:40px;'  href='http://www.cloudad.asia/aff/index.php/track/19030_186' target='_blank'>瓷肌医生</a>
<br><br>
<a style='margin-left:40px;'  href='http://www.cloudad.asia/aff/index.php/track/19030_131' target='_blank'>优米网</a>
<a style='margin-left:40px;'  href='http://www.cloudad.asia/aff/index.php/track/19030_110' target='_blank'>韩都衣舍</a>
<a style='margin-left:40px;'  href='http://www.cloudad.asia/aff/index.php/track/19030_203' target='_blank'>麦考林</a>
<br><br></div>
</div></aside>