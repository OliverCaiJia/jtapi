<?php

namespace App\Models\Factory;

use App\Models\AbsModelFactory;
use App\Models\Orm\AccountMessage;
use App\Models\Orm\AccountMessageConfig;
use App\Models\Orm\SystemConfig;

class SmsFactory extends AbsModelFactory
{
    /**
     * 根据nid查找系统配置中的短信通道的value
     *
     * @param string $config
     *
     * @return mixed
     */
    public static function getSmsSystemConfig($config = 'con_sms_config')
    {
        $systemConfig = SystemConfig::select('value')
            ->where('nid', $config)
            ->where('status', '1')
            ->first();

        return $systemConfig ? $systemConfig->value : '';
    }

    /** 获取大汉三通的配置
     *
     * @param string $message_nid
     *
     * @return array
     */
    public static function getShadowSmsConfigByNid($message_nid = 'dahansantong_shadow_jieqian360')
    {
        $message = AccountMessage::select('nid', 'username', 'password', 'id_code', 'url')
            ->where('nid', $message_nid)
            ->first();

        return $message ? $message->toArray() : [];
    }
}
