<?php

namespace App\Http\Middleware;

use App\Helpers\RestUtils;
use Closure;
use App\Helpers\RestResponseFactory;

class SignMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //取所有请求参数，按Key正序排序后，按Key+Value方式连接，加放当前请求页面的url，url走sha1加密，如是登录状态，在字符串的第三位加入登录token
        $sign = $request->header('X-Sign');
        $formArray = $request->all();
        $errorMessage = '验签未通过';

        if (empty($sign)) {
            return RestResponseFactory::ok(RestUtils::getStdObj(), $errorMessage, 409, $errorMessage);
        }

        ksort($formArray);
        $sha1Text = '';
        foreach ($formArray as $key => $val) {
            $sha1Text = $sha1Text . $key . $val;
        }
        $token = ($request->input('token') ?: $request->header('X-Token')) ?: '';

        $startString = '';
        $endString = '';
        if (!empty($sha1Text)) {
            $startString = mb_substr($sha1Text, 0, 3);
            $endString = mb_substr($sha1Text, -3);
        }
        $url = $request->url();

        $salt = sha1($url);
        $sha1Text = $startString . $token . $endString . $salt;

        $sha1Sign = sha1($sha1Text);
        if ($sign !== $sha1Sign) {
//                $errorMessage = '验签未通过,服务器验签:' . $sha1Sign . ';加密原串:' . $sha1Text;;
            return RestResponseFactory::ok(RestUtils::getStdObj(), $errorMessage, 409, $errorMessage);
        }

        return $next($request);
    }
}
