<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappSearchScreen extends ZywxappPostsScreen{

	public function run(){}

	public function runByQuery()
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
}