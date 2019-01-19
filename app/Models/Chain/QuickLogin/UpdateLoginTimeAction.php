<?php

namespace App\Models\Chain\QuickLogin;

use App\Models\Factory\AuthFactory;
use App\Models\Chain\AbstractHandler;
use App\Models\Factory\UserFactory;
use Cache;
use Carbon\Carbon;

class UpdateLoginTimeAction extends AbstractHandler
{

    private $params = array();
    protected $error = array('error' => '用户登录失败!', 'code' => 9001);

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * 更新用户最后登录时间和token
     *
     * @return bool
     * @throws \Exception
     */
    public function handleRequest()
    {
        if ($this->updateLoginTime($this->params['user_id']) == true) {
            $this->setSuccessor(new FetchUserInfoAction($this->params));
            return $this->getSuccessor()->handleRequest();
        }

        return true;
    }

    /**
     * @param $params
     *
     * @return bool
     * @throws \Exception
     */
    private function updateLoginTime($params)
    {
        $user = UserFactory::updateLoginTimeIpAndToken($params);

        if ($user) {
            Cache::put('user_token_' . $params['user_id'], $user, Carbon::now()->addDays(7));
            return true;
        }

        return false;
    }
}
