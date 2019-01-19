<?php

namespace App\Http\Controllers\V1;

use App\Helpers\RestResponseFactory;
use App\Helpers\RestUtils;
use App\Models\Chain\QuickLogin\DoQuickLoginHandler;
use App\Models\Chain\Register\DoRegisterHandler;
use App\Models\Factory\UserFactory;
use App\Strategies\SmsStrategy;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    /**
     * @SWG\Post(
     *     path="/v1/quick-login",
     *     summary="快速登录",
     *     operationId="quick-login",
     *     description="手机验证码快速登录注册",
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
     *         in="query",
     *         required=true,
     *         type="integer",
     *         description="13812345678"
     *     ),
     *     @SWG\Parameter(
     *         name="code",
     *         in="query",
     *         required=true,
     *         type="integer",
     *         description="1234"
     *     ),
     *     @SWG\Parameter(
     *         name="sign",
     *         in="query",
     *         required=true,
     *         type="string",
     *         description="QDOpDMr6GCS53Uq78w4X69NaY4fq1H1x"
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
     *                 @SWG\Property(property="mobile", type="integer", example=15910481607, description="手机号"),
     *                 @SWG\Property(property="name", type="string", example="jt", description="姓名"),
     *                 @SWG\Property(property="accessToken", type="string", example="Ow5K3O4QsLsM0FQLp3kiB7VHXkpwuDgZ", description="token"),
     *                 @SWG\Property(property="account_name", type="string", example="jt", description="jt"),
     *                 @SWG\Property(property="redirect_to_result", type="integer", example="1", description="是否跳转到结果页：0->否, 1->是")
     *             ),
     *             @SWG\Property(property="error_code", type="integer", example=0),
     *             @SWG\Property(property="error_message", type="integer", example=""),
     *             @SWG\Property(property="time", type="string", example="2018-05-09 14:28:13"),
     *         )
     *     ),
     * )
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function quickLogin(Request $request)
    {
        $data = $request->all();

        //检查验证码
        $isValid = SmsStrategy::checkCode($data);
        if (!$isValid) {
            return RestResponseFactory::ok(RestUtils::getStdObj(), '验证码输入不正确!', '9000', '验证码输入不正确!');
        }

        $user = UserFactory::getByMobile($data['mobile']);
        //检查该手机号是否已经注册
        if ($user) {
            //登录
            $data['user_id'] = $user->id;
            $login = new DoQuickLoginHandler($data);
            $re = $login->handleRequest();
            if (isset($re['error'])) {
                return RestResponseFactory::ok(RestUtils::getStdObj(), $re['error'], $re['code'], $re['error']);
            }
        } else {
            //注册
            $register = new DoRegisterHandler($data);
            $re = $register->handleRequest();
            if (isset($re['error'])) {
                return RestResponseFactory::ok(RestUtils::getStdObj(), $re['error'], $re['code'], $re['error']);
            }
        }

        return RestResponseFactory::ok($re);
    }
}
