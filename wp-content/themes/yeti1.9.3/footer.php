<div id="footer" class="layout_overall">
	<div class="copyright">
	<?php $CR = blueria_get_option('site_copyright'); if ( !$CR ) : ?>
		Copyright © 2014 <font color="red">zhangleisir.cn</font> Powered by <a href='http://www.zhangleisir.cn' target="_blank">我爱笑</a>&<a href='http://www.zhangleisir.cn/' target="_blank">晨风</a> <a target="_blank" href="http://www.zhangleisir.cn" rel="nofollow"> &nbsp;沪ICP备13033667号-2</a>
	<?php else :
		echo $CR;
	endif; ?>
	</div>   
</div>
<div id="notify" class="jGrowl bottom-right"></div>
<div class='jam' src="<?php template_url();?>/jam.bin?ver=<?php global $theme_ver;echo $theme_ver;?>"></div>
<div class='ticker clearfix'>
	<a class='topbtn'></a>
	<div class='shortcut'>
		<a class='listbtn'></a>
		<ul class='sub_menu'>
			<?php wp_nav_menu( array('theme_location'=>'shortcut_menu', 'container'=>false, 'items_wrap'=>'%3$s', 'depth'=>1, 'fallback_cb'=>false) );?>			<li><a href='http://t.heminjie.com/public/about.html' target="_blank">关于我们</a></li>			<li><a href='http://t.heminjie.com/public/contact.html' target="_blank">联系我们</a></li>			<li><a href='http://t.heminjie.com/public/policy.html' target="_blank">免责声明</a></li>
			<li class='line'><a href='#'></a></li>
		</ul>
	</div>   
	<a class='bottombtn'></a>
</div>
<script>
$(function(){
	$(".floatStart").scrollFollow({
		bottomObj: '#footer',
		marginTop: 65,
		marginBottom: 5
	});
	$(".floatbar").scrollFollow(floatOptions);
	$("#nav").scrollFollow({zindex: 99999});
	$(".jam").php();
});
</script>
<div style='display:none;'><?php echo blueria_get_option('site_statistics'); ?></div>
<?php if ( is_active_sidebar('script_column') ) : ?>
<div class='script hidden'><?php dynamic_sidebar('script_column'); ?></div>
<?php endif; ?>
<?php wp_footer(); ?>
<!-- Baidu Button BEGIN -->
<?php
$bdshare = blueria_get_option('bdshare_options');
?>
<script type="text/javascript" id="bdshare_js" data="type=tools&amp;uid=<?php echo $bdshare['id'];?>" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript">
	var bds_config={"snsKey":{'tsina':'<?php echo $bdshare["tsina"]; ?>','tqq':'<?php echo $bdshare["tqq"]; ?>','t163':'<?php echo $bdshare["t163"]; ?>','tsohu':'<?php echo $bdshare["tsohu"]; ?>'}}
	document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + Math.ceil(new Date()/3600000)
</script>
<!-- Baidu Button END -->

<!--[if IE 6]>
<div id="ie6-warning">您正在使用 Internet Explorer 6，会导致页面显示出错。IE6已是<font color=red>微软淘汰产品</font>，强烈建议您升级到 <a href="http://windows.microsoft.com/zh-cn/internet-explorer/download-ie" target="_blank">Internet Explorer 8</a> 或推荐浏览器：
	<a href="http://www.zhangleisir.cn/">Firefox</a> / <a href="http://www.google.com/chrome/?hl=zh-CN">Chrome</a> /
	<a href="http://www.apple.com.cn/safari/">Safari</a> / <a href="http://www.operachina.com/">Opera</a>
</div>
<![endif]-->
<!-- body_end -->
</body>
</html>