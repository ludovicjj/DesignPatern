<?php


namespace App\Middleware;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AppMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $response = new Response();

        if ($path === "/blog") {
            $body = Psr7\Utils::streamFor('Current page is blog');
        } elseif ($path === "/contact") {
            $body = Psr7\Utils::streamFor('Current page is contact');
        } else {
            $body = Psr7\Utils::streamFor('Not found');
            $response = $response->withStatus(404);
        }
        $response = $response->withBody($body);

        return $response;
    }
}