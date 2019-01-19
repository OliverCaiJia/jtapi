<?php

namespace App\Http\Controllers\V1;

use App\Helpers\RestResponseFactory;
use App\Helpers\RestUtils;
use App\Http\Controllers\V1\Transformer\OrderHistoryTransformer;
use App\Models\Factory\OrderFactory;
use App\Models\Factory\ChannelFactory;
use App\Strategies\OrderStrategy;
use Auth;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class OrderController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/v1/orders/history",
     *     summary="历史申请记录",
     *     operationId="order-history",
     *     description="获取历史申请记录",
     *     tags={"订单"},
     *     @SWG\Parameter(
     *         name="X-Token",
     *         in="header",
     *         required=true,
     *         type="string",
     *         description="9a71d0ae5df7ab153eb1c6d3698dfbdd"
     *     ),
     *     @SWG\Parameter(
     *         name="X-Sign",
     *         in="header",
     *         required=true,
     *         type="string",
     *         description="9a71d0ae5df7ab153eb1c6d3698dfbdd"
     *     ),
     *     @SWG\Parameter(
     *         name="channel_id",
     *         in="query",
     *         required=true,
     *         type="string",
     *         description="vsco2H"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Operation succeeds.",
     *         @SWG\Schema(
     *             required={"code", "message", "data", "error_code", "error_message", "time"},
     *             @SWG\Property(property="code", type="integer", example=20),
     *             @SWG\Property(property="message", type="string", example="OK"),
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 @SWG\Property(
     *                     property="list",
     *                     type="array",
     *                     @SWG\Items(ref="#/definitions/OrderHistoryTransformer")
     *                 ),
     *                 @SWG\Property(
     *                     property="meta",
     *                     type="object",
     *                     @SWG\Property(
     *                          property="pagination",
     *                          type="object",
     *                          @SWG\Property(property="total", type="integer", default=4),
     *                          @SWG\Property(property="per_page", type="integer", default=3),
     *                          @SWG\Property(property="current_page", type="integer", default=1),
     *                          @SWG\Property(property="last_page", type="integer", default=2),
     *                          @SWG\Property(
     *                              property="links",
     *                              type="object",
     *                              @SWG\Property(property="prev_page_url", type="string", default="https://aaa/order/history?page=1"),
     *                              @SWG\Property(property="next_page_url", type="string", default="https://aaa/order/history?page=2")
     *                          ),
     *                      ),
     *                 ),
     *             ),
     *             @SWG\Property(property="error_code", type="integer", example=0),
     *             @SWG\Property(property="error_message", type="integer", example=""),
     *             @SWG\Property(property="time", type="string", example="2018-05-09 14:28:13"),
     *         )
     *     ),
     *     @SWG\Response(response=401, description="Authentication failed."),
     * )
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request)
    {
        $channelId = ChannelFactory::getIdByHashId($request->input('channel_id'));
        if (!$channelId) {
            return RestResponseFactory::ok(RestUtils::getStdObj(), '出错啦', 1130);
        }

        $orders = OrderFactory::getOrdersByChannelIdAndUserId($channelId, Auth::user()->id);
        $orders = fractal()->collection($orders, new OrderHistoryTransformer())->paginateWith(new IlluminatePaginatorAdapter($orders))->toArray();

        return RestResponseFactory::ok([
            'list' => $orders['data'],
            'meta' => $orders['meta'],
        ]);
    }

    /**
     * @SWG\Get(
     *     path="/v1/orders/result",
     *     summary="申请结果",
     *     operationId="order-result",
     *     description="获取申请结果",
     *     tags={"订单"},
     *     @SWG\Parameter(
     *         name="X-Token",
     *         in="header",
     *         required=true,
     *         type="string",
     *         description="9a71d0ae5df7ab153eb1c6d3698dfbdd"
     *     ),
     *     @SWG\Parameter(
     *         name="X-Sign",
     *         in="header",
     *         required=true,
     *         type="string",
     *         description="9a71d0ae5df7ab153eb1c6d3698dfbdd"
     *     ),
     *     @SWG\Parameter(
     *         name="channel_id",
     *         in="query",
     *         required=true,
     *         type="string",
     *         description="vsco2H"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Operation succeeds.",
     *         @SWG\Schema(
     *             required={"code", "message", "data", "error_code", "error_message", "time"},
     *             @SWG\Property(property="code", type="integer", example=20),
     *             @SWG\Property(property="message", type="string", example="OK"),
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 @SWG\Property(property="status", type="string", example="no_audit", description="订单状态"),
     *                 @SWG\Property(
     *                      property="content",
     *                      type="array",
     *                      description="内容",
     *                      @SWG\Items(
     *                          @SWG\Property(
     *                              property="step1",
     *                              type="array",
     *                              description="第一步",
     *                              @SWG\Items(
     *                                  @SWG\Property(property="text", type="string", example="提交资料"),
     *                                  @SWG\Property(property="time", type="string", example="2018-05-09 14:28:13"),
     *                              )
     *                          ),
     *                          @SWG\Property(
     *                              property="step2",
     *                              type="array",
     *                              description="第二步",
     *                              @SWG\Items(
     *                                  @SWG\Property(property="text", type="string", example="审核中"),
     *                                  @SWG\Property(property="time", type="string", example="2018-05-09 14:28:13"),
     *                              )
     *                          ),
     *                      )
     *                  )
     *             ),
     *             @SWG\Property(property="error_code", type="integer", example=0),
     *             @SWG\Property(property="error_message", type="integer", example=""),
     *             @SWG\Property(property="time", type="string", example="2018-05-09 14:28:13"),
     *         )
     *     ),
     *     @SWG\Response(response=401, description="Authentication failed."),
     * )
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function result(Request $request)
    {
        $channelId = ChannelFactory::getIdByHashId($request->input('channel_id'));
        if (!$channelId) {
            return RestResponseFactory::ok(RestUtils::getStdObj(), '出错啦', 1130);
        }

        // 获取最近一次订单
        $order = OrderFactory::getLastOrderByChannelIdAndUserId($channelId, Auth::user()->id);
        if (empty($order)) {
            return RestResponseFactory::ok(RestUtils::getStdObj(), '出错啦', 1141);
        }

        // 获取此订单的处理状态
        $finalStatus = OrderStrategy::getOrderStatusV11($order->id);

        return RestResponseFactory::ok(OrderStrategy::getOrderResultV11($order, $finalStatus));
    }
}
