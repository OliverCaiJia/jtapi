<?php

namespace App\Services\Core\Sms\Yimei;

use App\Services\Core\Sms\SmsService;
use App\Helpers\Http\HttpClient;
use App\Models\Orm\MessageLog;
use Carbon\Carbon;

/**
 * 亿美短信通道
 *
 */
class YimeiService extends SmsService
{

    /**
     * @param $data
     *
     * @return bool|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send($data)
    {
        $appId = config('sms.yimei.cdkey');
        $timestamp = date('YmdHis');
        $request = [
            'query' => [
                'appId' => $appId,
                'timestamp' => $timestamp,
                'sign' => md5($appId.config('sms.yimei.password').$timestamp),
                'mobiles' => $data['mobile'],
                'content' => $data['message'],
            ]
        ];
        $promise = HttpClient::i()->request('GET', config('sms.yimei.smsSendUrl'), $request);
        $result = $promise->getBody()->getContents();

        if (json_decode($result)->code != 'SUCCESS') {
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
