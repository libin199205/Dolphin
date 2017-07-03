<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Db;
// 应用公共文件


if (!function_exists('is_signin'))
{
	/**
	 * 判断是否登录
	 * @return mixed
	 */
	function is_signin()
	{
		return model('lib/user')->isLogin();
	}
}


if (!function_exists('get_client_ip'))
{
	/**
     * 获取客户端IP地址
     * @param int $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param bool $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
	function get_client_ip($type = 0, $adv = false) {
		$type 		=	$type ? 1 : 0;
		static $ip 	=	NULL;
		if ($ip !== NULL) return $ip[$type];
		if($adv){
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				$pos    =   array_search('unknown',$arr);
				if(false !== $pos) unset($arr[$pos]);
				$ip     =   trim($arr[0]);
			}elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$ip     =   $_SERVER['HTTP_CLIENT_IP'];
			}elseif (isset($_SERVER['REMOTE_ADDR'])) {
				$ip     =   $_SERVER['REMOTE_ADDR'];
			}
		}elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		// IP地址合法验证
		$long = sprintf("%u",ip2long($ip));
		$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
		return $ip[$type];
	}
}	


if (!function_exists('get_avatar'))
{
	/**
     * 获取用户头像路径
     * @param int $id 附件id
     * @author 蔡伟明 <314013107@qq.com>
     * @return string
     */
    function get_avatar($id = 0)
    {
        $path = Db::name('user')->where('id',$id)->field('avatar')->find();
        if ($path['avatar']=='') {
            return PUBLIC_PATH.'/static/lib/img/avatars/avatar10.jpg';
        }
        return PUBLIC_PATH.'/upload'.$path['avatar'];
    }
}


if(!function_exists('cutstr_html'))
{
	/**
	 * 去除html所有标签和js,css,以及截取文字长度
	 * @param $string  字符串
	 * @param $sublen  截取长度
	 */
	function cutstr_html($string, $sublen){
		$string = strip_tags($string);
		$string = preg_replace ('/\n/is', '', $string);
		$string = preg_replace ('/ |　/is', '', $string);
		$string = preg_replace ('/&nbsp;/is', '', $string);

		preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $t_string);
		if(count($t_string[0]) - 0 > $sublen) $string = join('', array_slice($t_string[0], 0, $sublen))."…";
		else $string = join('', array_slice($t_string[0], 0, $sublen));

		return $string;
	}
}

