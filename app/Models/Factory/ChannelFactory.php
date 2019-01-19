<?php

namespace App\Models\Factory;

use App\Models\Orm\SaasChannel;
use App\Models\AbsModelFactory;

class ChannelFactory extends AbsModelFactory
{
    /**
     * 通过渠道id获取渠道信息
     * @param $id
     * @return array
     */
    public static function getChannelInfoById($id)
    {
        $channel = SaasChannel::where('id', $id)->first();

        return $channel ? $channel->toArray() : [];
    }

    /**
     * 通过 hash_id 获取 channel id
     *
     * @param $hashId
     *
     * @return mixed|string
     */
    public static function getIdByHashId($hashId)
    {
        $channel = SaasChannel::where('hash_id', $hashId)
            ->where('is_deleted', 0)
            ->select('id')
            ->first();

        return $channel ? $channel->id : '';
    }

    /**
     * 通过 hash_id 获取一个有效的channel
     *
     * @param string $hashId
     *
     * @return string
     */
    public static function getChannelInfoByHashId($hashId)
    {
        $channel = SaasChannel::where('hash_id', $hashId)
            ->where('is_deleted', 0)
            ->select('id', 'name', 'borrowing_balance', 'repayment_method', 'cycle', 'picture', 'type', 'product_name')
            ->first();

        return $channel ?: '';
    }
}
