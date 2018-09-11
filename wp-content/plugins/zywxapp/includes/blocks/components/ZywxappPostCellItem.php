<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappPostCellItem extends ZywxappLayoutComponent
{
	public $subNumber = 2000;
    public $attrMap = array(
        'L1' => array('title', 'author', 'categories', 'tags', 'comment_count', 'attachment_type', 'attachment_url', 'date', 'date_format'),
        'L2' => array('title', 'content', 'categories', 'tags', 'author', 'comment_count', 'attachments', 'date', 'content_paged'),
    	'L3' => array('content')
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
    	if ('L1' == $this->layout) {
			return ZywxappHelpers::makeShortString($this->data['post_title'], 30);
		}else if ('L2' == $this->layout) {
    		return $this->data['post_title'];
    	} 
        return ZywxappHelpers::makeShortString($this->data['post_title'], 30);
    }
	
    public function get_content_attr()
    {
    	$content = $this->data['post_content'];
		$content = parent::simplifyText($content);
		if ('L3' == $this->layout) {
			return $content;
		}else if ('L1' == $this->layout) {
    		$subNumber = 60;
    	} else if ('L2' == $this->layout) {
    		$subNumber = $this->subNumber;
    	} 
        return ZywxappHelpers::makeShortString($content, $subNumber);
    }
    
	public function get_author_attr()
    {
    	return $this->data['post_author'];
    }
    
    public function get_categories_attr()
    {
        foreach((get_the_category($this->get_id_attr())) as $category) { 
            $categories[] = $category->cat_name; 
        }   
        
        return implode(",", $categories);       
    }
    
	public function get_tags_attr()
    {
        foreach((get_the_tags($this->get_id_attr())) as $tag) { 
            $tags[] = $tag->name; 
        }   
        
        return implode(",", $tags);       
    }
    
    public function get_comment_count_attr()
    {
        return $this->data['comment_count'];
    }
    
    public function get_date_attr()
    {
        return date('r',strtotime($this->data['post_date']) - (get_option( 'gmt_offset' ) * 3600));
    }
	
	public function get_date_format_attr()
    {
    	return strtotime($this->data['post_date']);
    }
    
    public function get_attachment_type_attr()
	{
        return $this->data['attachment_type'] &&  $this->data['attachment_type'] != 'null' ? $this->data['attachment_type'] : 'text';
    }
	
    public function get_attachment_url_attr()
	{
        return $this->data['attachment_url'];
    }

    public function get_attachments_attr()
	{
        return $this->data['attachments'];
    }

    public function get_content_paged_attr()
    {	
    	$content_len = ZywxappHelpers::getStrLength(parent::simplifyText($this->data['post_content']));
		$page_count = ceil( $content_len / $this->subNumber );
    	return $page_count;
    }
}