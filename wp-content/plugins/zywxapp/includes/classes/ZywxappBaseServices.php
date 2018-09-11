<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappBaseServices
{
    public function outputSection($return,$message = '')
    {
    	$result = array();
    	if (is_wp_error($return)) {
        	$status = FALSE;
        	$code = 400;
        	$message = strip_tags(implode('', $return->get_error_messages()));
        } else {
        	$status = true;
        	$code = 200;
        	$message = $message ? $message : __('操作成功', 'zywxapp');
        	$result = $return;
        }
		$this->output($this->prepare($status,$code,$message,$result));
    }
	
	public function prepare($status=false, $code=400, $message='', $page=array(),$key = 'items')
	{
		$services = array(
			'status'  => $status,
      		'code'    => $code,
			'message' => $message,
        );
        if (is_array($page) && $page) {
        	$services[$key] = $page;
        }
        return $services;
	}
	
	public function output($services_content)
	{
		@header('Content-Type: application/json');
		$content = json_encode($services_content);
		if (isset($_GET['callback'])){
            header('Content-Type: text/javascript; charset: utf-8');
            $content = $_GET["callback"] . "({$content})";  
        } 
		echo $content;exit;
    }
}