<?php

namespace Http\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Error implements ErrorMiddlewareInterface
{
    /**
     * Execute the middleware
     *
     * @param mixed                  $err      error
     * @param ServerRequestInterface $request  request
     * @param ResponseInterface      $response response
     * @param Callable               $next     callable
     *
     * @return ResponseInterface
     */
    public function __invoke($err, Request $request, Response $response, callable $next = null)
    {
        if ($err) {

            echo $err->getMessage();

            return $response;
        }
        return $next($request, $response);
    }
}