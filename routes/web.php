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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/retiro/{id_billetera}/e', 'RetiroController@retiro_exitoso');
$router->get('/retiro/{id_billetera}/f', 'RetiroController@retiro_fallido');
//ruta: http://emoney/retiro/crear
$router->get("/retiro/crear","RetiroController@create");
