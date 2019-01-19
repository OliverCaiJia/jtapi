<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;

/**
 * @SWG\Swagger(
 *     swagger="2.0",
 *     schemes={"http"},
 *     consumes={"application/vnd.jietiao.v1+json"},
 *     produces={"application/json"},
 *     basePath="/",
 *     @SWG\Info(
 *         version="v1",
 *         title="借条 API",
 *         description="### 概述
1.目的
本文档用于描述移动客户端与服务器端的数据交互接口，目的在于便于双方开发人员在开发程序时有开发依据，避免出现约定性错误。该文档是项目能否顺利推进的关键性文档之一。

2.阅读对象
项目移动客户端和服务器端开发人员以及项目的管理人员。

3.约定
返回数据为JSON格式，多组数据为JSONArray格式。

### 说明：
- 需要登录才能使用的接口，必须在HTTP的`Header`中添加授权字段，如下：`X-Token：9a71d0ae5df7ab153eb1c6d3698dfbdd` 。
- 全部接口，必须在HTTP的`Header`中添加验签字段，如下：`X-Sign：9a71d0ae5df7ab153eb1c6d3698dfbdd` 。

### 域名
正式线：https://api.jt.com
测试线：https://norm.api.jt.com ",
 *     )
 * )
 */
class ApiController extends Controller
{
}
