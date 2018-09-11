<?php
//如果允许TimThumb.php加载所有网站的图片 下面的值应该为true 否则为false
define ('ALLOW_ALL_EXTERNAL_SITES', true);

//如果只想TimThumb.php加载指定几个域名下的图片 请将上面的值设置为false
//并将允许的域名添加到下面的列表中(按下面这种格式添加即可)
$ALLOWED_SITES = array (
	'bcs.duapp.com',
	'sinaimg.cn',
	'qpic.cn',
	'itc.cn',
);