<?php

namespace App\Services;

/**
 * 外部Http Service服务调用
 */
class AppService
{

    /**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        date_default_timezone_set('Asia/Shanghai'); //时区配置
    }

    private static $serve;

    public static function o($config = [])
    {
        if (!(self::$serve instanceof static)) {
            self::$serve = new static($config);
        }

        return self::$serve;
    }
}
