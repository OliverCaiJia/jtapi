<?php

namespace App\Http\Validators\V1;

use App\Http\Validators\AbstractValidator;

class ChannelValidator extends AbstractValidator
{

    /**
     * Validation rules
     *
     * @var Array
     */
    protected $rules = [
        'channel_id' => ['required'],
    ];

    /**
     * Validation messages
     *
     * @var Array
     */
    protected $messages = array(
        'channel_id.required' => '渠道码必填!',
    );
}
