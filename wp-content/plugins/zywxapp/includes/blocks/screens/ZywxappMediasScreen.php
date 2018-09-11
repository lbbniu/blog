<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappMediasScreen extends ZywxappBaseScreen
{
	public function runByImage()
	{
		$this->get_list('image');
	}
	
	public function runByAudio()
	{
		$this->get_list('audio');
	}
	
	public function runByVideo()
	{
		$this->get_list('video');
	}
	
	public function get_list($type = 'image')
	{
		$screen_conf = array("class"=>"zywxappMediaCellItem","layout"=>"L1");
        $page = array();
		$limit = !empty($_GET['limit']) ? intval($_GET['limit']) : ZywxappConfig::getInstance()->medias_list_limit;
		$args = array(
			'type' => $type,
			'number' => $limit
		);
        $posts = ZywxappDB::getInstance()->get_medias($args);
        foreach ($posts as $post) {
            $this->appendComponentByLayout($page, $screen_conf, $post);
        }
        $this->output($this->prepare($page));
	}
}