<?php

namespace App\Services\Core\Sms\Changzhuo;

use App\Helpers\Http\HttpClient;
use App\Services\Core\Sms\SmsService;
use App\Models\Orm\MessageLog;
use Carbon\Carbon;

/**
 * 畅卓短信通道
 * Class ChangzhuoService
 *
 * @package App\Services\Core\Sms\Changzhuo
 */
class ChangzhuoService extends SmsService
{

    /**
     * 根据系统参数配置去选择短信通道
     *
     * @param $data
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send($data)
    {
        $request = [
            'form_params' => [
                'account' => config('sms.changzhuo.account'),
                'password' => config('sms.changzhuo.password'),
                'mobile' => $data['mobile'],
                'content' => $data['message'],
                'sendTime' => '',
                'extno' => ''
            ]
        ];
        $promise = HttpClient::i()->request('POST', config('sms.changzhuo.smsSendUrl'), $request);
        $result = $promise->getBody()->getContents();
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
