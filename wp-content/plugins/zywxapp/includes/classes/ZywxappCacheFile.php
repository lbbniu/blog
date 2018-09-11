<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
 * file cache
 *
 * class_description
 *
 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
 * @since      File available since Release 1.0 -- 2012-4-24 下午08:31:12
 * @author zywx phpteam
 */
class ZywxappCacheFile extends ZywxappCache
{
	/**
	* path to directory with cache files
	* @var string
	*/
	protected $_fileDir = 'WP_ROOT_PATH/wp-content/uploads/zywxapp_cache/';

	public function __construct( array $options = array())
	{
		$this->_prefix = 'file';
		parent::__construct($options);
		ZywxappLog::getInstance()->write('INFO', "Did not find APC installed, using File caching", "ZywxappCacheFile.__construct");

		$uploads_dir = wp_upload_dir();
		$fileDir = str_replace('WP_ROOT_PATH/wp-content/uploads', $uploads_dir['basedir'], $this->_fileDir);

		if ( ! file_exists($fileDir)) {
			if ( ! @mkdir($fileDir, 0777, true)) {
				$this->_is_enabled = FALSE;
				ZywxappLog::getInstance()->write('ERROR', 'Could not create the wiziapp file caching directory: '.$fileDir, "ZywxappCacheFile.__construct");
				return;
			}
		} else {
			if ( ! @is_readable($fileDir) || ! @is_writable($fileDir)) {
				if ( ! @chmod($fileDir, 0777)) {
					$this->_is_enabled = FALSE;
					ZywxappLog::getInstance()->write('ERROR', 'The upload directory exists, but its not readable or not writable: '.$fileDir, "ZywxappCacheFile.__construct");
					return;
				}
			}
		}

		$this->_fileDir = $fileDir;
	}

	public function deleteOldFiles()
	{
		if ($handle = opendir($this->_fileDir))	{
			// loop over the directory.
			while (($file = readdir($handle)) !== false) {
				if ($file != "." && $file != "..")
				{
					if ((time() - filemtime($this->_fileDir.$file)) > $this->_options['duration']) {
						@unlink($this->_fileDir.$file);
					}
				}
			}
			closedir($handle);
		}
	}

	/**
	* start cache if need of print content form cache
	* @param array $output
	* @return void
	*/
	protected function _getFromCache( & $output)
	{
		if ( ! $this->_is_enabled )	return;

		$file = $this->_fileDir.$output['key'];

		if ( ! file_exists($file) || ((time() - filemtime($file)) >= $this->_options['duration'])) return;

		$output['headers'] = @unserialize( @file_get_contents($file.'_headers') );
		$output['content'] = @unserialize( @file_get_contents($file) );
	}

	/**
	* @param array $output
	* @return void
	*/
	protected function _renewCache($output)
	{
		if ( ! $this->_is_enabled )	return;

		ZywxappLog::getInstance()->write('DEBUG', 'Saving cache key: '.$output['key'],"ZywxappCacheFile._renewCache");

		if (! @file_put_contents( $this->_fileDir.$output['key'].'_headers', serialize($output['headers']) ) ||
			! @file_put_contents( $this->_fileDir.$output['key'], 			 serialize($output['content']) ) ) {
			ZywxappLog::getInstance()->write('ERROR', 'Can\'t write file.', "ZywxappCacheFile._renewCache");
		}
	}

}