<?php
//类:初始化设置
if ( !function_exists('wp_insert_category') && is_admin() ) {
	require_once (ABSPATH . '/wp-admin/includes/taxonomy.php');
}
class Init_Sittings {
	static $image_format_id = 0;
	static $video_format_id = 0;
	
	function __construct() {}

	static public function init() {
		$Yeti_inited = get_option('Yeti_inited');
		if ($Yeti_inited) return false;			//如果已经初始化了就终止
		else add_option('Yeti_inited', '1');	//标记为已经初始化了

		self::init_addpost();	//安装投稿页面
		self::init_post_format();	//创建post_format
		self::init_field_group();	//创建分类类型字段组
		self::init_image_size();	//设置图像尺寸
	}

	//安装投稿页面
	private function init_addpost() {
		global $wpdb;
		$id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = 'add-post'");
		if ($id) return false;	//如果存在就到此为止

		$tougao = array(
			'post_title' => '投稿',
			'post_name' => 'add-post',
			'post_author' => 1,	
			'post_status' => 'publish',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_type' => 'page'
		);
		$postID = wp_insert_post($tougao);
		if ($postID) {
			add_post_meta($postID, '_wp_page_template', 'add_post.php');
		}
	}

	private function init_image_size() {
		update_option('thumbnail_size_w','80');
		update_option('thumbnail_size_h','60');
		update_option('thumbnail_crop','1');	//总是裁剪
		update_option('medium_size_w','440');
		update_option('medium_size_h','0');
		update_option('large_size_w','600');
		update_option('large_size_h','0');
	}

	//创建post_format(image和video)
	private function init_post_format() {
		$img_format = get_term_by('slug', 'post-format-image', 'post_format');
		if ( !$img_format ) wp_insert_term('post-format-image', 'post_format');
		$vdo_format = get_term_by('slug', 'post-format-video', 'post_format');
		if ( !$vdo_format ) wp_insert_term('post-format-video', 'post_format');
	}

	private function init_field_group() {
		$video_field_group = array(
			'post_title' => '视频信息',
			'post_name' => 'acf_video',
			'post_author' => 1,	
			'post_status' => 'publish',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_type' => 'acf'
		);
		$video_ID = wp_insert_post($video_field_group);
		if ($video_ID) {
			add_post_meta($video_ID, 'field_101', 'a:10:{s:5:"label";s:12:"视频地址";s:4:"name";s:8:"videourl";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"1";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:0;s:3:"key";s:9:"field_101";}');
			add_post_meta($video_ID, 'field_102', 'a:10:{s:5:"label";s:28:"视频缩略图[大](免填)";s:4:"name";s:3:"img";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:1;s:3:"key";s:9:"field_102";}');
			add_post_meta($video_ID, 'field_103', 'a:10:{s:5:"label";s:28:"视频缩略图[小](免填)";s:4:"name";s:9:"img_thumb";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:2;s:3:"key";s:9:"field_103";}');
			add_post_meta($video_ID, 'field_104', 'a:10:{s:5:"label";s:17:"投稿者(免填)";s:4:"name";s:8:"username";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:3;s:3:"key";s:9:"field_104";}');
			add_post_meta($video_ID, 'field_105', 'a:10:{s:5:"label";s:23:"投稿者网址(免填)";s:4:"name";s:3:"url";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:4;s:3:"key";s:9:"field_105";}');
			add_post_meta($video_ID, 'field_106', 'a:10:{s:5:"label";s:23:"投稿者头像(免填)";s:4:"name";s:6:"avatar";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:5;s:3:"key";s:9:"field_106";}');
			add_post_meta($video_ID, 'field_107', 'a:10:{s:5:"label";s:28:"投稿者的多说ID(免填)";s:4:"name";s:7:"duoshuo";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:6;s:3:"key";s:9:"field_107";}');
			add_post_meta($video_ID, 'field_108', 'a:10:{s:5:"label";s:16:"投稿IP(免填)";s:4:"name";s:2:"ip";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:7;s:3:"key";s:9:"field_108";}');
			add_post_meta($video_ID, 'allorany', 'all');
			add_post_meta($video_ID, 'rule', 'a:5:{s:5:"param";s:11:"post_format";s:8:"operator";s:2:"==";s:5:"value";s:5:"video";s:8:"order_no";i:0;s:8:"group_no";i:0;}');
			add_post_meta($video_ID, 'position', 'normal');
			add_post_meta($video_ID, 'layout', 'default');
			add_post_meta($video_ID, 'hide_on_screen', '');
		}
		$image_field_group = array(
			'post_title' => '图片信息',
			'post_name' => 'acf_image',
			'post_author' => 1,	
			'post_status' => 'publish',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_type' => 'acf'
		);
		$image_ID = wp_insert_post($image_field_group);
		if ($image_ID) {
			add_post_meta($image_ID, 'field_111', 'a:10:{s:5:"label";s:6:"图片";s:4:"name";s:3:"pic";s:4:"type";s:5:"image";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:11:"save_format";s:2:"id";s:12:"preview_size";s:6:"medium";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:0;s:3:"key";s:9:"field_111";}');
			add_post_meta($image_ID, 'field_112', 'a:10:{s:5:"label";s:17:"投稿者(免填)";s:4:"name";s:8:"username";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:1;s:3:"key";s:9:"field_112";}');
			add_post_meta($image_ID, 'field_113', 'a:10:{s:5:"label";s:23:"投稿者网址(免填)";s:4:"name";s:3:"url";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:2;s:3:"key";s:9:"field_113";}');
			add_post_meta($image_ID, 'field_114', 'a:10:{s:5:"label";s:23:"投稿者头像(免填)";s:4:"name";s:6:"avatar";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:3;s:3:"key";s:9:"field_114";}');
			add_post_meta($image_ID, 'field_115', 'a:10:{s:5:"label";s:28:"投稿者的多说ID(免填)";s:4:"name";s:7:"duoshuo";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:4;s:3:"key";s:9:"field_115";}');
			add_post_meta($image_ID, 'field_116', 'a:10:{s:5:"label";s:16:"投稿IP(免填)";s:4:"name";s:2:"ip";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:5;s:3:"key";s:9:"field_116";}');
			add_post_meta($image_ID, 'field_117', 'a:10:{s:3:"key";s:9:"field_117";s:5:"label";s:12:"网络图片";s:4:"name";s:6:"imgurl";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:6;}');
			add_post_meta($image_ID, 'field_118', 'a:10:{s:3:"key";s:9:"field_118";s:5:"label";s:15:"网络图片宽";s:4:"name";s:5:"img_w";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:7;}');
			add_post_meta($image_ID, 'field_119', 'a:10:{s:3:"key";s:9:"field_119";s:5:"label";s:15:"网络图片高";s:4:"name";s:5:"img_h";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:8;}');
			add_post_meta($image_ID, 'field_120', 'a:10:{s:3:"key";s:9:"field_120";s:5:"label";s:18:"网络图片类型";s:4:"name";s:8:"img_type";s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:17:"conditional_logic";a:3:{s:6:"status";s:1:"0";s:5:"rules";a:1:{i:0;a:2:{s:5:"field";s:4:"null";s:8:"operator";s:2:"==";}}s:8:"allorany";s:3:"all";}s:8:"order_no";i:9;}');
			add_post_meta($image_ID, 'allorany', 'all');
			add_post_meta($image_ID, 'rule', 'a:5:{s:5:"param";s:11:"post_format";s:8:"operator";s:2:"==";s:5:"value";s:5:"image";s:8:"order_no";i:0;s:8:"group_no";i:0;}');
			add_post_meta($image_ID, 'position', 'normal');
			add_post_meta($image_ID, 'layout', 'default');
			add_post_meta($image_ID, 'hide_on_screen', '');
		}
	}
}

//主题设置
function bleria_theme_settings(){	  
	add_theme_page( 'Yeti主题设置', 'Yeti主题设置', 'administrator', 'yeti_settings','Yeti_settings');	  
}	  
add_action('admin_menu', 'bleria_theme_settings');

function Yeti_settings() { ?>
<div class='wrap'>
	<?php screen_icon('options-general');?><h2>Yeti主题设置</h2>
	<?php if ( !get_option('Yeti_inited') ) : ?>
	<div style='margin-top:15px;'>第一次使用本主题，需要初始化，请点击下面的初始化按钮！</div>
	<div style='margin-top:15px;'>注：初始化成功后将直接显示设置页面！</div>
	<p class='pressthis'><a href='javascript:;' id='init_theme'><span>初始化主题</span></a></p>
	<?php else : ?>
	<div class='widget-liquid-left'>
		<div id='widgets-left'>
			<form method="post" name="theme_tpl_options" id="theme_tpl_options" action="">
			<div class="postbox">
				<h3>主题布局模板</h3>
				<?php
					$theme_tpl = blueria_get_option('theme_tpl');
				?>
				<div class='inside'>
					<label for='theme_tpl'>选择布局模板</label>
					<select name="theme_tpl" id="theme_tpl">
						<option value="default" <?php selected( $theme_tpl, 'default' ); ?>>三列式布局(默认)</option>
						<option value="nice" <?php selected( $theme_tpl, 'nice' ); ?>>两列式布局</option>
					</select>
				</div>
				<div class='inside'>
					说明：<br />
					1.三列式 = 图片/视频 + 内容 + 侧边栏 (支持纯文字)<br />
					2.两列式 = 混合内容 + 侧边栏 (支持纯文字)
				</div>
				<div class='inside clearfix'>
					<input type="submit" class='button-primary' value="保存设置" style='float:left;' id='theme_tpl_save' />
					<span class="spinner" style="float:left;display: none;"></span>
				</div>
			</div>
			</form>

			<form method="post" name="settings_form" id="settings_form" action="">
			<div class="postbox">
				<h3>常规设置</h3>
				<fieldset class="inside">
					<label for='site_description'>网站描述</label>
					<textarea id='site_description' name="site_description" rows="3" placeholder="对主页进行重要描述" style='width:100%'><?php echo blueria_get_option('site_description'); ?></textarea>
				</fieldset>

				<fieldset class="inside">
					<label for='site_keywords'>网站关键字</label>
					<textarea id='site_keywords' name="site_keywords" rows="2" placeholder="网站关键词 用逗号或者空格隔开" style='width:100%'><?php echo blueria_get_option('site_keywords'); ?></textarea>
				</fieldset>
				
				<fieldset class="inside">
					<label for='site_copyright'>版权信息</label>
					<textarea id='site_copyright' name="site_copyright" rows="2" placeholder="填写网站的版权信息 将显示在网站的最下面(支持HTML)" style='width:100%'><?php echo blueria_get_option('site_copyright'); ?></textarea>
				</fieldset>
				
				<fieldset class="inside">
					<label for='site_statistics'>统计代码</label>
					<textarea id='site_statistics' name="site_statistics" rows="2" placeholder="将统计代码直接粘贴到这里(注:统计效果不会显示出来 如果需要显示 请添加到上面的[版权信息]里)" style='width:100%'><?php echo blueria_get_option('site_statistics'); ?></textarea>
				</fieldset>
				
				<fieldset class="inside">
					<label for='site_notice'>首页公告</label>
					<textarea id='site_notice' name="site_notice" rows="3" placeholder="首页轮播公告 按行轮播(每行限60字以内)" style='width:100%'><?php echo blueria_get_option('site_notice'); ?></textarea>
				</fieldset>
				
				<div class='inside clearfix'>
					<input type="submit" name="option_save" class='button-primary' value="保存以上设置" style='float:left;' id='option_save' />
					<span class="spinner" style="float:left;display: none;"></span>
				</div>
			</div>
			</form>

			<form method="post" name="bdshare_options" id="bdshare_options" action="">
			<div class="postbox lable_1">
				<h3>百度分享设置</h3>
				<?php
					$bdshare_options = blueria_get_option('bdshare_options');
					$bdshare_id = $bdshare_options ? esc_attr($bdshare_options['id']) : 6813726;
					$bdshare_tsina = $bdshare_options ? esc_attr($bdshare_options['tsina']) : '';
					$bdshare_tqq = $bdshare_options ? esc_attr($bdshare_options['tqq']) : '';
					$bdshare_t163 = $bdshare_options ? esc_attr($bdshare_options['t163']) : '';
					$bdshare_tsohu = $bdshare_options ? esc_attr($bdshare_options['tsohu']) : '';
				?>
				<div class="inside">
					<label for='bdshare_id'>百度分享id</label>
					<input type="text" name="bdshare_id" id="bdshare_id" value="<?php echo $bdshare_id;?>" style="width:260px" /> (在<a href="http://share.baidu.com/code" target="_blank">百度分享</a>页面的代码框里 uid=后面的数字)
				</div>
				<div class="inside">
					<label for='bdshare_tsina'>新浪微博Appkey</label>
					<input type="text" name="bdshare_tsina" id="bdshare_tsina" value="<?php echo $bdshare_tsina;?>" style="width:260px" />
				</div>
				<div class="inside">
					<label for='bdshare_tqq'>腾讯微博Appkey</label>
					<input type="text" name="bdshare_tqq" id="bdshare_tqq" value="<?php echo $bdshare_tqq;?>" style="width:260px" />
				</div>
				<div class="inside">
					<label for='bdshare_t163'>网易微博Appkey</label>
					<input type="text" name="bdshare_t163" id="bdshare_t163" value="<?php echo $bdshare_t163;?>" style="width:260px" />
				</div>
				<div class="inside">
					<label for='bdshare_tsohu'>搜狐微博Appkey</label>
					<input type="text" name="bdshare_tsohu" id="bdshare_tsohu" value="<?php echo $bdshare_tsohu;?>" style="width:260px" />
				</div>
				<div class='inside clearfix'>
					<input type="submit" class='button-primary' value="保存以上设置" style='float:left;' id='bdshare_options_save' />
					<span class="spinner" style="float:left;display: none;"></span>
				</div>
			</div>
			</form>

			<form method="post" name="related_options" id="related_options" action="">
			<div class="postbox lable_1">
				<h3>相关设置</h3>
				<?php
					global $union56id;
					global $comment_notice;
					global $weixin_original_id;
				?>
				<div class="inside">
					<label for='union56id'>56网计费id</label>
					<input type="text" name="union56id" id="union56id" value="<?php echo $union56id;?>" style="width:260px" /> (前提：先加入56网视<a href="http://union.56.com/" target="_blank">频推广计划</a>)
				</div>
				<div class="inside">
					<label for='comment_notice'>评论框提示语</label>
					<input type="text" name="comment_notice" id="comment_notice" value="<?php echo $comment_notice;?>" style="width:260px" />
				</div>
				<div class="inside">
					<label for='weixin_original_id'>微信原始id</label>
					<input type="text" name="weixin_original_id" id="weixin_original_id" value="<?php echo $weixin_original_id;?>" style="width:260px" /> (并非微信账号)
				</div>
				<div class='inside clearfix'>
					<input type="submit" class='button-primary' value="保存以上设置" style='float:left;' id='related_options_save' />
					<span class="spinner" style="float:left;display: none;"></span>
				</div>
			</div>
			</form>
			
			<form method="post" name="mobi_ads_settings" id="mobi_ads_settings" action="">
			<div class="postbox">
				<h3>手机版广告位设置</h3>
				<fieldset class="inside">
					<label for='mobi_home_fixed_ad'>首页底部停靠广告代码</label>
					<textarea id='mobi_home_fixed_ad' name="mobi_home_fixed_ad" rows="2" style='width:100%'><?php echo blueria_get_option('mobi_home_fixed_ad'); ?></textarea>
				</fieldset>
				<fieldset class="inside">
					<label for='mobi_post_top_ad'>文章页页顶广告代码</label>
					<textarea id='mobi_post_top_ad' name="mobi_post_top_ad" rows="2" style='width:100%'><?php echo blueria_get_option('mobi_post_top_ad'); ?></textarea>
				</fieldset>
				<fieldset class="inside">
					<label for='mobi_post_bottom_ad'>文章页页底广告代码</label>
					<textarea id='mobi_post_bottom_ad' name="mobi_post_bottom_ad" rows="2" style='width:100%'><?php echo blueria_get_option('mobi_post_bottom_ad'); ?></textarea>
				</fieldset>
				<div class="inside clearfix">
					<input type="button" class='button-primary' value="保存以上代码" style='float:left;' id='mobi_ads' />
					<span class="spinner" style="float:left;display: none;"></span>
				</div>
			</div>
			</form>
			
			<?php
				$tougao_option = get_option('tougao_option');
				$tags = get_terms( 'post_tag', array( 'hide_empty' => 0, 'orderby' => 'id', 'order' => 'DESC' ) );
				$cats = get_terms( 'category', array( 'hide_empty' => 0, 'orderby' => 'id', 'order' => 'DESC' ) );
			?>
			<div class="postbox">
				<h3>前台投稿相关设置</h3>
				<div class="inside">
					<fieldset class='bl_cats'>
						<legend>显示可供投稿者选择的分类</legend>
						<?php
						if ( !$cats ) : echo "没有找到任何分类 在你添加后将会显示在这里";
						else :
						foreach ($cats as $cat) {
							if ($cat->slug == 'video' || $cat->slug == 'image') continue;	//默认分类不显示
							$tid = $cat->term_id;
							$tname = $cat->name;
							$select = ( $tougao_option['cats'] && in_array($tid, $tougao_option['cats']) ) ? ' selected' : '';
							echo "<div class='term{$select}' tid='{$tid}' type='cat'>{$tname}<i></i></div>";
						}
						endif;
						?>
					</fieldset>
					<fieldset class='bl_tags'>
						<legend>显示可供投稿者选择的标签</legend>
						<?php
						if ( !$tags ) : echo "没有找到任何标签 在你添加后将会显示在这里";
						else :
						foreach ($tags as $tag) {
							$tid = $tag->term_id;
							$tname = $tag->name;
							$select = ( $tougao_option['tags'] && in_array($tid, $tougao_option['tags']) ) ? ' selected' : '';
							echo "<div class='term{$select}' tid='{$tid}' type='tag'>{$tname}<i></i></div>";
						}
						endif;
						?>
					</fieldset>
					<div class='item clearfix'>
						<input type="button" class='button-primary' value="保存设置" style='float:left;' id='tougao_option_save' />
						<span class="spinner" style="float:left;display: none;"></span>
					</div>
				</div>
			</div>
			
		</div>
	</div>
	
	<div class="widget-liquid-right">
		<div class="postbox">
			<h3>杂项</h3>
			<div class='inside'>
				<div>
					<input type="checkbox" id='post_audit' <?php checked( '1', get_option('post_audit') ); ?> /> <label for='post_audit'>用户投稿需要审核<span class='tip'></span></label>
				</div>
				<div>
					<input type="checkbox" id='stop_external_link' <?php checked( '1', blueria_get_option('stop_external_link') ); ?> /> <label for='stop_external_link'>禁止站外链接被点击<span class='tip'></span></label>
				</div>
				<div>
					<input type="checkbox" id='stop_imageup' <?php checked( '1', get_option('stop_imageup') ); ?> /> <label for='stop_imageup'>禁用前台投稿上传图片<span class='tip'></span></label>
				</div>
				<div>
					<input type="checkbox" id='as_radio' <?php checked( '1', get_option('as_radio') ); ?> /> <label for='as_radio'>前台投稿只能单选分类<span class='tip'></span></label>
				</div>
				<div>
					<input type="checkbox" id='stop_ietips' <?php checked( '1', blueria_get_option('stop_ietips') ); ?> /> <label for='stop_ietips'>关闭IE模式切换提示<span class='tip'></span></label>
				</div>
				<div>
					<input type="checkbox" id='show_links' <?php checked( '1', blueria_get_option('show_links') ); ?> /> <label for='show_links'>默认显示友情链接<span class='tip'></span></label>
				</div>
			</div>
		</div>

		<form method="post" name="sitetop_options" id="sitetop_options" action="">
		<div class="postbox">
			<h3>网站顶部设置</h3>
			<?php
				$sitetop_options = blueria_get_option('sitetop_options');
				$sitetop_height = $sitetop_options ? esc_attr($sitetop_options['height']) : 150;
				$sitetop_bgimg = $sitetop_options ? esc_attr($sitetop_options['bgimg']) : 'top_bg.jpg';
				$sitetop_bg_type = $sitetop_options ? esc_attr($sitetop_options['type']) : 'color';
				$sitetop_bg_color = $sitetop_options ? esc_attr($sitetop_options['bg_color']) : '#6BBFFF';
				$sitetop_bg_image = $sitetop_options ? esc_attr($sitetop_options['bg_image']) : '';
				$sitetop_logo_top = $sitetop_options ? esc_attr($sitetop_options['logo_top']) : 60;
				$sitetop_logo_left = $sitetop_options ? esc_attr($sitetop_options['logo_left']) : 0;
			?>
			<div class='inside'>
				<label for='sitetop_height'>顶部高度：</label>
				<input type="text" name="sitetop_height" id='sitetop_height' value="<?php echo $sitetop_height;?>" style="width:50px" /> 像素
			</div>
			<hr class='line' />
			<div class='inside'>
				<label for='sitetop_bgimg'>背景图文件名：</label>
				<input type="text" name="sitetop_bgimg" id='sitetop_bgimg' value="<?php echo $sitetop_bgimg;?>" style="width:32%" /> (填写图片名称即可)
			</div>
			<div class='inside'>
				<label for='sitetop_bg_color'>
					<input type="radio" name='sitetop_bg_type' id='sitetop_bg_type_color' value='color' <?php checked( 'color', $sitetop_bg_type ); ?> />
					背景图底色：
				</label>
				<input type="text" name="sitetop_bg_color" id='sitetop_bg_color' value="<?php echo $sitetop_bg_color;?>" style="width:30%" /> (如#FFFFFF)
			</div>
			<div class='inside'>
				<label for='sitetop_bg_image'>
					<input type="radio" name='sitetop_bg_type' id='sitetop_bg_type_image' value='image' <?php checked( 'image', $sitetop_bg_type ); ?> />
					背景图底图：
				</label>
				<input type="text" name="sitetop_bg_image" id='sitetop_bg_image' value="<?php echo $sitetop_bg_image;?>" style="width:30%" /> (可选，填名称)
			</div>
			<div class='inside'>
				说明：<br />
				1.将图片放在主题目录下的images文件夹里<br />
				2.背景图的宽度至少为960像素(注:宽度大点最好)<br />
				3.当访客屏幕宽度大于你的背景图宽度时 将用背景底色或底图来填充
				4.如果有背景底图 请保证高度与背景图一样
			</div>
			<hr class='line' />
			<div class='inside'>
				<label for='sitetop_logo_top'>Logo距上边缘距离：</label>
				<input type="text" name="sitetop_logo_top" id='sitetop_logo_top' value="<?php echo $sitetop_logo_top;?>" style="width:50px" /> 像素
			</div>
			<div class='inside'>
				<label for='sitetop_logo_left'>Logo左边偏移距离：</label>
				<input type="text" name="sitetop_logo_left" id='sitetop_logo_left' value="<?php echo $sitetop_logo_left;?>" style="width:50px" /> 像素
			</div>
			<div class='inside clearfix'>
				<input type="submit" class='button-primary' value="保存设置" style='float:left;' id='sitetop_options_save' />
				<span class="spinner" style="float:left;display: none;"></span>
			</div>
		</div>
		</form>
		
		<form method="post" name="bcs_settings" id="bcs_settings" action="">
		<div class="postbox">
			<h3>图片外部存储</h3>
			<?php
				$bcs_options = get_option('bcs_options', TRUE);
				$bcs_bucket = esc_attr($bcs_options['bucket']);
				$bcs_ak = esc_attr($bcs_options['ak']);
				$bcs_sk = esc_attr($bcs_options['sk']);
			?>
			<div class='inside'>
				<input type="checkbox" name='save_to_bcs' id='save_to_bcs' <?php checked( '1', get_option('save_to_bcs') ); ?> /> <label for='save_to_bcs'>图片存放到百度云存储(此项若勾选 下面3项不能空)</label>
			</div>
			<div class='inside'>
				<label for='bucket'>Bucket 设置</label>
				<input type="text" name="bucket" id='bucket' value="<?php echo $bcs_bucket;?>" placeholder="请输入云存储使用的 bucket" style="width:100%" />
				<p>请先访问 <a href="http://developer.baidu.com/bae/bcs/bucket/" target="_blank">百度云存储</a> 创建 bucket 后，填写以上内容。</p>
			</div>
			<div class='inside'>
				<label for='ak'>Access Key (公钥)</label>
				<input type="text" name="ak" id='ak' value="<?php echo $bcs_ak;?>" placeholder="请输入你的云存储公钥" style="width:100%" />
				<p>访问 <a href="http://developer.baidu.com/bae/ref/key/" target="_blank">BAE 密钥管理页面</a>，获取 这里所需的公钥和下面需要的密钥</p>
			</div>
			<div class='inside'>
				<label for='sk'>Secret Key (密钥)</label>
				<input type="text" name="sk" id='sk' value="<?php echo $bcs_sk;?>" placeholder="请输入你的云存储密钥" style="width:100%" />
			</div>
			<div class='inside clearfix'>
				<input type="submit" class='button-primary' value="保存设置" style='float:left;' id='bcs_option_save' />
				<span class="spinner" style="float:left;display: none;"></span>
			</div>
		</div>
		</form>
		
		<div class="postbox">
			<h3>首页图片截断</h3>
			<div class='inside'>
				<input type="checkbox" name='image_cut' id='image_cut' <?php checked( '1', blueria_get_option('home_image_cut') ); ?> /> <label for='image_cut'>对首页长图片进行截断显示</label>
				<div><label for='image_cut_height'>当图片高度超过<input type='text' name='image_cut_height' id='image_cut_height' value='<?php echo blueria_get_option('home_image_cut_height','800'); ?>' style='width:35px;' />像素就对其截断显示</label></div>
			</div>
			<div class='inside clearfix'>
				<button class='button-primary' style='float:left;' id='image_cut_option_save'>保存设置</button>
				<span class="spinner" style="float:left;display: none;"></span>
			</div>
		</div>

		<form method="post" name="wechat_options" id="wechat_options" action="">
		<div class="postbox">
			<h3>微信机器人设置(初版)</h3>
			<?php
				$wechat_options = get_option('wechat_options');
				$wechat_welcome = $wechat_options ? esc_attr($wechat_options['welcome']) : "欢迎收听我爱笑微信\n";
				$wechat_help = $wechat_options ? esc_attr($wechat_options['help']) 
					: "回复 最新 查看最新笑料\n回复 最热 查看最热门笑料\n回复 最赞 查看赞最多笑料\n回复 随机 查看推荐的笑料\n回复 帮助 查看帮助信息\n\n我爱笑woaixiao.cn";
				
			?>
			<div class='inside'>
				<label for='wechat_welcome'>收听欢迎语(后面会自动跟上帮助提示语)</label>
				<textarea id='wechat_welcome' name="wechat_welcome" rows="2" style='width:100%'><?php echo $wechat_welcome;?></textarea>
			</div>
			<div class='inside'>
				<label for='wechat_help'>帮助提示语</label>
				<textarea id='wechat_help' name="wechat_help" rows="4" style='width:100%'><?php echo $wechat_help;?></textarea>
			</div>
			<div class='inside clearfix'>
				<button class='button-primary' style='float:left;' id='wechat_options_save'>保存设置</button>
				<span class="spinner" style="float:left;display: none;"></span>
			</div>
		</div>
		</form>

		<form method="post" name="watermark_options" id="watermark_options" action="">
		<div class="postbox">
			<h3>水印设置</h3>
			<?php
				$watermark_options = get_option('watermark_options');
				$watermark_enabled = $watermark_options['enabled'];
				$watermark_type = $watermark_options ? esc_attr($watermark_options['type']) : 'image';
				$watermark_filename = $watermark_options ? esc_attr($watermark_options['filename']) : 'mark.png';
				$watermark_text = $watermark_options ? esc_attr($watermark_options['text']) : 'Blueria.cn';
				$watermark_font = $watermark_options ? esc_attr($watermark_options['font']) : 'arial.ttf';
				$watermark_size = $watermark_options ? esc_attr($watermark_options['size']) : 24;
				$watermark_pos = $watermark_options ? esc_attr($watermark_options['pos']) : 7;
				$watermark_color = $watermark_options ? esc_attr($watermark_options['color']) : '#000000';
			?>
			<div class='inside'>
				<input type="checkbox" name='watermark_enabled' id='watermark_enabled' <?php checked( '1', $watermark_enabled ); ?> /> <label for='watermark_enabled'>给图片添加水印</label>
			</div>
			<div class='inside'>
				<label for='watermark_type_image'>图片水印</label>
				<input type="radio" id="watermark_type_image" name="watermark_type" value="image" <?php checked( 'image', $watermark_type ); ?> />
				<span> 　 </span>
				<label for='watermark_type_text'>文字水印</label>
				<input type="radio" id="watermark_type_text" name="watermark_type" value="text" <?php checked( 'text', $watermark_type ); ?> />
			</div>
			<div class='inside watermark_img'>
				<label for='watermark_filename'>水印图片名称(填写名称即可)</label>
				<input type="text" name="watermark_filename" id='watermark_filename' value="<?php echo $watermark_filename;?>" style="width:100%" />
				<p>注：将你的水印图片放到主题目录里的shuiyin文件夹下</p>
			</div>
			<div class='inside watermark_txt'>
				<label for='watermark_text'>水印文字</label>
				<input type="text" name="watermark_text" id='watermark_text' value="<?php echo $watermark_text;?>" style="width:100%" />
			</div>
			<div class='inside watermark_txt'>
				<label for='watermark_font'>字体(填写字体名称即可)</label>
				<input type="text" name="watermark_font" id='watermark_font' value="<?php echo $watermark_font;?>" style="width:100%" />
				<p>注：将(漂亮的中文)字体文件放到主题目录里的shuiyin文件夹下</p>
			</div>
			<div class='inside watermark_txt'>
				<label for='watermark_size'>字体大小：
				<input type="text" name="watermark_size" id='watermark_size' value="<?php echo $watermark_size;?>" style="width:25px" /> 像素</label>
			</div>
			<div class='inside watermark_txt'>
				<label for='watermark_color'>字体颜色：
				<input type="text" name="watermark_color" id='watermark_color' value="<?php echo $watermark_color;?>" style="width:60px" /> (如：#FF0000)</label>
			</div>
			<hr class='line' />
			<div class='inside'>
				<label>水印显示位置</label>
				<table class='watermark_pos'>
					<tr>
						<td><input type="radio" name="watermark_pos" value="1" <?php checked( 1, $watermark_pos ); ?> /></td>
						<td><input type="radio" name="watermark_pos" value="2" <?php checked( 2, $watermark_pos ); ?> /></td>
						<td><input type="radio" name="watermark_pos" value="3" <?php checked( 3, $watermark_pos ); ?> /></td>
					</tr>
					<tr>
						<td><input type="radio" name="watermark_pos" value="4" <?php checked( 4, $watermark_pos ); ?> /></td>
						<td><input type="radio" name="watermark_pos" value="5" <?php checked( 5, $watermark_pos ); ?> /></td>
						<td><input type="radio" name="watermark_pos" value="6" <?php checked( 6, $watermark_pos ); ?> /></td>
					</tr>
					<tr>
						<td><input type="radio" name="watermark_pos" value="7" <?php checked( 7, $watermark_pos ); ?> /></td>
						<td><input type="radio" name="watermark_pos" value="8" <?php checked( 8, $watermark_pos ); ?> /></td>
						<td><input type="radio" name="watermark_pos" value="9" <?php checked( 9, $watermark_pos ); ?> /></td>
					</tr>
				</table>
			</div>
			<div class='inside clearfix'>
				<button class='button-primary' style='float:left;' id='watermark_options_save'>保存设置</button>
				<span class="spinner" style="float:left;display: none;"></span>
			</div>
		</div>
		</form>
		
	</div>
	
	<style>
		.widget-liquid-left, .widget-liquid-right {margin-top: 15px;}
		.widget-liquid-left {margin-right: -425px !important;}
		.widget-liquid-left #widgets-left {margin-right: 425px;margin-left: 3px;}
		.widget-liquid-right {width: 400px !important;}
		.postbox h3 {
			font-size: 15px;
			font-weight: normal;
			line-height: 1;
			margin: 0;
			padding: 7px 10px;
			cursor: default !important;
		}
		.postbox p {margin: 2px 0;}
		.watermark_txt {display: none;}
		.line {border: 0 none;height: 1px;background-color: #DFDFDF;}
		.watermark_pos td {
			border: 1px solid #CED2D6;
			padding: 3px 5px;
		}
		.lable_1 label {width: 100px;display: inline-block;}
	</style>
	<?php endif; ?>
</div>
<?php } ?>