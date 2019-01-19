<?php

/**
 * @author zhaoqiying
 */
/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It is a breeze. Simply tell Lumen the URIs it should respond to
  | and give it the Closure to call when that URI is requested.
  |
 */

$router->get('/', function () use ($router) {
    return \App\Helpers\RestResponseFactory::ok(null, 'jt API');
});

// V1版本接口
$router->get('/v1', function () {
    return \App\Helpers\RestResponseFactory::ok(null, 'jt API v1.0');
});

/**
 * Load all routes
 */
foreach (app()->make('files')->allFiles(__DIR__ . '/api') as $partial) {
    require_once $partial->getPathname();
}

foreach (app()->make('files')->allFiles(__DIR__ . '/view') as $partial) {
    require_once $partial->getPathname();
}
