<?php

namespace App\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HandlerMiddleware implements RequestHandlerInterface
{
    /**
     * @var MiddlewareInterface[] $middlewares
     */
    public $middlewares = [];

    /**
     * @var int $index
     */
    private $index = 0;

    /**
     * @var ResponseInterface $response
     */
    private $response;

    /**
     * Register middleware
     *
     * @param MiddlewareInterface $middleware
     */
    public function pipe(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
        $this->response = new Response();
    }


    /**
     * Execute middleware
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        $this->index++;

        if (is_null($middleware)) {
            return new Response(200, ["X-Powered-By" => "toto"]);
        }
        return $middleware->process($request, $this);
    }

    /**
     * @return MiddlewareInterface|null
     */
    private function getMiddleware(): ?MiddlewareInterface
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            return $this->middlewares[$this->index];
        }
        return null;
    }
}