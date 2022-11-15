<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Illuminate\Support\Facades\DB;

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
    echo "<center> Welcome </center>";
//     return $router->app->version();
});

$router->get('/version', function () use ($router) {
    dd(DB::getPDO());
    return $router->app->version();
});

    $router->group(['middleware' => ['Cors','verified','auth']], function () use ($router) {
        $router->post('/logout', 'UserController@logout');
        $router->post('/refresh', 'UserController@refresh');
        $router->post('users',  ['uses' => 'UserController@showAllUsers']);
        $router->options('users',  ['uses' => 'UserController@showAllUsers']);
        $router->post('/user', 'UserController@me');
        $router->options('/user', 'UserController@me');
        // $router->get('users/{id}', ['uses' => 'UserController@showOneUser']);

        $router->post('createTask', 'TaskController@createTask');
        $router->options('createTask', 'TaskController@createTask');
        $router->post('getTasks', 'TaskController@getTasks');
        $router->options('getTasks', 'TaskController@getTasks');
        $router->post('stats', 'TaskController@statistics');
        $router->options('stats', 'TaskController@statistics');
        $router->post('changeStatus', 'TaskController@changeStatus');
        $router->options('changeStatus', 'TaskController@changeStatus');
        $router->post('changeTaskStatusBulk', 'TaskController@changeStatusBulk');
        $router->options('changeTaskStatusBulk', 'TaskController@changeStatusBulk');
      });

      
    $router->group(['middleware' => 'Cors'], function () use ($router){
        $router->post('/email/request-verification', ['as' => 'email.request.verification', 'uses' => 'UserController@emailRequestVerification']);
        $router->options('/email/request-verification', ['as' => 'email.request.verification', 'uses' => 'UserController@emailRequestVerification']);
        $router->post('/login', 'UserController@login');
        $router->post('/register', 'UserController@register');
        $router->post('/email/verify', ['as' => 'email.verify', 'uses' => 'UserController@emailVerify']);
        $router->options('/email/verify', ['as' => 'email.verify', 'uses' => 'UserController@emailVerify']);
        $router->post('/captcha', 'UserController@captcha');
        $router->options('/captcha', 'UserController@captcha');
        $router->post('/password/reset-request', 'RequestPasswordController@sendResetLinkEmail');
        $router->options('/password/reset-request', 'RequestPasswordController@sendResetLinkEmail');
        $router->post('/password/reset', [ 'as' => 'password.reset', 'uses' => 'ResetPasswordController@reset' ]);
        $router->options('/password/reset', [ 'as' => 'password.reset', 'uses' => 'ResetPasswordController@reset' ]);
      });