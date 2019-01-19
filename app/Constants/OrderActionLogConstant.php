<?php

namespace App\Constants;

class OrderActionLogConstant extends AppConstant
{
    //类型：1、管理员分配 2、分配给合作方 3、合作方分配 4、审核
    const ADMIN_ASSIGN = 1;
    const ASSIGN_TO_SAAS = 2;
    const SAAS_ASSIGN = 3;
    const REVIEW = 4;
}
