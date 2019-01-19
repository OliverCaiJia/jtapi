<?php

namespace App\Constants;

class ChannelConstant extends AppConstant
{
    // 渠道删除
    const SAAS_CHANNEL_DELETED_TRUE = 1;
    // 渠道正常
    const SAAS_CHANNEL_DELETED_FALSE = 0;

    // 渠道类型-非定向
    const SAAS_CHANNEL_TYPE_UNDIRECTED = 2;
    // 渠道类型-定向
    const SAAS_CHANNEL_TYPE_DIRECTED = 1;
    // 渠道类型-其他
    const SAAS_CHANNEL_TYPE_OTHERS = 0;

    const SAAS_CHANNEL_TYPE_MAP = [
        self::SAAS_CHANNEL_TYPE_UNDIRECTED => '非定向',
        self::SAAS_CHANNEL_TYPE_DIRECTED => '定向',
        self::SAAS_CHANNEL_TYPE_OTHERS => '其他'
    ];

    // 渠道URL前缀
    const CHANNEL_URL_PREFIX='https://data.jt.com';
}
