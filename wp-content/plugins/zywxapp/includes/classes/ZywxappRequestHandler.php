<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
* 处理请求类
* 
* @package ZywxappWordpressPlugin
* @subpackage Core
* @author mxg<jiemack@163.com>
* 
*/
class ZywxappRequestHandler
{
	//异常标识
    private $_errorReportingLevel = 0;

    /**
     * 构造函数绑定钩子请求处理
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-2 下午04:09:31
     * @author author
     * 
     */
    public function __construct()
    {
    	//在主WordPress函数wp中解析查询请求后，执行该动作函数。函数接收的参数：引用全局变量$wp对象的数组
        add_action('parse_request', array(&$this, 'handleRequest'));
        add_action('init', array(&$this, 'logInitRequest'), 1);
    }
    
	/**
	 * url请求拦截，如果是 zywxapp/ 我们的插件那执行插件功能处理
     * 
     * @see ZywxappRequestHandler::_routeRequest
     * @param WP object  the main wordpress object is passed by reference
     */
    public function handleRequest($wp)
    {
        $request = $wp->request;
        if (empty($request)){
            $request = urldecode($_SERVER['QUERY_STRING']);
        }
        ZywxappLog::getInstance()->write('info', "Got a request for the blog: ".print_r($request, TRUE),"ZywxappRequestHandler.handleRequest");
        // www.wordpress.com/?zywxapp/ 通过插件请求数据  处理请求分发
        if (($pos = strpos($request, 'zywxapp/')) !== FALSE){
            if ($pos != 0){
                $request = substr($request, $pos);
            }            
            $request = str_replace('?', '&', $request);
            //操作 action 分发器
            $this->_routeRequest($request);
        } 
    }
    
    /**
     * 删除禁止访问我们插件的钩子action
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午08:01:24
     * @author mxg<jiemack@163.com>
     * 
     */
    public function logInitRequest()
    {
        $request = $_SERVER['QUERY_STRING'];
        if (strpos($request, 'zywxapp/') !== FALSE){
            global $restricted_site_access;
            if ( !empty($restricted_site_access) ){
                remove_action('parse_request', array($restricted_site_access, 'restrict_access'), 1);
            }
        }
    }

    /**
     * 异常处理
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-2 下午04:07:28
     * @author mxg<jiemack@163.com>
     * 
     */
    public function handleGeneralError()
    {
    	$error = error_get_last();
        if(($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR)){
            ob_end_clean();
            $header = array(
                'action' => 'handleGeneralError',
                'status' => FALSE,
                'code' => 500,
                'message' => 'There was a critical error running the service',
            );
            if(stripos($error['message'], 'Allowed memory size of ') === false) {
                ZywxappLog::getInstance()->write('Error', "Caught an error: " . print_r($error, TRUE),"ZywxappRequestHandler.handleGeneralError");
            }
            if ( $this->_errorReportingLevel !== 0 ){
                //$header['message'] = $error['message'];
                $header['message'] = implode('::', $error);
            }
			$header = json_encode($header);
			if (isset($_GET['callback'])){
	            header('Content-Type: text/javascript; charset: utf-8');
	            $header = $_GET["callback"] . "({$header})";  
	        } 
			echo $header;exit;
        }
    }
    
    /**
     * 统一action分发器
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-3 下午02:15:41
     * @author author
     * 
     */
    private function _routeRequest($request)
    {
        $this->_errorReportingLevel = error_reporting(0);
        //处理异常 方法
        register_shutdown_function(array($this, 'handleGeneralError'));
        //请求实例 http://www.wordpress.com/?zywxapp/user/login/&user_login=admin&user_pass=admin  
        $fullReq = explode('&', $request);
        $req = explode('/', $fullReq[0]);
        $service = $req[1];//示例 user 
        $action = $req[2]; //示例 login
        $act_type = $req[3];
        switch ($service) {
        	//用户系统 /?zywxapp/user/$action
        	case 'user':
        		switch ($action) {
        			//用户登入
        			case 'login':
        				$this->runService('User', 'login');
        				break;
        			//检测用户名和密码是否正确
        			case 'check':
        				$this->runService('User', 'check');
        				break;
        			//登出 
        			case 'logout':
        				$this->runService('User', 'logout');
        				break;
        			//注册入口
        			case 'register':
        				$this->runService('User', 'register');
        				break;
        			//忘记密码入口
        			case 'forgotpass':
        				$this->runService('User', 'forgotPassword');
        				break;
        			default:
        				break;
        		}
        		break;
        	//给博客添加评论
        	case 'comment':
				$this->runService('Comment', 'add');
            	break;
        	//等比例缩放  并放入缓存   zywxapp/cache
        	case 'getimage':
 				ZywxappImageServices::getByRequest();
           	 	exit();
        		break;
        	//获取网站信息|搜索 统一入口分发 /?zywxapp/content|search/$action
        	case 'search':
        	case 'content':
        		ob_end_clean();
        		ob_start();
				$cache = ZywxappCache::getCacheInstance(array('duration' => 600));
				$key = str_replace('/', '_', $request);
				if (function_exists('is_multisite') && is_multisite()) {
					global $wpdb;
					$key .= $wpdb->blogid;
				}
				$key .= ZywxappContentEvents::getCacheTimestampKey();
				global $zywxappLoader;
				$key .= $zywxappLoader->getVersion();
	
				$encoding = '';
				if ( isset($_SERVER["HTTP_ACCEPT_ENCODING"]) ) {
					$encoding = $_SERVER["HTTP_ACCEPT_ENCODING"];
				} elseif ( isset($_SERVER["HTTP_X_CEPT_ENCODING"]) ){
					$encoding = $_SERVER["HTTP_X_CEPT_ENCODING"];
				}
				if ( strpos($encoding, 'x-gzip') !== FALSE ) {
					$encoding = 'x-gzip';
				} elseif ( strpos($encoding,'gzip') !== FALSE ) {
					$encoding = 'gzip';
				}
				$key .= $encoding;
	
				$eTagIncoming = (isset($_SERVER['HTTP_IF_NONE_MATCH'])) ? $cache->getEtagFromHeader($_SERVER['HTTP_IF_NONE_MATCH']) : '';
				$key .= $eTagIncoming;
	
				$output = array(
					'headers' => array(),
					'e_tag_incoming' => $eTagIncoming,
					'e_tag_stored' => '',
					'key' => md5($key),
					'encoding' => $encoding,
					'content' => '',
					'is_new_content' => '0',
				);
				$cache->getContent($output);
	
				if ($output['content'] == '') {
					$this->_routeContent($output, $req);
				}
	
				$cache->endCache($output);
	
				ob_end_flush();
	            exit();
        		break;
        	case 'system':
       			switch ($action) {
        			case 'client':
        				$this->runService('System', 'clientUpgrade');
        				break;
        			case 'logo':
        				$this->runService('System', 'updateLogo');
        				break;
        			case 'success':
        				$this->runService('System', 'success');
        				break;	
        			case 'promotion':
        				$this->runScreenBy('System', 'promotion');
        			break;
        			default:
        				break;
        		}
        		break;
        	default:
        		exit;
        		break;
        }
    }
    
    /**
     * 数据提取分发器
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-3 下午03:09:25
     * @author author
     * 
     */
    private function _routeContent(array & $output, $req)
    {
        ob_end_clean();
        ob_start();
        header('Cache-Control: no-cache, must-revalidate');
        $offset = 600; 
        header('Expires: ' . gmdate("D, d M Y H:i:s", time() + $offset) . ' GMT');
        header('Content-Type: application/json; charset: utf-8');
        $type = $req[2];
        $id = $req[3];
        if ($req[1] == 'search'){
			$this->runScreenBy('Search', 'Query');
        } else {
	        //URL示例 /?zywxapp/content/$type
	        switch ($type) {
	        	//获取文章详情
	        	case 'post':
	        		if ('content' == $_REQUEST['type']) {
	        			$this->runScreenBy('Posts', 'Content', $id);
	        		} else {
	        			$this->runScreenBy('Posts', 'Id', $id);
	        		}
	        		break;
	        	case 'logos':
	        		$this->runScreenBy('System', 'Logos');
	        		break;
	        	case 'weibo':
	        		$this->runScreenBy('System', 'Weibo');
	        		break;
	        	//获取 各种列表信息 入口
	        	case 'list':
		        	$sub_type = $req[3];
		        	$show_by = $req[4];
		        	ZywxappLog::getInstance()->write('info', "Listing... The sub type is: {$sub_type}", "ZywxappRequestHandler._routeContent");
	                switch ($sub_type) {
	                	//分类列表
	                	case 'categories':
	                		$this->runScreen('Categories');
	                		break;
	                	//标签列表
	                	case 'tags':
	                		$this->runScreen('Tags');
	                		break;
	                	//评论列表						
	                	case 'comments':
	                		switch ($show_by) {
	                			case 'post':
	                				$this->runScreenBy('Comments', 'Post', $req[5]);
	                				break;
	                		}
							break;
	                	//返回文章列表
	                	case 'posts':
		                    $this->runScreen('Posts');
	                		break;
	                	//分类下文章列表
	                	case 'category':
	                		$this->runScreenBy('Posts', 'Category', $show_by);
	                		break;
	                	//标签下文章列表
	                	case 'tag':
	                		$this->runScreenBy('Posts', 'Tag', $show_by);
	                		break;	
	                	//影音会列表获取
	                	case 'medias':
	                		switch ($show_by) {
	                			case 'image':
	                				$this->runScreenBy('Medias', 'image');
	                				break;
	                			case 'audio':
	                				$this->runScreenBy('Medias', 'audio');
	                				break;
	                			case 'video':
	                				$this->runScreenBy('Medias', 'video');
	                				break;
	                		}
	                		break;
	                	default:
	        				break;
	                }
	        		break;
	        	default:
	        		break;
	        }
        }
        $contents = ob_get_clean();
		ZywxappLog::getInstance()->write('DEBUG', "BTW the get params were:".print_r($_GET, TRUE), "ZywxappRequestHandler._routeContent");
		if (isset($_GET['callback'])) {
			ZywxappLog::getInstance()->write('DEBUG', "The callback GET param set:".$_GET["callback"] . "(" . $contents . ")", "ZywxappRequestHandler._routeContent");
			$output['headers'][] = 'Content-Type: text/javascript; charset: utf-8';
			$contents = $_GET["callback"] . '(' . $contents . ')';
		} else {
			ZywxappLog::getInstance()->write('INFO', "The callback GET param is not set", "ZywxappRequestHandler._routeContent");
		}
		echo $contents;
		$output['e_tag_stored'] = md5($contents);
		$output['e_tag_stored'] .= ZywxappContentEvents::getCacheTimestampKey();
		ZywxappLog::getInstance()->write('DEBUG', "The checksum for the content is: {$output['e_tag_stored']}", "ZywxappRequestHandler._routeContent");
		$output['headers'][] = 'ETag: "' . $output['e_tag_stored'] . '"';
		$output['is_new_content'] = '1';
    }

    /**
     * 数据操作类调用
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午06:58:18
     * @author author
     * 
     */
    public function runService($service_type, $service_method, $param=null)
    {
        $serviceClassName = "Zywxapp{$service_type}Services";
        $serviceClass = new $serviceClassName();
        if ( is_callable(array($serviceClass, $service_method))){
            if ( $param == null ){
                $serviceClass->$service_method();
            } else {
                $serviceClass->$service_method($param);
            }
        }
    }
    
    /**
     * 数据提取类调用默认方法
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午07:00:18
     * @author author
     * 
     */
    public function runScreen($screen_class_name)
    {
    	// action 分发 /blocks/screens 类库
        $className = "Zywxapp{$screen_class_name}Screen";
        $screen = new $className();
        $screen->run();
    }

    /**
     * 数据提取类调用通过详细方法
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午07:00:18
     * @author mxg<jiemack@163.com>
     * 
     */
	public function runScreenBy($screen_class_name, $by_func_name, $param=null)
    {
        $className = "Zywxapp{$screen_class_name}Screen";
        $funcName = "runBy{$by_func_name}";
        $screen = new $className();
        if ( $param == null ){
            $screen->$funcName();
        } else {
            $screen->$funcName($param);
        }
    }
}
