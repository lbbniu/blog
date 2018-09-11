<?php 
/**
* Plugin Name: AppCan-WP手机客户端插件
* Plugin URI: http://www.appcan.cn/
* Description:AppCan-WP手机客户端插件让你的WordPress博客拥有一个iPhone/Android客户端
* Author: 正益无线科技有限公司
* Version: 1.1.0beta
* Author URI: http://www.appcan.cn/
* License: GPL
*/

/**
 * 这个是插件入口文件，将会检测插件所需要的版本信息
 */
if (!defined('WP_ZYWXAPP_BASE')) {
	define('WP_ZYWXAPP_BASE', plugin_basename(__FILE__));//插件入口文件地址
	define('WP_ZYWXAPP_DEBUG', TRUE);//开启插件debug 插件记录网站所有操作 log功能
	define('WP_ZYWXAPP_CACHE', FALSE);//开启缓存
	define('ZYWX_ABSPATH', realpath(ABSPATH));//网站根目录
	define('ZYWXAPP_ENV', 'free'); // 插件平台api链接配置文件
	define('ZYWXAPP_VERSION', 'v1.1.0');   // 安装的插件版本号
	define('ZYWXAPP_P_VERSION', '1.1.0');   // 平台上发布的插件版本号 
	define('ZYWXAPP_DIR_NAME', 'zywxapp'); // 应用文件夹名称
	define('ZYWXAPP_PLUGINS_DIR', plugin_dir_path(__FILE__)); //插件绝对路径
	
	define('ZYWXAPP_UPLOADS_PATH', ZYWXAPP_PLUGINS_DIR.'uploads'); // 插件应用中附件上传路径
	define('ZYWXAPP_UPLOADS_URL', plugin_dir_url(__FILE__).'uploads'); //插件应用中附件上传url地址
	
	define('ZYWXAPP_IMAGES_PATH', ZYWXAPP_PLUGINS_DIR.'images'); //插件应用中图片库url地址
	define('ZYWXAPP_IMAGES_URL', plugin_dir_url(__FILE__).'images'); //插件应用中图片库url地址
	//版本符合要求
	if (version_compare (PHP_VERSION, "5.2", ">=") && version_compare (get_bloginfo ("version"), "3.2.9", ">=")) {
		include dirname (__FILE__) . "/includes/classes/ZywxappExceptions.php";//加载异常错误提示信息处理
		include dirname (__FILE__) . "/includes/blocks.inc.php"; // 插件入口 核心文件加载
		include dirname (__FILE__) . "/includes/hooks.inc.php"; // 插件安装卸载 后台菜单处理  绑定的钩子文件
	} elseif ( is_admin() ) { //是否后台操作界面 安装条件检测处理
		if (! version_compare (PHP_VERSION, "5.2", ">=")) {
			register_shutdown_function ('zywxapp_shutdownWrongPHPVersion');
		} elseif (!version_compare (get_bloginfo ("version"), "3.2.9", ">=")) {
			register_shutdown_function ('zywxapp_shutdownWrongWPVersion');
		}
	}
} else {
	function zywxapp_getDuplicatedInstallMsg() {
		return '<div class="error">'
			. __( '先停止或卸载老版本的插件，然后激活新的插件。', 'zywxapp')
			.'</div>';
	}
	die(zywxapp_getDuplicatedInstallMsg());
}

function zywxapp_shutdownWrongPHPVersion() {
	?>
		<script type="text/javascript">alert("<?php _e('你需要PHP版本5.2或更高的才能使用AppCan-WP手机客户端插件。', 'zywxapp');?>")</script>
	<?php
}

function zywxapp_shutdownWrongWPVersion() {
	?>
		<script type="text/javascript">alert("<?php _e('你需要 WordPress® 3.3.0 或更高的版本才能使用AppCan-WP手机客户端插件。', 'zywxapp');?>")</script>
	<?php
}
