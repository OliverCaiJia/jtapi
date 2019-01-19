<?php

namespace App\Models\Chain\QuickLogin;

use App\Models\Factory\ChannelFactory;
use App\Models\Factory\OrderFactory;
use App\Models\Factory\UserFactory;
use App\Models\Chain\AbstractHandler;
use App\Strategies\OrderStrategy;

class FetchUserInfoAction extends AbstractHandler
{
    private $params = [];
    protected $error = ['error' => '用户登录失败!!', 'code' => 9004];

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * 返回用户信息
     *
     * @return array
     */
    public function handleRequest()
    {
        $info = $this->getUserInfo($this->params);

        return $info ?: $this->error;
    }

    private function getUserInfo($params)
    {
        $info = UserFactory::getUserInfoById($params['user_id']);
        $info['redirect_to_result'] = $this->isRedirectV11($params);

        return $info ?: false;
    }

    /**
     * 是否重定向
     *
     * @param $params
     *
     * @return int
     */
    private function isRedirect($params)
    {
        $channelId = ChannelFactory::getIdByHashId($params['channel_id']);

        $order = OrderFactory::getPendingByChannelIdAndUserId($channelId, $params['user_id']);

        return $order ? 1 : 0;
    }

    /**
     * 是否重定向 V1.1版本
     *
     * @param $params
     *
     * @return int
     */
    private function isRedirectV11($params)
    {
        $channelId = ChannelFactory::getIdByHashId($params['channel_id']);

        $isRedirect = OrderStrategy::getIsDirectToResult($channelId, $params['user_id']);

        return $isRedirect ? 1 : 0;
    }
}
