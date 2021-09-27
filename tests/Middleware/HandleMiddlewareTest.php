<?php

namespace Tests\Middleware;

use App\Middleware\HandlerMiddleware;
use App\Middleware\PoweredByAMiddleware;
use App\Middleware\PoweredByBMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class HandleMiddlewareTest extends TestCase
{
    public function testCountMiddlewaresRegister(): void
    {
        $handler = new HandlerMiddleware();
        $handler->pipe(new PoweredByAMiddleware());
        $handler->pipe(new PoweredByBMiddleware());

        $reflectionClass = new \ReflectionClass(HandlerMiddleware::class);
        $property = $reflectionClass->getProperty("middlewares");
        $property->setAccessible(true);
        $middlewares = $property->getValue($handler);

        $this->assertCount(2, $middlewares);
    }

    public function testResponseHasHeader(): void
    {
        $request = ServerRequest::fromGlobals();

        $handler = new HandlerMiddleware();
        $handler->pipe(new PoweredByAMiddleware());
        $handler->pipe(new PoweredByBMiddleware());

        $response = $handler->handle($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $header = $response->getHeader("X-Powered-By");
        $this->assertTrue(in_array("Middleware-A", $header,true));
    }
}