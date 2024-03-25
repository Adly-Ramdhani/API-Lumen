<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
    return $router->app->version();
});

$router->post('/login', 'UserController@logib');
 
$router->group(['prefix' => 'stuff/','middlewar' => 'Auth'], function() use ($router){
    //static routes
    $router->get('/data', 'StuffController@index');
    $router->post('/', 'StuffController@store');
    $router->get('/trash', 'StuffController@trash');

    //dynamic routes
    $router->get('{id}', 'StuffController@show');
    $router->patch('/{id}', 'StuffController@update');
    $router->delete('{id}', 'StuffController@destroy');
    $router->get('/restore/{id}', 'StuffController@restore');
    $router->delete('/permanent/{id}', 'StuffController@deletePermanent');
});


$router->group(['prefix' => 'user/','middlewar' => 'Auth'], function() use ($router){
    $router->get('/data', 'UserController@index');
    $router->post('/register','UserController@register');
    $router->post('/', 'UserController@store');
    $router->patch('/{id}', 'UserController@update');
    $router->delete('/{id}', 'UserController@destroy');
    $router->get('/restore/{id}', 'UserController@restore');
    $router->get('/{id}', 'UserController@show'); 
    $router->delete('/permanent/{id}', 'UserController@deletePermanent');
});

$router->get('/data', 'LendingController@index');
$router->group(['prefix' => 'lending'], function() use ($router){
    $router->get('/data', 'LendingController@index');
    $router->post('/', 'LendingController@store');
    $router->get('/{id}','LendingController@show');
});

$router->group(['prefix' => 'stuffstock'], function() use ($router){
    $router->get('/data', 'StuffStockController@index');
    $router->post('/', 'StuffStockController@store');
});



