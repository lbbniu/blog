<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappTagsScreen extends ZywxappBaseScreen
{
    public function run()
    {
        $screen_conf = array("class"=>"zywxappTagCellItem","layout"=>"L1");
        $page = array();
		$limit = !empty($_GET['limit']) ? intval($_GET['limit']) : ZywxappConfig::getInstance()->tags_list_limit;
		$args = array(
            'number' => $limit,
            'hierarchical' => false,
            'hide_empty' => true,
			//'pad_counts' => 1,
			'orderby' => 'id'
        );
        $tags = ZywxappDB::getInstance()->get_terms('tags',$args);
        foreach ($tags as $tag) {
        	$tag->name = str_replace('&amp;', '&', $tag->name);
            $this->appendComponentByLayout($page, $screen_conf, (array)$tag);
        }
        $this->output($this->prepare($page));
        
    }
}