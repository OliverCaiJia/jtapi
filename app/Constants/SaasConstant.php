<?php

namespace App\Constants;

class SaasConstant extends AppConstant
{
    // 账户冻结
    const SAAS_USER_LOCKED_TRUE = 1;
    // 账户没有冻结
    const SAAS_USER_LOCKED_FALSE = 0;
    // 账户删除
    const SAAS_USER_DELETED_TRUE = 1;
    // 账户正常
    const SAAS_USER_DELETED_FALSE = 0;
    // 合作方审查条件关系状态删除
    const SAAS_FILTER_DELETED_TRUE = 1;
    // 合作方审查条件关系状态正常
    const SAAS_FILTER_DELETED_FALSE = 0;
    // 合作方审查条件类别关系状态删除
    const SAAS_FILTER_TYPE_DELETED_TRUE = 1;
    // 合作方审查条件类别关系状态正常
    const SAAS_FILTER_TYPE_DELETED_FALSE = 0;

    // 合作方默认登陆密码
    const SAAS_USER_DEFAULT_PASSWORD = '000000';

    // 合作方审查条件参数前缀
    const SAAS_FILTER_PREFIX = ':param';

    // 合作方审查条件参数默认值 参数标识 => 参数值
    const SAAS_FILTER_DEFAULT_TYPE_PARAMS = [
        'repeated_apply_ignore_days' => 30,            // 默认不允许重复申请参数30天
    ];

    // 合作方审查条件种类
    const SAAS_FILTER_KIND_NECESSIRY = '必要';
    const SAAS_FILTER_KIND_UNNECESSIRY = '非必要';

    // 合作方审查条件类型是否默认常量
    const SAAS_FILTER_TYPE_IS_DEFAULT = 1;
    const SAAS_FILTER_TYPE_IS_NOT_DEFAULT = 0;

    // 合作方超级用户标识
    const SAAS_SUPER_USER_TRUE = 1;
    const SAAS_SUPER_USER_FALSE = 0;
}
