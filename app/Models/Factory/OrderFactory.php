<?php
namespace App\Models\Factory;

use App\Constants\UserOrderConstant;
use App\Models\AbsBaseModel;
use App\Models\Orm\SaasOrderSaas;
use App\Models\Orm\UserOrder;

class OrderFactory extends AbsBaseModel
{
    /**
     * @param $channelId
     * @param $userId
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public static function getReportIdByChannelIdAndUserId($channelId, $userId)
    {
        $order = UserOrder::where('channel_id', $channelId)
            ->where('user_id', $userId)
            ->where('status', UserOrderConstant::PENDING)
            ->select('user_report_id')
            ->first();

        return $order ? $order->user_report_id : '';
    }

    /**
     * 获取订单通过 channel_id 和 user_id
     *
     * @param     $channelId
     * @param     $userId
     * @param int $pageSize
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getOrdersByChannelIdAndUserId($channelId, $userId, $pageSize = 10)
    {
        return UserOrder::where('channel_id', $channelId)
            ->where('user_id', $userId)
            ->select('id', 'created_at', 'user_report_id')
            ->paginate($pageSize);
    }

    /**
     * 获取最近的一笔待处理订单，通过 channel_id 和 user_id
     *
     * @param $channelId
     * @param $userId
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public static function getPendingByChannelIdAndUserId($channelId, $userId)
    {
        return UserOrder::where('channel_id', $channelId)
            ->where('user_id', $userId)
            ->where('status', UserOrderConstant::PENDING)
            ->select('id')
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * 获取最近的一笔订单，通过 channel_id 和 user_id
     *
     * @param $channelId
     * @param $userId
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public static function getOrderByChannelIdAndUserId($channelId, $userId)
    {
        return UserOrder::where('channel_id', $channelId)
            ->where('user_id', $userId)
            ->where('status', UserOrderConstant::PENDING)
            ->select('status', 'created_at')
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * 根据渠道号和用户ID获取最新一条订单
     * @param $channelId
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public static function getLastOrderByChannelIdAndUserId($channelId, $userId)
    {
        return UserOrder::where(['channel_id' => $channelId, 'user_id' => $userId])
            ->orderByDesc('id')->first();
    }

    /**
     * 根据订单ID获取订单处理状态
     * @param $orderId
     * @return \Illuminate\Support\Collection
     */
    public static function getOrderStatus($orderId)
    {
        return SaasOrderSaas::where(['order_id' => $orderId])->pluck('status');
    }
}
