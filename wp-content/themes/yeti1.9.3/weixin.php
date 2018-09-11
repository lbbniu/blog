<?php

//定义微信 Token
define("WEIXIN_TOKEN", "weixin");
//定义默认缩略图
define("WEIXIN_DEFAULT_THUMB", '');

add_action('pre_get_posts', 'blueria_start_wechat_robot', 4);
function blueria_start_wechat_robot($wp_query) {
	if ( isset($_GET['weixin']) ) {
		global $wechat;
		if ( !isset($wechat) ) {	//不加 死循环
			$wechat = new Wechat();
			$wechat->valid();
			exit;
		}
	}
}

class Wechat {
	private $keyword = '';
	private $textTpl = '';
	private $newsTpl = '';
	private $items = '';
	private $articleCount = 0;
	private $welcome = '';
	private $help = '';
	private $defaultThumb = '';

	public function valid() {
		$echoStr = $_GET["echostr"];

		if ( $this->checkSignature() || isset($_GET['debug']) ) {
			echo $echoStr;
			$this->responseMsg();
			exit;
		}
	}

	public function responseMsg() {
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

		if ( !empty($postStr) ) {
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$fromUsername = $postObj->FromUserName;
			$toUsername = $postObj->ToUserName;
			$msgType = strtolower(trim($postObj->MsgType));

			if ($msgType == 'event') {
				$this->keyword = strtolower(trim($postObj->Event));
			} elseif ($msgType == 'text') {
				$this->keyword = strtolower(trim($postObj->Content));
			} elseif ($msgType == 'voice') {
				$this->keyword = '[voice]';
			}

			if ( empty($this->keyword) ) {
				echo "";
				exit;
			}

			$time = time();
			$this->textTpl = $textTpl = "
				<xml>
					<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>
					<FromUserName><![CDATA[".$toUsername."]]></FromUserName>
					<CreateTime>".$time."</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
				</xml>
			";
			$this->newsTpl = $newsTpl = "
				<xml>
					<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>
					<FromUserName><![CDATA[".$toUsername."]]></FromUserName>
					<CreateTime>".$time."</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<Content><![CDATA[]]></Content>
					<ArticleCount>%d</ArticleCount>
					<Articles>
					%s
					</Articles>
					<FuncFlag>1</FuncFlag>
				</xml>
			";

			$this->getWechatOptions();
			$help = array('help', 'h', '帮助', 'hi', 'hello', '你好', '您好');
			if ('subscribe' == $this->keyword) {
				echo sprintf($textTpl, $this->welcome."\n\n".$this->help);
			} elseif ( in_array($this->keyword, $help) ) {
				echo sprintf($textTpl, $this->help);
			} elseif ('[voice]' == $this->keyword) {
				echo sprintf($textTpl, "系统暂不支持语音回复，请直接回复文字进行搜索。\n获取更多帮助信息请回复：帮助");
			} else {
				var_dump($this->keyword);
				$keyword_length = mb_strwidth(preg_replace('/[\x00-\x7F]/', '', $this->keyword), 'utf-8') + str_word_count($this->keyword)*2;
				if ($keyword_length <= 16) {
					if ( in_array($this->keyword, array('new', '最新')) ) {
						$this->query('new');
					} elseif ( in_array($this->keyword, array('hot', '热门', '最热')) ) {
						$this->query('hot');
					} elseif ( in_array($this->keyword, array('top', '最赞')) ) {
						$this->query('top');
					} elseif ( in_array($this->keyword, array('rand', 'random', '随机')) ) {
						$this->query('ran');
					} else {
						$this->query();
					}
					if ($this->articleCount == 0) echo sprintf($textTpl, "抱歉，没有找到与【{$this->keyword}】相关的文章，你可以更换一个关键字进行搜索");
					else echo sprintf($newsTpl, $this->articleCount, $this->items);
				} else {
					echo sprintf($textTpl, "你输入的关键字太长了，系统无法处理，请等待管理员回复。");
				}
			}
		} else {
			echo "";
			exit;
		}
	}

	private function query($action = null) {
		global $wp_query;
		$post_count = 6;
		if ($action == 'new') $query_array = array('posts_per_page' => $post_count);
		if ($action == 'ran') $query_array = array('posts_per_page' => $post_count, 'orderby' => 'rand');
		if ($action == 'hot') $query_array = array('posts_per_page' => $post_count, 'orderby' => 'meta_value_num', 'meta_key' => 'views');
		if ($action == 'top') $query_array = array('posts_per_page' => $post_count, 'orderby' => 'meta_value_num', 'meta_key' => 'blueria_vote');
		if ($action === null) $query_array = array('posts_per_page' => $post_count, 's' => $this->keyword);
		$wp_query->query($query_array);
		if ( have_posts() ) : while ( have_posts() ) : the_post();
			global $post;
			$title   = get_the_content();
			$excerpt = wechat_get_post_excerpt($post);
			$thumb   = blueria_get_post_thumb_src($post->ID);
			if (!$thumb && $this->defaultThumb) $thumb = $this->defaultThumb;
			$link    = get_permalink();
			$items  .= $this->get_item($title, $excerpt, $thumb, $link);
		endwhile; endif;
		$this->articleCount = count($wp_query->posts);
		$this->items = $items;
		wp_reset_query();
	}

	public function get_item($title, $description, $picUrl, $url){
		if(!$description) $description = $title;
		$item = "
			<item>
				<Title><![CDATA[{$title}]]></Title>
				<Discription><![CDATA[{$description}]]></Discription>
				<PicUrl><![CDATA[{$picUrl}]]></PicUrl>
				<Url><![CDATA[{$url}]]></Url>
			</item>
		";
		return $item;
	}

	private function getWechatOptions() {
		$wechat_options = get_option('wechat_options');
		$this->welcome = $wechat_options ? esc_attr($wechat_options['welcome']) 
			: "欢迎收听我爱笑微信\n";
		$this->help = $wechat_options ? esc_attr($wechat_options['help']) 
			: "回复 最新 查看最新笑料\n回复 最热 查看最热门笑料\n回复 最赞 查看赞最多笑料\n回复 随机 查看推荐的笑料\n回复 帮助 查看帮助信息\n\n我爱笑woaixiao.cn";
		// $this->defaultThumb = $wechat_options ? esc_attr($wechat_options['thumb']) : '';
	}

	private function checkSignature() {
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];

		$weixin_token = WEIXIN_TOKEN;
		$tmpArr = array($weixin_token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );

		return ($tmpStr == $signature) ? true : false;
	}
}

function wechat_get_post_excerpt($post, $width = 120) {
	$post_excerpt = strip_tags($post->post_excerpt);
	if ( !$post_excerpt ) {
		$post_excerpt = mb_strimwidth(strip_tags(do_shortcode($post->post_content)), 0, $width, '...', 'utf-8');
	}
	return $post_excerpt;
}

add_filter('posts_orderby_request', 'wechat_search_request');
function wechat_search_request($orderby = '') {
	global $wpdb, $wp_query;
	$keyword = stripslashes($wp_query->query_vars[s]);
	if ($keyword) { 
		$n = !empty($q['exact']) ? '' : '%';
		preg_match_all('/".*?("|$)|((?<=[\r\n\t ",+])|^)[^\r\n\t ",+]+/', $keyword, $matches);
		$search_terms = array_map('_search_terms_tidy', $matches[0]);
		$case_when = "0";
		foreach( (array) $search_terms as $term ){
			$term = esc_sql( like_escape( $term ) );
			$case_when .=" + (CASE WHEN {$wpdb->posts}.post_title LIKE '{$term}' THEN 3 ELSE 0 END) + (CASE WHEN {$wpdb->posts}.post_title LIKE '{$n}{$term}{$n}' THEN 2 ELSE 0 END) + (CASE WHEN {$wpdb->posts}.post_content LIKE '{$n}{$term}{$n}' THEN 1 ELSE 0 END)";
		}
		return "({$case_when}) DESC, {$wpdb->posts}.post_modified DESC";
	}else{
		return $orderby;
	}
}