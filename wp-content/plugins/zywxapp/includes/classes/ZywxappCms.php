<?php
class ZywxappCms
{
	/**
	 * 向平台请求配置数据信息
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-6 上午10:13:13
	 * @author author
	 * 
	 */
    public function activate()
    {
        $updatedApi = FALSE;
        $profile = $this->generateProfile();

        //开启插件时向平台获取平台信息 写到数据中 
        $r = new ZywxappHTTPRequest();
        $response = $r->api($profile, '/index.php?m=curl&a=guid', 'POST');
        if (!is_wp_error($response)) {
            $tokenResponse = json_decode($response['body'], TRUE);
            if (!empty($tokenResponse) && $tokenResponse['status']){
            	//给配置类中 设定相关更新的信息
                ZywxappConfig::getInstance()->startBulkUpdate();//开始设定 配置信息
                ZywxappConfig::getInstance()->app_token = $tokenResponse['zywxid'];
                
                if ($tokenResponse['zywxemail']) {
                	ZywxappConfig::getInstance()->app_email = $tokenResponse['zywxemail'];
                	ZywxappConfig::getInstance()->email_verified = TRUE;
                }
            	if ($tokenResponse['zywxappkey']) {
                	ZywxappConfig::getInstance()->app_key = $tokenResponse['zywxappkey'];
                }
            	if ($tokenResponse['app_version']) {
                	ZywxappConfig::getInstance()->version = $tokenResponse['app_version'];
                }
                ZywxappConfig::getInstance()->bulkSave();//结束配置信息  保存到数据库中 
                $updatedApi = TRUE;
            } else {
            	echo $tokenResponse['msg'];exit;
            }
        }
        return $updatedApi;
    }
    
    /**
     * 插件停用上报平台
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-5-17 上午11:50:21
     * @author mxg<jiemack@163.com>
     * 
     */
    public function disable()
    {
        $r = new ZywxappHTTPRequest();
        $response = $r->external(array(), ZywxappConfig::getInstance()->getCdnServer().'/plugin/installStatus.action?app_key='.ZywxappConfig::getInstance()->app_key.'&status=0', 'POST');
    }

    /**
     * 卸载插件上报平台
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-7 下午07:35:15
     * @author author
     * 
     */
    public function deactivate()
    {
        
    }

    /**
     * 获取网站系统信息
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-6 上午10:47:05
     * @author author
     * 
     */
    protected function generateProfile()
    {
		$profile = array(
			'appkey' => ZywxappConfig::getInstance()->api_key,
			'plugin_name' => ZywxappConfig::getInstance()->plugin_name,
			'domain' => get_bloginfo('url'),
		);    	
        return $profile;
    }
    
	/**
	 * 去平台注册帐号
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-5-9 下午03:05:39
	 * @author author
	 * 
	 */
    public function registerUser()
    {
    	if (! ZywxappConfig::getInstance()->app_token) {
    		$return = $this->activate();
    	} 
	    $email = trim($_POST['email']);
    	$nickname = trim($_POST['nickname']);
    	$password = $_POST['password'];
        $profile = array(
        	'authcode' => ZywxappConfig::getInstance()->app_token,
        	'email' => $email,
        	'domain' => get_bloginfo('url'),
        	'password' => $password,
        	'nickname' => $nickname,
        );
        $r = new ZywxappHTTPRequest();
        $response = $r->api($profile, '/index.php?m=curl', 'POST');
        if (!is_wp_error($response)) {
            $registerResponse = json_decode($response['body'], TRUE);
        	if ( empty($registerResponse) ){
                //给配置类中 设定相关更新的信息
                ZywxappConfig::getInstance()->startBulkUpdate();//开始设定 配置信息
                ZywxappConfig::getInstance()->email_verified = TRUE;
                ZywxappConfig::getInstance()->app_email = $email;
                ZywxappConfig::getInstance()->bulkSave();//结束配置信息  保存到数据库中 
                return TRUE;
            } else {
  				return new ZywxappError('email_verified_failed', $registerResponse['msg']);
            }
        }
        return new ZywxappError('register_failed', __('AppCan-WP手机客户端插件遇到了问题。看我们如何能帮助您解决这个问题，请加入我们AppCan wordpress交流群236201529。', 'zywxapp'));
    }
    
    /**
     * 去平台注册应用
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-5-9 下午03:06:01
     * @author author
     * 
     */
    public function registerApp()
    {
        $profile = array(
        	'authcode' => ZywxappConfig::getInstance()->app_token,
        	'pluginName' => ZywxappConfig::getInstance()->plugin_name,
        );
        $r = new ZywxappHTTPRequest();
        $response = $r->api($profile, '/index.php?m=curl&a=registeApp', 'POST');
        
        if (!is_wp_error($response)) {
            $registerResponse = json_decode($response['body'], TRUE);
        	if (!empty($registerResponse) && $registerResponse['status']){
                //给配置类中 设定相关更新的信息
                //ZywxappConfig::getInstance()->startBulkUpdate();//开始设定 配置信息
                ZywxappConfig::getInstance()->app_key = $registerResponse['appkey'];
                //ZywxappConfig::getInstance()->bulkSave();//结束配置信息  保存到数据库中 
                return TRUE;
            } else {
            	return new ZywxappError('register_appcan_failed', $registerResponse['msg']);
            }
        }
        return new ZywxappError('register_failed', __('AppCan-WP手机客户端插件遇到了问题。看我们如何能帮助您解决这个问题，请加入我们AppCan wordpress交流群236201529。', 'zywxapp'));
    }
     
    /**
     * 创建应用
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-5-9 下午04:37:16
     * @author author
     * 
     */
    public function createApp()
    {
    	$profile = array(
        	'authcode' => ZywxappConfig::getInstance()->app_token,
    		'app_style' => ZywxappConfig::getInstance()->app_color,
        	'version' => ZywxappConfig::getInstance()->version,
        );
        $r = new ZywxappHTTPRequest();
        $response = $r->api($profile, '/index.php?m=curl&a=create', 'POST');
        
        if (!is_wp_error($response)) {
            $response = json_decode($response['body'], TRUE);
        	if ($response['status']){
                //给配置类中 设定相关更新的信息
                ZywxappConfig::getInstance()->startBulkUpdate();//开始设定 配置信息
                ZywxappConfig::getInstance()->settings_done = TRUE;
                ZywxappConfig::getInstance()->configured = TRUE;
                ZywxappConfig::getInstance()->bulkSave();//结束配置信息  保存到数据库中 
                return TRUE;
            } else {
            	return new ZywxappError('create_app_failed', $response['msg']);
            }
        }
        return new ZywxappError('register_failed', __('AppCan-WP手机客户端插件遇到了问题。看我们如何能帮助您解决这个问题，请加入我们AppCan wordpress交流群236201529。', 'zywxapp'));
    }
     
    /**
     * 登录操作
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-6-8 下午12:45:57
     * @author author
     * 
     */
	public function loginApp()
    {
    	if (! ZywxappConfig::getInstance()->app_token) {
    		$return = $this->activate();
    	} 
    	$profile = array(
        	'authcode' => ZywxappConfig::getInstance()->app_token,
    		'domain' => get_bloginfo('url'),
        );
        $r = new ZywxappHTTPRequest();
        $response = $r->api($profile, '/index.php?m=curl&a=loginReport&callback=?', 'POST');
        if (!is_wp_error($response)) {
            $loginrResponse = json_decode($response['body'], TRUE);
        	if ( !empty($loginrResponse) && $loginrResponse['status'] ){
                //给配置类中 设定相关更新的信息
                ZywxappConfig::getInstance()->startBulkUpdate();//开始设定 配置信息
                //ZywxappConfig::getInstance()->email_verified = TRUE;
                //ZywxappConfig::getInstance()->app_email = $loginrResponse['zywxemail'];
                ZywxappConfig::getInstance()->bulkSave();//结束配置信息  保存到数据库中 
                return TRUE;
            } else {
  				return new ZywxappError('email_verified_failed', $loginrResponse['msg']);
            }
        }
        return new ZywxappError('register_failed', __('AppCan-WP手机客户端插件遇到了问题。看我们如何能帮助您解决这个问题，请加入我们AppCan wordpress交流群236201529。', 'zywxapp'));
    }
     
    /**
     * 获取客户端下载二维码图片
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-6-12 下午05:29:15
     * @author mxg<jiemack@163.com>
     * 
     */
    public function getQrcode()
    {
    	$profile = array(
        	'app_key' => ZywxappConfig::getInstance()->app_key,
        	'pt' => $_GET['client'],
        );
        $r = new ZywxappHTTPRequest();
        $response = $r->external($profile, 'http://'.ZywxappConfig::getInstance()->cdn_server.'/plugin/getDownload.action', 'POST');
        if (!is_wp_error($response)) {
            $qrcodeResponse = $response['body'];
        	if (!empty($qrcodeResponse) ){
        		$qrcode = explode('|', $qrcodeResponse);
        		ZywxappConfig::getInstance()->zywxapp_client_download_url = $qrcode[0];
                ZywxappConfig::getInstance()->zywxapp_qrcode_url = $qrcode[1];
                return TRUE;
            } else {
            	ZywxappConfig::getInstance()->zywxapp_client_download_url = '';
            	return FALSE;
            }
        }
    }
     
}