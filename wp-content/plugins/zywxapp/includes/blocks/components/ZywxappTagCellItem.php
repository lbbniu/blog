<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappTagCellItem extends ZywxappLayoutComponent
{
    public $attrMap = array(
		'L1' => array('name', 'post_count'),
    );
    
    public function __construct($layout='L1', $data)
    {
        parent::init($layout, $data);
    }
	
    public function get_id_attr()
    {
        $id = '';
        if ( !empty($this->data['cat_ID']) ){
            $id = $this->data['cat_ID'];
        } else {
            $id = $this->data['term_id'];
        }
        return $id;
    }
	
    public function get_name_attr()
    {
        $name = '';
        if ( !empty($this->data['cat_name']) ){
            $name = $this->data['cat_name'];
        } else {
            $name = $this->data['name'];
        }
        return ZywxappHelpers::makeShortString($name, 30);
    }
	
    public function get_post_count_attr()
    {
    	$count = '';
        if ( !empty($this->data['category_count']) ){
            $count = $this->data['category_count'];
        } else {
            $count = $this->data['count'];
        }
    	return $count;
    }
    
}
