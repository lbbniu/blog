<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
* 数据处理基类
* 
* @package ZywxappWordpressPlugin
* @subpackage UIComponents
* @author zywx phpteam
*/
class ZywxappLayoutComponent
{
    public $layout;
    
    public $data;
    
    public $attributes = array();  
    
    public $baseAttrMap = array('id');
    
    public $attrMap = array();
    
    public $layoutClasses = array();
    
    public $valid = FALSE;
    
    public function init($layout='L1', $data, $process=TRUE)
    {
        $this->layout = $layout;
        $this->data = $data;
        
        if ( $process ){
            $this->process();   
        }
        $this->valid = TRUE;
    }
    
    public function isValid()
    {
        return $this->valid;
    }
    
    public function getComponent()
    {
		return $this->attributes;
    }     
    
    public function getDefaultClass()
    {
        return $this->layoutClasses['L1'];
    }   
    
    public function process()
    {
        if ( is_array($this->attrMap[$this->layout]) ){
            $layoutAttrMap = $this->attrMap[$this->layout];
        } else {
            $layoutAttrMap = $this->attrMap[$this->attrMap[$this->layout]];
        }
        
        $attrMap = array_merge($this->baseAttrMap, $layoutAttrMap);
        
        for ( $a=0, $total=count($attrMap) ; $a < $total ; ++$a ){
            $methodName = "get_{$attrMap[$a]}_attr";
            if ( method_exists($this, $methodName) ){
                $value = $this->$methodName();
                $this->attributes[$attrMap[$a]] = $value;
                ZywxappLog::getInstance()->write('info', "Processing component, method: {$methodName} the value is: {$value}", "zywxappLayoutComponent.process");
            }
        }
    }

    public static function simplifyText($text)
    {
    	$text = preg_replace('/\[.*?\]|\r\n/si', '', $text);
        $text = preg_replace('/<br\\s*?\/??>/i', "\n", $text);
        $text = strip_tags($text);
        $text = stripslashes($text);
        return $text;
    }
}
