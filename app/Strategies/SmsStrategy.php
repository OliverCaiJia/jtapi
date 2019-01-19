<?php

namespace App\Strategies;

use App\Helpers\DateUtils;
use App\Models\Strategies\CacheStrategy;
use Cache;
use Carbon\Carbon;

class SmsStrategy extends AppStrategy
{
    /**
     * 验证短信1分钟之内不能重复发送
     *
     * @param $mobile
     * @param $type
     *
     * @return bool
     */
    public static function checkCodeExistenceTime($mobile, $type)
    {
        $codeArr = SmsStrategy::getCodeKeyAndSignKey($mobile, $type);
        if (CacheStrategy::existValueFromCache($codeArr['codeKey'])) {
            return false;
        }

        return true;
    }

    /**
     * 获取 codeKey signKey
     * phone普通手机号，forgetpwd忘记密码，password修改密码，updatephone修改手机号，register注册
     *
     * @param string $mobile
     * @param string $type
     *
     * @return mixed
     */
    public static function getCodeKeyAndSignKey($mobile = '', $type = '')
    {
        switch ($type) {
            case 'phone':
                //手机号
                $codeArr['codeKey'] = 'mobile_code_' . $mobile;
                $codeArr['signKey'] = 'mobile_random_' . $mobile;
                break;
            case 'forgetpwd':
                //忘记密码
                $codeArr['codeKey'] = 'forget_password_code_' . $mobile;
                $codeArr['signKey'] = 'forget_password_random_' . $mobile;
                break;
            case 'password':
                //修改密码
                $codeArr['codeKey'] = 'password_code_' . $mobile;
                $codeArr['signKey'] = 'password_random_' . $mobile;
                break;
            case 'updatephone':
                //修改手机号
                $codeArr['codeKey'] = 'update_mobile_code_' . $mobile;
                $codeArr['signKey'] = 'update_mobile_random_' . $mobile;
                break;
            case 'register':
                //注册
                $codeArr['codeKey'] = 'login_phone_code_' . $mobile;
                $codeArr['signKey'] = 'login_random_' . $mobile;
                break;
            default:
                //修改手机号
                $codeArr['codeKey'] = 'mobile_code_' . $mobile;
                $codeArr['signKey'] = 'mobile_random_' . $mobile;
                break;
        }
        return $codeArr;
    }


    /**
     * 把短信验证码存储在cache中（过期时间为60s）
     *
     * @param     $key
     * @param     $value
     * @param int $sec
     *
     * @return bool
     */
    public static function putSmsCodeToCache($key, $value, $sec = 100)
    {
        Cache::put($key, $value, Carbon::now()->second($sec));

        return true;
    }

    /**
     * 检查验证码有效性
     *
     * @param array $params
     *
     * @return bool
     */
    public static function checkCode($params)
    {
        $key = $params['mobile']. $params['channel_id'];
        $codeCacheKey = 'mobile_code_' . $key;
        $signCacheKey = 'mobile_random_' . $key;

        if (Cache::has($codeCacheKey) && Cache::has($signCacheKey)) {
            if (Cache::get($codeCacheKey) == $params['code'] && Cache::get($signCacheKey) == $params['sign']) {
                return true;
            }
        }

        return false;
    }
}
