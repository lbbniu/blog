<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappSystemScreen extends ZywxappBaseScreen
{
    public function run()
    {

    }

    public function runByWeibo()
    {
    	$weibo = array(
    		'qq_appkey' => ZywxappConfig::getInstance()->qq_appkey,
    		'qq_callback_url' => ZywxappConfig::getInstance()->qq_callback_url,
    		'sina_appkey' => ZywxappConfig::getInstance()->sina_appkey,
    		'sina_callback_url' => ZywxappConfig::getInstance()->sina_callback_url,
    	);
        $this->output($this->prepare($weibo));
    }
	
	public function runByLogos()
    {
    	$logo = ZywxappConfig::getInstance()->logo;
    	rsort($logo);
        $this->output($this->prepare($logo));
    }
	
	public function runByPromotion()
    {
     	if (ZywxappConfig::getInstance()->zywxapp_promotion_status && ZywxappConfig::getInstance()->app_key) {
        	ZywxappTemplateHandler::load(dirname(__FILE__).'/../../../themes/admin/promotion.php');
        }
        exit();
    }
}