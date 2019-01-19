<?php

namespace App\Strategies;

use App\Constants\UserOrderConstant;
use App\Helpers\DateUtils;
use App\Helpers\Generator\TokenGenerator;
use App\Helpers\UserAgent;
use App\Helpers\Utils;
use App\Models\Factory\ChannelFactory;
use App\Models\Factory\UserFactory;
use App\Models\Factory\UserReportFactory;
use Carbon\Carbon;

/**
 * 用户公共策略
 *
 * Class UserStrategy
 *
 * @package App\Strategies
 */
class UserStrategy extends AppStrategy
{
    /**
     * @desc 生成随机字符串
     *
     * @param        $length
     * @param string $format
     *
     * @return null|string
     */
    public static function getRandChar($length, $format = 'ALL')
    {
        $str = null;

        switch ($format) {
            case 'ALL':
                $strPol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
            case 'NC':
                $strPol = '0123456789abcdefghijklmnopqrstuvwxyz';
                break;
            case 'CHAR':
                $strPol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case 'NUMBER':
                $strPol = '0123456789' . time() . mt_rand(100, 1000000);
                break;
            default:
                $strPol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        }
        $max = strlen($strPol) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[mt_rand(0, $max)]; //rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }

    /**
     * @desc
     * 判断用户使用的終端類型
     *
     * "id"    "nid"    "name"    "status"    "remark"    "product_type"
     * "1"    "ios_native"    "iOS原生"    "1"    "iOS原生"    "jietiaozhijia"
     * "2"    "android_native"    "Android原生"    "1"    "Android原生"    "jietiaozhijia"
     * "3"    "h5_web"    "H5.Wechat"    "1"    "H5"    "jietiaozhijia"
     * "4"    "event_landing"    "Landing"    "1"    "Landing浏览器"    "jietiaozhijia"
     * "5"    "ios_web"    "ios_web"    "1"    "iOS浏览器"    "jietiaozhijia"
     * "6"    "android_web"    "android_web"    "1"    "Android浏览器"    "jietiaozhijia"
     * "7"    "pc_web"    "pc_web"    "1"    "PC"    "jietiaozhijia"
     * "8"    "wechat"    "wechat"    "1"    "微信"    "jietiaozhijia"
     */
    public static function version()
    {
        $user_agent = UserAgent::i()->getUserAgent();
        $version = 3; //M
        if ($user_agent) {
            if (strpos($user_agent, 'iPhone') || strpos($user_agent, 'iPad') || strpos($user_agent, 'iPod')) {
                $version = 1;
            } elseif (strpos($user_agent, 'Android')) {
                $version = 2;
            } elseif (strpos($user_agent, 'MicroMessenger')) {
                $version = 8;
            }
        }

        return $version;
    }

    /**
     * 创建用户
     *
     * @param $params
     *
     * @return $this|bool|\Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public static function createUser($params)
    {
        $name = !empty($params['name']) ? trim($params['name']) : 'jt' . UserStrategy::getRandChar(8, 'NUMBER');
        $version = isset($params['version']) ? intval($params['version']) : UserStrategy::version();

        $userData = [
            'name' => $name,
            'account_name' => $params['mobile'],
            'mobile' => $params['mobile'],
            'version' => $version,
            'accessToken' => TokenGenerator::generateToken(),
            'update_ip' => Utils::ipAddress(),
            'last_login_time' => Carbon::now(),
            'last_login_ip' => Utils::ipAddress(),
        ];
        return UserFactory::store($userData);
    }

    /**
     * 创建用户报告
     * @param $params
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public static function createReport($params)
    {
        $reportData = [
            'channel_id' => ChannelFactory::getIdByHashId($params['channel_id']),
            'user_id' => $params['user_id'],
            'status' => UserOrderConstant::USER_REPORT_STATUS_REGISTERED
        ];

        return UserReportFactory::create($reportData);
    }
}
