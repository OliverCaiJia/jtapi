<?php

namespace App\Strategies;

class ChannelStrategy extends AppStrategy
{
    /**
     * 获取还款方式的文本
     *
     * @param $param
     *
     * @return string
     */
    public static function getRepaymentMethodText($param)
    {
        if ($param == '1') {
            return '一次还';
        } elseif ($param == '2') {
            return '分期还';
        }

        return '';
    }
}
