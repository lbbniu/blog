<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappBaseScreen
{
	public function check_login()
	{
		$user = wp_get_current_user();
		if (! $user->ID) {
			$message =  __('用户未登入', 'zywxapp');
			$this->exitOutput($this->prepare(array(),false,410,$message));
		}
	}
	
    public function prepare($page = array(), $status = true, $code = 200, $message ='')
    {
		$key = 'items';
        $screen = array(
				'status'  => $status,
        		'code'    => $code,
        		'message' => $message,
                $key      => $page,
        );
        return $screen;
    }

    public function output($screen_content)
    {
		echo json_encode($screen_content);
    }

    public function exitOutput($screen_content)
    {
    	@header('Content-Type: application/json');
		$content = json_encode($screen_content);
		if (isset($_GET['callback'])){
            header('Content-Type: text/javascript; charset: utf-8');
            $content = $_GET["callback"] . "({$content})";  
        } 
		echo $content;exit;
    }
    
    public function appendComponentByLayout(&$page, $block, $data, $type = false)
    {
        $className = ucfirst($block['class']);
        $layout = $block['layout'];
        if (class_exists($className)){
            $obj = new $className($layout, $data);
            if ($obj->isValid()){
            	if ($type) {
            		$page = $obj->getComponent();
            	} else {
            		$page[] = $obj->getComponent();
            	}
            }
        }
    }
}