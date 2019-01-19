<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Routing\Middleware\ThrottleRequests;

class ApiThrottleRequests extends ThrottleRequests
{

    protected function resolveRequestSignature($request)
    {
        return sha1(
            $request->method() .
            '|' . $request->server('SERVER_NAME') .
            '|' . $request->path() .
            '|' . $request->ip()
        );
    }

}
