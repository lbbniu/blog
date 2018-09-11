/*任意位置浮动固定层*/
/*调用：
1 无参数调用：默认浮动在右下角
jQuery("#id").floatdiv();
2 内置固定位置浮动
//右下角
jQuery("#id").floatdiv("rightbottom");
//左下角
jQuery("#id").floatdiv("leftbottom");
//左上角
jQuery("#id").floatdiv("lefttop");
//右上角
jQuery("#id").floatdiv("righttop");
//居中
jQuery("#id").floatdiv("middle");
3 自定义位置浮动
jQuery("#id").floatdiv({left:"10px",top:"10px"});
以上参数，设置浮动层在left 10个像素,top 10个像素的位置*/
jQuery.fn.floatdiv = function(location){
	//ie6要隐藏纵向滚动条
	var isIE6=false;
	if(jQuery.browser.msie && jQuery.browser.version=="6.0"){
		jQuery("html").css("overflow-x","auto").css("overflow-y","hidden");
		isIE6=true;
	};
	jQuery("body").css({
	   margin:"0px",
	   //padding:"0 10px",
	   border:"0px",
	   //height:"100%",
	   overflow:"auto"
	});
	return this.each(function(){
		var loc;//层的绝对定位位置
		if(location==undefined || location.constructor == String){
			switch(location){
				case("rightbottom")://右下角
					loc={right:"0px",bottom:"0px"};
					break;
				case("leftbottom")://左下角
					loc={left:"0px",bottom:"0px"};
					break; 
				case("lefttop")://左上角
					loc={left:"0px",top:"0px"};
					break;
				case("righttop")://右上角
					loc={right:"0px",top:"0px"};
					break;
				case("middle")://居中
					var l=0;//居左
					var t=0;//居上
					var windowWidth,windowHeight;//窗口的高和宽
					//取得窗口的高和宽
					if (self.innerHeight) {
						windowWidth = self.innerWidth;
						windowHeight = self.innerHeight;
					} else if (document.documentElement&&document.documentElement.clientHeight) {
						windowWidth = document.documentElement.clientWidth;
						windowHeight = document.documentElement.clientHeight;
					} else if (document.body) {
						windowWidth = document.body.clientWidth;
						windowHeight = document.body.clientHeight;
					}
					l = windowWidth/2-jQuery(this).width()/2;
					t = windowHeight/2-jQuery(this).height()/2;
					loc = {left:l+"px",top:t+"px"};
				break;
				default://默认为右下角
					loc={right:"0px",bottom:"0px"};
				break;
			}
		} else {
			loc=location;
		}      
		jQuery(this).css("z-index","9999").css(loc).css("position","fixed");
		if(isIE6){
			if(loc.right!=undefined){
				jQuery(this).css("right","18px");
			}
			jQuery(this).css("position","absolute");
		}
	});
};

jQuery(function(){
	jQuery("#win_right_down").floatdiv("");

	jQuery(window).load(function(){
		jQuery("div[id=win_right_down]").slideDown("slow");
	});

	jQuery("label[id=tomin]").click(function(){
	   jQuery("div[id=win_right_down_con]","div[id=win_right_down]").slideUp();
	   jQuery(this).hide();
	   jQuery("label[id=tomax]").show();
	});

	jQuery("label[id=tomax]").click(function(){
	   jQuery("div[id=win_right_down_con]","div[id=win_right_down]").slideDown();
	   jQuery(this).hide();
	   jQuery("label[id=tomin]").show();
	});

	jQuery("label[id=toclose]").click(function(){
	   jQuery("div[id=win_right_down]").hide();
	});

});

suspendcode ='<div id="win_right_down">'+
	'<p class="top_box"><span class="title">站长推荐</span>'+
		'<span id="bts">'+
		'<label class="button" id="toclose" title="关闭"></label>'+
			'<label class="button" id="tomin" title="最小化"></label>'+
			'<label class="button" id="tomax" title="最大化"></label>'+
			
		'</span>'+
	'</p>'+
	'<div id="win_right_down_con">'+
		'<p class="fcb">本站推出手机客户端，支持Iphone，Android双平台，点击进入下载页面</p>'+
		'<p><a href="?zywxapp/system/promotion&client=iPhone" class="pop_system">iPhone</a><a href="?zywxapp/system/promotion&client=Android">Android</a></p>'+
		'<div class="pop_btn"><a href="?zywxapp/system/promotion">查看</a></div>'+
	'</div></div>';
document.write(suspendcode);
