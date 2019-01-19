<?php

namespace App\Services\Core\Sms;

use App\Helpers\UserAgent;
use App\Models\Factory\SmsFactory;
use App\Models\Orm\MessageLog;
use App\Services\AppService;
use App\Services\Core\Sms\Changzhuo\ChangzhuoService;
use App\Services\Core\Sms\Chuanglan\ChuanglanService;
use App\Services\Core\Sms\Dahansantong\DahansantongService;
use App\Services\Core\Sms\Laiao\LaiaoEventService;
use App\Services\Core\Sms\Wwtl\WwtlService;
use App\Services\Core\Sms\Yimei\YimeiService;
use Carbon\Carbon;
use App\Helpers\Utils;
use Cache;
use App\Helpers\Generator\TokenGenerator;

class SmsService extends AppService
{
    public static $services;

    public static function i()
    {
        if (!(self::$services instanceof static)) {
            self::$services = new static();
        }

        return self::$services;
    }

    /**
     * 根据系统参数配置去选择短信通道
     *
     * @param $data
     *
     * @return bool|array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function to($data)
    {
        $ipNums = $this->limitIp();
        if ($ipNums >= 36) {
            return ['errorCode' => '1206'];
        }
        $mobileNums = $this->limitMobile($data['mobile']);
        if ($mobileNums >= 6) {
            return ['errorCode' => '1206'];
        }
        $uaNums = $this->limitUA();
        if ($uaNums >= 10000) {
            return ['errorCode' => '1206'];
        }

        $config = SmsFactory::getSmsSystemConfig();
        $data['channel'] = $config;
        switch ($config) {
            case 'chuanglan':
                $data['nid'] = $this->sendBefore($data);
                $re = ChuanglanService::i()->send($data);
                break;
            case 'changzhuo':
                $data['nid'] = $this->sendBefore($data);
                $re = ChangzhuoService::i()->send($data);
                break;
            case 'wwtl':
                $data['nid'] = $this->sendBefore($data);
                $re = WwtlService::i()->send($data);
                break;
            case 'yimei':
                $data['nid'] = $this->sendBefore($data);
                $re = YimeiService::i()->send($data);
                break;
            case 'dahansantong':
                $data['nid'] = $this->sendBefore($data);
                $re = DahansantongService::i()->send($data);
                break;
            case 'laiao':
                $data['nid'] = $this->sendBefore($data);
                $re = LaiaoEventService::i()->send($data);
                break;
            default:
                $data['nid'] = $this->sendBefore($data);
                $re = ChangzhuoService::i()->send($data);
                break;
        }

        return $re ?: ['errorCode' => '1205'];
    }

    /**
     * 发送短信之前入库创建发送内容
     *
     * @param $data
     *
     * @return mixed|string
     * @throws \Exception
     */
    protected function sendBefore($data)
    {
        $messageLog = MessageLog::create([
            'mobile' => $data['mobile'],
            'nid' => TokenGenerator::generateToken(),
            'content' => $data['message'],
            'send_time' => Carbon::now(),
            'channel' => $data['channel'],
            'send_type' => isset($data['send_type']) ? $data['send_type'] : 1,//1 验证码短信 2 通知短信
            'auto' => 0,
            'code' => isset($data['code']) ? $data['code'] : '',
            'code_time' => date('Y-m-d H:i:s', time() + 60),
            'send_ip' => Utils::ipAddress(),
            'user_agent' => UserAgent::i()->getUserAgent(),
        ]);

        return $messageLog->nid;
    }

    /**
     * 限制ip
     *
     * @return mixed
     */
    protected function limitIp()
    {
        $ip = Utils::ipAddress();
        $key = 'jt_ip_value_' . $ip;
        if (Cache::has($key)) {
            Cache::increment($key);
        } else {
            Cache::put($key, 1, Carbon::now()->second(60 * 60 * 24));
        }
        return Cache::get($key);
    }

    /**
     * 手机号限制
     *
     * @param $mobile
     *
     * @return mixed
     */
    protected function limitMobile($mobile)
    {
        $key = 'jt_mobile_value_' . $mobile;
        if (Cache::has($key)) {
            Cache::increment($key);
        } else {
            Cache::put($key, 1, Carbon::now()->second(60 * 60 * 24));
        }
        return Cache::get($key);
    }

    /**
     * UA限制
     *
     * @return mixed
     */
    protected function limitUA()
    {
        $key = 'jt_ua_value_' . md5(UserAgent::i()->getUserAgent());
        if (Cache::has($key)) {
            Cache::increment($key);
        } else {
            Cache::put($key, 1, Carbon::now()->second(60 * 60 * 24));
        }
        return Cache::get($key);
    }
}
