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

$router->group(['middleware' => 'cors'], function ($router) {

    $router->post('/login', 'AuthController@login');
    $router->get('/logout', 'AuthController@logout');
    $router->get('/profile', 'AuthController@me');
    
     
    $router->group(['prefix' => 'stuff/','middleware' => 'auth'], function() use ($router){
        //static routes
        $router->get('/data', 'StuffController@index');
        $router->post('/store', 'StuffController@store');
        $router->get('/trash', 'StuffController@trash');
    
        //dynamic routes
        $router->get('{id}', 'StuffController@show');
        $router->patch('/update/{id}', 'StuffController@update');
        $router->delete('/delete/{id}', 'StuffController@destroy');
        $router->get('/restore/{id}', 'StuffController@restore');
        $router->delete('/permanent/{id}', 'StuffController@deletePermanent');
    });
    
    
    $router->group(['prefix' => 'user/','middleware' => 'auth'], function() use ($router){
        $router->get('/data', 'UserController@index');
        $router->get('/trash', 'UserController@trash');
        $router->post('/register','UserController@register');
        $router->post('/store', 'UserController@store');
        $router->patch('update/{id}', 'UserController@update');
        $router->delete('delete/{id}', 'UserController@destroy');
        $router->get('/restore/{id}', 'UserController@restore');
        $router->get('/{id}', 'UserController@show'); 
        $router->delete('/permanent/{id}', 'UserController@deletePermanent');
    });
    
    // $router->get('/data', 'LendingController@index');
    $router->group(['prefix' => 'lending/', 'middleware' => 'auth'], function() use ($router){
        $router->get('/data', 'LendingController@index');
        $router->post('/store', 'LendingController@store');
        $router->post('/restore/{id}', 'LendingController@restore');
        $router->get('/show/{id}','LendingController@show');
        $router->patch('/update/{id}','LendingController@update');
        $router->delete('delete/{id}', 'LendingController@destroy');
        $router->get('/trash', 'LendingController@trash');
    });
    
    $router->group(['prefix' => 'inbound-stuff/','middleware' => 'auth'], function() use ($router){
        $router->get('/data', 'InboundStuffController@index');
        $router->post('/store', 'InboundStuffController@store');
        $router->get('/restore/{id}', 'InboundStuffController@restore');
        $router->delete('/delete/{id}', 'InboundStuffController@destroy');
        $router->delete('/permanent/{id}', 'InboundStuffController@deletePermanent');
        $router->get('/trash', 'InboundStuffController@trash');
        $router->patch('/update/{id}', 'InboundStuffController@update');
        
    });
    
    $router->group(['prefix' => 'stuff-Stock','middleware' => 'auth'], function() use ($router){
        $router->get('/data', 'StuffStockController@index');
        $router->post('/store', 'StuffStockController@store');
        $router->post('add-Stock/{id}', 'StuffStockController@addStock');
    });
    
    $router->group(['prefix' => 'restoration/','middleware' => 'auth'], function() use ($router){
       
        $router->post('/store', 'RestorationController@store');
    
    });
    
});

