<?php

namespace App\Http\Validators\V1;

use App\Http\Validators\AbstractValidator;

class QuickLoginValidator extends AbstractValidator
{

    /**
     * Validation rules
     *
     * @var Array
     */
    protected $rules = array(
        'mobile' => ['required', 'is_not_email', 'is_phone'],
        'code' => ['required', 'integer'],
        'sign' => ['required', 'alpha_num', 'size:32']
    );

    /**
     * Validation messages
     *
     * @var Array
     */
    protected $messages = array(
        'mobile.required' => '手机号必须输入',
        'mobile.is_not_email' => '请使用手机号进行登录',
        'mobile.is_phone' => '请输入正确的手机号格式',
        'code.required' => '验证码必须传值',
        'code.integer' => '验证码必须是整数',
        'sign.required' => 'sign必须传值',
        'sign.alpha_num' => 'sign必须是md5后的字符串',
        'sign.size' => 'sign必须是32位',
    );

    /**
     * Validation codes
     *
     * @var Array
     */
    public $codes = array(
        'keywords.required' => '1001',
        'keywords.is_not_email' => '1003',
        'password.required' => '1004',
        'password.alpha_num' => '1005',
        'password.size' => '1006',
    );

    /*
     * 自定义验证规则或者扩展Validator类
     */
    public function before()
    {
        // 移动端不支持邮箱登陆
        $this->extend('is_not_email', function ($attribute, $value, $parameters) {
            return !filter_var($value, FILTER_VALIDATE_EMAIL);
        });

        //自定义规则检查用户手机号
        $this->extend('is_phone', function ($attribute, $value, $paramters) {
            if (preg_match('/^1[3|4|5|6|7|8|9]\d{9}$/', $value)) {
                return true;
            } else {
                return false;
            }
        });
    }
}
