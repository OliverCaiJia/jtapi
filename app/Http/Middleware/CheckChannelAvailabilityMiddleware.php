<?php

namespace App\Http\Middleware;

use App\Helpers\RestUtils;
use App\Models\Factory\ChannelFactory;
use Closure;
use App\Helpers\RestResponseFactory;

class CheckChannelAvailabilityMiddleware
{

    /**
     * 检查 channel 有效性
     *
     * @param          $request
     * @param \Closure $next
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        $channel = ChannelFactory::getChannelInfoByHashId($request->input('channel_id'));
        if (!$channel) {
            return RestResponseFactory::ok(RestUtils::getStdObj(), '出错啦', 1130);
        }

        return $next($request);
    }
}
