<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
interface ZywxappIInstallable
{
    public function isInstalled();
    public function install();
    public function uninstall();
    public function needUpgrade();
    public function upgrade();
}