<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
* 处理插件绑定 动作钩子 和 过滤器钩子
*
* @package ZywxappWordpressPlugin
* @author zywx phpteam
*/
function zywxapp_attach_hooks(){
    $ce = new ZywxappContentEvents();
	//admin_menu 控制板中的菜单结构显示无误后，执行此动作函数
    add_action('admin_menu', array('ZywxappAdminDisplay', 'setup'));//设定后台插件菜单图标、插件功能设定安装、卸载、升级功能

    add_action('created_term', array(&$ce, 'updateCacheTimestampKey'));
    add_action('edited_term', array(&$ce, 'updateCacheTimestampKey'));
    
    add_filter('cron_schedules', array('ZywxappCronSchedules','addSchedules'));
    
   	// Add "Delete Old Log Files" and "Delete Old Cache Files" daily Wordpress Cron job
	add_action('zywxapp_daily_function_hook', array(ZywxappLog::getInstance(), 'deleteOldFiles'));
	add_action('zywxapp_daily_function_hook', array(ZywxappCache::getCacheInstance(), 'deleteOldFiles'));
    
    // Handle installation functions  启用或停用
    register_activation_hook(WP_ZYWXAPP_BASE, array('ZywxappInstaller', 'install'));
    register_deactivation_hook(WP_ZYWXAPP_BASE, array('ZywxappInstaller', 'disable'));
    add_action('delete_blog', array('ZywxappInstaller', 'deleteBlog'), 10, 2);
    
    // admin
	add_action('wp_ajax_zywxapp_hide_upgrade_msg', array('ZywxappAdminDisplay', 'hideUpgradeMsg'));
	add_action('wp_ajax_zywxapp_logo_delete', array('ZywxappSystemServices', 'updateLogo'));
	add_action('wp_ajax_zywxapp_login_save', array('ZywxappPlatformRegisterDisplay', 'loginSave'));
	
    // Upgrade
	add_action('wp_ajax_zywxapp_upgrade_database', array('ZywxappUpgradeDisplay', 'upgradeDatabase'));
	add_action('wp_ajax_zywxapp_upgrade_configuration', array('ZywxappUpgradeDisplay', 'upgradeConfiguration'));
	add_action('wp_ajax_zywxapp_upgrading_finish', array('ZywxappUpgradeDisplay', 'upgradingFinish'));
    
}

//开始处理 插件动作和过滤器钩子
if ( !defined('WP_ZYWXAPP_HOOKS_ATTACHED') ) {
    define('WP_ZYWXAPP_HOOKS_ATTACHED', TRUE);
    zywxapp_attach_hooks();
}