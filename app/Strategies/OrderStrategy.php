<?php

namespace App\Strategies;

use App\Constants\UserOrderConstant;
use App\Models\Factory\OrderFactory;

class OrderStrategy extends AppStrategy
{
    /**
     * 通过状态值获取对应前端显示文本
     *
     * @param $status
     *
     * @return string
     */
    public static function getStatusText($status)
    {
        if ($status == UserOrderConstant::PENDING) {
            return '审核中';
        } elseif ($status == UserOrderConstant::PASSED) {
            return '已通过';
        } elseif ($status == UserOrderConstant::REFUSED) {
            return '已拒绝';
        }

        return '审核中';
    }

    /**
     * 获取订单审核结果
     *
     * @param $order
     *
     * @return array
     */
    public static function getOrderResult($order)
    {
        if ($order->status == UserOrderConstant::PASSED) {
            return [
                'status' => 'success_audit',
                'content' => [
                    'step1' => ['text' => '提交资料', 'time' => $order->created_at],
                    'step2' => ['text' => '审核中', 'time' => $order->created_at],
                    'step3' => ['text' => '审核结果'],
                    'step4' => ['text' => '等待放款']
                ],
            ];
        } elseif ($order->status == UserOrderConstant::REFUSED) {
            return [
                'status' => 'fail_audit',
                'content' => [
                    'step1' => ['text' => '提交资料', 'time' => $order->created_at],
                    'step2' => ['text' => '审核中', 'time' => $order->created_at],
                    'step3' => ['text' => '审核结果'],
                ],
            ];
        }

        return [
            'status' => 'no_audit',
            'content' => [
                'step1' => ['text' => '提交资料', 'time' => $order->created_at],
                'step2' => ['text' => '审核中', 'time' => $order->created_at],
                'step3' => ['text' => '审核结果']
            ],
        ];
    }

    /**
     * 获取订单审核结果
     * @param $order
     * @param $status
     * @return array
     */
    public static function getOrderResultV11($order, $status)
    {
        if ($status == UserOrderConstant::PASSED) {
            return [
                'status' => 'success_audit',
                'content' => [
                    'step1' => ['text' => '提交资料', 'time' => $order->created_at],
                    'step2' => ['text' => '审核中', 'time' => $order->created_at],
                    'step3' => ['text' => '审核结果'],
                    'step4' => ['text' => '等待放款']
                ],
            ];
        } elseif ($status == UserOrderConstant::REFUSED) {
            return [
                'status' => 'fail_audit',
                'content' => [
                    'step1' => ['text' => '提交资料', 'time' => $order->created_at],
                    'step2' => ['text' => '审核中', 'time' => $order->created_at],
                    'step3' => ['text' => '审核结果'],
                ],
            ];
        }

        return [
            'status' => 'no_audit',
            'content' => [
                'step1' => ['text' => '提交资料', 'time' => $order->created_at],
                'step2' => ['text' => '审核中', 'time' => $order->created_at],
                'step3' => ['text' => '审核结果']
            ],
        ];
    }

    /**
     * 构建运营商三方请求链接
     *
     * @param int $userId
     * @param string $loginParams
     *
     * @return string
     */
    public static function generateCarrierUrl($userId, $loginParams)
    {
        $apiKey = config('service.moxie.api_key');

        return 'https://api.51datakey.com/h5/importV3/index.html#/carrier?apiKey=' .
            $apiKey . '&userId=' . $userId . '&loginParams=' . $loginParams. '&backUrl=';
    }

    /**
     * 获取是否要跳转到结果页
     * @param $channelId
     * @param $userId
     * @return bool
     */
    public static function getIsDirectToResult($channelId, $userId)
    {
        // 获取最近一次订单
        $order = OrderFactory::getLastOrderByChannelIdAndUserId($channelId, $userId);
        if (empty($order)) {
            return false;
        }

        // 获取此订单的处理状态
        $finalStatus = OrderStrategy::getOrderStatusV11($order->id);
        if ($finalStatus != UserOrderConstant::PENDING) {
            return false;
        }

        // 判断订单是否超过７天
        if ($order->created_at < date("Y-m-d　H:i:s", strtotime("-7 days"))) {
            return false;
        }

        return true;
    }

    /**
     * 获取展示用的订单状态
     * @param $statusArray
     * @return int
     */
    public static function getDisplayStatus($statusArray)
    {
        if (!is_array($statusArray)) {
            $statusArray = $statusArray->toArray();
        }
        if (in_array(UserOrderConstant::PASSED, $statusArray)) {
            return UserOrderConstant::PASSED;
        } elseif (in_array(UserOrderConstant::REFUSED, $statusArray)) {
            return UserOrderConstant::REFUSED;
        }
        return UserOrderConstant::PENDING;
    }

    /**
     * 根据order_id获取订单审批状态v1.1
     * @param $orderId
     * @return int
     */
    public static function getOrderStatusV11($orderId)
    {
        $status = OrderFactory::getOrderStatus($orderId);
        return OrderStrategy::getDisplayStatus($status);
    }
}
