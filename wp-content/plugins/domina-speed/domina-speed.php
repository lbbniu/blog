<?php
/*
Plugin Name: 域名加速插件
Plugin URI: http://www.yunshanding.com/
Author: yunshangdian
Author URI: http://www.yunshangdian.com/
Description: 使未备案域名的wordpress运行速度更快。
Version: 1.1
*/
if(isset($_SERVER['HTTP_APPNAME'])){
    add_filter('pre_option_siteurl','sae_siteurl');
    add_filter('pre_option_home','sae_home');
    add_filter('site_url','sae_site_url',10,2);
    add_filter('login_url','sae_login_url');
    add_filter('logout_url','sae_logout_url');
    add_filter('admin_url','sae_admin_url');
    add_action('wp_before_admin_bar_render','sae_before_admin_bar_render');
    add_action('wp_after_admin_bar_render','sae_after_admin_bar_render');
}
function sae_siteurl(){
    return 'index.php'==basename($_SERVER['SCRIPT_FILENAME']) && false===strpos($_SERVER['SCRIPT_FILENAME'], 'wp-admin')?'http://'.substr($_SERVER['HTTP_APPNAME'],0).'.sinaapp.com':'http://'.$_SERVER['HTTP_HOST'];
}
function sae_home(){
    return 'http://'.$_SERVER['HTTP_HOST'];
}
function sae_site_url($url,$path){
    if(in_array($path,array('wp-login.php?action=register','/wp-comments-post.php'))){
        return str_replace('http://'.substr($_SERVER['HTTP_APPNAME'],0).'.sinaapp.com', 'http://'.$_SERVER['HTTP_HOST'],$url);
    }else{
        return $url;
    }
}
function sae_before_admin_bar_render(){
    ob_start();
}
function sae_after_admin_bar_render(){
    echo str_replace('http://'.substr($_SERVER['HTTP_APPNAME'],0).'.sinaapp.com', 'http://'.$_SERVER['HTTP_HOST'],ob_get_clean());
}
function sae_login_url($login_url){
    return str_replace('http://'.substr($_SERVER['HTTP_APPNAME'],0).'.sinaapp.com', 'http://'.$_SERVER['HTTP_HOST'],$login_url);
}
function sae_logout_url($logout_url){
    return str_replace('http://'.substr($_SERVER['HTTP_APPNAME'],0).'.sinaapp.com', 'http://'.$_SERVER['HTTP_HOST'],$logout_url);
}
function sae_admin_url($url){
    return str_replace('http://'.substr($_SERVER['HTTP_APPNAME'],0).'.sinaapp.com', 'http://'.$_SERVER['HTTP_HOST'],$url);
}