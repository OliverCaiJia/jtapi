<?php

namespace App\Models\Strategies;

use App\Models\AbsModelFactory;
use Carbon\Carbon;
use Cache;

class CacheStrategy extends AbsModelFactory
{
    /**
     * cache存储  7天
     *
     * @param $key
     * @param $value
     */
    public static function putValueToCache($key, $value)
    {
        return Cache::put($key, $value, Carbon::now()->second(7 * 24 * 3600));
    }

    /**
     * 永久存储
     *
     * @param $key
     * @param $value
     */
    public static function putValueToCacheForever($key, $value)
    {
        return Cache::forever($key, $value, Carbon::now());
    }

    /**
     * 存储7200秒
     *
     * @param $key
     * @param $value
     */
    public static function putValueToCacheTwoMinutes($key, $value)
    {
        return Cache::put($key, $value, Carbon::now()->second(7200));
    }

    /**
     * 从cache 读取数据
     *
     * @param $key
     *
     * @return mixed
     */
    public static function getValueFromCache($key)
    {
        return Cache::get($key);
    }

    /**
     * cache 中数据是否存在
     *
     * @param $key
     *
     * @return bool
     */
    public static function existValueFromCache($key)
    {
        return Cache::get($key) ? true : false;
    }
}
