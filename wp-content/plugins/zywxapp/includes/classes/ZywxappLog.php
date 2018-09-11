<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
* 插件的log记录类
*
* 此记录类可以帮助我们追踪运行的信息
*
* @package ZywxappWordpressPlugin
* @subpackage Utils
* @author zywx phpteam
*
*/
final class ZywxappLog
{
    /**
    * The desired logging level
    *
    * @var integer
    */
    public $threshold = 5;

    /**
    * Is the log enabled?
    *
    * @var boolean
    */
    public $enabled = WP_ZYWXAPP_DEBUG;

    /**
    * The log levels
    *
    * @var array
    */
    private $levels = array('DISABLED' => 0, 'ERROR' => 1, 'WARNING' => 2, 'DEBUG' => 3, 'INFO' => 4, 'ALL' => 5,);

    /**
     * The file maximum size in bytes
     *
     * @var integer
     *
     */
    public $max_size = 1048576; // 1MB

    public $max_days = 10;

    public $path = '';

    private static $_instance = null;

    public static function getInstance()
    {
        if( is_null(self::$_instance) ) {
            self::$_instance = new ZywxappLog();
        }

        return self::$_instance;
    }

    private function  __clone()
    {
        // Prevent cloning
    }

    private function __construct()
    {
        $this->path = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
        if (!$this->checkPath() || $this->isIIS()){
            $this->enabled = FALSE;
        }
        $this->threshold = intval(ZywxappConfig::getInstance()->zywxapp_log_threshold);
    }
	
    /**
     * 检测log 目录是否可写
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-6 上午11:39:02
     * @author author
     * 
     */
    public function checkPath()
    {
        return is_writable($this->path);
    }

    public function isIIS()
    {
        if (isset($_SERVER['SERVER_SOFTWARE'])) { // Microsoft-IIS/x.x (Windows xxxx)
            if (stripos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

//    Checks the file size, if file to big starts new one.
    private function _toLarge($filepath)
    {
        if (!file_exists($filepath)) {
            return false;
        }
        if(@filesize($filepath) > $this->max_size) {
            return true;
        } else {
			return false;
        }
    }

    private function _getFileNamePrefix()
    {
		$prefix = 'zywxapplog-';
		if (function_exists('is_multisite') && is_multisite()) {
			global $wpdb;
			$details = get_blog_details((int) $wpdb->blogid, true);
			$msPath = str_replace(array('/', '.', ':'), '_', $details->siteurl);

			$prefix = $prefix . $msPath . '-';
		}
		return $prefix;
	}
	 
    private function _getFilePath()
    {
		$fileprefix = $this->_getFileNamePrefix();
        $filepath = $this->path . $fileprefix . date('Y-m-d') . '.log.php';

        if(@filesize($filepath) > $this->max_size){
            $file_indx = 1;
            $new_filepath = $this->path . $fileprefix . date('Y-m-d') . '.log' . $file_indx . '.php';
            while ($this->_toLarge($new_filepath)){
                $file_indx++;
                $new_filepath = $this->path . $fileprefix . date('Y-m-d') . '.log' . $file_indx . '.php';
            }
            return $new_filepath;
        }else{
            return $filepath;
        }
    }

    public function deleteOldFiles()
    {
        $oldest_date = mktime(0, 0, 0, date('m'), date('d') - $this->max_days, date('Y'));

        $dirHandle = opendir($this->path);
        while($file = readdir($dirHandle)){
            $fileinfo = pathinfo($file);
            $basename = $fileinfo['basename'];
            if(preg_match("/^zywxapplog-/", $basename)){
                //$date = strtotime(substr($basename, strlen($this->_getFileNamePrefix()), 10));
                $date = filemtime($this->path . $fileinfo['basename']);
                if($date <= $oldest_date){
                    @unlink($this->path . $fileinfo['basename']);
                }
            }
        }
    }

    /**
    * writes a log message to the log file
    *
    * The messages sent to this method will be filtered according to their level
    * if the level meets the threshold and the logging is enabled the message
    * will be written to a log file. The method also receives the component
    * related to this log message to ease the reading of the log file itself
    * If you want to keep your sanity make sure to send this "optional" parameter
    *
    * @param string $level The log message level
    * @param string $msg The log message
    * @param string $component The component related to this message
    * @return boolean success
    */
    public function write($level = 'error', $msg, $component='')
    {
        /**
         * NOTICE: Since this function is being run everywhere is, it might be run under the
         * an output handling method that wraps the entire content to do one last search and replace
         * like W3 total cache with cnd configured, they replace the hostname with the cdn host name
         * From that reason, this function must never use output buffering
         */
        @clearstatcache(); // We need to clear the cache of the file size function so that the size checks will work
        if ($this->enabled === FALSE){
            return FALSE;
        }
        
        // Don't trust the user to use the right case, switch to upper
        $level = strtoupper($level);
        // If the wanted level is above the threshold nothing to do
        if (!isset($this->levels[$level]) || ($this->levels[$level] > $this->threshold)){
            return FALSE;
        }

        $filepath = $this->_getFilePath();
        $message  = '';
        // Prevent direct access to the log, to avoid security issues
        if (!file_exists($filepath)){
            $message .= "<?php if (!defined('WP_ZYWXAPP_BASE')) exit(); ?" . ">\n\n";
            $message .= print_r($this->writeServerConfiguration(), TRUE);
            $message .= "==================================================================\n\n";
        }

        // If we can't open the file for appending there isn't much we can do
        if (!$fp = @fopen($filepath, 'ab')){
            return FALSE;
        }

        $date = date('Y-m-d H:i:s');
        $message .= "[$level][{$date}][$component]$msg\n";

        @flock($fp, LOCK_EX);
        @fwrite($fp, $message);
        @flock($fp, LOCK_UN);
        @fclose($fp);

        @chmod($filepath, 0666);

        return TRUE;
    }

    public function writeServerConfiguration()
    {
        global $wpdb;

        // mysql version
        $sqlversion = $wpdb->get_var("SELECT VERSION() AS version");

        // sql mode
        $mysqlinfo = $wpdb->get_results("SHOW VARIABLES LIKE 'sql_mode'");
        if (is_array($mysqlinfo)){
            $sql_mode = $mysqlinfo[0]->Value;
        }

        if (empty($sql_mode)) {
            $sql_mode = 'Not Set';
        }

        $config = array(
            'php_os' => PHP_OS,
            'sql version' => $sqlversion,
            'sql mode' => $sql_mode,
            'safe_mode' => ini_get('safe_mode'),
            'output buffer size' => ini_get('pcre.backtrack_limit') ? ini_get('pcre.backtrack_limit') : 'NA',
            'post_max_size'  => ini_get('post_max_size') ? ini_get('post_max_size') : 'NA',
            'max_execution_time' => ini_get('max_execution_time') ? ini_get('max_execution_time') : 'NA',
            'memory_limit' => ini_get('memory_limit') ? ini_get('memory_limit') : 'NA',
            'memory_get_usage' => function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 2).'MByte' : 'NA',
            'server config' => $_SERVER,
            'display_errors' => ini_get('display_error'),
            'error_reporting' => ini_get('error_reporting'),
        );

        return $config;
    }
}