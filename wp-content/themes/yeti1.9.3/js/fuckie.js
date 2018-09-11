(function($){
	$(function(){
		if ('off' != getCookie('fuckie')) {
			if ('yes' != stop_ietips) $("<div id='fuckie'><div class='tips'><div class='btn'></div></div></div>").appendTo('body');
		}
		$("#fuckie .btn").click(function(){
			$("#fuckie").hide();
			setCookie('fuckie', 'off', 30*24*3600);	//如果没有切换 一个月后继续提示
		});
		$(window).scroll(function(){
			$("#ie6-warning").css({
				'top': $(document).scrollTop(),
				'left': $(document).scrollLeft()
			});
		});
	});
})(jQuery);