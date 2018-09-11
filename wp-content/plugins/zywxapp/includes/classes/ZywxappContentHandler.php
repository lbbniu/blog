<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
* 主要功能用于插件初始设定 通过非后台插件入口请求数据 设定显示 模版、CSS等
* @package ZywxappWordpressPlugin
* @subpackage ContentDisplay
* @author zywx phpteam
*/
class ZywxappContentHandler
{
    public $mobile;
    private $inApp;
    private $inSave = FALSE;

    private static $_instance = null;

	/**
	 * 构造方法
	 *
	 * 降低一些网站开销，设定后台CSS，添加内容处理视频过滤器
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-12 下午05:53:47
	 * @author author
	 * 
	 */
    private function __construct()
    {
        $this->mobile = false;
        $this->inApp = false;
		//在所有用户启用的插件都被 WordPress 加载之后执行
        add_action('plugins_loaded', array(&$this, 'detectAccess'), 99);//降低网站开销
        if (ZywxappConfig::getInstance()->zywxapp_promotion_status) {
        	add_action('wp_head', array(&$this, 'promotionSet'), 10);//添加推广弹出层
        }
        
		//非进后台管理 通过插件接口请求数据
        if ( strpos($_SERVER['REQUEST_URI'], '/wp-admin') === false && strpos($_SERVER['REQUEST_URI'], 'xmlrpc') === false) {
        	if (strpos($_SERVER['REQUEST_URI'], 'zywxapp/') !== FALSE) {
        		add_filter('auth_cookie_expiration', array(&$this, 'customCookieExpiration'), 99, 3);
        		add_filter('list_terms_exclusions' , array($this, 'addListTermsExclusions'),10,1);
			}
        } else {
        	//后台插件管理菜单  http://wo.wordpress.com/wp-admin/admin.php?page=zywxapp
            if (strpos($_SERVER['REQUEST_URI'], 'zywxapp') !== false){
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Expires: " . gmdate("D, d M Y H:i:s", time() - 3600) . " GMT");
                add_filter('admin_head', array(&$this, 'doAdminHeadSection'), 99);//点击后台插件菜单设置 添加css
            }
        }
    }

    public function isInApp()
    {
        return $this->inApp;
    }

	/**
	 * 删除一些钩子降低网站开销
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-5 下午05:52:03
	 * @author author
	 * 
	 */
    public function removeKnownFilters()
    {
        remove_filter('the_content', 'addthis_social_widget');
        remove_filter('the_content', 'A2A_SHARE_SAVE_to_bottom_of_content', 98);
        remove_filter("gettext", "ws_plugin__s2member_translation_mangler", 10, 3);
        remove_filter('the_content', 'shrsb_position_menu');
        remove_action('wp_head',   'dl_copyright_protection');
        remove_action('wp_footer', 'thisismyurl_noframes_killframes');
    }

    /**
     * 设定在应用中
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午05:50:59
     * @author author
     * 
     */
    private function _setInApp()
    {
        $this->mobile = TRUE;
        $this->inApp = TRUE;
        $this->removeKnownFilters();
    }

    /**
     * 针对在应用中为网站降低开销
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午05:48:04
     * @author author
     * 
     */
    public function detectAccess()
    {
		//通过插件请求网站数据
        if (strpos($_SERVER['REQUEST_URI'], 'zywxapp/') !== FALSE) {
        	$this->inApp = TRUE;
        	ZywxappLog::getInstance()->write('INFO', "In the application display", "ZywxappContentHandler.detectAccess");
            remove_action( 'plugins_loaded', 'wptouch_create_object' );
            $this->_setInApp();
        }
    }
    
    /**
     * 设置登入cookie时间
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-4-10 上午11:02:46
     * @author mxg<jiemack@163.com>
     * 
     */
	public function customCookieExpiration($expiration, $user_id = 0, $remember = true)
	{
	    if($remember) {
	        $expiration = 31536000;
	    }
    	return $expiration;
	}
	
	/**
	 * 增加搜索分类条件sql语句
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-5-14 上午11:10:35
	 * @author author
	 * 
	 */
	public function addListTermsExclusions($exclusions)
	{
		global $wpdb;
		if (isset($_GET['up']) && !empty($_GET['up'])) {
        	$exclusions .= $wpdb->prepare(" AND t.term_id > %d", $_GET['up']);
        } elseif (isset($_GET['down']) && !empty($_GET['down'])) {
        	$exclusions .= $wpdb->prepare(" AND t.term_id < %d", $_GET['down']);
        }
		return $exclusions;
	}

	/**
	 * 添加浮动层推广界面
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-6-11 下午02:55:45
	 * @author mxg<jiemack@163.com>
	 * 
	 */
	public function promotionSet()
	{
		if (is_home()) {
			$promotionJsFile = WP_PLUGIN_URL."/". ZYWXAPP_DIR_NAME. '/themes/admin/promotion.js';
    		wp_register_script('zywxapp_promotion_script', $promotionJsFile, array('jquery'));
    		wp_enqueue_script('zywxapp_promotion_script');
		}
		$promotionCssFile = WP_PLUGIN_URL."/". ZYWXAPP_DIR_NAME. '/themes/admin/promotion.css';
    	wp_register_style('zywxapp_promotion_style', $promotionCssFile);
    	wp_enqueue_style('zywxapp_promotion_style');
	}
	
    /**
     * 点击后台插件菜单设置 添加css
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午06:45:12
     * @author author
     * 
     */
    public function doAdminHeadSection()
    {
        $cssFile = dirname(__FILE__) . '/../../themes/admin/style.css';
        if ( file_exists($cssFile) ){
            $css = file_get_contents($cssFile);
            $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
            $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
            $css = str_replace('@@@ZYWXAPP_IMAGES@@@', ZYWXAPP_IMAGES_URL, $css);
            echo '<style type="text/css">'. $css . '</style>';
        }
    }
    
    /**
     * @static
     * @return ZywxappContentHandler
     */
    public static function getInstance() 
    {
        if( is_null(self::$_instance) ) {
            self::$_instance = new ZywxappContentHandler();
        }
        return self::$_instance;
    }
}
