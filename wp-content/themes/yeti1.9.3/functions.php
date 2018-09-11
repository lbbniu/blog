<?php
function mypo_parse_query_useronly( $wp_query ) {
    if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/edit.php' ) !== false ) {
        if ( !current_user_can( 'level_10' ) ) {
            global $current_user;
            $wp_query->set( 'author', $current_user->id );
        }
    }
}
add_filter('parse_query', 'mypo_parse_query_useronly' );

function wpdx_get_comment_list_by_user($clauses) { 
        if (is_admin()) { 
                global $user_ID, $wpdb; 
                $clauses['join'] = ", wp_posts"; 
                $clauses['where'] .= " AND wp_posts.post_author = ".$user_ID." AND wp_comments.comment_post_ID = wp_posts.ID"; 
        }; 
        return $clauses; 
}; 
if(!current_user_can('edit_others_posts')) { 
add_filter('comments_clauses', 'wpdx_get_comment_list_by_user'); 
}

//head部输出处理方案
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
remove_action( 'wp_head', 'wp_generator' );
//顶部28px的空白处理方案
add_filter( 'show_admin_bar' , '__return_false');//margin-top: 28px
//禁用自动P标签
//remove_filter ('the_content',  'wpautop');
remove_filter ('comment_text', 'wpautop');

//空载WP自带的jQuery(某些插件会加载)
//另外一种方案 使用action钩子[wp_enqueue_scripts] 先注销默认的jquery 然后再注册一个新的jquery 路径为空
add_action('wp_default_scripts', 'noload_jQuery');
function noload_jQuery($scripts) {
	if ( !is_admin() ) $scripts->registered['jquery']->src = "";
}

$theme_ver = '1.9.2';

include_once "config.php";
$theme_tpl = $theme_tpl ? trim($theme_tpl) : 'default';	//主题设置的模板
include_once "weixin.php";
include_once "core.php";
include_once "common.php";









//显示[链接]菜单
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

//后台初始化
add_action('admin_init', 'blueria_admin');
function blueria_admin() {
	wp_register_script( 'blueria_ajax', get_bloginfo('template_url')."/js/admin_stage.js", array('jquery'), '1.0', true );
	wp_enqueue_script( 'blueria_ajax' );
	wp_register_style( 'blueria_style', get_bloginfo('template_url').'/js/admin_stage.css', false, '1.0' );
	wp_enqueue_style( 'blueria_style' ); 

	//remove_submenu_page( 'themes.php', 'nav-menus.php' );	//移除[外观]下的[菜单]
	register_nav_menus(array('top_menu' => '页面顶部菜单', 'home_menu' => '首页菜单' ,'shortcut_menu' => '快捷菜单'));
	add_theme_support('post-formats', array( 'image', 'video', 'aside' ) );	//文章形式
}

// 如果文章是用户投稿,显示用户昵称
add_filter ('the_author', 'tougao_replaceAuthor');
function tougao_replaceAuthor($author) {
	global $post;
	$submissionAuthor = get_post_meta($post->ID, 'username', true);
	if ( !empty($submissionAuthor) ) {
		return $submissionAuthor."(投稿)";
	} else {
		return $author;
	}
}

//自定义登陆logo的url
add_filter( 'login_headerurl', 'custom_loginlogo_url' );
function custom_loginlogo_url($url) {
    return 'http://www.woaixiao.cn';
}

add_filter( 'login_headertitle', 'custom_loginlogo_desc' );
function custom_loginlogo_desc($url) 
{return ''; } 

//随机生成上传图片的名称
function csyor_build_upload_filename($file){
    $time=date("mdHis");
    $file['name'] = $time."".mt_rand(10,99).".".pathinfo($file['name'] , PATHINFO_EXTENSION);
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'csyor_build_upload_filename');


//搜索高亮
function search_word_replace($buffer){
    if(is_search()){
        $arr = explode(" ", get_search_query());
        $arr = array_unique($arr);
        foreach($arr as $v)
            if($v)
                $buffer = preg_replace("/(".$v.")/i", "<strong>$1</strong>", $buffer);
    }
    return $buffer;
}
add_filter("the_title", "search_word_replace", 200);
add_filter("the_excerpt", "search_word_replace", 200);
add_filter("the_content", "search_word_replace", 200);


//主题设置
include_once "settings.php";
?>