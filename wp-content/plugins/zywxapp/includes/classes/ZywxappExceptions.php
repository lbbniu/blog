<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
* 异常信息输出类
* @package Zywxapp Wordpress Plugin
* @author  mxg<jiemack@163.com>
*/
class ZywxappOperationNotAllowed extends Exception
{
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }
}

class ZywxappUnknownType extends Exception
{
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }
}