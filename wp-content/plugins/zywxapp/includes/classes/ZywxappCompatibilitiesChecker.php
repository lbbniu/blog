<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
 * 获取网站服务器相关信息类
 *
 * class_description
 *
 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
 * @since      File available since Release 1.0 -- 2012-3-6 上午11:57:05
 * @author author
 */
class ZywxappCompatibilitiesChecker
{
    public $critical = FALSE;
    public $testedConnection = FALSE;
    public $hadConnectionError = FALSE;
	
	/**
	 * 检测插件安装时出现的错误信息
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2011-12-31 下午07:14:46
	 * @author author
	 * 
	 */	 
    public function scanningTestAsHtml()
    {
		$html = '';
		//测试是否能链接到平台
        $netCheck = $this->testConnection();
        if ( ZywxappError::isError($netCheck) ){
            $html .= $netCheck->getHTML();
        }
		//检测php 一些扩展功能
        $php = $this->testPhpRequirements();
        if ( ZywxappError::isError($php) ){
            $html .= $php->getHTML();
        }
		//检测数据库是否安装成功
        $db = $this->testDatabase();
        if ( ZywxappError::isError($db) ){
            $html .= $db->getHTML();
        }
        
    	$token = $this->testToken();
		if ( ZywxappError::isError($token) ){
			$html .= $token->getHTML();
		}
        $html = $this->getErrorButtonHtml($html);
       
        return $html;
    }

    public function fullTestAsHtml()
    {
        $html = '';
        $netCheck = $this->testConnection();
        if ( ZywxappError::isError($netCheck) ){
            $html .= $netCheck->getHTML();
        }
        $php = $this->testPhpRequirements();
        if ( ZywxappError::isError($php) ){
            $html .= $php->getHTML();
        }
        $phpGraphic = $this->testPhpGraphicRequirements();
        if ( ZywxappError::isError($phpGraphic) ){
            $html .= $phpGraphic->getHTML();
        }
        $db = $this->testDatabase();
        if ( ZywxappError::isError($db) ){
            $html .= $db->getHTML();
        }
        $allowFopen = $this->testAllowUrlFopen();
        if ( ZywxappError::isError($allowFopen) ){
            $html .= $allowFopen->getHTML();
        }
    	$token = $this->testToken();
		if ( ZywxappError::isError($token) ){
			$html .= $token->getHTML();
		}
        $dirs = $this->testWritingPermissions();
        if ( ZywxappError::isError($dirs) ){
            $html .= $dirs->getHTML();
        }
        $html = $this->getErrorButtonHtml($html);
       
        return $html;
    }

    /**
     * 获取插件安装是否成功标识
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-6 下午07:07:31
     * @author author
     * 
     */
    public function foundCriticalIssues()
    {
        return $this->critical;
    }

    /**
     * 检测插件log目录、网站数据缓存目录、插件图片缓存目录是否可写
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-6 上午11:37:23
     * @author author
     * 
     */
    public function testWritingPermissions($return_as_html = true)
    {
    	//检测log 目录是否可写
        $logs = ZywxappLog::getInstance()->checkPath();
        //检测网站数据缓存目录是否可写
        $cache = ZywxappCache::getCacheInstance()->checkPath();
		//检测是否关闭缓存
		$isCacheEnabled = ZywxappCache::getCacheInstance()->isCacheEnabled();
		//图片处理功能
        $thumbsHandler = new ZywxappImageHandler();
        $thumbs = $thumbsHandler->checkPath();//检测图片缓存目录是否可写
		//给出异常缓存功能目录文件不可写 $return_as_html = true 参数设定
        if ( !$cache || !$logs || !$thumbs ){
            if ($return_as_html) {
                $message = '似乎您的服务器设置阻止访问特定的目录。AppCan-WP手机客户端插件需要以下目录写入权限:<br /><ul>';
                if ( !$cache ){
                    $message .= '<li>wp-content/uploads</li>';
                }
               	if ( !$logs ) {
                    $message .= '<li>wp-content/plugins/zywxapp/logs</li>';
                }
                if ( !$thumbs ){
                    $message .= '<li>wp-content/plugins/zywxapp/cache</li>';
                }
                $message .= '</ul>虽然您可以选择不提供这些权限，从而放弃了您的缓存的优势，但是这将造成您的客户端用户请求实时。';
                return new ZywxappError('writing_permissions_error', __($message, 'zywxapp'));
            } else {
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * 检测数据库是否安装成功
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-6 下午06:43:22
     * @author author
     * 
     */
    public function testDatabase()
    {
        if ( !ZywxappDB::getInstance()->isInstalled() ){
            ZywxappDB::getInstance()->install();
            if ( !ZywxappDB::getInstance()->isInstalled() ){
                $this->critical = TRUE;
                return new ZywxappError('database_error', __('没有您的wordpress数据库安装权限', 'zywxapp'));
            }
        }

        return TRUE;
    }
    
	/**
	 * 检测web服务器系统
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-6 下午02:39:23
	 * @author author
	 * 
	 */
    public function testWebServer($return_as_html = true)
    {
        if (isset($_SERVER['SERVER_SOFTWARE'])) { // Microsoft-IIS/x.x (Windows xxxx)
            if (stripos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') === FALSE) {
                return TRUE;
            } else {
                if ($return_as_html) {
                    return new ZywxappError('iis_server_found', __('您的博客是在IIS服务器上运行; AppCan-WP手机客户端插件log记录文件不保存在您服务器上', 'zywxapp'));
                } else {
                    return FALSE;
                }
            }
        } else {
            if ($return_as_html) {
                return new ZywxappError('iis_server_found', __('您的博客是在IIS服务器上运行; AppCan-WP手机客户端插件log记录文件不保存在您服务器上', 'zywxapp'));
            } else {
                return FALSE;
            }
        }
    }

    /**
     * 得到网站电脑系统
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-6 上午11:36:05
     * @author author
     * 
     */
    public function testOperatingSystem()
    {
        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            if (stripos($_SERVER['SERVER_SOFTWARE'], 'Win32') === FALSE) {
                return 'Linux';
            } else {
                return 'Windows';
            }
        } else {
            return 'Unknown';
        }
    }

    /**
     * 加载PHP图片处理扩展
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-6 下午02:09:33
     * @author author
     * 
     */
    public function testPhpGraphicRequirements($return_as_html = true)
    {
    	//加载图片处理扩展
        $gotGD = extension_loaded('gd');
        $gotImagick = extension_loaded('imagick');
        if (! $gotGD && !$gotImagick ){
            if ($return_as_html) {
                return new ZywxappError('missing_php_requirements', __('AppCan-WP手机客户端插件需要在服务器上安装的GD或ImageMagick的PHP扩展。请联系您的托管服务提供商，使这些扩展，否则缩略图将无法正常工作', 'zywxapp'));
            } else {
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * 检测php allow_url_fopen开启状态
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-6 下午02:12:45
     * @author author
     * 
     */
    public function testAllowUrlFopen($return_as_html = true)
    {
        if ( ini_get('allow_url_fopen') != '1' ){
            if ($return_as_html) {
                return new ZywxappError('missing_php_requirements', __('您的主机阻止PHP指令allow_url_fopen选项，如果需要由AppCan-WP手机客户端插件平台提供图片压缩功能，请编辑您的php.ini文件，找到“allow_url_fopen = OFF”改成“allow_url_fopen=On"', 'zywxapp'));
            } else {
                return FALSE;
            }
        }
        // If we got till here all is good
        return TRUE;
    }

    public function testPhpRequirements()
    {
        $errors = new ZywxappError();
        if ( !extension_loaded('libxml') || !extension_loaded('dom') ){
            $errors->add('missing_php_requirements', __('为了AppCan-WP手机客户端插件操作, libxml 和 DOM 扩展都必须安装和启用。 ', 'zywxapp'));
            $this->critical = TRUE;
        }
        if ( !empty($errors) ){
            return $errors;
        } else {
            return TRUE;
        }

    }
    
	/**
	 * 检测网站和插件平台是否能正常通信
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-6 下午06:03:15
	 * @author author
	 * 
	 */
    public function testConnection()
    {
        $this->testedConnection = TRUE;
        $r = new ZywxappHTTPRequest();
        $response = $r->api(array('param'=>1), '/index.php?m=curl&a=getNewestVersion', 'POST');
        if ( is_wp_error($response) ) {
            if ( "couldn't connect to host" == $response->get_error_message() ){
                $this->critical = TRUE;
                $this->hadConnectionError = TRUE;
                return new ZywxappError('testing_connection_failed', __('您的服务器阻止了向我们服务器发出请求。请修改您的防火墙和任何其他的安全措施，使能够发出请求。', 'zywxapp'));
            } else {
                return new ZywxappError($response->get_error_code(), $response->get_error_message());
            }
        } else {
            $checkResult = json_decode($response['body']);
            if ( empty($checkResult) ){
                if ( isset($response['response']) && isset($response['response']['code']) && $response['response']['code'] === FALSE ){
                    $this->critical = TRUE;
                    $this->hadConnectionError = TRUE;
                    return new ZywxappError('testing_connection_failed', __('您的主机不允许任何类型的发出请求。AppCan-WP手机客户端插件需要使用HTTP扩展，cURL，Streams，或fsockopen被安装并启用。请联系您的托管服务提供商来解决这个问题。', 'zywxapp'));
                } else {
                    return new ZywxappError('testing_connection_failed',__('AppCan-WP手机客户端插件遇到了问题。看我们如何能帮助您解决这个问题，请加入我们AppCan wordpress交流群236201529。', 'zywxapp'));
                }
            } /*else {
                if ( isset($checkResult['status']) ){
                    return new ZywxappError('testing_connection_failed', $checkResult['msg']);
                }
            }*/
        }
        return TRUE;
    }
    
	public function testToken()
	{
		$activated = !empty(ZywxappConfig::getInstance()->app_token);
		if (!$activated ){
			$cms = new ZywxappCms();
			$activated = $cms->activate();
		}

		if ( !$activated ) {
			$errors = new ZywxappError();
			if ( !$this->testedConnection ){
				$connTest = $this->testConnection();
				if ( ZywxappError::isError($connTest) ){
					$errors = $connTest;
				}
			}
			if ( !$this->hadConnectionError ){
				$this->critical = TRUE;
				$errors->add('missing_token', __('看来主服务器没有应答。请确保您的互联网连接工作,然后再试一次。', 'zywxapp'));
			}
			return $errors;
		}
		return TRUE;
	}

	/**
	 * 得到错误提示button
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-5-14 下午03:47:08
	 * @author author
	 * 
	 */
	public function getErrorButtonHtml($html)
	{
		$buttons = '<div class="buttons">';
        if ( $this->foundCriticalIssues() ){
            $buttons .= '<a href=javascript:window.location.reload(); id="zywxapp_retry_compatibilities">'.__('重试', 'zywxapp') .'</a>';
        } else {
            $buttons .= '<a href=javascript:void(0); id="zywxapp_close_compatibilities" class="close">'.__('关闭', 'zywxapp') .'</a>';
        }
        $buttons .= '</div>';
        if ( !empty($html) ){
            $html = '<div id="zywxapp_compatibilities_errors" class="zywxapp_errors_container"><div class="errors_container"><div class="errors">' 
            . $html . '</div>' . $buttons . '</div><div class="hidden report_container"></div></div>';
        } 
        return $html;
	}
	 
}