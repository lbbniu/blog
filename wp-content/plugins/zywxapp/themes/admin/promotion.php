<?php
get_header(); 
if (in_array($_GET['client'],array('iPhone', 'Android'))) {
	$client = $_GET['client'];
} else {
	$client = 'iPhone';
}
$jqueryJsFile = WP_PLUGIN_URL."/". ZYWXAPP_DIR_NAME. '/themes/admin/jquery.tools.min.js';
wp_register_script('zywxapp_jquery_tools_script', $jqueryJsFile, array('jquery'));
wp_enqueue_script('zywxapp_jquery_tools_script');

$download_url = get_bloginfo('url').'?zywxapp/system/promotion&client='.$client.'&download=1';
$errors = '';
if (isset($_GET['download']) && !empty($_GET['download'])) {
	$cms = new ZywxappCms();
	$cms->getQrcode(); 
	if (ZywxappConfig::getInstance()->zywxapp_client_download_url) {
		wp_redirect( ZywxappConfig::getInstance()->zywxapp_client_download_url);
	} else {
		$url = remove_query_arg( array('download'), $download_url );
		$errors = '没有下载包';
	}
}

if (!ZywxappConfig::getInstance()->zywxapp_qrcode_url) {
	$cms = new ZywxappCms();
	$cms->getQrcode(); 
} 
/*读取服务器地址*/
$http_host = ZywxappConfig::getInstance()->api_server;
/*创建服务器请求地址*/
$path = "/index.php?m=qrcode&d=".ZywxappConfig::getInstance()->zywxapp_qrcode_url."&e=H&s=6";
/*编码 URL 请求字符串*/
$qrcode_url = urlencode("http://{$http_host}{$path}");
$qrcode_img = get_bloginfo('url').'?zywxapp/getimage/&width=100&height=100&url='.$qrcode_url;

?>
<!--top-->
<div class="top">
    <div class="top_nav">
        <img src="<?php echo ZYWXAPP_IMAGES_URL;?>/mobile_client.png" width="169" height="48" class="fleft mobile_client"  />
        <ul class="fright mobile_system">
            <li class="mobile_iphone"><a <?php if ('iPhone' == $client) {echo 'class="top_stop"';}?> href="?zywxapp/system/promotion&client=iPhone"><span>iPhone</span></a></li>
            <li class="mobile_android"><a <?php if ('Android' == $client) {echo 'class="top_stop"';}?> href="?zywxapp/system/promotion&client=Android"><span>Android</span></a></li>
        </ul>
        <div class="clear"></div>
    </div>
</div>
<!--应用下载-->
<div class="part">
    <div class="mobile">
        <div class="fleft">
        <?php if ('iPhone' == $client) {?>
        <img src="<?php echo ZYWXAPP_IMAGES_URL;?>/mobile_iphone.png" width="492" height="311" />
        <?php } else {?>
        <img src="<?php echo ZYWXAPP_IMAGES_URL;?>/mobile_android.png" width="492" height="311" />
        <?php }?>
        </div>
        <div class="fright mobile_des">
            <div>
            <?php if ('iPhone' == $client) {?>
	        <img src="<?php echo ZYWXAPP_IMAGES_URL;?>/system_des_iphone.png" width="450" height="74" />
	        <?php } else {?>
	        <img src="<?php echo ZYWXAPP_IMAGES_URL;?>/system_des_android.png" width="330" height="81" />
	        <?php }?>
            </div>
            <a href="<?php echo $download_url;?>" id="download" class="download down_margin fright"></a>
            <div class="clear"></div>
            <div class="two_code fright">
                <p>使用您手机上的条形码扫描程序扫描此二维码，即可安装下载</p>
                <img src="<?php echo $qrcode_img;?>" width="100" height="100" />
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<!--功能介绍-->
<div class="part">
	<ul class="subtool">
    	<li class="li_mb">
        	<h3>手机流量优化</h3>
            <p>开启流量优化功能，可根据您所处的网络环境，智能优化2G、 3G和Wifi环境下的上传下载数据，为您省钱又省时。</p>
            <img src="<?php echo ZYWXAPP_IMAGES_URL;?>/andriod_icon1.png" />
        </li>
        <li>
        	<h3>快速标签，分类浏览</h3>
            <p>轻点分类或标签名称，即可浏览相关文章，所有分类和标签将会同步更新至您的客户端上。</p>
            <img src="<?php echo ZYWXAPP_IMAGES_URL;?>/andriod_icon3.png"  />
        </li>
        <li class="li_mb">
        	<h3>强大的分享和收藏功能</h3>
            <p>可以随时随地将文章分享到微博，把感兴趣的文章收藏起来。让推广效果更快速，浏览体验更强大。</p>
            <img src="<?php echo ZYWXAPP_IMAGES_URL;?>/andriod_icon2.png" />
        </li>
        <li>
        	<h3>丰富多彩的媒体库</h3>
            <p>将文章中的媒体信息自动保存到媒体库中。可让用户实时查看媒体信息。</p>
            <img src="<?php echo ZYWXAPP_IMAGES_URL;?>/andriod_icon4.png" />
        </li>
    </ul>
    <div class="clear"></div>
</div>
<!--footer-->
<div class="part">
	<div class="mobile_footer mb"><p>© 2011 - Appcan版权所有</p></div>
</div>


<div id="zywxapp_download_error" class="search_pop" style="display:none">
    <h2 class="pop_title"><span class="tip">来自网页的消息</span><a href="javascript:void(0);" class="close"></a></h2>
    <div class="range">
        <p class="tip_con"><?php echo $errors;?></p>
        <div class="pop_btn"><input type="button" value="关 闭" class="close" /></div>
    </div>
</div>

<?php get_footer(); ?>
<script type="text/javascript">
	var can_run = <?php echo (empty($errors)) ? 'true' :'false'; ?>;
	jQuery(document).ready(function(){
		if (! can_run ){
			var $box = jQuery('#zywxapp_download_error');
	        var overlayParams = {
	            top: 300,
	            left: (screen.width / 2) - ($box.outerWidth() / 2),
	            onClose: function(){
	                jQuery("#zywxapp_error_mask").hide();
	            },
	            onBeforeLoad: function(){
	                var $toCover = jQuery('body');
	                var $mask = jQuery('#zywxapp_error_mask');
	                if ( $mask.length == 0 ){
	                    $mask = jQuery('<div></div>').attr("id", "zywxapp_error_mask");
	                    jQuery("body").append($mask);
	                }
	                $mask.css({
	                    position:'absolute',
	                    top: $toCover.offset().top,
	                    left: $toCover.offset().left,
	                    width: $toCover.outerWidth(),
	                    height: $toCover.outerHeight(),
	                    display: 'block',
	                    opacity: 0.9,
	                    backgroundColor: '#444444',
	                });
	                $mask = $toCover = null;
	            },
	            closeOnClick: false,
	            closeOnEsc: false,
	            load: true
	      };
			$box.overlay(overlayParams);
	    }
	});
</script>