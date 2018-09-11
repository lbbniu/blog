<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappCommentServices extends ZywxappBaseServices
{
	/**
	 * 评论添加方法
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-17 下午04:45:48
	 * @author   mb  mengbing880814@yahoo.com.cn
	 * 
	 */
    public function add($request)
    {
    	$status = FALSE;
        $code = 400;
    	$comment_post_ID = $_REQUEST['post_id'];
    	$error = null;
		$post = get_post($comment_post_ID);
		if ( empty($post->comment_status) ) {
			$error = new WP_Error('post_not_exists',__('要评论的文章不存在。', 'zywxapp'));
		}
		
    	$status = get_post_status($post);

		$status_obj = get_post_status_object($status);
		
		if ( !comments_open($comment_post_ID) ) {
			$error = new WP_Error('comment_closed',__('Sorry, comments are closed for this item.'));
		} elseif ( 'trash' == $status ) {
			$error = new WP_Error('post_on_trash',__('在回收站的文章不能评论。', 'zywxapp'));
		} elseif ( !$status_obj->public && !$status_obj->private ) {
			$error = new WP_Error('post_on_draft',__('草稿不能评论。', 'zywxapp'));
		} elseif ( post_password_required($comment_post_ID) ) {
			$error = new WP_Error('post_on_password_protected',__('加密的文章不能评论。', 'zywxapp'));
		} else {
			do_action('pre_comment_on_post', $comment_post_ID);
		}
		
		$comment_author       = ( isset($_REQUEST['author']) )  ? trim(strip_tags($_REQUEST['author'])) : null;
		$comment_author_email = ( isset($_REQUEST['email']) )   ? trim($_REQUEST['email']) : null;
		$comment_author_url   = ( isset($_REQUEST['url']) )     ? trim($_REQUEST['url']) : null;
		$comment_content      = ( isset($_REQUEST['content']) ) ? trim($_REQUEST['content']) : null;
		global $wpdb;
		$user = wp_get_current_user();
	    if ( $user->ID ) {
			if ( empty( $user->display_name ) ) {
				$user->display_name = $user->user_login;
			}
			$user_ID = $user->ID;
			$comment_author       = $wpdb->escape($user->display_name);
			$comment_author_email = $wpdb->escape($user->user_email);
			$comment_author_url   = $wpdb->escape($user->user_url);
			if ( current_user_can('unfiltered_html') ) {
				if ( wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_REQUEST['_wp_unfiltered_html_comment'] ) {
					kses_remove_filters(); // start with a clean slate
					kses_init_filters(); // set up the filters
				}
			}
		} else {
			if ( get_option('comment_registration') || 'private' == $status ) {
				$code = 410;
				$error = new WP_Error('post_comment_registration',__('Sorry, you must be logged in to post a comment.'));
			}
		}
		
		$comment_type = '';

		if ( get_option('require_name_email') && !$user->ID ) {
			if ( 6 > strlen($comment_author_email) || '' == $comment_author ) {
				$error = new WP_Error('comment_name_and_email_required',__('<strong>ERROR</strong>: please fill the required fields (name, email).'));
			} elseif ( !is_email($comment_author_email)) {
				$error = new WP_Error('email_invalid', __('<strong>ERROR</strong>: please enter a valid email address.'));
			}
		}
	
		if ( '' == $comment_content ) {
			$error = new WP_Error('comment_content_null', __('<strong>ERROR</strong>: please type a comment.'));
		}
		
		if (!is_wp_error($error)) {
			$comment_parent = isset($_REQUEST['comment_id']) ? absint($_POST['comment_id']) : 0;
			$commentData = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');
		
			$dup = $this->simulateWpDupCheck($commentData);
			if ( !$dup ){
				$comments_notify = get_option('comments_notify');
    			$moderation_notify = get_option('comments_notify');
    			update_option('comments_notify',0);
    			update_option('moderation_notify',0);
    			remove_action( 'comment_post', 'comment_mail_notify' );
				$comment_id = wp_new_comment($commentData);
				$comment = get_comment($comment_id);
				
				$message = '';
                if ( $comment->comment_approved == 1 ){
                    $message = __('评论已成功提交!', 'zywxapp');
                } else {
                    $message = __('评论进入审核!', 'zywxapp');
                }
                $status = TRUE;
                $code = 200;
                update_option('comments_notify',$comments_notify);
    			update_option('moderation_notify',$moderation_notify);
    			add_action( 'comment_post', 'comment_mail_notify' );
				if ( $comment->comment_approved == 1 ){
					$message = __('评论已成功提交!', 'zywxapp');
				} else {
					$message = __('评论进入审核!', 'zywxapp');
				}
			} else {
				$error = new WP_Error('comment_repeat',__('本条内容与上一条重复，请检查后重新发表!', 'zywxapp'));
			}
		}
		$this->outputSection($error,$message);
    }
    
     
	/**
	 * 检测评论是否存在
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-17 下午05:13:37
	 * @author   mb  mengbing880814@yahoo.com.cn
	 * 
	 */
	public function simulateWpDupCheck($commentData)
	{
        global $wpdb;
        extract($commentData, EXTR_SKIP);
        $duplicated = FALSE;
        $dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND comment_approved != 'trash' AND ( comment_author = '$comment_author' ";
        if ( $comment_author_email )
            $dupe .= "OR comment_author_email = '$comment_author_email' ";
        $dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
        if ( $wpdb->get_var($dupe) ) {
            $duplicated = TRUE;
        }
        return $duplicated;
    }
}

