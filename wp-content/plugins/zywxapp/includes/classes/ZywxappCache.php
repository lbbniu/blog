<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
* @package ZywxappWordpressPlugin
* @subpackage Cache
* @author mxg<jiemack@163.com>
*
*/
abstract class ZywxappCache
{
	/**
	* defaults options
	* @var array
	*
	* $default_options['duration'](second) - Cache life time
	*/
	protected $_options = array('duration' => 3600);

	public $_prefix;
	/**
	* Stack of caches for nesting cache
	* @var array
	*/
	private $cache_stack = array();

	protected $_is_enabled = WP_ZYWXAPP_CACHE;

	public function __construct( array $options)
	{
		$this->_options = array_merge($this->_options, $options);
	}

	public static function getCacheInstance( array $options = array())
	{
		if (function_exists('apc_store')) {
			return new ZywxappCacheAPC($options);
		} else {
			return new ZywxappCacheFile($options);
		}
	}
	
	public function isCacheEnabled()
	{
		return $this->_is_enabled;
	}
	
	public function checkPath()
    {
    	if ('apc' == $this->_prefix) {
    		return TRUE;
    	}
        return (!empty($this->_fileDir)) ? is_writable($this->_fileDir) : TRUE;
    }

	/**
	* start cache if need of print content form cache
	* @param array $output
	* @param array $options
	* @return void
	*/
	public function getContent(array & $output)
	{
		ob_start();
		//add to stack information about cache block, need for inherited caching
		array_push($this->cache_stack, array('key' => $output['key'], 'options' => $this->_options));
		/**
		* First make sure we are able to cache.
		* If not - return FALSE, so let the caller know it needs to continue with the rest of the code.
		*/
		$this->_getFromCache($output);

		if ( ! is_array($output['headers']) || empty($output['headers']) || empty($output['content'])) {
			$output['headers'] = array();
			$output['content'] = '';
			return;
		}
		// eTag value getting from headers.
		// If there is not eTag, do Output empty.
		for ($h=0, $total=count($output['headers']), $found=FALSE; $h<$total && !$found; ++$h) {
			if ( isset($output['headers'][$h]) && strpos($output['headers'][$h], 'ETag:') !== FALSE ) {
				$output['e_tag_stored'] = $this->getEtagFromHeader($output['headers'][$h]);
				$found = TRUE;
			}
		}
		if ($output['e_tag_stored'] == '') {
			$output['headers'] = array();
			$output['content'] = '';
		}
	}

	public function endCache($output)
	{
		ZywxappLog::getInstance()->write('DEBUG', "The content is: {$output['content']}","ZywxappCache._getFromCache");
		$is_must_to_send = TRUE;
		// If eTag is proper, there is no need to return content.
		// We send code 304 Not Modified".
		ZywxappLog::getInstance()->write('DEBUG', "The if not matched header is: {$output['e_tag_incoming']}", "ZywxappCache.endCache");
		if ($output['e_tag_incoming'] != '' && $output['e_tag_incoming'] === $output['e_tag_stored']) {
			ZywxappLog::getInstance()->write('DEBUG', "It's a match!!!", "ZywxappCache.endCache");
			//$this->_setUseCachedResponse();
			//$is_must_to_send = FALSE;
		} else {
			// The headers do not match
			ZywxappLog::getInstance()->write('DEBUG', "The headers do not match: " . $output['e_tag_incoming'] . " and the etag was {$output['e_tag_stored']}","ZywxappCache.endCache");
		}

		if ($output['is_new_content'] === '1') {
			$output['content'] = ob_get_clean();
		}

		if ($is_must_to_send) {
			$this->_sendContent($output);
		}

		// If Content is new, we must to renew it in Cache.
		if ($output['is_new_content'] === '1' && $output['content'] != '') {
			// Because a Headers might be sent by other plugins also,
			// we must to get all sent Headers, before store them to Cache.
			// But we must not put to Cache 304 Not Modified header.
			$output['headers'] = array_filter( array_merge( headers_list(), $output['headers'] ), array($this, '_headersFilter') );
			$output['headers'] = array_unique($output['headers']);
			sort($output['headers']);

			$this->_renewCache($output);
		}
	}

	public function getEtagFromHeader($string)
	{
		preg_match('/[a-z0-9]{30,}/', $string, $eTagIncomingMatches);
		if (isset($eTagIncomingMatches[0]) && $eTagIncomingMatches[0] != '') {
			return $eTagIncomingMatches[0];
		}
		return '';
	}

	private function _sendContent($output)
	{
		// First - send Headers
		for ($h=0, $total=count($output['headers']); $h<$total; ++$h) {
			header($output['headers'][$h]);
		}
		// Check, if compressed Content expected - sent compressed
		if ( $output['encoding'] !== '' ) {
			/**
			* Although gzip encoding is best handled by the zlib.output_compression,
			* our clients sometimes send a different accept encoding header like X-cpet-Encoding
			* In that case the only way to catch it is to manually handle the compression and headers check.
			*/
			if ( !in_array('ob_gzhandler', ob_list_handlers()) ){
				$len = strlen($output['content'])+1;
				header('Content-Encoding: '.$output['encoding']);
				echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
				$output['content'] = gzcompress($output['content'], 9);
				$output['content'] = substr($output['content'], 0, $len);
			}
		}
		echo $output['content'];
	}

	private function _setUseCachedResponse()
	{
		ZywxappLog::getInstance()->write('INFO', "Nothing to output the app should use the cache","ZywxappCache.setUseCachedResponse");
		header('Content-Length: 0');
		ZywxappLog::getInstance()->write('INFO', "Sent the content-length","ZywxappCache.setUseCachedResponse");
		header("HTTP/1.1 304 Not Modified");
		ZywxappLog::getInstance()->write('INFO', "sent 304 Not Modified for the app","ZywxappCache.setUseCachedResponse");
	}

	private function _headersFilter($header_value)
	{
		return ! in_array($header_value, array('Content-Length: 0', 'HTTP/1.1 304 Not Modified',));
	}

	abstract protected function deleteOldFiles();
	abstract protected function _getFromCache( & $output);
	abstract protected function _renewCache($output);
}