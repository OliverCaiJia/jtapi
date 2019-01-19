<?php

namespace App\Http\Validators\V1;

use App\Http\Validators\AbstractValidator;

class UpdateBasicInfoValidator extends AbstractValidator
{

    /**
     * Validation rules
     *
     * @var Array
     */
    protected $rules = [
        'channel_id' => ['required'],
        'name' => ['required'],
        'id_card' => ['required'],
        'mobile' => ['required'],
        'location' => ['required'],
        'address' => ['required'],
    ];

    /**
     * Validation messages
     *
     * @var Array
     */
    protected $messages = array(
        'channel_id.required' => '渠道码必填!',
        'name.required' => '姓名必填!',
        'id_card.required' => '身份证号必填!',
        'mobile.required' => '手机号码必填!',
        'location.required' => '所在地区必填!',
        'address.required' => '详细地址必填!',
    );
}
