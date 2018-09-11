<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
 * 此类是插件配置文件
 *
 * class_description
 *
 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
 * @since      File available since Release 1.0 -- 2012-3-6 下午04:52:54
 * @author mxg<jiemack@163.com>
 */
class ZywxappConfig implements ZywxappIInstallable
{
    private $_options = array();

    private $_saveAsBulk = FALSE;

    private $_name = 'zywxapp_settings';
    //保存在wp数据库中配置信息版本号
    private $_internalVersion =  32;

    private static $_instance = null;

    /**
     * @static
     * @return ZywxappConfig
     */
    public static function getInstance()
    {
        if( is_null(self::$_instance) ) {
            self::$_instance = new ZywxappConfig();
        }

        return self::$_instance;
    }

    private function  __clone()
    {
        
    }

    private function __construct()
    {
        $this->_load();
    }

    /**
     * 获取数据库信息配置信息
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-3 下午06:17:55
     * @author author
     * 
     */
    private function _load()
    {
        $this->_options = get_option($this->_name);
    }

    /**
     * 升级配置文件
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-7 下午04:31:16
     * @author author
     * 
     */
    public function upgrade()
    {
        $resetOptions = array(
//        	'plugin_name',
//        	'app_style',
//        	'qq_appkey',
//	    	'qq_callback_url' ,
//	    	'sina_appkey',
//	    	'sina_callback_url',
//	    	'app_style',
//        	'default_color',
//	    	'app_color',
//        	'loge',
//        	'zywxapp_qrcode_url',
//        	'zywxapp_promotion_status',
//        	'zywxapp_client_download_url',
//        	'upgrade_version',
        ); 
        $removeOptions = array();

        $newDefaults = $this->getDefaultConfig();
        foreach ($resetOptions as $optionName) {
            $this->_options[$optionName] = $newDefaults[$optionName];
        }

        foreach ($removeOptions as $optionName) {
           unset($this->_options[$optionName]);
        }

        $this->_options['options_version'] = $this->_internalVersion;

        $this->_options['zywxapp_avail_version'] = ZYWXAPP_P_VERSION;
        $this->_options['show_need_upgrade_msg'] = TRUE;
        
        return $this->_save();
    }

    /**
     * 配置信息版本检测
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午03:01:51
     * @author author
     * 
     */
    public function needUpgrade()
    {
        return ( $this->_internalVersion != $this->_options['options_version'] );
    }

    /**
     * 删除配置信息
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-7 下午07:42:54
     * @author author
     * 
     */
    public function uninstall()
    {
        delete_option( $this->_name );
    }
    
    /**
     * 停用插件时删除指定配置信息
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-5-30 下午03:45:53
     * @author mxg<jiemack@163.com>
     * 
     */
    public function disable()
    {
        $this->_options['app_token'] = '';
        $this->_options['app_email'] = '';
        $this->_options['email_verified'] = FAlSE;
        $this->_options['app_key'] = '';
        $this->_save();
    }

    /**
     * 设定插件配置信息
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午03:58:38
     * @author author
     * 
     */
    public function install()
    {
    	//检测是否插件核心配置已经安装
        if ( ! $this->isInstalled() ){
        	//加载默认的配置文件信息
            $this->_loadDefaultOptions();
            $this->_options['options_version'] = $this->_internalVersion;
            //zywxapp_settings 插件配置信息 保存到 wp_options 数据库中
            $this->_save();
        }

        return $this->isInstalled();
    }

    public function isInstalled()
    {
        $this->_load();
        return ( ! empty($this->_options) && isset($this->_options['options_version']) );
    }

    private function _loadDefaultOptions()
    {
        $this->_options = $this->getDefaultConfig();
    }

    /**
     * 开始设定配置信息
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-6 下午04:38:31
     * @author author
     * 
     */
    public function startBulkUpdate()
    {
        $this->_saveAsBulk = TRUE;
    }

    /**
     * 保存配置信息到数据中
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-6 下午05:42:38
     * @author author
     * 
     */
    public function bulkSave()
    {
        $this->_saveAsBulk = FALSE;
        return $this->_save();
    }

    /**
     * 保存配置信息到数据库中
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 上午10:42:35
     * @author author
     * 
     */
    private function _save()
    {
    	ZywxappLog::getInstance()->write('DEBUG', "config options save info: " . print_r($this->_options, TRUE),"ZywxappConfig._save");
    	return update_option($this->_name, $this->_options);
    }

    public function __get($option)
    {
        $value = null;
        if ( isset($this->_options[$option]) ){
            $value = $this->_options[$option];
        }
        return $value;
    }

    public function saveUpdate($option, $value)
    {
        $saved = FALSE;
        if ( isset($this->_options[$option]) ){
            $this->_options[$option] = $value;

            $this->_save();
            $saved = TRUE;
        }
        return $saved;
    }

    public function __isset($option)
    {
        return isset($this->_options[$option]);
    }
    
    public function __set($option, $value)
    {
        $saved = FALSE;
        $this->_options[$option] = $value;
        if ( !$this->_saveAsBulk ){
        	$saved = $this->_save();
        }
        return $saved;
    }
   
    public function getCdnServer()
    {
        $cdn = $this->_options['cdn_server'];
        $protocol = 'http://';

        if ( isset($_GET['secure']) && $_GET['secure']==1 ){
            $cdn = $this->_options['secure_cdn_server'];
            $protocol = 'https://';
        }
        return $protocol.$cdn;
    }

    /**
     * 获取插件中的token和版本信息
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-6 下午04:33:23
     * @author author
     * 
     */
    public function getCommonApiHeaders()
    {
        $app_token = $this->options['app_token'];
        $headers = array(
            'Application' => $app_token,
            'zywxapp_version' => ZYWXAPP_P_VERSION
        );
        if ( !empty($this->options['api_key']) ){
            $headers['Authorization'] = 'Basic '.$this->options['api_key'];
        }
        return $headers;
    }
	
    /**
     * 获取写在文件中的配置信息
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午04:03:26
     * @author 
     * 
     */
    public function getDefaultConfig()
    {
        $envSettings = array();
        require_once('conf/' . ZYWXAPP_ENV . '_config.inc.php');

        $settings = array(
        
        	'links_list_limit' => 20,
			'pages_list_limit' => 20,
			'posts_list_limit' => 20,
			'categories_list_limit' => 20,
			'tags_list_limit' => 20,
			'videos_list_limit' => 12,
            'audios_list_limit' => 20,
        	'images_list_limit' => 12,
        	'default_list_limit' => 20,
        
        	// API
			'app_token' => '',
			'app_id' => 0,
        	'app_key' => 0,
        	'app_email' => '',
        	// app
			'app_description' => '',
			'app_name' => get_bloginfo('name'),
			'app_icon' => '',
        	'app_style' => '',
			'version' => '0.1',
			'icon_url' => '',
        	'iphone_path' => '',
        	'android_path' => '',
        	'upgrade_version' => '',
        
            'qq_appkey' => '',
	    	'qq_callback_url' => '',
	    	'sina_appkey' => '',
	    	'sina_callback_url' => '',
	    	'app_style' => 'default',
        	'default_color' => '1C95DC',
	    	'app_color' => '1C95DC',
        
        	'logo' => '',
        
        	// General
            'last_recorded_save' => time(),
            'reset_settings_on_uninstall' => TRUE,
        	'settings_done' => FALSE,
            'configured' => FALSE,
            'app_live' => FALSE,
            'email_verified' => FALSE,
            'show_email_verified_msg' => TRUE,
            'zywxapp_showed_config_once' => TRUE,
        	'zywxapp_log_threshold' => 5,
        
        	'zywxapp_promotion_status' => FALSE,
       		'zywxapp_qrcode_url' => '',
        	'zywxapp_client_download_url' => '',
        );

        return array_merge($settings, $envSettings);
    }
}
