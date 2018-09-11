<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
* 内容管理类
*
* @package ZywxappWordpressPlugin
* @subpackage Core
* @author zywx phpteam
*
*/
class ZywxappContentEvents
{
    /**
     * 最后记录时间
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-7 下午04:42:01
     * @author author
     * 
     */
    public function updateCacheTimestampKey()
    {
        ZywxappConfig::getInstance()->last_recorded_save = time();
    }

    public static function getCacheTimestampKey()
    {
        return ZywxappConfig::getInstance()->last_recorded_save;
    }

}
