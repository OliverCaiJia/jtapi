<?php

namespace App\Services\Core\Sms\Chuanglan;

use App\Services\Core\Sms\SmsService;
use App\Helpers\Http\HttpClient;
use App\Models\Orm\MessageLog;
use Carbon\Carbon;

/**
 * 创蓝短信通道
 * Class ChuanglanService
 *
 * @package App\Services\Core\Sms\Chuanglan
 */
class ChuanglanService extends SmsService
{

    /**
     * @param $data
     *
     * @return bool|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send($data)
    {
        $request = [
            'json' => [
                'account' => config('sms.chuanglan.smsAccount'),
                'password' => config('sms.chuanglan.smsPassword'),
                'msg' => $data['message'],
                'phone' => $data['mobile'],
            ]
        ];
        $promise = HttpClient::i()->request('POST', config('sms.chuanglan.smsSendUrl'), $request);
        $result = $promise->getBody()->getContents();

        if (json_decode($result)->code != 0) {
            return false;
        }

        $this->sendAfter($result, $data);

        return $result;
    }

    /**
     * 发送之后把返回短信商结果入库并执行更新
     *
     * @param       $result
     * @param array $data
     */
    public function sendAfter($result, $data = [])
    {
        if ($result) {
            MessageLog::where('nid', $data['nid'])
                ->where('mobile', $data['mobile'])
                ->update(['result' => addslashes($result), 'response_time' => Carbon::now()]);
        }
    }
}
