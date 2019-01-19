<?php

namespace App\Models\Factory;

use App\Helpers\Generator\TokenGenerator;
use App\Helpers\Utils;
use App\Models\Orm\UserAuth;
use App\Models\AbsModelFactory;
use Carbon\Carbon;

class UserFactory extends AbsModelFactory
{
    /**
     * 设置用户access token
     *
     * @param $userId
     *
     * @return mixed
     * @throws \Exception
     */
    public static function setUserToken($userId)
    {
        return UserAuth::where('id', $userId)
            ->update(['accessToken' => TokenGenerator::generateToken()]);
    }

    /**
     * 根据手机号码获取用户
     *
     * @param $mobile
     *
     * @return mixed|static
     */
    public static function getByMobile($mobile)
    {
        return UserAuth::where('mobile', $mobile)
            ->where('is_import', 0)
            ->first();
    }

    /**
     * 根据主键ID获取用户信息
     *
     * @param $id
     *
     * @return mixed|static
     */
    public static function getUserInfoById($id)
    {
        return UserAuth::select('name', 'mobile', 'accessToken', 'account_name')
            ->find($id);
    }

    /**
     * 根据手机号码更新用户信息
     *
     * @param $mobile
     * @param $updateData
     *
     * @return bool
     */
    public static function updateByMobile($mobile, $updateData)
    {
        return UserAuth::where('mobile', $mobile)->update($updateData);
    }

    /**
     * 保存用户信息
     *
     * @param $params
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public static function store($params)
    {
        return UserAuth::create($params);
    }

    /**
     * 更新用户最后登录时间和ip
     *
     * @param $userId
     *
     * @return bool
     */
    public static function updateLoginTime($userId)
    {
        return UserAuth::where('id', $userId)->update([
            'last_login_time' => Carbon::now(),
            'last_login_ip' => Utils::ipAddress(),
        ]);
    }

    /**
     * 更新用户最后登录时间, ip, token
     *
     * @param $userId
     *
     * @return bool
     * @throws \Exception
     */
    public static function updateLoginTimeIpAndToken($userId)
    {
        return UserAuth::where('id', $userId)->update([
            'last_login_time' => Carbon::now(),
            'last_login_ip' => Utils::ipAddress(),
            'accessToken' => TokenGenerator::generateToken()
        ]);
    }
}
