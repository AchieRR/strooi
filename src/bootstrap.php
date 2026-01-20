<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\ServerRequest; 
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use HttpSoft\Emitter\SapiEmitter;
use League\Route\Router;

ini_set("display_errors", 1);

require dirname(__DIR__) . '/vendor/autoload.php';

$request = ServerRequest::fromGlobals();

$router = new Router;

$router->map('GET', '/', function () {
ob_start();

$stream = Utils::streamFor("Homepage");

$response = new Response;

$response = $response->withBody($stream);

return $response;
});

$router->get('/products', function () {
 
$steam = Utils::streamFor("List of Products");

$response = new Response;

$response = $response->withBody($steam);

return $response;
});

$router->get("/product/{id:number}", function ($request, $args) {

$id = $args['id'];

$stream = Utils::streamFor("Single Details with ID $id");

$response = new Response;

$response = $response->withBody($stream);

return $response;
});

$response = $router->dispatch($request);

$emitter = new SapiEmitter;

$emitter->emit($response);