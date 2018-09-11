<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<html xmlns:wb="http://open.weibo.com/wb">
<head>
<script src="http://tjs.sjs.sinajs.cn/open/api/js/wb.js" type="text/javascript" charset="utf-8"></script>
<meta property="qc:admins" content="1565267454677110117636" />
<meta property="wb:webmaster" content="4e017cb48d5e4758" />
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<?php if( is_single() && $cpage = intval(get_query_var('cpage')) && !empty($cpage) ) echo "<meta name=\"robots\" content=\"noindex, nofollow\" />\n"; ?>
<?php 
$cat_info = strip_tags(category_description());	//如果当前页面为分类页
$tag_info = strip_tags(tag_description());		//如果当前页面为标签页
$head_info = '';
if ( !empty($cat_info) ) $head_info = $cat_info;
if ( !empty($tag_info) ) $head_info = $tag_info;
if ( !empty($head_info) ) {
	$headInfo = explode("#", $head_info);
	$wp_title = trim($headInfo[0])." - ";
	$wp_keywords = trim($headInfo[1]);
	$wp_description = trim($headInfo[2]);
} else {
	$wp_title = wp_title('-', 0, 'right');
	if ( is_single() ) {
		$wp_description = strip_tags($post->post_content);
		$wp_description = str_replace(array("\r\n","\n"), " ", $wp_description);	//替换换行符
		$wp_keywords = $post->post_title;
	} else {
		$wp_description = blueria_get_option('site_description');
		$wp_keywords = blueria_get_option('site_keywords');
	}
}
global $theme_tpl;
// if (isset($_GET['newlayout'])) $theme_tpl = 'nice';
$css = get_bloginfo('template_url').($theme_tpl == 'default' ? "/style.css" : "/style-{$theme_tpl}.css");
$ie = get_bloginfo('template_url').($theme_tpl == 'default' ? "/ie.php" : "/ie.php?tpl={$theme_tpl}");
?>
<title><?php
 if ($wp_description) {
		if (mb_strlen($wp_description) > 20) $wp_description = mb_substr($wp_description, 0, 20, 'UTF-8');	//只显示前150个字
		echo "{$wp_description} \n... - 我爱笑 woaixiao.cn";
	}
?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo('template_url');?>/favicon.ico" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $css; ?>" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<?php
	$wp_keywords="我爱笑,我们都爱笑,轻松减压,娱乐生活";
?>
<script type="text/javascript" src="<?php bloginfo('template_url');?>/js/jquery.min.js?v=1.8.3"></script>
<script type="text/javascript" src="<?php bloginfo('template_url');?>/js/jquery.flash.js?v=1.0"></script>
<link type="text/css" rel="stylesheet" media="all" href="<?php bloginfo('template_url');?>/js/jquery.jgrowl.css" />
<link type="text/css" rel="stylesheet" media="all" href="<?php bloginfo('template_url');?>/ui/ds.css" />
<?php wp_head(); ?>
<script>
	if (screen.width < 1080 && theme_tpl == 'default')
		document.write("<link rel=\"stylesheet\" type=\"text/css\" href=\"<?php bloginfo('template_url');?>/ui/1024.css\" />");
	var floatOptions = {container: '.panel', marginTop: 55};
</script>
<script type="text/javascript" src="<?php bloginfo('template_url');?>/js/base.js"></script>
<!--[if lt IE 9]>
<link rel="stylesheet" type="text/css" href="<?php echo $ie; ?>" />
<script type="text/javascript" src="<?php bloginfo('template_url');?>/js/fuckie.js"></script>
<![endif]-->
<!--[if lte IE 7]><script src="<?php bloginfo('template_url');?>/js/webfont.js"></script><![endif]-->
</head>
<body>
<!-- body_start -->


<?php
$sitetop_options = blueria_get_option('sitetop_options');
if ( !$sitetop_options ) :
?>
<div id="top_box"><div class="top_bg">
	<div class="top_container layout_overall clearfix">
		<div class="left">
			<div class="logo"><a href="<?php echo home_url( '/' );?>"><img src="<?php bloginfo('template_url');?>/ui/logo.png" /></a></div>
		</div>
		<div class="right">
			<table cellpadding="0" cellspacing="0" class='userinfo layout_right'>
				<tr class="usershow"><td class='clearfix'><div class="userbox"><div class="userwrap">
					<a class="avatar" href="#" target="_blank"><img src="" /></a>
					<span class="name"></span>
					<a class="setting" href="http://duoshuo.com/settings/" target="_blank" rel="external nofollow">设置</a>
					<a class="setting" href="http://t.heminjie.com/wp-admin/profile.php" target="_blank" rel="external nofollow">个人中心</a>
					<a class="exit" href="#" >退出</a>
				</div></div></td></tr>
				<tr class="userlogin"><td>
					<a class="weibo" href="#" title="新浪微博登陆"></a>
					<a class="qq" href="#" title="QQ登陆"></a>
				</td></tr>
			</table>
		</div>
	</div>
</div></div>
<?php else : 
	$sitetop_height = esc_attr($sitetop_options['height']);
	$heightstyle = "height:{$sitetop_height}px;";
	$sitetop_bgimg = get_bloginfo('template_url')."/images/".esc_attr($sitetop_options['bgimg']);
	$bgimgstyle = "background: url({$sitetop_bgimg}) no-repeat scroll center 0 transparent;";
	if (esc_attr($sitetop_options['type']) == 'color') {
		$sitetop_bg_color = esc_attr($sitetop_options['bg_color']);
		$bgstyle = "background: none repeat scroll 0 0 {$sitetop_bg_color};";
	} else {
		$sitetop_bg_image = get_bloginfo('template_url')."/images/".esc_attr($sitetop_options['bg_image']);
		$bgstyle = "background: url({$sitetop_bg_image}) repeat-x scroll 0 0 transparent;";
	}
	$sitetop_logo_top = esc_attr($sitetop_options['logo_top']);
	$sitetop_logo_left = esc_attr($sitetop_options['logo_left']);
	$logostyle = "padding-top:{$sitetop_logo_top}px;margin-left:{$sitetop_logo_left}px;";
?>
<div id="top_box" style="<?php echo $bgstyle;?>"><div class="top_bg" style="<?php echo $bgimgstyle.$heightstyle;?>">
	<div class="top_container layout_overall clearfix">
		<div class="left">
			<div class="logo" style="<?php echo $logostyle;?>"><a href="<?php echo home_url( '/' );?>"><img src="<?php bloginfo('template_url');?>/ui/logo.png" /></a></div>
		</div>
		<div class="right">
			<table cellpadding="0" cellspacing="0" class='userinfo layout_right'>
				<tr class="usershow"><td class='clearfix'><div class="userbox"><div class="userwrap">
					<a class="avatar" href="#" target="_blank"><img src="" /></a>
					<span class="name"></span>
					<a class="setting" href="http://duoshuo.com/settings/" target="_blank" rel="external nofollow">设置</a>
					<a class="setting" href="http://www.woaixiao.cn/wp-admin/profile.php" target="_blank" rel="external nofollow">个人中心</a>
					<a class="exit" href="#" >退出</a>
				</div></div></td></tr>
				<tr class="userlogin"><td>
					<a class="weibo" href="#" title="新浪微博登陆"></a>
					<a class="qq" href="#" title="QQ登陆"></a>
				</td></tr>
			</table>
		</div>
	</div>
</div></div>
<?php endif; ?>
<?php
function addpost_link() {
	global $wpdb;
	global $pagename;
	$this_page = $wpdb->get_results("SELECT ID,post_title FROM $wpdb->posts WHERE post_name = 'add-post' AND post_status = 'publish' AND post_type = 'page'");
	if ($this_page) {
		$link = get_page_link($this_page[0]->ID);
		$title = $this_page[0]->post_title;
		$curr = ($pagename == 'add-post')?'current-':'';
		echo "<li class='{$curr}menu-item addpost_link'><a href='{$link}' class='icon-flag'>{$title}</a></li>";
	}
}
?>
<div id="nav">
	<table cellpadding="0" cellspacing="0" class='layout_overall'>
		<tr>
			<td class='menu'><ul>
				
				<li class='<?php if (!$_GET['v'] && is_home()) echo 'current-';?>menu-item'><a href="<?php echo home_url('/');?>">最新</a></li>
				 
				<li class='<?php if ($_GET['v'] == 'top') echo 'current-';?>menu-item'><a href="<?php echo home_url('/');?>?v=top">最热</a></li>
				<?php wp_nav_menu( array('theme_location'=>'top_menu', 'container'=>false, 'items_wrap'=>'%3$s', 'depth'=>2, 'fallback_cb'=>false) );?>
				<?php addpost_link(); ?>
			</ul></td>
			<td class='search layout_right'>
				<form id="search_form" action="<?php echo home_url('/');?>" method="get" role="search">
					<input id="search_value" type="text" name="s" value="搜索精彩笑料..." onblur="if(this.value=='')this.value='搜索精彩笑料...';" onclick="this.value=''">
					<input id="search_submit" type="submit" value="" title="搜索">
				</form>
			</td>
			<!-- <td class='userinfo layout_right'>
				<div class='userlogin'>
					<a class="weibo" href="#">新浪微博登陆</a>
					<a class="qq" href="#">QQ登陆</a>
				</div>
				<div class='usershow'>
					<span class="name"></span>
					<a class="avatar" href="#" target="_blank"><img src="" /></a>
					<span class="info_arrow"></span>
				</div>
				<div class="userbox">
					<a class="profile" href="#" target="_blank">最近动态</a>
					<a class="bind-more" href="javascript:;">绑定更多</a>
					<a class="setting" href="http://duoshuo.com/settings/" target="_blank" rel="external nofollow">设置</a>
					<a class="exit" href="#" >退出</a>
				</div>
			</td> -->
		</tr>
	</table>
</div>