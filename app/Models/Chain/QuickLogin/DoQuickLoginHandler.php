<?php

namespace App\Models\Chain\QuickLogin;

use App\Models\Chain\AbstractHandler;
use DB;
use App\Helpers\Logger\SLogger;

class DoQuickLoginHandler extends AbstractHandler
{
    private $params = array();

    public function __construct($params)
    {
        $this->params = $params;
        $this->setSuccessor($this);
    }

    /**
     * 验证码快捷登录
     *
     * 第一步: 更新用户最后登录时间和token
     * 第二步: 返回用户信息
     */

    /**
     * @return array
     * @throws \Exception
     */
    public function handleRequest()
    {
        $result = ['error' => '出错啦', 'code' => 1000];

        DB::beginTransaction();
        try {
            $this->setSuccessor(new UpdateLoginTimeAction($this->params));
            $result = $this->getSuccessor()->handleRequest();
            if (isset($result['error'])) {
                DB::rollback();

                SLogger::getStream()->error('用户注册, 事务异常-try');
                SLogger::getStream()->error($result['error']);
            } else {
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();

            SLogger::getStream()->error('用户注册, 事务异常-catch');
            SLogger::getStream()->error($e->getMessage());
        }
        return $result;
    }
}
