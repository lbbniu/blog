<?php if (!defined('WP_ZYWXAPP_BASE')) exit();
class ZywxappHelpers
{
    public static function makeShortString($str, $len, $style = '...')
    {
    	if (self::getStrLength($str) > $len ){  
	       $str = mb_substr($str, 0, $len);
	       $str .= $style;
	    }
	    return $str;
    }
    
    /** 
	 * PHP获取字符串中英文混合长度  
	 * @param $str string 字符串 
 	 * @return 返回长度，1中文=1位，1英文=1位 
	 */  
	public static function getStrLength($str)
	{  
		// 将字符串分解为单元 
		preg_match_all("/./us", $str, $match); 
		// 返回单元个数 
		return count($match[0]);    
	} 
    
    /**
     *  字符串截取，支持中文和其它编码
     *
     * function_description
     *
     * @copyright  2011-2012 Bei Jing Zheng Yi Wireless
     * @since      File available since Release 1.0 -- 2012-6-7 下午03:56:40
     * @author mxg<jiemack@163.com>
     * @param string $str 需要转换的字符串
 	 * @param string $start 开始位置
 	 * @param string $length 截取长度
 	 * @param string $charset 编码格式
 	 * @param string $suffix 截断显示字符
 	 * @return string
     */
	public static function makeSubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
	{
		if (self::getStrLength($str) > $length ){  
			if ( function_exists("mb_substr") ) {
				$slice = mb_substr($str, $start, $length, $charset);
			} elseif ( function_exists('iconv_substr') ) {
				$slice = iconv_substr($str,$start,$length,$charset);
			} else {
				$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
				$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
				$re['gbk']	  = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
				$re['big5']	  = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
				preg_match_all($re[$charset], $str, $match);
				$slice = join("",array_slice($match[0], $start, $length));
			}
			if($suffix && $str != $slice){ 
				$slice .= "...";
			}
	    } else {
	    	$slice = $str;
	    }
		return $slice;
	}
     
}