<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappTemplateHandler
{
    public static function load($template_file)
    {
        global $posts, $post, $wp_did_header, $wp_did_template_redirect, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

        if ( is_array($wp_query->query_vars) )
            extract($wp_query->query_vars, EXTR_SKIP);

        require($template_file);
    }
}