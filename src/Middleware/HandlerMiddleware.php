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
    private $middlewares = [];

    /**
     * @var int $index
     */
    private $index = 0;

    /**
     * Register middleware
     *
     * @param MiddlewareInterface $middleware
     */
    public function pipe(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
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
            // Init response if current middleware call next middleware and next middleware is null
            return new Response();
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