<?php
# Theme Name: Yeti
# Author: Blueria
add_action('wp_head', 'jsinit', 0);
function jsinit()
{
    echo "<script type='text/javascript'>\n";
    echo "var ajaxurl = '" . admin_url('admin-ajax.php') . "';\n";
    echo "var tpl_url = '" . get_bloginfo('template_url') . "/';\n";
    echo "var sitename = '" . get_bloginfo('name') . "';\n";
    $stop_ietips = blueria_get_option('stop_ietips');
    $stop_ietips = $stop_ietips ? 'yes' : 'no';
    echo "var stop_ietips = '{$stop_ietips}';\n";
    if (blueria_get_option('stop_external_link'))
        echo "var stop_external_link = true;\n";
    else
        echo "var stop_external_link = false;\n";
    global $theme_tpl;
    echo "var theme_tpl = '{$theme_tpl}';\n";
    global $comment_notice;
    echo "var comment_notice = '{$comment_notice}';\n";
    echo "</script>\n";
}
function getImgMeta($url)
{
    $curl = curl_init(get_bloginfo('template_url') . '/fastimage.php?url=' . $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);
    if (empty($data))
        return false;
    parse_str($data);
    return array(
        $w,
        $h,
        $t
    );
}
function getAttachmentType($attachment_id)
{
    global $wpdb;
    $mime_type = $wpdb->get_var($wpdb->prepare("SELECT post_mime_type FROM $wpdb->posts WHERE ID = %d AND post_type = 'attachment';", $attachment_id));
    if (!$mime_type)
        return null;
    switch ($mime_type) {
        case 'image/gif':
            return 'gif';
        case 'image/jpeg':
            return 'jpg';
        case 'image/png':
            return 'png';
        default:
            return null;
    }
}
function blueria_cat_id($name, $slug)
{
    require_once(ABSPATH . '/wp-admin/includes/taxonomy.php');
    if (!($cat_id = category_exists($slug)))
        $cat_id = wp_insert_category(array(
            'cat_name' => $name,
            'category_nicename' => $slug
        ));
    return $cat_id;
}
function blueria_get_post_thumb_src($ID = null)
{
    global $post;
    if (!$ID)
        $post_ID = $post->ID;
    else
        $post_ID = $ID;
    $media_type = get_post_format($post_ID);
    ;
    if ($media_type == 'video') {
        $src = get_post_meta($post_ID, 'img', true);
    } elseif ($media_type == 'image') {
        $imgurl = get_post_meta($post_ID, 'imgurl', true);
        if (!empty($imgurl)) {
            $src = $imgurl;
        } else {
            $img = wp_get_attachment_image_src(get_post_meta($post_ID, "pic", true), 'full');
            $src = $img[0];
        }
    }
    return $src;
}
function blueria_get_sticky($count = 5)
{
    $sticky = get_option('sticky_posts');
    if (!is_array($sticky))
        return false;
    rsort($sticky);
    return array_slice($sticky, 0, $count);
}
add_action('wp_ajax_blueria_vote', 'blueria_vote');
add_action('wp_ajax_nopriv_blueria_vote', 'blueria_vote');
function blueria_vote()
{
    global $wpdb;
    if (!isset($_POST['post_ID']) || !isset($_POST['doit']))
        wp_die(0);
    $post_ID =& $_POST['post_ID'] ? $_POST['post_ID'] : 0;
    if ($post_ID == 0)
        wp_die(-1);
    $doit =& $_POST['doit'];
    if ($doit != 'up' && $doit != 'down')
        wp_die(-1);
    $id_exist = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE ID = %d AND post_type = 'post';", $post_ID));
    if ($id_exist != $post_ID)
        wp_die(-1);
    if ($doit == 'up') {
        $vote = get_post_meta($post_ID, "blueria_vote", true);
        if ($vote != '') {
            $vote = (int) $vote + 1;
            update_post_meta($post_ID, "blueria_vote", $vote);
			setcookie("blueria_vote_" . $post_ID, $vote, time() + 3600 * 24, COOKIEPATH);
            if ($vote >= 5000 && get_post_status($post_ID) == 'pending'){
		// 更新编号为$post_ID的文章
  		$my_post = array();
 		$my_post['ID'] = $post_ID;
  		$my_post['post_date'] = current_time('mysql');
		// Update the post into the database
  		wp_update_post( $my_post );
		//发布文章
		wp_publish_post($post_ID);
	    }
        } else {
            add_post_meta($post_ID, "blueria_vote", 1, true);
            setcookie("blueria_vote_" . $post_ID, 1, time() + 3600 * 24, COOKIEPATH);
        }
    } elseif ($doit == 'down') {
        $vote2 = get_post_meta($post_ID, "blueria_vote2", true);
        if ($vote2 != '') {
            $vote2 = (int) $vote2 - 1;
            update_post_meta($post_ID, "blueria_vote2", $vote2);
            setcookie("blueria_vote_" . $post_ID, $vote2, time() + 3600 * 24, COOKIEPATH);
        } else {
            add_post_meta($post_ID, "blueria_vote2", -1, true);
            setcookie("blueria_vote_" . $post_ID, -1, time() + 3600 * 24, COOKIEPATH);
        }
    }
    echo 'ok';
    wp_die();
}
function addWatermark($imgfile)
{
    $watermark_options = get_option('watermark_options');
    if (!$watermark_options['enabled'])
        return null;
    $type     = $watermark_options ? esc_attr($watermark_options['type']) : 'image';
    $filename = $watermark_options ? esc_attr($watermark_options['filename']) : 'mark.png';
    $text     = $watermark_options ? esc_attr($watermark_options['text']) : 'Blueria.cn';
    $font     = $watermark_options ? esc_attr($watermark_options['font']) : 'arial.ttf';
    $size     = $watermark_options ? esc_attr($watermark_options['size']) : 24;
    $pos      = $watermark_options ? esc_attr($watermark_options['pos']) : 7;
    $color    = $watermark_options ? esc_attr($watermark_options['color']) : 7;
    include_once "ImageWatermark.php";
    $img = new ImageWatermark($imgfile);
    if (!$img)
        return null;
    if ($img->getImageType() == 'gif')
        return null;
    $img->markPosType = $pos;
    if ($type == 'image') {
        $markfile = TEMPLATEPATH . '/shuiyin/' . $filename;
        $img->appendImageMark($markfile);
    } elseif ($type == 'text') {
        $fontFile      = TEMPLATEPATH . '/shuiyin/' . $font;
        $img->fontFile = $fontFile;
        $img->fontSize = $size;
        $img->color    = $color;
        $img->appendTextMark($text);
    } else {
        $img = null;
        return null;
    }
    $img->write($imgfile);
    $img = null;
}
function blueria_add_watermark($fileinfo)
{
    $file = $fileinfo['file'];
    if (!preg_match("#(\.jpg|\.jpeg|\.png|\.gif)$#", $file))
        return $fileinfo;
    addWatermark($file);
    return $fileinfo;
}
add_filter('wp_handle_upload', 'blueria_add_watermark');
function move_attachments_to_bcs($metadata)
{
    $wp_upload_dir = wp_upload_dir();
    $object        = "/yeti/" . $metadata['file'];
    $file          = $wp_upload_dir['basedir'] . '/' . $metadata['file'];
    blueria_upload_bcs($object, $file) && blueria_del_file($file);
    if (isset($metadata['sizes']) && count($metadata['sizes']) > 0) {
        foreach ($metadata['sizes'] as $size) {
            $object = "/yeti/" . $wp_upload_dir['subdir'] . '/' . $size['file'];
            $file   = $wp_upload_dir['path'] . '/' . $size['file'];
            $opt    = array(
                'headers' => array(
                    'Content-Type' => $size['mime-type']
                )
            );
            blueria_upload_bcs($object, $file, $opt) && blueria_del_file($file);
        }
    }
    return $metadata;
}
if (get_option('save_to_bcs') == '1') {
    add_filter('wp_generate_attachment_metadata', 'move_attachments_to_bcs', 999);
}
function blueria_del_file($file)
{
    try {
        if (!@file_exists($file))
            return TRUE;
        if (!@unlink($file))
            return FALSE;
        return TRUE;
    }
    catch (Exception $e) {
        return FALSE;
    }
}
function blueria_upload_bcs($object, $file, $opt = array())
{
    require_once('bcs/bcs.class.php');
    $bcs_options = get_option('bcs_options');
    if (!$bcs_options)
        return false;
    $bcs_bucket = esc_attr($bcs_options['bucket']);
    $bcs_ak     = esc_attr($bcs_options['ak']);
    $bcs_sk     = esc_attr($bcs_options['sk']);
    if (!is_object($baidu_bcs))
        $baidu_bcs = new BaiduBCS($bcs_ak, $bcs_sk);
    $opt['acl']                = "public-read";
    $opt['headers']['Expires'] = 'access plus 1 years';
    $response                  = $baidu_bcs->create_object($bcs_bucket, $object, $file, $opt);
    if ($response->isOK())
        return true;
    else
        return false;
}
function get_attachment_url_switch($url)
{
    $wp_upload_dir = wp_upload_dir();
    $baseurl       = $wp_upload_dir['baseurl'];
    $arr           = explode($baseurl, $url);
    $bcs_options   = get_option('bcs_options');
    if (!$bcs_options)
        return $url;
    $bcs_bucket = esc_attr($bcs_options['bucket']);
    $file       = $wp_upload_dir['basedir'] . $arr[1];
    if (@file_exists($file))
        return $url;
    $url = "http://bcs.duapp.com/{$bcs_bucket}/yeti" . $arr[1];
    return $url;
}
add_filter('wp_get_attachment_url', 'get_attachment_url_switch');
function blueria_delete_bcs($file)
{
    if (!preg_match("#(\.jpg|\.jpeg|\.png|\.gif|\.bmp)$#", $file))
        return $file;
    require_once('bcs/bcs.class.php');
    $bcs_options = get_option('bcs_options');
    if (!$bcs_options)
        return $file;
    $bcs_bucket    = esc_attr($bcs_options['bucket']);
    $bcs_ak        = esc_attr($bcs_options['ak']);
    $bcs_sk        = esc_attr($bcs_options['sk']);
    $wp_upload_dir = wp_upload_dir();
    $delFile       = str_replace($wp_upload_dir['basedir'], '', $file);
    $delFile       = str_replace('./', '', $delFile);
    $delFile       = "/yeti/" . ltrim($delFile, '/');
    if (!is_object($baidu_bcs))
        $baidu_bcs = new BaiduBCS($bcs_ak, $bcs_sk);
    @$baidu_bcs->delete_object($bcs_bucket, $delFile);
    return $file;
}
add_action('wp_delete_file', 'blueria_delete_bcs');
function html5player($swf_url)
{
    $ua     = strtolower($_SERVER['HTTP_USER_AGENT']);
    $iphone = preg_match("/ipad|iphone/iU", $ua);
    $result = "<embed width='100%' height='240' src='{$swf_url}' wmode='Opaque' allowfullscreen='true' allownetworking='internal' allowscriptaccess='never' type='application/x-shockwave-flash' pluginspage='http://www.adobe.com/go/getflashplayer' />";
    if (!preg_match("/(youku\.com|tudou\.com|56\.com|ku6\.com|pps\.tv|qq\.com|pptv\.com|video\.sina\.com\.cn|sohu\.com)/", $swf_url, $video_site))
        return $swf_url;
    if (!$iphone)
        return $result;
    switch ($video_site[1]) {
        case 'youku.com':
            preg_match("#\/sid\/(\w+)\/#", $swf_url, $vid);
            $result = "<video width='100%' height='240' controls='controls' autoplay='' preload='' src='http://v.youku.com/player/getRealM3U8/vid/{$vid[1]}/type/mp4/v.m3u8'></video>";
            break;
        case 'tudou.com':
            preg_match("#iid=(\d+)#", $swf_url, $vid);
            $result = "<video width='100%' height='240' controls='controls' autoplay='' preload='' src='http://vr.tudou.com/v2proxy/v2.m3u8?it={$vid[1]}'></video>";
            break;
        case '56.com':
            preg_match("#v\_(\w+)#", $swf_url, $vid);
            $result = "<video width='100%' height='240' controls='controls' autoplay='' preload='' src='http://vxml.56.com/m3u8/{$vid[1]}/'></video>";
            break;
        case 'ku6.com':
            preg_match("#refer\/([\w\-\.]+)\/#", $swf_url, $vid);
            $result = "<video width='100%' height='240' controls='controls' autoplay='' preload='' src='http://v.ku6.com/fetchwebm/{$vid[1]}.m3u8'></video>";
            break;
        case 'pps.tv':
            preg_match("#sid\/(\w+)#", $swf_url, $vid);
            $result = '<video width="100%" height="240" controls="controls" autoplay="" preload="" src=""></video><script type="text/javascript">pps_player("' . $vid[1] . '");</script>';
            break;
        case 'qq.com':
            preg_match("#vid=(\w+)#", $swf_url, $vid);
            $result = '<video width="100%" height="240" controls="controls" autoplay="" preload="" src=""></video><script type="text/javascript">qq_player("' . $vid[1] . '");</script>';
            break;
        case 'pptv.com':
            preg_match("#cid=(\w+)#", $swf_url, $cid);
            $result = '<video width="100%" height="240" controls="controls" autoplay="" preload="" src=""></video><script type="text/javascript">pptv_player("' . $cid[1] . '");</script>';
            break;
        case 'video.sina.com.cn':
            preg_match("#iid=(\d+)#", $swf_url, $iid);
            $result = "<video width='100%' height='240' controls='controls' autoplay='' preload='' src='http://edge.v.iask.com.sinastorage.com/{$iid[1]}.mp4'></video>";
            break;
        case 'sohu.com':
            if (preg_match("#com\/(\d+)#", $swf_url, $vid))
                $src = "http://hot.vrs.sohu.com/ipad{$vid[1]}.m3u8";
            if (preg_match("#id=(\d+)#", $swf_url, $vid))
                $src = "http://my.tv.sohu.com/ipad/{$vid[1]}.m3u8";
            $result = "<video width='100%' height='240' controls='controls' autoplay='' preload='' src='$src'></video>";
            break;
    }
    return $result;
}
class VideoUrlParser
{
    const SUPPORT_LIST = "/(youku\.com|tudou\.com|56\.com|ku6\.com|pps\.tv|qq\.com|pptv\.com|video\.sina\.com\.cn|sohu\.com)/";
    var $url;
    var $curl_info;
    var $curl_err;
    var $video_key;
    var $swf;
    var $img;
    var $img_thumb;
    var $success;
    function __construct($url)
    {
        $this->url     = $url;
        $this->success = 0;
        $this->parse();
    }
    function parse()
    {
        $lowerurl = strtolower($this->url);
        preg_match(self::SUPPORT_LIST, $lowerurl, $matches);
        if (!$matches) {
            $this->success = -1;
            return false;
        }
        switch ($matches[1]) {
            case 'youku.com':
                $this->_parseYouku();
                break;
            case 'tudou.com':
                $this->_parseTudou();
                break;
            case '56.com':
                $this->_parse56();
                break;
            case 'ku6.com':
                $this->_parseKu6();
                break;
            case 'pps.tv':
                $this->_parsePPS();
                break;
            case 'qq.com':
                $this->_parseQQ();
                break;
            case 'pptv.com':
                $this->_parsePPTV();
                break;
            case 'video.sina.com.cn':
                $this->_parseSina();
                break;
            case 'sohu.com':
                $this->_parseSohu();
                break;
        }
    }
    private function _parseYouku()
    {
        preg_match("#\/id\_(\w+)\.#", $this->url, $matches) || preg_match("#\/sid\/(\w+)\/#", $this->url, $matches);
        if (empty($matches)) {
            $this->success = -2;
            return false;
        }
        $this->video_key = $matches[1];
        $json_url        = "http://v.youku.com/player/getPlayList/VideoIDS/" . $this->video_key . "/timezone/+08/version/5/";
        $videoInfoJSON   = $this->curl($json_url);
        if ($videoInfoJSON === FALSE) {
            $image       = '';
            $image_thumb = '';
        } else {
            $videoInfo   = json_decode($videoInfoJSON);
            $image       = $videoInfo->data[0]->logo;
            $image_thumb = preg_replace("/com\/1/", 'com/0', $image);
        }
        $this->swf       = "http://player.youku.com/player.php/sid/" . $this->video_key . "/v.swf";
        $this->img       = $image;
        $this->img_thumb = $image_thumb;
        $this->success   = 1;
    }
    private function _parseTudou()
    {
        if (preg_match("#\/v(iew)?\/([\w\-]+)#", $this->url, $matches)) {
            $this->video_key = $matches[2];
            $swf_url         = "http://www.tudou.com/v/{$matches[2]}/";
            $this->curl($swf_url . "v.swf");
            preg_match("#iid=(\d+)#", $this->curl_info['url'], $iid);
            $iid = $iid[1];
            $swf_url .= "&iid={$iid}/v.swf";
        } elseif (preg_match("#\/l\/([\w\-]+)\/.*iid=(\d+)#i", $this->url, $matches)) {
            $iid     = $matches[2];
            $swf_url = "http://www.tudou.com/l/{$matches[1]}/&iid={$matches[2]}/v.swf";
        } elseif (preg_match("#\/listplay\/([\w\-]+)#i", $this->url, $matches)) {
            $videoInfo = $this->curl($this->url);
            preg_match("#\siid:(\d+)#", $videoInfo, $iid);
            $iid     = $iid[1];
            $swf_url = "http://www.tudou.com/l/{$matches[1]}/&iid={$iid}/v.swf";
        } elseif (preg_match("#\/a\/([\w\-]+)\/.*iid=(\d+)#i", $this->url, $matches)) {
            $iid       = $matches[2];
            $swf_url   = "http://www.tudou.com/a/{$matches[1]}/&iid={$matches[2]}/v.swf";
            $album_url = "http://www.tudou.com/albumplay/{$matches[1]}.html";
            $videoInfo = $this->curl($album_url);
            preg_match("#,aid\s?=\s?(\d+)#", $videoInfo, $aid);
            $picInfo = $this->curl("http://www.tudou.com/outplay/goto/getAlbumItems.html?aid={$aid[1]}");
            $picInfo = json_decode($picInfo);
            foreach ($picInfo->message as $i => $item) {
                if ($iid == $item->itemId) {
                    $pic = $item->picUrl;
                    break;
                }
            }
            $img = preg_replace("#com\/.*\/#", "com/", $pic);
        } elseif (preg_match("#\/albumplay\/([\w\-]+)#i", $this->url, $matches)) {
            $videoInfo = $this->curl($this->url);
            preg_match("#iid: (\d+)#", $videoInfo, $iid);
            preg_match("#,pic:\s?\"(.*)\"#", $videoInfo, $pic);
            $iid     = $iid[1];
            $swf_url = "http://www.tudou.com/a/{$matches[1]}/&iid={$iid}/v.swf";
            $img     = $pic[1];
        } else {
            $this->success = -2;
            return false;
        }
        if (!$img) {
            $iid = substr(strval((int) $iid + 1000000000), 1, 9);
            preg_match_all('#(\d{3})#', $iid, $m_iid);
            $iidEncode   = $m_iid[0][0] . '/' . $m_iid[0][1] . '/' . $m_iid[0][2];
            $image       = "http://i1.tdimg.com/{$iidEncode}/w.jpg";
            $image_thumb = "http://i1.tdimg.com/{$iidEncode}/p.jpg";
        } else {
            $image       = preg_replace("/com\/[01]/", 'com/1', $img);
            $image_thumb = preg_replace("/com\/[01]/", 'com/0', $img);
        }
        $this->swf       = $swf_url;
        $this->img       = $image;
        $this->img_thumb = $image_thumb;
        $this->success   = 1;
    }
    private function _parse56()
    {
        preg_match("#v\_(\w+)#", $this->url, $matches);
        if (empty($matches)) {
            $this->success = -2;
            return false;
        }
        $this->video_key = $matches[1];
        $json_url        = "http://vxml.56.com/json/" . $this->video_key . "/?src=out";
        $videoInfoJSON   = $this->curl($json_url);
        if ($videoInfoJSON === FALSE) {
            $image       = '';
            $image_thumb = '';
        } else {
            $videoInfo   = json_decode($videoInfoJSON);
            $image       = $videoInfo->info->bimg;
            $image_thumb = $videoInfo->info->img;
        }
        $this->swf       = "http://player.56.com/v_" . $this->video_key . ".swf";
        $this->img       = $image;
        $this->img_thumb = $image_thumb;
        $this->success   = 1;
    }
    private function _parseKu6()
    {
        if (preg_match("#(refer|show)\/([\w\-\.]+)(/v\.swf|\.html)#", $this->url, $matches) || preg_match("#special\/show_(\d+)\/([\w\-\.]+)\.html#", $this->url, $matches) || preg_match("#film\/show_(\d+)\/([\w\-\.]+)\.html#", $this->url, $matches)) {
            $this->video_key = $matches[2];
        } elseif (preg_match("#film\/index_\d+\.html#", $this->url, $matches)) {
            $videoInfo = $this->curl($this->url);
            preg_match("#vid : '([\w\-\.]+)'#", $videoInfo, $vid);
            $this->video_key = $vid[1];
        } else {
            $this->success = -2;
            return false;
        }
        $json_url      = "http://v.ku6.com/fetchVideo4Player/" . $this->video_key . ".html";
        $videoInfoJSON = $this->curl($json_url);
        if ($videoInfoJSON === FALSE) {
            $image       = '';
            $image_thumb = '';
        } else {
            $videoInfo   = json_decode($videoInfoJSON);
            $image_thumb = $videoInfo->data->picpath;
            $image       = $videoInfo->data->bigpicpath;
            if (empty($image))
                $image = preg_replace("#\/(\d)\.jpg#", '/10\\1.jpg', $image_thumb);
        }
        $this->swf       = "http://player.ku6.com/refer/" . $this->video_key . "/v.swf";
        $this->img       = $image;
        $this->img_thumb = $image_thumb;
        $this->success   = 1;
    }
    private function _parsePPS()
    {
        if (preg_match("#sid\/(\w+)#", $this->url, $matches) || preg_match("#play_(\w+)\.html#", $this->url, $matches)) {
            $this->video_key = $matches[1];
        } else {
            $this->success = -2;
            return false;
        }
        $videoInfo = $this->curl("http://ipd.pps.tv/play_" . $this->video_key . ".html");
        if (preg_match("#\"sharepic\":\"(.*)\"#U", $videoInfo, $img)) {
            $img         = stripslashes($img[1]);
            $image       = preg_replace("#\/(480_360|128_80)_pps#", '/480_360_pps', $img);
            $image_thumb = preg_replace("#\/(480_360|128_80)_pps#", '/128_80_pps', $img);
        } else {
            $image       = '';
            $image_thumb = '';
        }
        $this->swf       = "http://player.pps.tv/player/sid/" . $this->video_key . "/v.swf";
        $this->img       = $image;
        $this->img_thumb = $image_thumb;
        $this->success   = 1;
    }
    private function _parseQQ()
    {
        if (preg_match("#vid=(\w+)#", $this->url, $matches) || preg_match("#cover\/\w\/\w+\/(\w+)\.html#", $this->url, $matches) || preg_match("#page\/.*\/(\w+)\.html#", $this->url, $matches)) {
            $this->video_key = $matches[1];
        } elseif (preg_match("#cover\/\w\/\w+\.html#", $this->url)) {
            $videoInfo = $this->curl($this->url);
            preg_match("#\svid:\"(\w+)\"#", $videoInfo, $vid);
            $this->video_key = $vid[1];
        } else {
            $this->success = -2;
            return false;
        }
        $cid             = @$this->_getQQcid($this->video_key);
        $this->swf       = "http://static.video.qq.com/TPout.swf?vid=" . $this->video_key;
        $this->img       = "http://vpic.video.qq.com/{$cid}/" . $this->video_key . ".png";
        $this->img_thumb = "http://vpic.video.qq.com/{$cid}/" . $this->video_key . ".jpg";
        $this->success   = 1;
    }
    private function _getQQcid($key)
    {
        $_3 = 4294967296;
        $_5 = 10000 * 10000;
        $_6 = $key;
        $_8 = $_9 = 0;
        while ($_8 < strlen($_6)) {
            $_10 = ord(substr($_6, $_8, 1));
            $_9  = $_9 * 32 + $_9 + $_10;
            if ($_9 >= $_3)
                $_9 = fmod($_9, $_3);
            $_8 += 1;
        }
        return fmod($_9, $_5);
    }
    private function _parsePPTV()
    {
        if (preg_match("#(show|v)\/(\w+)\.#", $this->url, $matches)) {
            $this->video_key = $matches[2];
        }
        $videoInfo = $this->curl("http://v.pptv.com/show/" . $this->video_key . ".html");
        preg_match("#\"id\":(\d+),#U", $videoInfo, $cid) || preg_match("#\"channel_id\":(\d+),#U", $videoInfo, $cid);
        $cid             = $cid[1];
        $this->swf       = "http://player.pptv.com/v/" . $this->video_key . ".swf?cid={$cid}";
        $this->img       = "http://s1.pplive.cn/v/cap/{$cid}/h160.jpg";
        $this->img_thumb = "http://s1.pplive.cn/v/cap/{$cid}/h120.jpg";
        $this->success   = 1;
    }
    private function _parseSina()
    {
        if (preg_match("#vid=(\d+)#", $this->url, $matches)) {
            if (!preg_match("#iid=(\d+)#", $this->url, $iid)) {
                $this->success = '未检测到iid值,请使用网页地址进行解析,而不是使用swf地址!';
                return false;
            }
            $iid             = $iid[1];
            $this->video_key = $matches[1];
            $img_url         = "http://interface.video.sina.com.cn/interface/common/getVideoImage.php?vid=" . $this->video_key;
            $imgInfo         = $this->curl($img_url);
        } else {
            $videoInfo = $this->curl($this->url);
            preg_match("#\svid:'(\d+)['\|]#", $videoInfo, $vid);
            preg_match("#\sipad_vid:'(\d+)'#", $videoInfo, $iid);
            preg_match("#\spic:\s?'(.*)'#", $videoInfo, $pic);
            $this->video_key = $vid[1];
            $iid             = $iid[1];
            $imgInfo         = $pic[1];
        }
        preg_match("#(imgurl=)?(.*)_\d\.jpg#", $imgInfo, $img);
        if (!$imgInfo) {
            $image       = '';
            $image_thumb = '';
        } else {
            $image       = $img[2] . "_2.jpg";
            $image_thumb = $img[2] . "_1.jpg";
        }
        $this->swf       = "http://you.video.sina.com.cn/api/sinawebApi/outplayrefer.php/vid=" . $this->video_key . "_0/s.swf?iid={$iid}";
        $this->img       = $image;
        $this->img_thumb = $image_thumb;
        $this->success   = 1;
    }
    private function _parseSohu()
    {
        if (preg_match("#share\.vrs\.sohu\.com\/(\d+)#", $this->url, $matches)) {
            $this->video_key = $matches[1];
            $videoInfoJSON   = $this->curl('http://hot.vrs.sohu.com/vrs_flash.action?vid=' . $this->video_key);
            $videoInfo       = json_decode($videoInfoJSON);
            $img             = $videoInfo->data->coverImg;
            $this->swf       = "http://share.vrs.sohu.com/" . $this->video_key . "/v.swf";
        } elseif (preg_match("#http:\/\/tv\.sohu\.com.*html#", $this->url, $matches)) {
            $videoInfo = $this->curl($this->url);
            preg_match("#\svid\s?=\s?\"(\d+)\"#", $videoInfo, $vid);
            preg_match("#\scover\s?=\s?\"(.*)\"#", $videoInfo, $pic);
            $this->video_key = $vid[1];
            $img             = $pic[1];
            $this->swf       = "http://share.vrs.sohu.com/" . $this->video_key . "/v.swf";
        } elseif (preg_match("#share\.vrs\.sohu\.com\/my\/.*id=(\d+)#", $this->url, $matches) || preg_match("#my\.tv\.sohu\.com\/.*\/(\d+)\.shtml#", $this->url, $matches)) {
            if (preg_match("#\/user\/detail\/#", $this->url)) {
                $videoInfo = $this->curl($this->url);
                preg_match("#\svid\s?=\s?\"(\d+)\"#", $videoInfo, $matches);
            }
            $this->video_key = $matches[1];
            $videoInfoJSON   = $this->curl('http://my.tv.sohu.com/play/videonew.do?vid=' . $this->video_key);
            preg_match("#\"coverImg\":\"(.*)\"#U", $videoInfoJSON, $img);
            $img       = stripslashes($img[1]);
            $this->swf = "http://share.vrs.sohu.com/my/v.swf&id=" . $this->video_key;
        } else {
            $this->success = -2;
            return false;
        }
        if (preg_match("/vrsb?(\d+)/i", $img)) {
            $image       = preg_replace("/vrsb?(\d+)/i", "vrs\\1", $img);
            $image_thumb = preg_replace("/vrsb?(\d+)/i", "vrsb\\1", $img);
        } elseif (preg_match("/_S_[sb]/i", $img)) {
            $image       = preg_replace("#(\/[0-9a-z\-]+_\d+)_S_[bs]#i", "\\1_S_b", $img);
            $image_thumb = preg_replace("#(\/[0-9a-z\-]+_\d+)_S_[bs]#i", "\\1_S_s", $img);
        } else {
            $image       = preg_replace("#_(\d+)b?\.#i", "_\\1b.", $img);
            $image_thumb = preg_replace("#_(\d+)b?\.#i", "_\\1.", $img);
        }
        $this->img       = $image;
        $this->img_thumb = $image_thumb;
        $this->success   = 1;
    }
    static function checkImage($src, $thumb = false)
    {
        if (!$src) {
            if (!$thumb)
                echo get_bloginfo('template_url') . '/ui/shibai.jpg';
            else
                get_bloginfo('template_url') . '/ui/shibai_thumb.jpg';
        } else {
            echo $src;
        }
    }
    static function checkVideo($url)
    {
        global $player56, $union56id;
        preg_match(self::SUPPORT_LIST, $url, $matches);
        if ($matches[1] == '56.com') {
            preg_match("#v\_(\w+)#", $url, $key);
            if (!empty($union56id)) {
                echo "{$url}/{$union56id}.swf";
            } else {
                echo $player56 . "?vid=" . $key[1];
            }
            return null;
        } elseif ($matches[1] == 'pps.tv') {
            preg_match("#sid\/(\w+)#", $url, $key);
            echo "http://player.pps.tv/static/vs/v1.0.0/v/swf/flvplay_s.swf?url_key=" . $key[1];
        } else {
            echo $url;
        }
    }
    function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result          = curl_exec($ch);
        $this->curl_info = curl_getinfo($ch);
        $this->curl_err  = curl_error($ch);
        curl_close($ch);
        return $result;
    }
    function curl_redir_exec($ch, $debug = "")
    {
        static $curl_loops = 0;
        static $curl_max_loops = 20;
        if ($curl_loops++ >= $curl_max_loops) {
            $curl_loops = 0;
            return FALSE;
        }
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data   = curl_exec($ch);
        $debbbb = $data;
        list($header, $data) = explode("\n\n", $data, 2);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code == 301 || $http_code == 302) {
            $matches = array();
            preg_match('/Location:(.*?)\n/', $header, $matches);
            $url = @parse_url(trim(array_pop($matches)));
            if (!$url) {
                $curl_loops = 0;
                return $data;
            }
            $last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
            $new_url  = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query'] ? '?' . $url['query'] : '');
            curl_setopt($ch, CURLOPT_URL, $new_url);
            return curl_redir_exec($ch);
        } else {
            $curl_loops = 0;
            return $debbbb;
        }
    }
}