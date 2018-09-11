<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappImageServices
{
    public static function getByRequest()
    {
        $image = new ZywxappImageHandler($_GET['url']);
        ZywxappLog::getInstance()->write('INFO', 'Requesting image: '.$_GET['url'], 'ZywxappImageServices.getByRequest');
        $type = $_GET['type'] ? $_GET['type'] : 'resize';
        $image->zywxapp_getResizedImage($_GET['width'], $_GET['height'], $type, $_GET['allow_up']);
    }
}