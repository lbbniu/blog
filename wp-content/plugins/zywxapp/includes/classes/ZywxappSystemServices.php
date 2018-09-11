<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappSystemServices extends ZywxappBaseServices
{
	/**
	 * 客户端升级接口
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-5-23 上午11:53:04
	 * @author mxg<jiemack@163.com>
	 * 
	 */
	public function clientUpgrade()
	{
		$version = $_REQUEST['ver'];
		$platform = $_REQUEST['platform'];
		if(empty($version) || !isset($platform)) exit;
		$newver = ZywxappConfig::getInstance()->upgrade_version;
		if($newver > $version) {
			$iphone_path = ZywxappConfig::getInstance()->iphone_path;
			$android_path = ZywxappConfig::getInstance()->android_path;
			if($platform == '0') { //iphone
				$fileurl = $iphone_path;
			} elseif($platform == '1') { //android
				$fileurl = $android_path;
			}
			if(preg_match('/^http:\/\//', $fileurl)) {
				$filesize = zywxapp_get_file_size($fileurl);
			} else {
				$filesize = filesize(ZYWXAPP_UPLOADS_PATH.'/'.$fileurl);
				$fileurl = ZYWXAPP_UPLOADS_URL.'/'.$fileurl;
			}
			if(empty($filesize)) exit;
			echo '<?xml version="1.0" encoding="utf-8" ?><results><updateFileName>wordpress</updateFileName><updateFileUrl>'.$fileurl.'</updateFileUrl><fileSize>'.$filesize.'</fileSize><version>'.$newver.'</version></results>';
		}
		exit;
	}
	 
	/**
	 * 设定宣传图片
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-6-6 下午06:14:35
	 * @author author
	 * 
	 */
	public function updateLogo()
	{
		$status = FALSE;
		$code = 400;
		$message = '';
		
		$user = wp_get_current_user();
		if (! $user->ID) {
			$status = FALSE;
        	$code = 410;
        	$message = __('用户未登入', 'zywxapp');
		} else if (! current_user_can('administrator')) {
			$status = FALSE;
        	$code = 420;
        	$message = __('你没有权限对此操作', 'zywxapp');
		} else {
			$logos = (array) ZywxappConfig::getInstance()->logo;
			$type = $_REQUEST['type'];
			if ('add' == $type) {
				if (count($logos) >= 9) {
					$message = __('上传的图片已经超出最大限制','zywxapp');
				} else if ($_FILES['logo']['name'] && $_FILES['logo']['error'] != 4) {
			       	$file = zywxapp_upload_image($_FILES['logo'],'logo');
			       	if (isset($file['error']) && $file['error']) {
			       		$message = $file['error'];
			       	} else {
			       		$newLogo = $file['url'];
			    		$logos[] = $newLogo;
			    		$status = true;
			       	}
			       	unset($_FILES);
		       	} else {
		       		$message = __('请选择图片','zywxapp');
		       	}
			} else {
				if (!isset($_REQUEST['logo_url']) && empty($_REQUEST['logo_url'])) {
					$message = __('请选择图片','zywxapp');
				} else {
					$key = array_search(trim($_REQUEST['logo_url']),$logos);
					$status = true;
					if ($key !== false) {
						unset($logos[$key]);
					}
				}
			}
			
			if ($status) {
				$status = ZywxappConfig::getInstance()->saveUpdate('logo', $logos);
		       	if ( $status ){
		       		$code = 200;
		       		$message = __('操作成功', 'zywxapp');
			        zywxapp_delete_attachment($_REQUEST['logo_url']);
			    } else {
			    	$message =  __('操作失败', 'zywxapp');
			    }
			}
		}
        $header = array(
            'status' => $status,
            'code' => $code,
            'message' => $message,
        );
		if ($newLogo) {
        	$header['items'] = array($newLogo);
        }
		$content = json_encode($header);
		if (isset($_GET['callback'])){
            header('Content-Type: text/javascript; charset: utf-8');
            $content = $_GET["callback"] . "({$content})";  
        } 
        echo $content; exit;
	}
	
	/**
	 * 平台登录成功回调地址
	 *
	 * function_description
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-6-8 下午01:30:41
	 * @author mxg<jiemack@163.com>
	 * 
	 */
	public function success()
	{
		$request_url = 'http://'. ZywxappConfig::getInstance()->api_server
    			.'/index.php?m=curl&a=loginReport&authcode='.ZywxappConfig::getInstance()->app_token
    			.'&domain='.get_bloginfo('url').'&callback=?';
    	ZywxappLog::getInstance()->write('DEBUG', 'loginReport URL :'.print_r($request_url, TRUE), 'ZywxappSystemServices.success');
		?>
<html lang="zh-CN" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
<title></title>
<script type="text/javascript" src="<?php echo includes_url('js/jquery/jquery.js');?>"></script>
<script type="text/javascript">
	var url = "<?php echo $request_url;?>";
	jQuery.getJSON( url ,function(data){
		if (data.status == 1){
			window.parent.loginSave(data.zywxemail);
		} else {
			window.parent.showMessage(data.msg);
		}
	});
</script>
</head>
<body>
</body>
</html>
		
		<?php 
		exit;
	}
	 
}
