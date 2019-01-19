<?php

namespace App\Http\Controllers\V1\Transformer;

use App\Models\Factory\UserReportFactory;
use App\Strategies\OrderStrategy;
use League\Fractal\TransformerAbstract;

/**
 * @SWG\Definition(
 *     definition="OrderHistoryTransformer",
 *     required={"name", "mobile", "status", "created_at"},
 *     @SWG\Property(property="name", type="string", example="借条", description="姓名"),
 *     @SWG\Property(property="mobile", type="integer", example="13812345678", description="手机号"),
 *     @SWG\Property(property="status", type="string", example="审核中", description="状态"),
 *     @SWG\Property(property="created_at", type="string", example="2018-01-01   12:00:01", description="申请时间"),
 * )
 */
class OrderHistoryTransformer extends TransformerAbstract
{
    public function transform($orders)
    {
        $basicInfo = UserReportFactory::getBasicInfoById($orders->user_report_id);
        $orderStatus = OrderStrategy::getOrderStatusV11($orders->id);
        return [
            'name' => $basicInfo->name,
            'mobile' => $basicInfo->mobile,
            'status' => OrderStrategy::getStatusText($orderStatus),
            'created_at' => $orders->created_at
        ];
    }
}
