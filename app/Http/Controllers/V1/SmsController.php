<?php

namespace App\Http\Controllers\V1;

use App\Helpers\Generator\TokenGenerator;
use App\Helpers\RestResponseFactory;
use App\Helpers\RestUtils;
use App\Http\Controllers\Controller;
use App\Models\Factory\ChannelFactory;
use App\Services\Core\Sms\SmsService;
use App\Strategies\SmsStrategy;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/v1/verification/phone",
     *     summary="发送手机验证码",
     *     operationId="verification-phone",
     *     description="发送手机短信验证码",
     *     consumes={"application/x-www-form-urlencoded"},
     *     tags={"登录注册"},
     *     @SWG\Parameter(
     *         name="X-Sign",
     *         in="header",
     *         required=true,
     *         type="string",
     *         description="9a71d0ae5df7ab153eb1c6d3698dfbdd"
     *     ),
     *     @SWG\Parameter(
     *         name="mobile",
     *         in="formData",
     *         required=true,
     *         type="integer",
     *         description="13812345678"
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
     *             @SWG\Property(
     *                 property="data",
     *                 type="object",
     *                 @SWG\Property(property="sign", type="string", example="Ow5K3O4QsLsM0FQLp3kiB7VHXkpwuDgZ"),
     *             ),
     *             @SWG\Property(property="error_code", type="integer", example=0),
     *             @SWG\Property(property="error_message", type="integer", example=""),
     *             @SWG\Property(property="time", type="string", example="2018-05-09 14:28:13"),
     *         )
     *     )
     * )
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendVerificationCodeToPhone(Request $request)
    {
        $channelId = $request->input('channel_id');
        $channel = ChannelFactory::getChannelInfoByHashId($channelId);
        if (!$channel) {
            return RestResponseFactory::ok(RestUtils::getStdObj(), '出错啦', 1130);
        }
        $data['sms-sign'] = $channel->product_name;
        $data['mobile'] = $request->input('mobile');

        //验证短信1分钟之内不能重复发送
        $expired = SmsStrategy::checkCodeExistenceTime($data['mobile'], 'phone');
        if (!$expired) {
            return RestResponseFactory::ok(RestUtils::getStdObj(), RestUtils::getErrorMessage(1201), 1201);
        }

        $code = mt_rand(1000, 9999);
        $data['message'] = "【{$data['sms-sign']}】您的验证码是{$code}，10分钟内有效，请勿将验证码提供给他人，若非本人操作可忽略。";
        $data['code'] = $code;
        $re = SmsService::i()->to($data);
        $random = [];
        $random['sign'] = TokenGenerator::generateToken();

        SmsStrategy::putSmsCodeToCache('mobile_code_' . $data['mobile'] . $channelId, $code);
        SmsStrategy::putSmsCodeToCache('mobile_random_' . $data['mobile'] . $channelId, $random['sign']);

        if (isset($re['errorCode'])) {
            return RestResponseFactory::ok(
                RestUtils::getStdObj(),
                RestUtils::getErrorMessage($re['errorCode']),
                $re['errorCode']
            );
        }

        return RestResponseFactory::ok($random);
    }
}
