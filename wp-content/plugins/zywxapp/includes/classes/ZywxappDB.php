<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
/**
* 插件应用的数据库操作类
* 
* 
* @package ZywxappWordpressPlugin
* @subpackage Database
* @author mxg<jiemack@163.com>
*/
class ZywxappDB implements ZywxappIInstallable
{
	//数据库内部版本号
    private $internal_version = '0.1';

    public $query_vars;
    public $query_where;
    public $query_order;
	public $query_orderby;
	public $query_limit;
	public $query_total;
	public $query_rsort = false;
	
	public $post_total = 0;
	
    private static $_instance = null;

    /**
     * @static
     * @return ZywxappDB
     */
    public static function getInstance()
    {
        if( is_null(self::$_instance) ) {
            self::$_instance = new ZywxappDB();
        }
        return self::$_instance;
    }
    
    private function  __clone()
    {
        
    }

    private function __construct()
    {
    	
    }
    
	public function prepare_query($query)
    {
    	global $wpdb;
		$this->query_vars = wp_parse_args( $query, array(
			'field' => 'id',
			'orderby' => 'id',
			'order' => 'ASC',
			'offset' => '',
			'number' => '',
			'total' => false,
		) );

		if ( $this->query_vars['total'] ) {
			$this->query_total = 'SQL_CALC_FOUND_ROWS ';
		}
		
		if ( 'ASC' == strtoupper($this->query_vars['order']) ) {
			$order = 'ASC';
		} else {
			$order = 'DESC';
		}
		
		$id = $this->query_vars['field'];
		$orderby = $this->query_vars['orderby'];
		
        if (isset($_GET['up']) && !empty($_GET['up'])) {
        	$this->query_where = $wpdb->prepare(" AND {$id} > %d", $_GET['up']);
        	$order = "ASC";
        	$this->query_rsort = true;
        } elseif (isset($_GET['down']) && !empty($_GET['down'])) {
        	$this->query_where = $wpdb->prepare(" AND {$id} < %d", $_GET['down']);
        	$order = 'DESC';
        } elseif (isset($_GET['since_id']) && !empty($_GET['since_id'])) {
        	$this->query_where = $wpdb->prepare(" AND {$id} > %d", $_GET['since_id']);
        	$order = "ASC";
        	$this->query_rsort = true;
        } elseif (isset($_GET['max_id']) && !empty($_GET['max_id'])) {
        	$this->query_where = $wpdb->prepare(" AND {$id} < %d", $_GET['max_id']);
        	$order = 'DESC';
        } elseif (isset($_GET['since_time']) && !empty($_GET['since_time'])) {
        	$time = date('Y-m-d H:i:s',$_GET['since_time']);
        	$this->query_where = $wpdb->prepare(" AND {$id} > %s", $time);
        	$order = "ASC";
        	$this->query_rsort = true;
        } elseif (isset($_GET['max_time']) && !empty($_GET['max_time'])) {
        	$time = date('Y-m-d H:i:s',$_GET['max_time']);
        	$this->query_where = $wpdb->prepare(" AND {$id} < %s", $time);
        	$order = 'DESC';
        }
        
        $this->query_order = $order;
		$this->query_orderby = "ORDER BY {$orderby} $order";
		// limit
		if ( $this->query_vars['number'] ) {
			if ( $this->query_vars['offset'] ) {
				$this->query_limit = $wpdb->prepare("LIMIT %d, %d", $this->query_vars['offset'], $this->query_vars['number']);
			} else {
				$this->query_limit = $wpdb->prepare("LIMIT %d", $this->query_vars['number']);
			}
		}
    }
    
    public function get_total()
    {
    	global $wpdb;
    	return $wpdb->get_var('SELECT FOUND_ROWS()');
    }
    
    /**
     * 数据库是否已经安装
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午02:26:58
     * @author author
     * 
     */
    public function isInstalled()
    {
    	$version = get_option('zywxapp_db_version');
		return isset($version);
    }
    
    /**
     * 数据库安装
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午03:29:18
     * @author author
     * 
     */
    public function install()
    {
        //保存数据库版本号 用于数据库更新
        update_option("zywxapp_db_version", $this->internal_version);
        //检测是否安装成功
        return $this->isInstalled();
    }

    /**
     * 删除数据库
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-7 下午07:29:51
     * @author author
     * 
     */
    public function uninstall()
    {
        delete_option('zywxapp_db_version');
    }

    /**
     * 数据库版本检测
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-5 下午02:59:00
     * @author author
     * 
     */
    public function needUpgrade()
    {
    	// 获取数据版本号 
        $installedVer = get_option("zywxapp_db_version");
        return ( $installedVer != $this->internal_version );
    }

    /**
     * 升级数据库
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-7 下午04:23:27
     * @author author
     * 
     */
    public function upgrade()
    {
        return $this->install();
    }

   	/**
   	 * 通过章ID获取评论列表
   	 *
   	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
   	 * @since      File available since Release 1.0 -- 2012-3-3 下午05:30:50
   	 * @author   mb  mengbing880814@yahoo.com.cn
   	 * 
   	 */
	public function get_post_comments($post_id, $limit = 10)
	{
		global $wpdb;

		$args = array(
			'field' => 'comment_ID',
        	'orderby'  => 'comment_date',
        	'order'  => 'DESC',
        	'number' => $limit,
        	'total' => false,
        );
        $this->prepare_query($args);
        $where = $wpdb->prepare(" WHERE comment_post_ID = %d AND comment_approved = '1' ",$post_id);
		$where .= $this->query_where;
		$sql = $wpdb->prepare("SELECT {$this->query_total} * FROM $wpdb->comments {$where} {$this->query_orderby} {$this->query_limit}");
		$result = $wpdb->get_results($sql,ARRAY_A);

		if ( $this->query_total ) {
			$total = $this->get_total();
        }
        if($this->query_rsort) rsort($result);
		return $result;
	}

	/**
	 * 获取文章列表
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-16 下午02:28:04
	 * @author  
	 * 
	 */
	public function get_posts($args = array())
    {
        global $wpdb;
        $type = $_REQUEST['type'];
        $query = array_merge(array(
        	'field'  => 'post.post_date',
        	'orderby'  => 'post.post_date',
        	'order'  => 'DESC',
        	'total' => false,
        ),$args);
        
        $this->prepare_query($query);
		$from = "FROM {$wpdb->posts} AS post";
        $where = 'WHERE 1=1 ';
        $where .= " AND post.post_status = 'publish' AND post.post_type = 'post'";
    	if ( (isset($_GET['category_id']) && !empty($_GET['category_id'])) || (isset($_GET['tag_id']) && !empty($_GET['tag_id'])) ) {
        	if ($_GET['category_id']) {
        		$term_id = $_GET['category_id'];
        		$taxonomy = 'category';
        	} else {
        		$term_id =  $_GET['tag_id'];
        		$taxonomy = 'post_tag';
        	}
    		$term_from = " INNER JOIN {$wpdb->term_relationships} AS term_rel ON (post.ID = term_rel.object_id)".
    					 " AND term_rel.term_taxonomy_id = (SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy}".
    					 " WHERE term_id = {$term_id} AND taxonomy = '{$taxonomy}')";
        	$from .= $term_from;
    	}
        if(in_array($type, array('image', 'video', 'audio'))){
        	$where .= " AND instr(attach.post_mime_type,'".$type."')"; 
        }
        if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
        	$where .= " AND (post.post_title LIKE '%%{$_REQUEST['keyword']}%%' OR post.post_content LIKE '%%{$_REQUEST['keyword']}%%')"; 
        }
        
		$where .= $this->query_where;
        $sql = $wpdb->prepare("SELECT {$this->query_total} post.ID, post.post_title, post.post_author, post.post_date,"
        	." post.comment_count, SUBSTRING(attach.post_mime_type,1,5) as attachment_type, attach.post_type, "
        	." attach.post_content as attachment_content, attach.guid as attachment_url, count(distinct post.ID)"
        	." {$from} LEFT JOIN {$wpdb->posts} AS attach ON post.ID = attach.post_parent"
        	." AND attach.post_type='attachment' {$where} GROUP BY post.ID {$this->query_orderby} {$this->query_limit}");

        $result = $wpdb->get_results($sql, ARRAY_A);
        if ( $this->query_total ) {
			$this->post_total = $this->get_total();
        }
        if($this->query_rsort) rsort($result);
        return $result ? $result : array();
    }

	/**
	 * 获取文章关联媒体
	 *
	 * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
	 * @since      File available since Release 1.0 -- 2012-3-17 上午11:50:27
	 * @author   mb  mengbing880814@yahoo.com.cn
	 * 
	 */
    public function get_post_medias($id)
    {
        global $wpdb;
        
        $where .= " WHERE (instr(post_mime_type,'image/') OR instr(post_mime_type,'video/') OR instr(post_mime_type,'audio/'))";
		$sql = $wpdb->prepare("SELECT ID as id, post_type, SUBSTRING(post_mime_type,1,5) as attachment_type, guid as attachment_url,"
			."post_content as attachment_content FROM {$wpdb->posts} {$where} AND post_parent = %d ", $id);
        $result = $wpdb->get_results($sql, ARRAY_A);
        return $result ? $result : array();
    }
	
    /**
     * 获取媒体库列表
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-3-21 上午09:25:22
     * @author mxg<jiemack@163.com>
     * @param  array $args
     * @return Array
     */
    public function get_medias($args = array())
    {
    	global $wpdb;
    	$type = $args['type'];
    	$query = array_merge(array(
    			'order' => 'DESC',
    			//'field'  => 'media.ID',
        		//'orderby'  => 'media.ID',
    			'field'  => 'media.post_date',
        		'orderby'  => 'media.post_date',
    		),$args);
    	$this->prepare_query($query);
    	
    	$where = "WHERE 1=1";
    	$where .= " AND media.post_type='attachment' AND media.post_status != 'trash'";
    	if(in_array($type, array('image', 'video', 'audio'))){
        	$where .= " AND instr(media.post_mime_type,'".$type."')"; 
        }
    	$where .= $this->query_where;
        $sql = $wpdb->prepare("SELECT {$this->query_total} media.ID, media.post_title, media.post_content,media.post_author,"
        	." media.guid AS attachment_url, SUBSTRING(media.post_mime_type,1,5) AS attachment_type, post.post_type,media.post_date,"
        	." media.post_parent FROM {$wpdb->posts} AS media LEFT JOIN {$wpdb->posts} AS post on media.post_parent = post.ID"
        	." {$where} {$this->query_orderby} {$this->query_limit}");

        $result = $wpdb->get_results($sql, ARRAY_A);
        if ( $this->query_total ) {
			$total = $this->get_total();
        }
        if($this->query_rsort) rsort($result);
        return $result ? $result : array();
    }
     
    /**
     * 获取分类列表
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-5-14 上午10:53:34
     * @author mxg<jiemack@163.com>
     * @param  string $taxonomies
     * @param  array $args
     * @return array
     */
    public function get_terms($taxonomies, $args = array())
    {
    	if (isset($_GET['up']) && !empty($_GET['up'])) {
        	$order = "ASC";
        	$rsort = true;
        } elseif (isset($_GET['down']) && !empty($_GET['down'])) {
        	$order = 'DESC';
        	$rsort = false;
        }
        $args['order'] = $order;
        if ('category' == $taxonomies) {
        	$terms = get_categories($args);
        } else {
        	$terms = get_tags($args);
        }
        
        if($rsort) rsort($terms);
        return $terms;
    }
}
