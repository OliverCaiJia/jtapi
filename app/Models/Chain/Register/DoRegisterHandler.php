<?php

namespace App\Models\Chain\Register;

use App\Models\Chain\AbstractHandler;
use DB;
use App\Helpers\Logger\SLogger;

class DoRegisterHandler extends AbstractHandler
{
    private $params = array();

    public function __construct($params)
    {
        $this->params = $params;
        $this->setSuccessor($this);
    }

    /**
     * 注册
     *
     * 第一步:创建用户返回用户信息
     * 第二步:用户表插入数据
     * 第三步:创建用户报告
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
            $this->setSuccessor(new CreateUserAction($this->params));
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
