<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappCommentsScreen extends ZywxappBaseScreen
{
    public function run()
    {
    
    }

    /**
     * 获取文章下评论
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-3 下午05:17:28
     * @author   mb  mengbing880814@yahoo.com.cn
     * 
     */
    public function runByPost($post_id)
    {
    	$post_id = $post_id ? $post_id : (int)$_GET['id'];
    	$screen_conf = array('class'=> "zywxappCommentCellItem",'layout' => 'L1');
    	$page = array();
		$limit = !empty($_GET['limit']) ? intval($_GET['limit']) : ZywxappConfig::getInstance()->comments_list_limit;
        $comments = ZywxappDB::getInstance()->get_post_comments($post_id, $limit);
        foreach ($comments as $comment) {
        	if($comment['comment_parent'] > 0){
        		$parent = get_comment($comment['comment_parent']);
        		$comment['to_uid'] = $parent->user_id;
        	}
            $this->appendComponentByLayout($page, $screen_conf, $comment);
        }
		$this->output($this->prepare($page));
    }
}