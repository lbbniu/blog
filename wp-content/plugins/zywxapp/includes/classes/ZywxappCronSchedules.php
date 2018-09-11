<?php
class ZywxappCronSchedules
{
    public function addSchedules()
    {
        return array(
            'weekly' => array('interval' => 604800, 'display' => __('每周一次', 'zywxapp')),
            'monthly' => array('interval' => 2592000, 'display' => __('每30天一次', 'zywxapp')),
        );
    }
}
