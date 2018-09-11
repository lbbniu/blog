<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappMediaCellItem extends ZywxappLayoutComponent
{
	public $attrMap = array(
		'L1' => array('title', 'content', 'author', 'type', 'url', 'post_type', 'post_parent', 'date_format'),
    );
    
    public function __construct($layout='L1', $data)
    {
        parent::init($layout, $data);
    }
	
    public function get_id_attr()
    {
        return $this->data['ID'];
    }
	
    public function get_title_attr()
    {
        return $this->data['post_title'];
    }
	
    public function get_content_attr()
    {
    	//去除标签
    	$content = $this->data['post_content'];
		$content = parent::simplifyText($content);
        return $content;
    }
    
	public function get_author_attr()
    {
    	return $this->data['post_author'];
    }
    
    public function get_type_attr()
	{
        return $this->data['attachment_type'];
    }
	
    public function get_url_attr()
	{
        return $this->data['attachment_url'];
    }
	
    public function get_post_type_attr()
	{
        return $this->data['post_type'];
    }
    
    public function get_post_parent_attr()
	{
        return (int)$this->data['post_parent'];
    }
    
	public function get_date_format_attr()
    {
    	return strtotime($this->data['post_date']);
    }
}