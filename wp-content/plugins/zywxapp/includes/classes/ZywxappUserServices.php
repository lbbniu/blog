<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappUserServices extends ZywxappBaseServices
{
	private $_cmsUser;
	
	public function __construct()
	{
		if(! $this->_CmsUser) {
			$this->_cmsUser = new ZywxappCmsUserAccountHandler();
		}
	}
	
	/**
	 * 登入功能
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-1-11 下午02:58:42
	 * @author mxg<jiemack@163.com>
	 * 
	 */
	public function login()
	{
        $return = $this->_cmsUser->login();
		$this->outputSection($return);
	}
	
	/**
	 * 登出
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-1-12 下午02:44:40
	 * @author mxg<jiemack@163.com>
	 * 
	 */
	public function logout()
	{
		wp_logout();
		$return = array();
        $this->outputSection($return,__('成功退出', 'zywxapp'));
	}
	
    /**
     * 用户注册
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-1-11 下午03:00:26
     * @author mxg<jiemack@163.com>
     * 
     */
 	public function register()
 	{
        $result = array();
        if (! get_option('users_can_register')) {
        	$return = new WP_Error('register_closed',__('新用户注册暂时关闭。', 'zywxapp'));
        } else {
        	$return = $this->_cmsUser->register();
        }
        $this->outputSection($return);
    }
    
    /**
     * 找回密码功能
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-1-11 下午03:00:57
     * @author mxg<jiemack@163.com>
     * 
     */
    public function forgotPassword()
    {
    	$message = __('获取新密码成功，您会收到一封包含创建新密码链接的电子邮件', 'zywxapp');
        $return = $this->_cmsUser->forgotPassword();
        $this->outputSection($return,$message);
    }
    
	/**
     * 检测用户名密码是否正确
     *
     * 为添加帐号时使用
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-1-14 下午01:09:32
     * @author mxg<jiemack@163.com>
     * 
     */
    public function check()
    {
        $result = array();
        $return = $this->_cmsUser->check();
        $this->outputSection($return,__('帐号密码正确', 'zywxapp'));
    }
}