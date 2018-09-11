<?php
/*
Template Name: 投稿页面(不懂勿用)
*/
?>

<?php get_header(); ?>

<style>
.add_post fieldset { margin: 10px 0; border: 0; }
.add_post fieldset label { float: left; width: 100px; line-height: 28px; }
.add_post fieldset input { float: left; width: 665px; border: #BBB solid 1px; }
.add_post fieldset input:focus, 
.add_post textarea:focus {
	border: #FEC42D solid 1px;
	box-shadow:0 0 2px #FFDB61;
}
.add_post textarea { width: 665px; border: #BBB solid 1px; padding: 2px; font-size: 12px; }
.add_post fieldset input, .add_post textarea {padding: 5px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) inset; border-radius: 3px;}
fieldset.user-image label { clear: both; }
.add_post input.user-clone { width: 100%; margin: 3px 0; padding: 0; line-height: 18px; font-size: 12px; border: 1px solid #ccc; background-color: #fafafa; }
#user-upload-message { float: right; font-size: 12px; }
#user-image { width: 676px; float: left; position: relative;  }
.image_tips {
	position: absolute;
	top: 5px;
	left: 5px;
	color: #Fa7d0f;
	z-index: 10;
}
.submit {
	border-radius: 3px;
	border: 1px solid #FA7D0F;
	box-shadow: 0px 0px 1px #FA7D0F;
	color: #Fa7d0f;
	font-weight: bold;
	padding: 8px 22px;
	cursor: pointer;
	font-size: 14px;
	margin-bottom: 12px;

	background: #ffffff; /* Old browsers */
	background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 47%, #ededed 100%); /* FF3.6+ */
	background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%); /* IE10+ */
	background: linear-gradient(to bottom,  #ffffff 0%,#f6f6f6 47%,#ededed 100%); /* W3C */
}
.submit:hover {background: #EEE;}

.tip a {color:#74B129;}
.panel h2 {font-size: 14px;font-weight: bold;margin-bottom: 5px;}
.video_hint {display: none;}
.video_hint table {background-color: #EFF4D4;}
.video_hint table td {border-right: 2px solid #FFF;border-bottom: 2px solid #FFF;padding: 3px 5px;}
.widget li{margin-top: 8px;color:#525252;text-shadow: 0 1px white;}
.uploadbtn {
	position: relative;
	overflow: hidden;
	display: inline-block;
	float: left;
	padding: 4px 10px 4px;
	font-size: 12px;
	line-height: 18px;
	text-align: center;
	vertical-align: middle;
	cursor: pointer;
	background: #Fa7d0f;
	border-radius: 5px;
	color: #FFF;
}
.uploadbtn input {
	width: inherit !important;
	position: absolute;
	top: 0;
	right: 0;
	margin: 0;
	border:solid transparent; 
	opacity: 0;
	filter:alpha(opacity=0);
	cursor: pointer;
	transform: scale(3);
}

.box {
	/*background-color: #F4F4F4;*/
	/*border-bottom: 1px solid #98CAFF;*/
	border-radius: 5px;
	padding: 5px;
	display: inline-block;
	width: 665px;
}
.box:hover {background-color: #EEEEEE;}
.term {
	border: 2px solid transparent;
	padding: 0 4px;
	display:inline-block; *display:inline;
	cursor: pointer;
	margin: 0 3px 3px 0;
	line-height: 16px;
	position: relative;
	background-color: #DAF7FF;
	color: #Fa7d0f;
	-moz-user-select: none;
}
.term:hover {
	background-color: #Fa7d0f;
	color: #FFF;
}
.user-cat .term:before {
	content: "\e007";
	color: #Fa7d0f;
	margin-right: 3px;
	font-family: 'blueria';
	font-size: 12px;
}
.user-cat .term:hover:before {color: #FFF;}
.user-cat .selected:hover:before {color: #Fa7d0f;}
.user-tag .term:before {
	content: "\e008";
	color: #Fa7d0f;
	margin-right: 3px;
	font-family: 'blueria';
}
.user-tag .term:hover:before {color: #FFF;}
.user-tag .selected:hover:before {color: #Fa7d0f;}
.term i {display: none;}
.term.selected {
	border: 2px solid #Fa7d0f;
	background-color: #FFF;
	color: #Fa7d0f;
}
.term.selected i {
	display: block;
	position: absolute;
	z-index: 999;
	bottom: -2px;
	right: -2px;
	width: 17px;
	height: 17px;
	background: url(<?php bloginfo('template_url');?>/ui/select.png) no-repeat scroll 0 0 transparent;
}
</style>

<div id="container" class="clearfix layout_overall">
	<div id="main" class='layout_left'>
		<div class="topbox">
			<span class="tab tab_img curr_tab" title="发布笑料"><a href='javascript:;'></a></span><span class="tab tab_vdo" title="发布视频笑料"><a href='javascript:;'></a></span>
		</div>
		
		<div class="panel clearfix">
			<form method="post" enctype="multipart/form-data" action="" class="add_post">
				<h2 class='title'>发布新笑料</h2>
				<div class='Introduction'></div>
				<div class="logstatus" style="display:none;"><font style="font-size:14px; color:#FF0000;">　　只有登录后，才能分享笑料，请点击右上方的登录按钮，或者</font><a href="javascript:;" style="font-size:14px;color:#74B129;"><font style="font-size:15px;">点击此处选择更多登陆方式</a></font><font style="font-size:14px; color:#FF0000;">，无需注册即可登录哦！~~~</font></div>
				
				<?php if($_REQUEST['error'] == '1') { ?>
					<div class='tip'><font style="font-size:14px; color:#FF0000;">　　出错了！（＞﹏＜）请确认您是否输入了内容▪▪▪ </font> <a href='javascript:history.go(-1);' style="border-bottom:1px solid; font-size:14px;">点此返回</a></div>
				<?php } elseif ($_REQUEST['error'] == '2') { ?>
					<div class='tip'><font style="font-size:14px; color:#FF0000;">　　出错了！（＞﹏＜）你没有添加图片或者视频哦...</font> <a href='javascript:history.go(-1);' style="border-bottom:1px solid; font-size:14px;">点此返回</a></div>
				<?php } elseif ($_REQUEST['success'] == '1') { ?>
					<div class='tip'><font style="font-size:14px; color:#FF0000;">　　发布成功！o(∩_∩)o 谢谢您的分享...您发布的新笑料将直接显示在首页。3秒后为您跳转▪▪▪</font></div>
					<script type="text/javascript">setTimeout("location.href='<?php echo home_url('/');?>'",3000);</script>
				<?php } elseif ($_REQUEST['success'] == '2') { ?>
					<div class='tip'><font style="font-size:14px; color:#FF0000;">　　发布成功，谢谢您的分享！o(∩_∩)o</br></br>
　　您发布的新笑料需要用户点赞审核，赞数达到一定量将自动显示到首页，</font><font style="font-size:14px; color:#74B129;"></br></br>　　马上为您跳转至&nbsp;审贴&nbsp;页面...</font></br></br></br></div>
					<script type="text/javascript">setTimeout("location.href='<?php echo home_url('/ticket-post');?>'",3000);</script>
				<?php } else { ?>
				
				<div class='post_form'>
				<fieldset class="user-name">
					<label for="user-name">用户名</label>
					<input name="user-name" type="text" value="" placeholder="你的昵称" style="background:#F5F5F5;">
					<input name="user-avatar" type="hidden" value="" />
					<input name="user-duoshuo" type="hidden" value="" />
					<input name="user-type" type="hidden" value="image" class='media_type' />
				</fieldset>
				
				
				<fieldset class="user-title" style='display:none;'>
					<label for="user-title">标题</label>
					<input name="user-title" type="text" value="" placeholder="输入一个简单的标题 不要超过30个汉字">
				</fieldset>
				
				<fieldset class="user-content">
					<label for="user-content">笑料内容</label>
					<textarea name="user-content" rows="10" placeholder="输入您要分享的笑料……"></textarea>
				</fieldset>
				
				<fieldset class="user-image">
					<label for="user-image">图片地址</label>
					<div id="user-image">
						<?php $stop_imageup = get_option('stop_imageup'); if ( $stop_imageup == '1' ) : ?>
						<div class="image_tips">粘贴来自网上的图片地址</div>
						<input name="user-image2" id='image_url' type="text">
						<?php else : ?>
						<div class="image_tips">粘贴来自网上的图片地址</div>
						<input name="user-image2" id='image_url' class='image_url2' type="text" style='width:456px'>
						<span style='padding:5px 3px;display:inline-block;float:left;'>或者</span>
						<div class='uploadbtn'><span>点此上传单张图片</span><input name="user-image" id='upload_image' type="file" size='1'></div>
						<span style='padding:5px 3px;display:inline-block;float:left;'>或</span><div class='uploadbtn' id='multi_image'>多图</div>
						<?php endif; ?>
						<div id="user-upload-message" style='white-space: nowrap;'>注：您上传的图片不能大于2M，超过2M将不会显示</div>
					</div>
				</fieldset>
				
				<fieldset class="user-video" style='display:none;'>
					<label for="user-video">视频地址</label>
					<div id="user-video">
						<input name="user-video" type="text" value="" placeholder="输入您要发布的视频地址，支持的视频网站及其地址格式请看下面的说明↓↓↓">
					</div>
				</fieldset>
				
				<?php
				$tougao_option = get_option('tougao_option');
				if ( $tougao_option['cats'] ) :
				?>
				<fieldset class="user-cat">
					<label for="user-cat">选择分类</label>
					<div class='box'>
					<?php
					foreach ($tougao_option['cats'] as $cat_id) {
						$cat = get_term_by('id', $cat_id, 'category');
						if ($cat->slug == 'video' || $cat->slug == 'image') continue;	//默认分类不显示
						echo "<div class='term' tid='{$cat_id}' type='cat'>{$cat->name}<i></i></div>";
					}
					?>
					</div>
				</fieldset>
				<?php endif; ?>
				
				<?php if ( $tougao_option['tags'] ) : ?>
				<fieldset class="user-tag">
					<label for="user-tag" style="margin-top: -5px;">贴上标签</label>
					<div class='box' style="margin-top: -5px;">
					<?php
					foreach ($tougao_option['tags'] as $tag_id) {
						$cat = get_term_by('id', $tag_id, 'post_tag');
						echo "<div class='term' tid='{$tag_id}' type='tag'>{$cat->name}<i></i></div>";
					}
					?>
					</div>
				</fieldset>
				<?php endif; ?>
				
				<div id="user-submit">
					<input type="hidden" value="send" name="addpost_form" />
					<input name="user-post" type="submit" class='submit' value="发 布">
				</div>
				</div>
				
				<?php } ?>
			</form>
		</div>
  
		<div class="main .panel">
			<div class='video_hint'>
				
				<TABLE width='100%' style="margin-bottom: 10px;">
					<tr><td><b>支持的视频网站</b></td><td><b>对应的格式如下</b></td></tr>
					<tr><td>优酷网<br />(youku.com)</td><td>
						http://v.youku.com/v_show/id_XNDgzNjE0MDg4.html<br />
						http://player.youku.com/player.php/sid/XNDgzNjE0MDg4/v.swf
					</td></tr>
					<tr><td>土豆网<br />(tudou.com)</td><td>
						http://www.tudou.com/programs/view/h__TnYYa9h0/<br />
						http://www.tudou.com/v/h__TnYYa9h0/v.swf<br />
						http://www.tudou.com/v/h__TnYYa9h0/&iid=164913545/v.swf<br />
						http://www.tudou.com/listplay/ceTuDvFh_C8/Hy7WoC0VtWY.html<br />
						http://www.tudou.com/l/ceTuDvFh_C8/&iid=173372904/v.swf<br />
						http://www.tudou.com/albumplay/Lqfme5hSolM/TNpbAE0HY0s.html<br />
						http://www.tudou.com/a/Lqfme5hSolM/&iid=131563536/v.swf
					</td></tr>
					<tr><td>56网<br />(56.com)</td><td>
						http://www.56.com/u17/v_ODk5MjY5NDI.html<br />
						http://player.56.com/v_ODk5MjY5NDI.swf
					</td></tr>
					<tr><td>酷6网<br />(ku6.com)</td><td>
						http://player.ku6.com/refer/9G0yzmMQX20OGVuCRWkRzw../v.swf<br />
						http://v.ku6.com/show/9G0yzmMQX20OGVuCRWkRzw...html<br />
						http://v.ku6.com/special/show_6605018/1F6PM2m4uz7Gjk7faY90nQ...html<br />
						http://v.ku6.com/film/index_124015.html<br />
						http://v.ku6.com/film/show_124015/mGXv_xMB9vj6rOZU7rqccg...html
					</td></tr>
					<tr><td>PPS<br />(pps.tv)</td><td>
						http://player.pps.tv/player/sid/37OOJC/v.swf<br />
						http://v.pps.tv/play_37OOJC.html<br />
						http://ipd.pps.tv/play_37QPEZ.html
					</td></tr>
					<tr><td>QQ视频<br />(v.qq.com)</td><td>
						http://static.video.qq.com/TPout.swf?vid=i0012vj54yu&auto=1<br />
						http://v.qq.com/cover/2/2mc5z2bk9i87ugw.html?vid=s0117xhl5qv<br />
						http://v.qq.com/cover/0/0114rk3aq7q1kqg/i0012vj54yu.html<br />
						http://v.qq.com/cover/0/0114rk3aq7q1kqg.html<br />
						http://film.qq.com/cover/0/0gtuho3asrigan4.html<br />
						http://v.qq.com/page/f/q/6/f0013twizq6.html<br />
						http://v.qq.com/boke/page/z/y/q/z0117a3b4yq.html
					</td></tr>
					<tr><td>PPTV聚力<br />(pptv.com)</td><td>
						http://v.pptv.com/show/K08cmkUFe7kcmgI.html<br />
						http://player.pptv.com/v/K08cmkUFe7kcmgI.swf
					</td></tr>
					<tr><td>新浪视频<br />(video.sina.com.cn)</td><td>
						http://video.sina.com.cn/p/news/w/v/2013-08-13/211162781691.html<br />
						http://video.sina.com.cn/v/b/111991028-1891602434.html<br />
						注: 新浪视频只能用结尾是html的地址 不能使用swf形式的地址
					</td></tr>
					<tr><td>搜狐视频<br />(tv.sohu.com)</td><td>
						http://tv.sohu.com/20130415/n372763492.shtml<br />
						http://share.vrs.sohu.com/1006748/v.swf<br />
						http://my.tv.sohu.com/us/174301892/59509922.shtml<br />
						http://my.tv.sohu.com/user/detail/91513718.shtml<br />
						http://share.vrs.sohu.com/my/v.swf&id=59509922
					</td></tr>
				</TABLE>
			</div>			
		</div>	
		<div style='display:none'><div id="multi_img_upload"></div></div>
	</div>
	<div id="sidebar" class='layout_right'>
		<ul class='widgetbox floatStart'>
			<li class='widget'>
				<font style="font-size:16px; color:#FF0000; font-weight:bold;">发布规则：</font>
				<ul>
					<li>1. 分享自己看到的笑料、笑图、视频，真实有笑点。</li>
					<li>2. 不含政治、色情、广告、个人隐私、歧视等内容。</li>		
					<li>3. 发视频请参考视频格式说明，<b>不符合将无法显示</b>。</li>
					<li>4. 笑料需用户<b>点赞审核</b>通过后，方可显示。</li>					
					<li>5. 转载请<b>注明出处</b>。</li>					
					<li>6. 内容版权归<font style="font-size:14px; color:#FF0000; font-weight:bold;">我爱笑</font>网站所有。</li>
				</ul>
			</li>
		</ul>
	</div>
	 
</div>

<script type="text/javascript" src="<?php bloginfo('template_url');?>/js/jquery.blockUI.js"></script>
<script src="http://open.web.meitu.com/sources/xiuxiu.js" type="text/javascript"></script>
<script>
window.prev_multi_img = '';	//用于存储前一个多图拼接
$(function(){
	$("#image_url").focus(function(){
		$(".image_tips").fadeOut('fast');
	}).blur(function(){
		if ($("#image_url").val() == '') $(".image_tips").fadeIn('fast');
		else $("#upload_image").val('');
		if ($("#upload_multi_image").size()) removeMutiImage(this, '网络地址');
	});
	$(".image_tips").click(function(){ 
		$(this).fadeOut('fast');
		$("#image_url").focus();
	});
	if ($("#image_url").val() != '') $(".image_tips").hide();

	$("#multi_image").click(function(){
		$.blockUI({
			message: $('#multi_img_upload'),
			overlayCSS: { cursor: 'default' },
			// onOverlayClick: $.unblockUI,
			css: { width: '700px', height: '500px', top: '10%', left: ($(document).width() - 700)/2 + 'px' } 
		});
	});
	$(window).on('beforeunload', function(){
		if (prev_multi_img != '') {
			delTempImg(prev_multi_img);		//页面关闭或刷新时，删除临时产生的多图拼接(提交情况已被排除)
			$(".image_tips").html( "粘贴来自网上的图片地址 也可以点右边的按钮进行上传" );
			$("#upload_multi_image").remove();
			return "客官，无需留念！请点『确定』！";
		}
	});
	$("form").on('submit', function(){
		$(window).off('beforeunload');	//提交时不触发beforeunload事件
	});
});
$("#upload_image").change(function(){
	if ($("#upload_multi_image").size() == 0 || removeMutiImage(this, '单张上传') == 1) {
		$(".image_tips").html( '你选择的是单张图片: '+$(this).val() ).fadeIn(800);
		$("#image_url").val('');
	}
});
function removeMutiImage(obj, msg) {
	if ($(obj).val() == '') return -1;
	if ( confirm("你确定使用"+msg+"，从而放弃多图拼接上传吗？") ) {
		$("#upload_multi_image").remove();
		delTempImg(prev_multi_img);
		$(".image_tips").html( "<font color='red'>你刚刚放弃了多图拼接 如需要请重新上传</font>" );
		return 1;
	} else { //如果不放弃多图拼接上传 就清空其他方式的值
		$(obj).val('');
		$(".image_tips").fadeIn(800);
		return 0;
	}
}
function delTempImg(filename) {
	$.post(ajaxurl, {'action':'blueria_delTempImg', 'file_name':filename}, function(result, stat) {
		if ( 'success' != stat ) return false;
		if ( 0 == result ) return false;
		if ( prev_multi_img == result ) prev_multi_img = '';	//如果已经删除的文件名与当前存储的文件名相同 则重置
	});
}

xiuxiu.params.wmode = "transparent";
xiuxiu.setLaunchVars ("scrollTrap", 1);				//禁用网页滚动
xiuxiu.setLaunchVars ("uploadBtnLabel", '确认上传');//上传按钮文本
xiuxiu.setLaunchVars ("maxFinalWidth", 500);		//生成图片的最大宽度
xiuxiu.embedSWF("multi_img_upload", 2, "100%", 500, 'multi_img_upload');
xiuxiu.onInit = function (id) {
	// xiuxiu.loadPhoto("http://open.web.meitu.com/sources/images/1.jpg");
	xiuxiu.setUploadURL("<?php bloginfo('template_url');?>/multi_images.php");
	xiuxiu.setUploadType(2);
	xiuxiu.setUploadDataFieldName("multi_image");
}
xiuxiu.onDebug = function (data, id) {
	alert("错误响应" + data);
}
xiuxiu.onBeforeUpload = function (data, id) {
	if (prev_multi_img != '') delTempImg(prev_multi_img); //如果存在前一个拼图操作就试图检测是否是临时文件以便删除
}
xiuxiu.onUploadResponse= function(data, id) {
	$.unblockUI();
	var info = eval(data);
	if (info[0] === 1) {
		$(".image_tips").html( '你选择的是多图拼接: '+info[1] ).fadeOut(1000).fadeIn(800);
		if ($("#upload_multi_image").size() == 0) {
			$("#image_url").after("<input name='user-image3' id='upload_multi_image' type='hidden' value='"+info[1]+"' />");
		} else {
			$("#upload_multi_image").val(info[1]);
		}
		prev_multi_img = info[1];
	} else if (info[0] === 0) {
		$(".image_tips").html( "<font color='red'>"+info[1]+"</font>" ).fadeOut(1000).fadeIn(800);
	}
	$("#image_url").val('');
}
xiuxiu.onClose = function (id) {
	$.unblockUI();
}



function ds(s) {
	if (s === 0) {
		$(".logstatus").show();
		$(".post_form").css('visibility','hidden');
		$(".logstatus a").on('click',function(e){e.stopPropagation();moreLogin();});
	}
	if (s === 1) {
		$(".logstatus").hide();
		// $(".user-avatar img").attr("src",DUOSHUO.visitor.data.avatar_url);
		$(".user-name input[name='user-name']").val(DUOSHUO.visitor.data.name).focus(function(){ $(this).blur(); });
		$(".user-name input[name='user-avatar']").val(DUOSHUO.visitor.data.avatar_url);
		$(".user-name input[name='user-duoshuo']").val(DUOSHUO.visitor.data.user_id);
		$(".user-url input").val(DUOSHUO.visitor.data.url).focus(function(){ $(this).blur(); });
	}
}

$('.user-title input').attr("max","30").inputlimiter();
$('.user-content textarea').live('focusin focusout', function(e){
	$(this).attr("max","300").inputlimiter(true);
});

$(".tab_img").click(function(){
	$(this).addClass('curr_tab');
	$(".tab_vdo").removeClass('curr_tab');
	$('.user-video').hide().find('input').val('');
	$('.user-image').show();
	$('.add_post .title').html('发布新笑料');
	$('.media_type').val('image');
	$('.video_hint').hide();
	setCookie('tab_name','img',7*24*60*60);
});
$(".tab_vdo").click(function(){
	$(this).addClass('curr_tab');
	$(".tab_img").removeClass('curr_tab');
	$('.user-image').hide();
	$("#image_url").val('');
	$("#upload_image").val('');
	if ( $("#upload_multi_image").size() ) $("#upload_multi_image").remove();
	$('.user-video').show();
	$('.add_post .title').html('发布新视频');
	$('.media_type').val('video');
	$('.video_hint').show();
	setCookie('tab_name','vdo',7*24*60*60);
});
$(function(){
	if (getCookie('tab_name') == 'img') $(".tab_img").click();
	if (getCookie('tab_name') == 'vdo') $(".tab_vdo").click();
	$(".floatStart").scrollFollow({
		bottomObj: '#footer',
		marginTop: 65,
		marginBottom: 5
	});
	$(".term").click(function(){
		var _this = $(this);
		var tid = _this.attr('tid');
		var type = _this.attr('type');
		if ( _this.hasClass("selected") ) {
			_this.removeClass("selected");
			if (type == 'cat') {
				$("#cat_"+tid).remove();
			} else if (type == 'tag') {
				$("#tag_"+tid).remove();
			}
		} else {
			if (type == 'cat') {
			<?php if (get_option('as_radio')) : ?>
				$("input[id^='cat_']").remove();
				$(".term[type='cat']").removeClass("selected");
			<?php endif; ?>
				_this.addClass("selected");
				_this.after("<input name='cats[]' id='cat_"+tid+"' type='hidden' value='"+tid+"' />");
			} else if (type == 'tag') {
				_this.addClass("selected");
				_this.after("<input name='tags[]' id='tag_"+tid+"' type='hidden' value='"+tid+"' />");
			}
		}
	});
});
</script>

<?php get_footer(); ?>