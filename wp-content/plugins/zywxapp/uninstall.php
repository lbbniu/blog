<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

delete_option('zywxapp_db_version');
delete_option('zywxapp_settings');

wp_clear_scheduled_hook('zywxapp_daily_function_hook');
wp_clear_scheduled_hook('zywxapp_weekly_function_hook');
wp_clear_scheduled_hook('zywxapp_monthly_function_hook');

//TODO：上报服务器