<?php

require "../vendor/autoload.php";

use App\Middleware\HandlerMiddleware;
use App\Middleware\PoweredByAMiddleware;
use App\Middleware\PoweredByBMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use function Http\Response\send;

$request = ServerRequest::fromGlobals();

$dispatcher = new HandlerMiddleware();
$dispatcher->pipe(new PoweredByAMiddleware());
$dispatcher->pipe(new PoweredByBMiddleware());
send($dispatcher->handle($request));