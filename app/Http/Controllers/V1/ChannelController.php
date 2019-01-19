<?php

namespace App\Http\Controllers\V1;

use App\Constants\ChannelConstant;
use App\Helpers\RestResponseFactory;
use App\Helpers\RestUtils;
use App\Models\Factory\ChannelFactory;
use App\Models\Factory\SaasAccountFactory;
use App\Models\Factory\SaasAuthFactory;
use App\Models\Orm\SaasChannelSaas;
use App\Strategies\ChannelStrategy;

class ChannelController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/v1/channels/{channel_id}",
     *     summary="获取渠道信息",
     *     operationId="get-channel-info",
     *     description="获取渠道信息",
     *     tags={"渠道"},
     *     @SWG\Parameter(
     *         name="X-Sign",
     *         in="header",
     *         required=true,
     *         type="string",
     *         description="9a71d0ae5df7ab153eb1c6d3698dfbdd"
     *     ),
     *     @SWG\Parameter(
     *         name="channel_id",
     *         in="path",
     *         required=true,
     *         type="string",
     *         description="vsco2H"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Operation succeeds.",
     *         @SWG\Schema(
     *             required={"code", "message", "data", "error_code", "error_message", "time"},
     *             @SWG\Property(property="code", type="integer", example=200),
     *             @SWG\Property(property="message", type="string", example="OK"),
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 @SWG\Property(property="name", type="string", example="超有钱", description="title"),
     *                 @SWG\Property(property="borrowing_balance", type="integer", example=12, description="借款金额"),
     *                 @SWG\Property(property="repayment_method", type="string", example="一次还", description="还款方式"),
     *                 @SWG\Property(property="cycle", type="integer", example="12", description="借款周期"),
     *                 @SWG\Property(property="picture", type="string", example="https://zhijie-jietiao.oss-cn-zhangjiakou.aliyuncs.com/test/chaiquan.jpg", description="产品图片地址"),
     *             ),
     *             @SWG\Property(property="error_code", type="integer", example=0),
     *             @SWG\Property(property="error_message", type="integer", example=""),
     *             @SWG\Property(property="time", type="string", example="2018-05-09 14:28:13"),
     *         )
     *     ),
     * )
     * @param string $channel_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChannelInfo($channel_id)
    {
        $channel = ChannelFactory::getChannelInfoByHashId($channel_id);
        if (!$channel) {
            return RestResponseFactory::ok(RestUtils::getStdObj(), '出错啦', 1130);
        }

        if ($channel->type == ChannelConstant::SAAS_CHANNEL_TYPE_DIRECTED) {
            //检查和作方是否有效, 账户余额是否可用
            $saasUserId = SaasChannelSaas::where('channel_id', $channel->id)->first()->saas_user_id;
            $user = SaasAuthFactory::getById($saasUserId);
            $balance = SaasAccountFactory::getBalanceById($saasUserId);
            if (!$user || $balance <= 0) {
                return RestResponseFactory::ok(RestUtils::getStdObj(), '出错啦', 20002);
            }
        }

        $channel->repayment_method = ChannelStrategy::getRepaymentMethodText($channel->repayment_method);

        return RestResponseFactory::ok($channel);
    }
}
