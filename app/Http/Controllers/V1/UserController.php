<?php

namespace App\Http\Controllers\V1;

use App\Constants\UserOrderConstant;
use App\Helpers\RestResponseFactory;
use App\Helpers\RestUtils;
use App\Models\Factory\ChannelFactory;
use App\Models\Factory\OrderFactory;
use App\Models\Factory\UserReportFactory;
use App\Strategies\OrderStrategy;
use Illuminate\Http\Request;
use Auth;

class UserController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/v1/basic-info",
     *     summary="获取基础信息",
     *     operationId="get-basic-info",
     *     description="获取申请基础信息",
     *     tags={"用户"},
     *      @SWG\Parameter(
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
     *             @SWG\Property(property="code", type="integer", example=200),
     *             @SWG\Property(property="message", type="string", example="OK"),
     *             @SWG\Property(property="data", type="object", ref="#/definitions/UserBasicInfo"),
     *             @SWG\Property(property="error_code", type="integer", example=0),
     *             @SWG\Property(property="error_message", type="integer", example=""),
     *             @SWG\Property(property="time", type="string", example="2018-05-09 14:28:13"),
     *         )
     *     ),
     * )
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBasicInfo(Request $request)
    {
        $channelId = ChannelFactory::getIdByHashId($request->input('channel_id'));
        if (!$channelId) {
            return RestResponseFactory::ok(RestUtils::getStdObj(), '出错啦', 1130);
        }

        $reportId = OrderFactory::getReportIdByChannelIdAndUserId($channelId, Auth::user()->id);
        $basicInfo = UserReportFactory::getBasicInfoById($reportId);

        return RestResponseFactory::ok($basicInfo);
    }

    /**
     * @SWG\Post(
     *     path="/v1/basic-info",
     *     summary="创建基础信息",
     *     operationId="create-basic-info",
     *     description="创建申请基础信息",
     *     consumes={"application/x-www-form-urlencoded"},
     *     tags={"用户"},
     *      @SWG\Parameter(
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
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="姓名",
     *     ),
     *     @SWG\Parameter(
     *         name="id_card",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="身份证号"
     *     ),
     *     @SWG\Parameter(
     *         name="location",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="所在地区"
     *     ),
     *      @SWG\Parameter(
     *         name="address",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="详细地址"
     *     ),
     *     @SWG\Parameter(
     *         name="contacts",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="紧急联系人"
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
     *                 @SWG\Property(property="url", type="string", example="http://api.datakey.com/h5/importV3/index.html#/carrier?apiKey=xx", description="H5请求地址"),
     *             ),
     *             @SWG\Property(property="error_code", type="integer", example=0),
     *             @SWG\Property(property="error_message", type="integer", example=""),
     *             @SWG\Property(property="time", type="string", example="2018-05-09 14:28:13"),
     *         )
     *     ),
     * )
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBasicInfo(Request $request)
    {
        $requests = $request->all();
        $userId = Auth::user()->id;

        $params = [
            'name' => $requests['name'],
            'id_card' => $requests['id_card'],
            'mobile' => $requests['mobile'],
            'location' => $requests['location'],
            'address' => $requests['address'],
            'contacts' => $requests['contacts'],
            'user_id' => $userId
        ];

        $report = UserReportFactory::create($params);

        $userId = $userId . ',' . $requests['channel_id'] . ',' . $report->id;
        $loginParams = urlencode(json_encode([
            'phone' => $requests['mobile'],
            'name' => $requests['name'],
            'idcard' => $requests['id_card']
        ]));

        return RestResponseFactory::ok([
            'url' => OrderStrategy::generateCarrierUrl($userId, $loginParams)
        ]);
    }

    /**
     * @SWG\Put(
     *     path="/v1/basic-info",
     *     summary="更新基础信息",
     *     operationId="update-basic-info",
     *     description="更新申请基础信息",
     *     consumes={"application/x-www-form-urlencoded"},
     *     tags={"用户"},
     *      @SWG\Parameter(
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
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="姓名",
     *     ),
     *     @SWG\Parameter(
     *         name="id_card",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="身份证号"
     *     ),
     *     @SWG\Parameter(
     *         name="location",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="所在地区"
     *     ),
     *      @SWG\Parameter(
     *         name="address",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="详细地址"
     *     ),
     *     @SWG\Parameter(
     *         name="contacts",
     *         in="formData",
     *         required=true,
     *         type="string",
     *         description="紧急联系人"
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
     *                 @SWG\Property(property="url", type="string", example="http://api.datakey.com/h5/importV3/index.html#/carrier?apiKey=xx", description="H5请求地址"),
     *             ),
     *             @SWG\Property(property="error_code", type="integer", example=0),
     *             @SWG\Property(property="error_message", type="integer", example=""),
     *             @SWG\Property(property="time", type="string", example="2018-05-09 14:28:13"),
     *         )
     *     ),
     * )
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBasicInfo(Request $request)
    {
        $requests = $request->all();
        $userId = Auth::user()->id;
        $channelId = ChannelFactory::getIdByHashId($requests['channel_id']);

        $reportId = UserReportFactory::getIdByChannelIdAndUserId($channelId, $userId);
        if (!$reportId) {
            return RestResponseFactory::ok(RestUtils::getStdObj(), '出错啦', 1142);
        }

        $params = [
            'name' => $requests['name'],
            'id_card' => $requests['id_card'],
            'mobile' => $requests['mobile'],
            'location' => $requests['location'],
            'address' => $requests['address'],
            'contacts' => $requests['contacts'],
            'status' => UserOrderConstant::USER_REPORT_STATUS_VERIFIED
        ];

        UserReportFactory::update($reportId, $params);

        $userId = $userId . ',' . $requests['channel_id'] . ',' . $reportId;
        $loginParams = urlencode(json_encode([
            'phone' => $requests['mobile'],
            'name' => $requests['name'],
            'idcard' => $requests['id_card']
        ]));

        return RestResponseFactory::ok([
            'url' => OrderStrategy::generateCarrierUrl($userId, $loginParams)
        ]);
    }
}
