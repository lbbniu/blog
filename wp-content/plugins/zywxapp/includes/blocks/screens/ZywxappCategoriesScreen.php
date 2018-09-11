<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappCategoriesScreen extends ZywxappBaseScreen
{
    public function run()
    {
    	$screen_conf = array("class"=>"zywxappCategoryCellItem","layout"=>"L1");
        $page = array();
		$limit = !empty($_GET['limit']) ? intval($_GET['limit']) : ZywxappConfig::getInstance()->categories_list_limit;
		$args = array(
            'number' => $limit,
            'hierarchical' => FALSE,
            'pad_counts' => 1,
			'orderby' => 'id'
        );
        $categories = ZywxappDB::getInstance()->get_terms('category',$args);
        foreach ($categories as $category) {
        	$category->name = str_replace('&amp;', '&', $category->name);
            $this->appendComponentByLayout($page, $screen_conf, (array)$category);
        }
        $this->output($this->prepare($page));
    }
}