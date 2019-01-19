<?php

namespace App\Constants;

class RechargeConstant extends AppConstant
{
    //充值失败
    const RECHARGE_STATUS_FAIL = 1;
    //充值完成
    const RECHARGE_STATUS_FINISHED = 2;
    //充值中
    const RECHARGE_STATUS_HANDLING = 3;
    //充值撤回
    const RECHARGE_STATUS_WITHDRAW = 4;
}
