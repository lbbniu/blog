<?php
	/*  功能:
	*	判断是否为分类目录别名，如果不是，则使用category-default.php 分类目录模板
	*   如果是，则使用对应的分类目录模板
	*/
if ( is_category('discuz') ) {  //当访问 http://www.cnsecer.com/pic这个分类时，使用category-pic.php这个模板
include(TEMPLATEPATH . '/cate-thumb.php');
}

// elseif 结束   //如果访问其他分类，则使用category-default.php'这个模板
else {
include(TEMPLATEPATH . '/cate-default.php');
}
?>


