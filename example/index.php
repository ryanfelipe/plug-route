<?php

require_once dirname(__DIR__).'/vendor/autoload.php';
require_once 'Auth.php';
require_once 'OtherMiddleware.php';

use \PlugRoute\PlugRoute;

/**** CORS ****/
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH");
header("Access-Control-Allow-Headers: Content-Type");
/**** CORS ****/

$route = new PlugRoute();

$route->get('/', function () {
	echo 'Hello';
});

$route->post('/people', function ($request) {
    var_dump($request->all());
});

$route->put('/people/{id}', function ($request, $response) {
    $id = $request->parameter('id');
    echo $response->json(['id' => $id]);
});

$route->delete('/people/{id}', function ($request) {
    echo $request->parameter('id');
});

$route->patch('/people/{id}', function ($request) {
    echo $request->parameter('id');
});

$route->any('/url', function () {
   echo 'Receive type requests GET, POST, PUT, PATCH and DELETE';
});

$route->group(['prefix' => '/news'], function($route) {
    $route->get('/sport', function() {
        echo 'Home news';
    });
});

$route->get('/sports', function() {
    echo 'Sports';
})->name('sports');

$route->get('/sports/{something}', function($request) {
    $request->redirectToRoute('sports');

    // If you use this library without virtualhost or php server built-in
    // use the redirect method
    //$request->redirect('http://localhost/plug-route/example/sports');
});

$route->group(['prefix' => '/products', 'middleware' => [OtherMiddleware::class]], function($route) {
    $route->get('', function() {
        echo 'Home news';
    })->name('news')->middleware(Auth::class);

    $route->get('/{something}', function($request) {
        echo $request->getWith('something');
    });
});

$route->get('/cars', '\NAMESPACE\YOUR_CLASS@method');

$route->on();