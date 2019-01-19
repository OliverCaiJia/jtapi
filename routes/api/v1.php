<?php

$router->group(['prefix' => 'v1', 'namespace' => 'V1', 'middleware' => ['sign', 'cros']], function ($router) {
    $router->group(['middleware' => ['checkChannelAvailability']], function ($router) {
        $router->post('basic-info', [
            'middleware' => ['auth', 'validate:createBasicInfo'],
            'uses' => 'UserController@createBasicInfo'
        ]);

        //auth
        $router->post('quick-login', [
            'middleware' => ['validate:quickLogin'],
            'uses' => 'AuthController@quickLogin'
        ]);
    });

    //获取基础申请信息
    $router->get('basic-info', [
        'middleware' => ['auth', 'validate:getBasicInfo'],
        'uses' => 'UserController@getBasicInfo'
    ]);

    //更新基础申请信息
    $router->put('basic-info', [
        'middleware' => ['auth', 'validate:updateBasicInfo'],
        'uses' => 'UserController@updateBasicInfo'
    ]);

    //渠道
    $router->get('channels/{channel_id}', [
        'uses' => 'ChannelController@getChannelInfo'
    ]);

    //历史申请记录
    $router->get('orders/history', [
        'middleware' => ['auth', 'validate:orderHistory'],
        'uses' => 'OrderController@history'
    ]);

    //申请结果
    $router->get('orders/result', [
        'middleware' => ['auth', 'validate:orderResult'],
        'uses' => 'OrderController@result'
    ]);

    //发送手机验证码
    $router->post('verification/phone', [
        'middleware' => ['validate:verificationCode'],
        'uses' => 'SmsController@sendVerificationCodeToPhone'
    ]);
});
