<?php

namespace Tests\Middleware;

use App\Middleware\AppMiddleware;
use App\Middleware\HandlerMiddleware;
use App\Middleware\PoweredByAMiddleware;
use App\Middleware\PoweredByBMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Generator;

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

    public function testResponseHasHeaderWithValueFromMiddlewareA(): void
    {
        $request = new ServerRequest("GET", "/");
        $response = $this->registerMiddlewares($request, new PoweredByAMiddleware(), new PoweredByBMiddleware());

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $header = $response->getHeader("X-Powered-By");
        $this->assertTrue(in_array("Middleware-A", $header,true));
    }

    public function testResponseHasHeaderWithValueFromMiddlewareB(): void
    {
        $request = new ServerRequest("GET", "/");
        $response = $this->registerMiddlewares($request, new PoweredByBMiddleware(), new PoweredByAMiddleware());

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $header = $response->getHeader("X-Powered-By");
        $this->assertTrue(in_array("Middleware-B", $header,true));
    }

    /**
     * @dataProvider provideRequestUri
     * @param string $uri
     * @param array $responseData
     */
    public function testResponseBodyAndStatusCode(string $uri, array $responseData): void
    {
        $request = new ServerRequest('GET', $uri);
        $response = $this->registerMiddlewares($request, new AppMiddleware());

        $this->assertEquals($responseData["body"], $response->getBody()->getContents());
        $this->assertEquals($responseData["code"], $response->getStatusCode());
    }

    /**
     * @return Generator
     */
    public function provideRequestUri(): Generator
    {
        yield [
            "/blog",
            [
                "body" => "Current page is blog",
                "code" => 200
            ]
        ];

        yield [
            "/contact",
            [
                "body" => "Current page is contact",
                "code" => 200
            ]
        ];

        yield [
            "/unknown",
            [
                "body" => "Not found",
                "code" => 404
            ]
        ];
    }

    /**
     * Init HandlerMiddleware and register middleware(s).
     * Handle all registered middlewares and return response.
     *
     * @param ServerRequestInterface $request
     * @param mixed ...$middlewares
     * @return ResponseInterface
     */
    private function registerMiddlewares(ServerRequestInterface $request, ...$middlewares): ResponseInterface
    {
        $handler = new HandlerMiddleware();
        foreach ($middlewares as $middleware) {
            $handler->pipe($middleware);
        }

        return $handler->handle($request);
    }
}