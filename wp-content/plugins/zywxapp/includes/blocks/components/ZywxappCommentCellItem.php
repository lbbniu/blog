<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappCommentCellItem extends ZywxappLayoutComponent
{
    public $attrMap = array(
    	'L1' => array('post_id', 'from_uid','from_name','from_avatar','to_uid','to_name','comment_content','date'),
        'L2' => array('post_id', 'from_uid','from_name','from_avatar','to_uid','to_name','to_avatar','comment_content',
			'comment_parent', 'post_type', 'parent_content', 'date'),
    );
   
   /**
    * constructor 
    * 
    * @uses ZywxappLayoutComponent::init()
    * 
    * @param string $layout the layout name
    * @param array $data the data the components relays on
    * @return ZywxappCommentCellItem
    */
    public function __construct($layout='L1', $data)
    {
        parent::init($layout, $data);
    }
    
	/**
    * Attribute getter method
    * 
    * @return the id of the component
    */
    public function get_id_attr()
    {
        return (int)$this->data['comment_ID'];
    }
    
    /**
    * Attribute getter method
    * 
    * @return the post_id of the component
    */
    public function get_post_id_attr()
    {
        return (int) $this->data['comment_post_ID'];
    }
    
	public function get_from_uid_attr()
	{
        return $this->data['from_uid'] ? $this->data['from_uid'] : $this->data['user_id'];
    }
    
    /**
    * Attribute getter method
    * 
    * @return the user of the component
    */
    public function get_from_name_attr()
    {
		return $this->data['comment_author'];
    }
    
	public function get_from_avatar_attr()
	{
        return get_avatar($this->get_from_uid_attr());
    }

	public function get_to_uid_attr()
	{
        return $this->data['to_uid'];
    }
    
	public function get_to_name_attr()
	{
        $to_uid = $this->data['to_uid'];
		$to_name = $to_uid > 0 ? get_comment_author($this->data['to_uid']) : "";
		return $to_name;
    }
    
	public function get_to_avatar_attr()
	{
        return $this->get_to_uid_attr() ? get_avatar($this->get_to_uid_attr()) : '';
    }
	
	/**
    * Attribute getter method
    * 
    * @return the content of the component
    */
    public function get_comment_content_attr()
    {
        $content = strip_tags($this->data['comment_content']);
		$content = str_replace(array("\r\n", "\r"), " ", $content);
        return $content;
    }
    
	public function get_comment_parent_attr()
	{
        return $this->data['comment_parent'];
    }
    
	public function get_post_type_attr()
	{
        return $this->data['post_type'];
    }
    
	public function get_parent_content_attr()
	{
		if ($this->get_comment_parent_attr()) {
			$parent = get_comment($this->get_comment_parent_attr());
			$parent_content = $parent->comment_content;
		} else {
			$parent_content = $this->data['post_content'];
		}
		$parent_content = parent::simplifyText($parent_content);
        return ZywxappHelpers::makeShortString($parent_content, 100);
    }
    
    /**
    * Attribute getter method
    * 
    * @return the date of the component
    */
    public function get_date_attr()
    {
	    return date('r',strtotime($this->data['comment_date']) - (get_option( 'gmt_offset' ) * 3600));
    }
}
