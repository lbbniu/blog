$(document).ready(function() {
	//tooltip
    $("[data-toggle=tooltip]").tooltip();

    // popover demo
    $("[data-toggle=popover]").popover();

    //unslider
	$('.carousel-inner').carousel({
	  interval: 2000,
	});
	//首页图片特效
	$('.main .thumb img').adipoli({
    'startEffect' : 'normal',
    'hoverEffect' : 'popout'
	});

	$('.main .thumb150 img').adipoli({
    'startEffect' : 'overlay',
    'hoverEffect' : 'sliceDown'
	});
	//分类页图片特效
	$('.cate-main img').adipoli({
    'startEffect' : 'normal',
    'hoverEffect' : 'popout'
	})
	$('.sidebar img').adipoli({
    'startEffect' : 'transparent',
    'hoverEffect' : 'boxRainGrowReverse'
	});
	//文章内容页特效
	$('.single img').adipoli({
    'startEffect' : 'transparent',
    'hoverEffect' : 'boxRainGrowReverse'
	});


});