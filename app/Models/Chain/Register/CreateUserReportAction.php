<?php

namespace App\Models\Chain\Register;

use App\Models\Chain\AbstractHandler;
use App\Strategies\UserStrategy;

class CreateUserReportAction extends AbstractHandler
{
    private $params = array();
    protected $error = array('error' => '对不起,用户注册失败(生成报告出错)！', 'code' => 112);

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * 第三步:创建用户报告
     *
     * @return array
     * @throws \Exception
     */
    public function handleRequest()
    {
        if ($this->createReport($this->params)) {
            return $this->params['user'];
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
    private function createReport($params)
    {
        return !!UserStrategy::createReport($params);
    }
}
