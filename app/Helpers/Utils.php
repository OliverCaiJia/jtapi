<?php

namespace App\Helpers;

class Utils
{

    /**
     * 获取浏览器名称
     * @return string
     */
    public static function getBrowser()
    {
        $agent = $_SERVER["HTTP_USER_AGENT"];
        //ie11判断
        if (strpos($agent, 'MSIE') !== false || strpos($agent, 'rv:11.0')) {
            return "ie";
        } else if (strpos($agent, 'Firefox') !== false) {
            return "firefox";
        } else if (strpos($agent, 'Chrome') !== false) {
            return "chrome";
        } else if (strpos($agent, 'Opera') !== false) {
            return 'opera';
        } else if ((strpos($agent, 'Chrome') == false) && strpos($agent, 'Safari') !== false) {
            return 'safari';
        } else if (strpos($agent, 'MicroMessenger') !== false) {
            return 'wechat';
        } else {
            return 'unknown';
        }
    }

    /**
     * 获取浏览器版本
     * @return string
     */
    public static function getBrowserVer()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/MSIE\s(\d+)\..*/i', $agent, $regs)) {
            return $regs[1];
        } elseif (preg_match('/FireFox\/(\d+)\..*/i', $agent, $regs)) {
            return $regs[1];
        } elseif (preg_match('/Opera[\s|\/](\d+)\..*/i', $agent, $regs)) {
            return $regs[1];
        } elseif (preg_match('/Chrome\/(\d+)\..*/i', $agent, $regs)) {
            return $regs[1];
        } elseif ((strpos($agent, 'Chrome') == false) && preg_match('/Safari\/(\d+)\..*$/i', $agent, $regs)) {
            return $regs[1];
        } elseif (preg_match('/MicroMessenger\/(\d+)\..*/i', $agent, $regs)) {
            return $regs[1];
        } else {
            return 'unknown';
        }
    }

    /**
     * 判断是否微信浏览器
     * @return type
     */
    public static function isWechatBrowser()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        return (strpos($user_agent, "MicroMessenger") !== false);
    }

    /**
     * 判断是iOS
     * @return type
     */
    public static function isiOS()
    {
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if (strpos($user_agent, 'iPhone') || strpos($user_agent, 'iPad') || strpos($user_agent, 'iPod')) {
            return true;
        }
        return false;
    }

    /**
     * 判断是iOS
     * @return type
     */
    public static function isMAPI()
    {
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if (strpos($user_agent, 'mapi')) {
            return true;
        }
        return false;
    }

    /**
     * 判断是Android
     * @return type
     */
    public static function isAndroid()
    {
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if (strpos($user_agent, 'Android')) {
            return true;
        }
        return false;
    }

    /**
     * 获取访问域名
     * @return type
     */
    public static function getHostUrl($request_url = null)
    {
        $request_url = empty($request_url) ? 'http://localhost' : $request_url;
        $parsed_url = parse_url($request_url);
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        return "$scheme$host$port";
    }

    /**
     * 获取IP地址
     */
    public static function ipAddress($type = 0)
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
            $cip = $_SERVER["REMOTE_ADDR"];
        } else {
            $cip = "127.0.0.1";
        }
        return $cip;
    }

    /**
     * @abstract 获取html代码中的img的src
     * @return array
     */
    public static function getHtmlImageSrc($html)
    {
        if (!$html)
            return array();

        $preg_partern = '/<img.+?src=\"?(.+?\.(jpg|gif|bmp|bnp|png))\"?.+?>/i';
        $match = array();
        preg_match_all($preg_partern, $html, $match);
        return $match[1];
    }

    /**
     * @abstract 替换html代码里面的img标签
     * @param type $html
     * @param type $replace default ''
     * @return string
     */
    public static function replaceHtmlImage($html, $replace = '')
    {
        if (!$html)
            return '';

        $preg_partern = '/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|png))\"?.+?>/i';
        return preg_replace($preg_partern, $replace, $html);
    }

    /**
     * 生成随机密码
     */
    public static function createPassword($pw_length = 8)
    {
        $randpwd = '';
        for ($i = 0; $i < $pw_length; $i++)
        {
            $randpwd .= chr(mt_rand(48, 122));
        }
        return $randpwd;
    }


    /**
     * 去除特殊符号
     */
    public static function removeSpe($string = "")
    {
        $string = htmlspecialchars_decode($string);
        $search = array("\\\"");
        $replace = array("\"");
        return str_replace($search, $replace, $string);
    }

    /**
     * 删除HTML标签
     */
    public static function removeHTML($string = "")
    {
        $string = html_entity_decode($string, ENT_COMPAT, 'UTF-8');
        $string = stripslashes($string);
        $string = strip_tags($string);
        $search = array(" ", "　", "\t", "\n", "\r");
        $replace = array("", "", "", "", "");
        return str_replace($search, $replace, $string);
    }

    /**
     * @param $param
     * @return string
     * 去除字符串中的空格
     */
    public static function removeSpace($param)
    {
        return isset($param) ? str_replace(" ", "", $param) : '';
    }

    /**
     * @param $param
     * @return mixed|string
     * 去除 空格 - +86
     */
    public static function removeSpaces($param)
    {
        return isset($param) ? preg_replace('/[\s-]*/', '', $param) : '';
    }

}
