<div id="sidebar" class='layout_right'>
	<ul class='widgetbox'>
	<?php if ( function_exists('dynamic_sidebar') && is_active_sidebar('home_sidebar') ) {
		if (  is_single() ) {
			if ( is_active_sidebar('post_sidebar') ) dynamic_sidebar('post_sidebar');
			else dynamic_sidebar('home_sidebar');
		} elseif (  is_page() ) {
			if ( is_active_sidebar('page_sidebar') ) dynamic_sidebar('page_sidebar');
			else dynamic_sidebar('home_sidebar');
		} else {
			dynamic_sidebar('home_sidebar');
		}
	} else { ?>
		<li class='widget floatStart'>你还没有添加小工具</li>
	<?php } ?>
	</ul>
</div>