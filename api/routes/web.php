<?php

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

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response as FacadesResponse;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('test/test_job', 'TestJobController@testJob');

// App Api
$router->group(['prefix' => 'app', 'namespace' => 'App'], function () use ($router) {
    $router->group(['middleware' => 'app_auth'], function () use ($router) {
        $router->put('ticket/check', 'TicketController@check');
        $router->put('ticket/use', 'TicketController@use');
        $router->put('ticket/detail', 'TicketController@detail');
    });
    $router->post('user/login', 'UserController@login');
    $router->put('firebase/test', 'FireBaseController@test');
});

// tool get @bodyParam for API doc for table
//$router->get('api_doc/get_body_params/{table_name}', 'ApiDocController@getBodyParams');

// Backend API
$router->post('user/login', 'UserController@login');
$router->put('user/request_reset_password', 'UserController@requestResetPassword');
$router->put('user/verify_checksum_reset_password', 'UserController@verifyChecksumResetPassword');
$router->put('user/verify_otp_reset_password', 'UserController@verifyOtpResetPassword');

$router->group(['middleware' => 'auth'], function () use ($router) {
    // API user
    $router->put('user/profile', 'UserController@profile');
    $router->put('user/profile_change_password', 'UserController@profileChangePassword');
    $router->put('user/profile_update', 'UserController@profileUpdate');
    $router->put('user/reset_password', 'UserController@resetPassword');
    $router->put('user/change_password', 'UserController@changePassword');
    $router->put('user/detail', 'UserController@detail');
    $router->put('user/list', 'UserController@list');
    $router->put('user/create', 'UserController@create');
    $router->put('user/update', 'UserController@update');
    $router->put('user/switch_group', 'UserController@switchGroup');
});


