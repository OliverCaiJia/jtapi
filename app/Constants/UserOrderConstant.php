<?php

namespace App\Constants;

class UserOrderConstant extends AppConstant
{
    //订单状态: 1、待处理 2、已通过 3、已拒绝
    const PENDING = 1;
    const PASSED = 2;
    const REFUSED = 3;

    //还款方式: 1、一次还款 2、分期还款
    const LUMP_SUM = 1;
    const INSTALLMENT = 2;

    // 订单还款方式常量
    const ORDER_PAYMENT_METHOD = [
        self::LUMP_SUM => '一次还',
        self::INSTALLMENT => '分期还',
    ];

    // 订单状态展示MAP
    const ORDER_STATUS_MAP = [
        self::PENDING => '待处理',
        self::PASSED => '已通过',
        self::REFUSED => '已拒绝'
    ];

    // jt_user_reports表未完成订单状态常量，1-注册已完成未实名， 2-实名已完成未认证， 3-手机认证完成
    const USER_REPORT_STATUS_REGISTERED = 1;
    const USER_REPORT_STATUS_VERIFIED = 2;
    const USER_REPORT_STATUS_FINISHED = 3;

    const USER_REPORT_STATUS_MAP = [
        self::USER_REPORT_STATUS_REGISTERED => '注册完成',
        self::USER_REPORT_STATUS_VERIFIED => '实名完成',
        self::USER_REPORT_STATUS_FINISHED => '报告已生成'
    ];
}
