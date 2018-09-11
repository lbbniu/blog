(function($) { 
	$(function() {
		$("#post-format-0").attr('disabled', 1);	//禁用[标准]形式
		if ($("#post-formats-select input[value!='0']:checked").length == 0) {
			$("#post-format-aside").click();		//新建文章时 默认选择[日志]
		}
		// $("select[name='post_format'] option[value='0']").attr('disabled', 1);

	//投稿是否需要审核
	$("#post_audit").click(function(){
		_this = $(this);
		var key = !!_this.attr('checked') ? '1' : '0';
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'post_audit', 'key':key}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			tip = _this.parent().find('.tip');
			if (result == 'yes') tip.html('(已设置为: 需要审核)');
			if (result == 'no') tip.html('(已设置为: 无需审核)');
		});
	});
	//禁止外链被点击
	$("#stop_external_link").click(function(){
		_this = $(this);
		var key = !!_this.attr('checked') ? '1' : '0';
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'stop_external_link', 'key':key}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			tip = _this.parent().find('.tip');
			if (result == 'yes') tip.html('(禁止点击)');
			if (result == 'no') tip.html('(允许点击)');
		});
	});
	//禁用前台投稿上传图片
	$("#stop_imageup").click(function(){
		_this = $(this);
		var key = _this.is(':checked') ? '1' : '0';
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'stop_imageup', 'key':key}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			tip = _this.parent().find('.tip');
			if (result == 'yes') tip.html('(已设置为: 禁用上传)');
			if (result == 'no') tip.html('(已设置为: 允许上传)');
		});
	});
	//前台投稿只能单选分类
	$("#as_radio").click(function(){
		_this = $(this);
		var key = _this.is(':checked') ? '1' : '0';
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'as_radio', 'key':key}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			tip = _this.parent().find('.tip');
			if (result == 'yes') tip.html('(已设置为: 只能单选分类)');
			if (result == 'no') tip.html('(已设置为: 允许多选分类)');
		});
	});
	//关闭IE模式切换提示
	$("#stop_ietips").click(function(){
		_this = $(this);
		var key = _this.is(':checked') ? '1' : '0';
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'stop_ietips', 'key':key}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			tip = _this.parent().find('.tip');
			if (result == 'yes') tip.html('(已设置为: 关闭提示)');
			if (result == 'no') tip.html('(已设置为: 有提示)');
		});
	});
	//默认显示友情链接
	$("#show_links").click(function(){
		_this = $(this);
		var key = _this.is(':checked') ? '1' : '0';
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'show_links', 'key':key}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			tip = _this.parent().find('.tip');
			if (result == 'yes') tip.html('(已设置为: 默认显示)');
			if (result == 'no') tip.html('(已设置为: 默认不显示)');
		});
	});

	$("#theme_tpl_save").click(function(){
		_this = $(this);
		var load = _this.next('.spinner').show();
		var data = $("#theme_tpl_options").serialize();
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'theme_tpl_save', 'key':data}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			if (result == 'ok') load.hide();
		});
		return false;
	});

	$("#bdshare_options_save").click(function(){
		_this = $(this);
		var load = _this.next('.spinner').show();
		var data = $("#bdshare_options").serialize();
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'bdshare_options_save', 'key':data}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			if (result == 'ok') load.hide();
		});
		return false;
	});

	$("#related_options_save").click(function(){
		_this = $(this);
		var load = _this.next('.spinner').show();
		var data = $("#related_options").serialize();
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'related_options_save', 'key':data}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			if (result == 'ok') load.hide();
		});
		return false;
	});

	//网站顶部设置
	$("#sitetop_bg_color").focus(function(){
		$("#sitetop_bg_type_color").attr('checked', true);
	});
	$("#sitetop_bg_image").focus(function(){
		$("#sitetop_bg_type_image").attr('checked', true);
	});
	$("#sitetop_options_save").click(function(){
		_this = $(this);
		var load = _this.next('.spinner').show();
		var data = $("#sitetop_options").serialize();
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'sitetop_options_save', 'key':data}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}
			if (result == -2) {alert('颜色值不合法!!!');return false;}

			if (result == 'ok') load.hide();
		});
		return false;
	});

	$("#option_save").click(function(){
		_this = $(this);
		var load = _this.next('.spinner').show();
		var data = $("#settings_form").serialize();
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'option_save', 'key':data}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			if (result == 'ok') load.hide();
		});
		return false;
	});
	$("#bcs_option_save").click(function(){
		_this = $(this);
		var load = _this.next('.spinner').show();
		var data = $("#bcs_settings").serialize();
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'bcs_option_save', 'key':data}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			if (result == 'ok') load.hide();
		});
		return false;
	});
	$("#image_cut_option_save").click(function(){
		_this = $(this);
		var load = _this.next('.spinner').show();
		var image_cut = $('#image_cut').prop("checked")?1:0;
		var image_cut_height = $('#image_cut_height').val();
		var data = 'home_image_cut='+image_cut+'&home_image_cut_height='+image_cut_height;
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'image_cut_option', 'key':data}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			if (result == 'ok') load.hide();
		});
		return false;
	});
	$("#wechat_options_save").click(function(){
		_this = $(this);
		var load = _this.next('.spinner').show();
		var data = $("#wechat_options").serialize();
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'wechat_options_save', 'key':data}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			if (result == 'ok') load.hide();
		});
		return false;
	});

	$("#init_theme").click(function(){
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'init_theme', 'key':1}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			if (result == 'ok') location.reload();	//刷新
		});
		return false;
	});

	$("#acf-field-videourl").live('focus', function(e){
		if ( $(this).next().hasClass("parser_video") ) return false;
		
		$(this).parent().addClass('clearfix');
		$(this).css({'width':'80%','float':'left','margin-right':'5px'});
		$(this).after("<button class='button parser_video' style='margin:1px;float:left;'>点此解析视频地址</button><span class='spinner' style='float:left;'></span>");
	});
	$(".parser_video").live('click', function(e){
		var video = $(this).prev();
		var v_url = video.val();
		if (v_url == '') return false;
		var load = $(this).next('.spinner');
		load.show();

		$.post(ajaxurl, {'action':'blueria_parser_video', 'url':v_url}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if ( '-1' == result ) {alert('此类视频网站未被支持,请参考前台投稿页...');return false;}
			if ( '-2' == result ) {alert('视频地址格式有误,请参考前台投稿页...');return false;}
			if (result.charAt(0) != '[') {alert(result);return false;}

			if ( result.length > 2 ) {
				var data = eval(result);
				$("#acf-field-img").val(data[0]);
				$("#acf-field-img_thumb").val(data[1]);
				video.val(data[2]);
				load.hide();
			}
		});

		return false;
	});
	$("#acf-field-imgurl").live('focus', function(e){
		if ( $(this).next().hasClass("parser_image") ) return false;
		
		$(this).parent().addClass('clearfix');
		$(this).css({'width':'80%','float':'left','margin-right':'5px'});
		$(this).after("<button class='button parser_image' style='margin:1px;float:left;'>点此获取尺寸</button><span class='spinner' style='float:left;'></span>");
	});
	$(".parser_image").live('click', function(e){
		var image = $(this).prev();
		var i_url = image.val();
		if (i_url == '') return false;
		var load = $(this).next('.spinner');
		load.show();

		$.post(ajaxurl, {'action':'blueria_parser_image', 'url':i_url}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if ( '-1' == result ) alert("获取失败");

			if ( result.length > 2 ) {
				var data = eval(result);
				$("#acf-field-img_w").val(data[0]);
				$("#acf-field-img_h").val(data[1]);
				load.hide();
			}
		});

		return false;
	});

	var cats = [];
	var tags = [];
	var tougao = {};
	if ($(".term").size()) {	//初始化数组(将已经设置的存入数组)
		$(".term").each(function(i){
			var _this = $(this);
			var tid = _this.attr('tid');
			var type = _this.attr('type');
			if ( !_this.hasClass("selected") ) return true; //如果未被选择 continue
			if (type == 'cat') {
				cats.push(tid);
			} else if (type == 'tag') {
				tags.push(tid);
			}
		});
	}
	$(".term").click(function(){
		var _this = $(this);
		var tid = _this.attr('tid');
		var type = _this.attr('type');
		if (_this.hasClass("selected")) {
			_this.removeClass("selected");
			if (type == 'cat') {
				index = $.inArray(tid, cats);
				if (index != -1) cats.splice(index, 1);	//如果id在数组中 删除指定位置的数组元素
			} else if (type == 'tag') {
				index = $.inArray(tid, tags);
				if (index != -1) tags.splice(index, 1);	//如果id在数组中 删除指定位置的数组元素
			}
		} else {
			_this.addClass("selected");
			if (type == 'cat') {
				index = $.inArray(tid, cats);
				if (index == -1) cats.push(tid);	//如果id不在数组中 添加进数组
			} else if (type == 'tag') {
				index = $.inArray(tid, tags);
				if (index == -1) tags.push(tid);	//如果id不在数组中 添加进数组
			}
		}
		tougao.cats = cats;
		tougao.tags = tags;
		//alert( decodeURIComponent($.param(tougao)) );
	});
	$("#tougao_option_save").click(function(){
		_this = $(this);
		var data = decodeURIComponent($.param(tougao));
		var load = _this.next('.spinner').show();
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'tougao_option_save', 'key':data}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			if (result == 'ok') load.hide();
		});
		return false;
	});

	$("#watermark_type_image").click(function(){
		$(".watermark_img").show();
		$(".watermark_txt").hide();
	});
	$("#watermark_type_text").click(function(){
		$(".watermark_txt").show();
		$(".watermark_img").hide();
	});
	$("[id^='watermark_type']:checked").click();	//触发点击
	$("#watermark_options_save").click(function(){
		_this = $(this);
		var load = _this.next('.spinner').show();
		var data = $("#watermark_options").serialize();
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'watermark_options_save', 'key':data}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}
			if (result == -2) {alert('颜色值不合法!!!');return false;}

			if (result == 'ok') load.hide();
		});
		return false;
	});

	$("#mobi_ads").click(function(){
		_this = $(this);
		var load = _this.next('.spinner').show();
		var data = $("#mobi_ads_settings").serialize();
		$.post(ajaxurl, {'action':'blueria_sittings', 'type':'mobi_ads_settings', 'key':data}, function(result, stat) {
			if ( 'success' != stat ) return false;
			if ( 0 == result ) return false;
			if (result == -1) {alert('非法操作!!!');return false;}

			if (result == 'ok') load.hide();
		});
		return false;
	});

	});
})(jQuery);
QTags.addButton( 'nextpage', '插入分页标记', "\n<!--nextpage-->", '' );