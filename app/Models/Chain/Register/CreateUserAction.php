<?php

namespace App\Models\Chain\Register;

use App\Models\Factory\AuthFactory;
use App\Models\Chain\AbstractHandler;
use App\Strategies\UserStrategy;

class CreateUserAction extends AbstractHandler
{
    private $params = array();
    protected $error = array('error' => '对不起,用户注册失败！', 'code' => 111);
    protected $data;
    protected $user;

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * 第二步:用户表插入数据
     *
     * @return array
     * @throws \Exception
     */
    public function handleRequest()
    {
        if ($this->createUser($this->params) == true) {
            $user = $this->user;
            $this->params['user'] = [
                'mobile' => $user->mobile,
                'name' => $user->name,
                'accessToken' => $user->accessToken,
                'account_name' => $user->account_name,
                'redirect_to_result' => 0
            ];
            $this->params['user_id'] = $user->id;
            $this->setSuccessor(new CreateUserReportAction($this->params));
            return $this->getSuccessor()->handleRequest();
        }

        return $this->error;
    }

    /**
     * 存储
     *
     * @param $params
     *
     * @return bool
     * @throws \Exception
     */
    private function createUser($params)
    {
        $this->user = UserStrategy::createUser($params);

        return true;
    }
}
