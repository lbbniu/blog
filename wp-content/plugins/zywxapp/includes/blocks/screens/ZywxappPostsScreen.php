<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappPostsScreen extends ZywxappBaseScreen
{
    public function run()
    {
    	$screen_conf = array("class"=>"zywxappPostCellItem","layout"=>"L1");
        $page = array();
		$limit = !empty($_GET['limit']) ? intval($_GET['limit']) : ZywxappConfig::getInstance()->posts_list_limit;
		$args = array(
			'number' => $limit,
		);
        $posts = ZywxappDB::getInstance()->get_posts($args);

        foreach ($posts as $post) {
        	$post['attachment_url'] = $post['attachment_type'] ? $post['attachment_url'] : '';
            $this->appendComponentByLayout($page, $screen_conf, $post);
        }
        $this->output($this->prepare($page));
    }
    
	public function runById($post_id)
	{
        $page = array();
		$screen_conf = array("class"=>"zywxappPostCellItem","layout"=>"L2");
		$post = (array)get_post($post_id);
		if ($post) {
			$medias = ZywxappDB::getInstance()->get_post_medias($post_id);
			$post['attachments'] = $medias;
			$post['attachment_url'] = $medias[0]['attachment_url'];
			$post['attachment_type'] = $medias[0]['attachment_type'];
			$this->appendComponentByLayout($page, $screen_conf, $post, true);
		}
		
        $this->output($this->prepare($page));
    }
	
    public function runByContent($post_id)
    {
    	$post = (array)get_post($post_id);
    	$page = array();
		$screen_conf = array("class"=>"zywxappPostCellItem","layout"=>"L3");
		if ($post) {
			$this->appendComponentByLayout($page, $screen_conf, $post, true);
		}
		if (isset($_REQUEST['paged']) && (int)$_REQUEST['paged'] > 0) {
			$paged = (int)$_GET['paged'];
		} else {
			$paged = 1;
		}
		$len = 2000;
		$content = $page['content'];
		$content_len = ZywxappHelpers::getStrLength($content);
		$page_count = ceil( $content_len / $len );
		$start = ( $paged - 1 )*$len;
		$content = mb_substr($content, $start, $len);
		$items = array('paged' => $paged, 'count' => $page_count, 'content' => $content);
		echo json_encode($items);
    }
    
    public function runByCategory($category_id)
    {
    	$this->run();
    }
    
	public function runByTag($tag_id)
    {
    	$this->run();
    }
}
