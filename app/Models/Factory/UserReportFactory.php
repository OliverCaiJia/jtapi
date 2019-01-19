<?php

namespace App\Models\Factory;

use App\Constants\UserOrderConstant;
use App\Models\AbsModelFactory;
use App\Models\Orm\AccountMessage;
use App\Models\Orm\AccountMessageConfig;
use App\Models\Orm\UserReport;

class UserReportFactory extends AbsModelFactory
{

    /**
     * @SWG\Definition(
     *     definition="UserBasicInfo",
     *     @SWG\Property(property="mobile", type="integer", example="13812345678", description="手机号"),
     *     @SWG\Property(property="name", type="string", example="借条", description="姓名"),
     *     @SWG\Property(property="id_card", type="integer", example=197288199001019876, description="身份证号码"),
     *     @SWG\Property(property="location", type="string", example="河北-石家庄-长安", description="所在地区"),
     *     @SWG\Property(property="address", type="string", example="育知路5号", description="详细地址"),
     *     @SWG\Property(
     *         property="contacts",
     *         type="array",
     *         description="紧急联系人",
     *         @SWG\Items(
     *             @SWG\Property(property="relationship", type="string", example="兄弟", description="关系"),
     *             @SWG\Property(property="name", type="string", example="张三", description="姓名"),
     *             @SWG\Property(property="mobile", type="string", example="13692087394", description="手机号"),
     *         )
     *     )
     * )
     */

    /**
     * 获取基础申请信息
     *
     * @param $id
     *
     * @return mixed|static
     */
    public static function getBasicInfoById($id)
    {
        return UserReport::select('name', 'id_card', 'mobile', 'location', 'address', 'contacts')
            ->find($id);
    }

    /**
     * 创建报告
     *
     * @param $params
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public static function create($params)
    {
        return UserReport::create($params);
    }

    /**
     * 更新报告
     *
     * @param $id
     * @param $params
     *
     * @return bool
     */
    public static function update($id, $params)
    {
        return UserReport::where('id', $id)->update($params);
    }

    /**
     * 通过 id 和 user_id 获取
     *
     * @param $id
     * @param $userId
     *
     * @return mixed|static
     */
    public static function getByIdAndUserId($id, $userId)
    {
        return UserReport::where('user_id', $userId)->find($id);
    }

    /**
     * 通过渠道和用户ID获取用户报告
     * @param $channelId
     * @param $userId
     * @return mixed
     */
    public static function getIdByChannelIdAndUserId($channelId, $userId)
    {
        return UserReport::where([
            'channel_id' => $channelId,
            'user_id' => $userId
        ])->whereIn('status', [
            UserOrderConstant::USER_REPORT_STATUS_REGISTERED, UserOrderConstant::USER_REPORT_STATUS_VERIFIED
        ])->orderByDesc('created_at')->value('id');
    }
}
