<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
 * 为插件图片处理类
 *
 * class_description
 *
 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
 * @since      File available since Release 1.0 -- 2012-3-6 下午12:03:00
 * @author author
 */
class ZywxappImageHandler
{
	//选择插件内部图片类或者调用平台api图片处理
    private $imp = 'PhpThumb';
    
    private $handler = null;
    
    /**
    * The directory to save the cache files in
    * 
    * @var mixed
    */
    private $cache = 'cache';
    
    /**
    * holds the image src as was given
    * 
    * @var string
    */
    private $imageFile = '';

    private $path = '';
    
    /**
     * 构造方法
     *
     * 设定图片缓存目录 选择图片处理类
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-6 下午12:06:30
     * @author author
     * 
     */
    public function __construct ($imageFile='')
    {
        $basePath = dirname(__FILE__) . '/../..';
        $this->cache = $basePath . '/' . $this->cache;

        if (!empty($imageFile)) {
            $this->imageFile = $imageFile;
            
            //php 图片库扩展功能 gd/imagick
            $this->_checkImp();
            //加载实例化 图片处理类功能
            $imageClass = "Zywxapp{$this->imp}Resizer";
            require_once('imageResizers/' . $imageClass . '.php');
            $this->handler = new $imageClass();
        }
    }

    /**
     * 检测图片缓存目录是否可写
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-6 下午01:42:29
     * @author author
     * 
     */
    public function checkPath()
    {
        return is_writable($this->cache);
    }

    public function getResizedImageUrl($url, $width, $height, $type = 'adaptiveResize', $allow_up = FALSE)
    {
        $url = urlencode($url);
        return get_bloginfo('url') . "/?zywxapp/getimage/&width={$width}&height={$height}&type={$type}&allow_up={$allow_up}&url={$url}";
    }

    public function zywxapp_getResizedImage($width, $height, $type = 'adaptiveResize', $allow_up = FALSE)
    {
        if ($this->handler == null){
        	ZywxappLog::getInstance()->write('error', 'No images handler', 'ZywxappImageHandler.zywxapp_getResizedImage');
            return false;
        }
        // Get the ext
        $tmp = explode('?', $this->imageFile);
        $ext = substr($tmp[0], strrpos($tmp[0], '.'));
		/*DEMON 添加php后缀图片文件处理*/
        if (strpos($ext, '/') !== FALSE || strpos($ext, '.php') !== FALSE){
            // There was a slash so this image doesn't have an extension, force file type change to png
            $ext = '.png';
        }
        
        $extraForKey = '';
        if (function_exists('is_multisite') && is_multisite()) {
        	ZywxappLog::getInstance()->write('info', 'The blog is a multisite installation, adding the blog id to the key', 'ZywxappImageHandler.zywxapp_getResizedImage');
			global $wpdb;
			$extraForKey = $wpdb->blogid;
		} 
		$cacheFileImageKey = md5($this->imageFile . $width . $height . $type . $extraForKey);
        
        $cacheFile = realpath($this->cache) . '/' . $cacheFileImageKey . $ext;
		
        ZywxappLog::getInstance()->write('info', 'Checking for cache key: '.$cacheFileImageKey.' in '.$cacheFile, 'ZywxappImageHandler.zywxapp_getResizedImage');
        
		if ($this->_cacheExists($cacheFile)){
            $url = str_replace(ZYWX_ABSPATH, get_bloginfo('wpurl') . '/', $cacheFile);
            ZywxappLog::getInstance()->write('info', "Before loading image from cache: " . $cacheFile, "image_resizing.getResizedImage");
            $this->handler->load($cacheFile, FALSE);
            ZywxappLog::getInstance()->write('info', "After loading image from cache: " . $cacheFile, "image_resizing.getResizedImage");
        } else {
            $this->imageFile = str_replace(' ', '%20', $this->imageFile);
            ZywxappLog::getInstance()->write('info', "Before resizing image: " . $this->imageFile, "image_resizing.getResizedImage");
            $url = $this->imageFile;
            if (strpos($this->imageFile, get_bloginfo('wpurl')) === 0){
                $url = str_replace(get_bloginfo('wpurl'), ZYWX_ABSPATH, $url);
                // Make sure we can read it like this
                if ( !file_exists($url) ){
                	ZywxappLog::getInstance()->write('WARNING', 'Local file: '.$url.' but does not exists? will try access by url if the blogs allows', 'ZywxappImageHandler.zywxapp_getResizedImage');
                    if ( ini_get('allow_url_fopen') == '1' ){
                        $url = str_replace(ZYWX_ABSPATH, get_bloginfo('wpurl'), $url);
                    } else {
                        ZywxappLog::getInstance()->write('WARNING', 'allow_url_fopen is off, the '.$url.' will most likely fail to load', 'ZywxappImageHandler.zywxapp_getResizedImage');
                    }
                }
            }
            //生成缓存缩略图 并返回缓存路径 
            ZywxappLog::getInstance()->write('INFO', 'Calling handler resize on::'.$url, 'ZywxappImageHandler.zywxapp_getResizedImage');
            $url = $this->handler->resize($url, $cacheFile, $width, $height, $type, $allow_up, $this->checkPath());
            ZywxappLog::getInstance()->write('INFO', "After resizing image: " . $this->imageFile.' url:: '.$url, "image_resizing.getResizedImage");
        }

        /**$thumb = PhpThumbFactory::create($url);  
        $thumb->show();*/
        if ( $url === FALSE || strlen($url) > 0 ){
        	//显示图片
            ZywxappLog::getInstance()->write('INFO', "Trying to show the image: " . $this->imageFile.' url:: '.$url, "image_resizing.getResizedImage");
			$this->handler->show();
            ZywxappLog::getInstance()->write('INFO', "The image was sent to the browser: " . $this->imageFile.' url:: '.$url, "image_resizing.getResizedImage");
        } else {
        	ZywxappLog::getInstance()->write('INFO', "There was some kind of problem processing the image: " . $this->imageFile.' url:: '.$url, "image_resizing.getResizedImage");
			// If the image is not local, just redirect to it
			if ( strpos($this->imageFile, 'https://') !== FALSE || strpos($this->imageFile, 'http://') !== FALSE ){
                ZywxappLog::getInstance()->write('INFO', "The image is a full url so will just try to redirect to it: " . $this->imageFile, "image_resizing.getResizedImage");
				header('Location: '.$this->imageFile);
				// On this special case we need to halt the functions from moving on
				exit;
			}
        }
        // If we show the image it means the output was sent and we should stop the request
        return true;
    }
    
    public function load()
    {
        /**
        * If the image is local, use the local path.
        * If we will access via the url we might end up stuck with allow_url_open off
        */
        $imagePath = $this->imageFile;
        $calcResize = TRUE; // Try to calc the size of the image, unless remote and allow_url_fopen is off
        if ( strpos($imagePath, get_bloginfo('wpurl')) === 0 ){
        	ZywxappLog::getInstance()->write('INFO', 'Loading local image::'.$imagePath, 'ZywxappImageHandler.load');
            $imagePath = str_replace(get_bloginfo('wpurl'), ZYWX_ABSPATH, $imagePath);
            // Make sure we can read this image file, if the filename is with special encoding, the os might not find it...
            if ( !file_exists($imagePath) ){
            	ZywxappLog::getInstance()->write('WARNING', 'Local image::'.$imagePath.' does not exists? Avoid calculations', 'ZywxappImageHandler.load');
                if ( ini_get('allow_url_fopen') != '1' ){
                    $calcResize = FALSE;
                }
                $imagePath = str_replace(ZYWX_ABSPATH, get_bloginfo('wpurl'), $imagePath);
            }
        } else {
            // The image is not local, if allow_url_fopen is off throw an alert
            if ( ini_get('allow_url_fopen') != '1' ){
                $calcResize = FALSE; // Will affect the ability to make the image a thumbnail
                ZywxappLog::getInstance()->write('ERROR', "allow_url_fopen is turned off, can't check the image size for: " . $imagePath, "ZywxappImageHandler.load");
            }                                
        }
        ZywxappLog::getInstance()->write('INFO', 'Going to request loading the image::'.$imagePath.' from the image handler', 'ZywxappImageHandler.load');
        $this->handler->load($imagePath, $calcResize);
    }
    
    public function getNewWidth()
    {
        $width = $this->handler->getNewWidth();
        if ($width == 0){
            $width = "auto";
        }
        return $width;
    }
    
    public function getNewHeight()
    {
        $height = $this->handler->getNewHeight();
        if ($height == 0){
            $height = "auto";
        }
        return $height;
    }
    
    private function _cacheExists($cacheFile)
    {
        return file_exists($cacheFile);
    }
    
	/**
	 * 检测网站服务器是否安装PHP图片扩展功能
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-6 下午12:10:44
	 * @author author
	 * 
	 */
    private function _checkImp()
    {
        if(extension_loaded('gd') || extension_loaded('imagick')){
            $this->imp = 'PhpThumb';
        } else {
        	ZywxappLog::getInstance()->write('WARNING', 'Server not installed imagick or gd expansion', 'ZywxappImageHandler._checkImp');
        	header('Location: '.$this->imageFile);
			exit;
        }
    }
}
