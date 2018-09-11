<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
 * apc cache
 *
 * class_description
 *
 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
 * @since      File available since Release 1.0 -- 2012-4-24 下午08:31:12
 * @author zywx phpteam
 */
class ZywxappCacheAPC extends ZywxappCache
{

	public function __construct( array $options = array())
	{
		parent::__construct($options);
		$this->_prefix = 'apc';
		ZywxappLog::getInstance()->write('INFO', "APC caching system is active", "ZywxappCacheAPC.__construct");
	}

	/**
	 * start cache if need of print content form cache
	 *
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-4-24 下午08:30:24
	 * @author zywx phpteam
	 * @param array $output
	 * @return void
	 */
	protected function _getFromCache( & $output)
	{
		if ( ! $this->_apcCheckCache($output['key'])) return;

		$output['headers'] = @unserialize( apc_fetch($output['key'].'_headers') );
		$output['content'] = @unserialize( apc_fetch($output['key']) );
	}

    /**
     * 检测缓存是否存在
     * @param string $key
     * @return bool
     */
    private function _apcCheckCache($key)
    {
        if (function_exists('apc_exists')) {
            if (!apc_exists($key)){
                return false;
            }else{
                return true;
            }
        } else {
            if (!apc_fetch($key)) {
                return false;
            } else {
                return true;
            }
        }
    }
	
	public function deleteOldFiles(){}

	/**
	 * start cache if need of print content form cache
	 *
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-4-24 下午08:30:24
	 * @author zywx phpteam
	 * @param array $output
	 * @return void
	 */
	protected function _renewCache($output)
	{
		ZywxappLog::getInstance()->write('INFO', 'Saving cache key: '.$output['key'], "ZywxappCacheAPC._renewCache");

		apc_delete($output['key'].'_headers');
		apc_delete($output['key']);

		if (! apc_store( $output['key'].'_headers', serialize($output['headers']), $this->_options['duration'] ) ||
			! apc_store( $output['key'], 		    serialize($output['content']), $this->_options['duration'] )) {
			ZywxappLog::getInstance()->write('ERROR', "Cant save cache: {$output['key']}","ZywxappCacheAPC._renewCache");
			return;
		}
		ZywxappLog::getInstance()->write('DEBUG', "Saving cache key: {$output['key']}","ZywxappCacheAPC._renewCache");
	}

}