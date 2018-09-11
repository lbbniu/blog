<?php
// * 公共方法文件 *
function random($num) {
	if(!isset($num)) $num = 4;

	$rand = '';
	for($i = 0;$i < $num; $i++){
		$rand .= rand(0,9);
	}
	return $rand;
}

/**
 * 获取当前页URL
 *
 * function_description
 *
 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
 * @since      File available since Release 1.0 -- 2012-3-31 下午02:59:06
 * @author mxg<jiemack@163.com>
 * 
 */
function zywxapp_get_current_page_url(){
    $current_page_url = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $current_page_url .= "s";
    }
    $current_page_url .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
    	$current_page_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $current_page_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $current_page_url;
}

/**
 * JS跳转方法
 *
 * function_description
 *
 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
 * @since      File available since Release 1.0 -- 2012-3-31 下午06:21:19
 * @author mxg<jiemack@163.com>
 * @param  string $url
 */
function zywxapp_redirect($url){
	echo '<script language=javascript>';
  	echo 'location.href="'.$url.'";';
  	echo '</script>';
  	exit();
}

/**
 * 获取远程文件大小
 *
 * function_description
 *
 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
 * @since      File available since Release 1.0 -- 2012-5-23 下午03:07:40
 * @author mxg<jiemack@163.com>
 * @param  string $url
 * @return 
 */
function zywxapp_get_file_size($url){ 
	$url = parse_url($url); 
	$fp = @fsockopen($url['host'],empty($url['port']) ? 80 : $url['port'], $error);
	if ($fp) { 
		fputs($fp,"GET ".(empty($url['path'])?'/':$url['path'])." HTTP/1.1\r\n"); 
		fputs($fp,"Host:$url[host]\r\n\r\n"); 
		while (!feof($fp)) { 
			$tmp = fgets($fp); 
			if (trim($tmp) == ''){ 
				break; 
			} else if(preg_match('/Content-Length:(.*)/si',$tmp,$arr)){ 
				return trim($arr[1]); 
			} 
		}
		return null; 
	} else { 
		return null; 
	}
} 

/**
 * 上传图片
 *
 * function_description
 *
 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
 * @since      File available since Release 1.0 -- 2012-2-29 上午11:08:25
 * @author mxg<jiemack@163.com>
 * @param  array  $file POST $_FILES
 * @param  string $dir 图片放置的目录
 * @param  string $filepath  要删除的图片 相对路径
 * @return array 
 */
function zywxapp_upload_image($file, $dir = null, $filepath = null) {
	$filename_parts = explode('.', $file['name']);
	$type = $filename_parts[1];
	$image_format = array('jpeg','gif','png','jpg');
	if (!in_array(strtolower($type),$image_format)) {
		return array('error' => '请选择正确的图片格式');
	}
	$upload = zywxapp_handle_upload($file, $dir);
	if (!isset($file['error']) && !$file['error']) {
		if ($filepath) {
			zywxapp_delete_attachment($filepath);
		}
    }
	return $upload;
}
 
/**
 * 上传路径
 *
 * function_description
 *
 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
 * @since      File available since Release 1.0 -- 2012-2-28 上午10:02:39
 * @author mxg<jiemack@163.com>
 * @param  string $dir_name
 * @return array See above for description.
 */
function zywxapp_upload_dir( $dir_name = null ) {
	if (defined('ZYWXAPP_UPLOADS_PATH')) {
 		$upload_path = ZYWXAPP_UPLOADS_PATH;
 	} else {
 		$upload_path = dirname(dirname(dirname(__FILE__))).'/uploads';
 	}
 	$dir = $upload_path;
 	if (0 !== strpos($upload_path, ABSPATH)) {
 		$dir = path_join( ABSPATH, $dir );
 	}
 	if (defined('ZYWXAPP_UPLOADS_URL')) {
 		$url = ZYWXAPP_UPLOADS_URL;
 	} else {
 		$url = get_option( 'siteurl' ).'/wp-content/plugins/'.ZYWXAPP_DIR_NAME.'/uploads' ;
 	}
	$bdir = $dir;
	$burl = $url;
	$subdir = '';
	if ($dir_name) {
		$subdir = '/'.$dir_name;
	} elseif (false === $dir_name) {
		$time = current_time( 'mysql' );
		$y = substr( $time, 0, 4 );
		$m = substr( $time, 5, 2 );
		$subdir = "/$y/$m";
	}
	$dir .= $subdir;
	$url .= $subdir;
	$uploads = apply_filters( 'upload_dir', array( 'path' => $dir, 'url' => $url, 'subdir' => $subdir, 'basedir' => $bdir, 'baseurl' => $burl, 'error' => false ) );
	if (! wp_mkdir_p( $uploads['path'] ) ) {
		$message = sprintf( __( 'Unable to create directory %s. Is its parent directory writable by the server?' ), $uploads['path'] );
		return array( 'error' => $message );
	}
	return $uploads;
}
 
 /**
  * 上传附件
  *
  * function_description
  *
  * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
  * @since      File available since Release 1.0 -- 2012-2-28 下午03:46:17
  * @author mxg<jiemack@163.com>
  * @param array $file Reference to a single element of $_FILES. Call the function once for each uploaded file.
  * @param array $dir  目录名称 例："avatar" | "avatar/user"
  * @return array 
  */
function zywxapp_handle_upload( &$file, $dir) {
	require_once(ZYWX_ABSPATH.'/wp-admin/includes/admin.php');
	
	if ( ! function_exists( 'wp_handle_upload_error' ) ) {
		function wp_handle_upload_error( &$file, $message ) {
			return array( 'error'=>$message );
		}
	}
	$file = apply_filters( 'wp_handle_upload_prefilter', $file );
	$upload_error_handler = 'wp_handle_upload_error';
	if ( isset( $file['error'] ) && !is_numeric( $file['error'] ) && $file['error'] )
		return $upload_error_handler( $file, $file['error'] );
	
	$unique_filename_callback = null;
	$upload_error_strings = array( false,
		__( "The uploaded file exceeds the upload_max_filesize directive in php.ini." ),
		__( "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form." ),
		__( "The uploaded file was only partially uploaded." ),
		__( "No file was uploaded." ),
		'',
		__( "Missing a temporary folder." ),
		__( "Failed to write file to disk." ),
		__( "File upload stopped by extension." ));
	
	$test_size = true;
	$test_upload = true;
	
	$test_type = true;
	$mimes = false;
	
	if ( $file['error'] > 0 )
		return call_user_func($upload_error_handler, $file, $upload_error_strings[$file['error']] );
	
	if ( $test_size && !($file['size'] > 0 ) ) {
		if ( is_multisite() ){
			$error_msg = __( 'File is empty. Please upload something more substantial.' );
		}else{
			$error_msg = __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.' );
		}
		return call_user_func($upload_error_handler, $file, $error_msg);
	}
	
	if ( $test_upload && ! @ is_uploaded_file( $file['tmp_name'] ) )
		return call_user_func($upload_error_handler, $file, __( 'Specified file failed upload test.' ));
	
	if ( $test_type ) {
		$wp_filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $mimes );
		extract( $wp_filetype );
		if ( $proper_filename )
			$file['name'] = $proper_filename;
	
		if ( ( !$type || !$ext ) && !current_user_can( 'unfiltered_upload' ) )
			return call_user_func($upload_error_handler, $file, __( 'Sorry, this file type is not permitted for security reasons.' ));
	
		if ( !$ext )
			$ext = ltrim(strrchr($file['name'], '.'), '.');
	
		if ( !$type )
			$type = $file['type'];
	} else {
		$type = '';
	}
	
	if ( ! ( ( $uploads = zywxapp_upload_dir($dir) ) && false === $uploads['error'] ) ) {
		return call_user_func($upload_error_handler, $file, $uploads['error'] );
	}
	//给附件重新命名
	$info = pathinfo($file['name']);
	$ext = !empty($info['extension']) ? '.' . $info['extension'] : '';
	$filename = md5(microtime()).$ext;
	$tmp_file = wp_tempnam($filename);
	if ( false === @ move_uploaded_file( $file['tmp_name'], $tmp_file ) ) {
		return $upload_error_handler( $file, sprintf( __('The uploaded file could not be moved to %s.' ), $uploads['path'] ) );
	}
	
	$new_file = $uploads['path'] . "/$filename";
	copy( $tmp_file, $new_file );
	unlink($tmp_file);
	$stat = stat( dirname( $new_file ));
	$perms = $stat['mode'] & 0000666;
	@ chmod( $new_file, $perms );
	$url = $uploads['url'] . "/$filename";
	if ( is_multisite() ) {
		delete_transient( 'dirsize_cache' );
	}
	$size = @filesize($new_file);
	$path = ltrim($uploads['subdir']. "/$filename",'/');
	return apply_filters( 'wp_handle_upload', 
		array( 'file' => $new_file, 'url' => $url, 'type' => $type, 'size' => $size, 'name'=>$filename, 'path' => $path), 
		'upload'
	);
}

/**
 * 删除指定路径的附件
 *
 * function_description
 *
 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
 * @since      File available since Release 1.0 -- 2012-2-29 下午03:21:46
 * @author mxg<jiemack@163.com>
 * @param  string $filepath
 * @return 
 */
function zywxapp_delete_attachment($filepath) {
	if (defined('ZYWXAPP_UPLOADS_PATH')) {
 		$upload_path = ZYWXAPP_UPLOADS_PATH;
 	} else {
 		$upload_path = dirname(dirname(dirname(__FILE__))).'/uploads';
 	}
 	if (strpos($filepath, ZYWXAPP_UPLOADS_URL) !== false) {
 		$filepath = str_replace(ZYWXAPP_UPLOADS_URL,'',$filepath);
 	} 
	$filename = $upload_path.'/'.$filepath;
	if (file_exists($filename) && is_file($filename)) {
		@unlink($filename);
	}
}