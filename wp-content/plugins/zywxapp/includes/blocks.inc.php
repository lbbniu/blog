<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
* 加载所需要的类库
* @package Zywxapp Wordpress Plugin
* @author  mxg<jiemack@163.com>
*/
class ZywxappLoader
{
    private $versions = array();
    private $defaultVersion = '1.1.0';
    private $version = ZYWXAPP_P_VERSION;
    private $prefix = 'zywxapp';

    /**
     * 构造方法
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午05:32:57
     * @author author
     * 
     */
    public function __construct()
    {
        $this->_checkSetIncludePath();//自动加载类 设定 include 环境路径
        // Register this class as autoloader for classes 注册Php __autoload 函数
        spl_autoload_register(array($this, 'loadClass'));
        $this->load();//插件开始执行加载
    }

    /**
     * 设定自动加载类php环境
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-3 下午01:21:53
     * @author author
     * 
     */
    private function _checkSetIncludePath()
    {
        $currentPath = get_include_path();//系统include路径
        $currentFilePath = dirname(__FILE__);//当前文件目录
        if ( strpos($currentPath, $currentFilePath) === FALSE ){
            $path =  $currentFilePath . DIRECTORY_SEPARATOR . 'blocks';
            $path .= PATH_SEPARATOR . $currentFilePath . DIRECTORY_SEPARATOR . 'blocks' . DIRECTORY_SEPARATOR . 'components';
            $path .= PATH_SEPARATOR . $currentFilePath . DIRECTORY_SEPARATOR . 'blocks' . DIRECTORY_SEPARATOR . 'screens';
            $path .= PATH_SEPARATOR . $currentFilePath . DIRECTORY_SEPARATOR . 'classes';

            set_include_path($currentPath . PATH_SEPARATOR . $path);
        }
    }

    public function getVersion()
    {
        return $this->version;
    }
    
    protected function load()
    {
        if (is_dir(dirname(__FILE__) . "/blocks")){
            if ($func_dir = opendir(dirname(__FILE__) . "/blocks")){
                while (($sub_dir = readdir($func_dir)) !== false){
                    if (preg_match("/\.php$/", $sub_dir) && !preg_match("/^index\.php$/i", $sub_dir)){
                        if ( strpos($sub_dir, "_") !== 0){
                            $block = $this->getFilePath("/blocks/".$sub_dir);
                            if ( $block !== FALSE ){
                            	//加载blocks/ 目录下配置文件
                                include_once dirname(__FILE__) . $block;
                            }
                        }
                    }
                }
            }
        }
		//插件绑定钩子 开始初始化插件
        $ch = ZywxappContentHandler::getInstance();
		// 通过插件请求数据  处理请求
        if (strpos($_SERVER['QUERY_STRING'], 'zywxapp/') !== FALSE){
            $rh = new ZywxappRequestHandler();
        }
    }

    public function loadClass($className)
    {
        if ( stripos($className, $this->prefix) === 0 ){
            if ( !class_exists($className, FALSE)  && !interface_exists($className, FALSE) ){
                $this->_checkSetIncludePath();
                $vClassName = $this->getClassFileName($className);
                @include($vClassName);
            }
        }
    }

    protected function getFilePath($name)
    {
        return $name;
    }

    protected function getClassFileName($name)
    {
        return $name.'.php';
    }
}

global $zywxappLoader;
$zywxappLoader = new ZywxappLoader();
