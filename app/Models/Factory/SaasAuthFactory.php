<?php

namespace App\Models\Factory;

use App\Models\AbsModelFactory;
use App\Models\Orm\SaasAuth;
use Carbon\Carbon;

class SaasAuthFactory extends AbsModelFactory
{
    /**
     * 获取一个可分配订单的的 saas 用户，接收订单最后期限，最大的订单数，可用订单数，有效期
     *
     * @param $id
     *
     * @return mixed|static
     */
    public static function getById($id)
    {
        return SaasAuth::where('is_deleted', 0)
            ->where('valid_deadline', '>', Carbon::now())
            ->where('max_order_num', '>', 0)
            ->where('remaining_order_num', '>', 0)
            ->where('order_deadline', '>', Carbon::now())
            ->find($id);
    }
}
