<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
 * 插件安装卸载类
 *
 * class_description
 *
 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
 * @since      File available since Release 1.0 -- 2012-5-17 上午11:01:31
 * @author mxg<jiemack@163.com>
 */
class ZywxappInstaller
{
    public function needUpgrade()
    {
    	// 检测是否安装插件数据库
		if ( !ZywxappDB::getInstance()->isInstalled() ){
			// 没有安装数据库，也就没有升级，需要全面检测没有安装原因
			return FALSE;
		} else {
			//获取插件数据库版本号和插件内部配置信息版本号比较
			return (ZywxappDB::getInstance()->needUpgrade() || ZywxappConfig::getInstance()->needUpgrade());
		}
    }

    /**
     * 升级数据库
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-7 下午04:17:41
     * @author author
     * 
     */
    public function upgradeDatabase()
    {
        $upgraded = TRUE;
        if ( ZywxappDB::getInstance()->needUpgrade() ){
            $upgraded = ZywxappDB::getInstance()->upgrade();
        }

        return $upgraded;
    }

    /**
     * 升级配置文件
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-7 下午04:30:11
     * @author author
     * 
     */
    public function upgradeConfiguration()
    {
        $upgraded = TRUE;

        if ( ZywxappConfig::getInstance()->needUpgrade() ){
            $upgraded = ZywxappConfig::getInstance()->upgrade();
        }

        return $upgraded;
    }

    /**
     * 插件启用时安装插件
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-1-4 下午01:23:26
     * @author mxg<jiemack@163.com>
     * 
     */     
    public function install()
    {
        // 检测wordpress插件激活功能
        if (!current_user_can('activate_plugins')) {
            return;
        }
		//插件数据库安装
        ZywxappDB::getInstance()->install();
        //设定插件核心配置 配置文件安装
        ZywxappConfig::getInstance()->install();
        // 注册计划任务
        if (!wp_next_scheduled('zywxapp_daily_function_hook')) {
            wp_schedule_event(time(), 'daily', 'zywxapp_daily_function_hook' );
            wp_schedule_event(time(), 'weekly', 'zywxapp_weekly_function_hook' );
            wp_schedule_event(time(), 'monthly', 'zywxapp_monthly_function_hook' );
        }
        $cms = new ZywxappCms();
        $cms->activate();
    }

	/**
	 * 停用插件
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-9 下午04:07:49
	 * @author author
	 * 
	 */
    public function disable()
    {
		$cms = new ZywxappCms();
        $cms->disable();
        ZywxappConfig::getInstance()->disable();
    }
	     
    /**
     * 卸载插件
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-7 下午07:28:15
     * @author author
     * 
     */
	protected static function doUninstall()
	{
		ZywxappDB::getInstance()->uninstall();
		
        wp_clear_scheduled_hook('zywxapp_daily_function_hook');
        wp_clear_scheduled_hook('zywxapp_weekly_function_hook');
        wp_clear_scheduled_hook('zywxapp_monthly_function_hook');

        try{
            $cms = new ZywxappCms();
            $cms->deactivate();
        } catch(Exception $e){
            // If it failed, it's ok... move on
        }
        ZywxappConfig::getInstance()->uninstall();
	}
    
    /**
     * 删除博客系统上卸载插件
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-7 下午07:46:50
     * @author author
     * 
     */
    public function deleteBlog($blog_id, $drop)
    {
		global $wpdb;
		$switched = false;
		$currentBlog = $wpdb->blogid;
		if ( $blog_id != $currentBlog ) {
			switch_to_blog($blog_id);
			$switched = true;
		}
		self::doUninstall();
		
		if ( $switched ) {
			switch_to_blog($currentBlog);
		}
	}
}
