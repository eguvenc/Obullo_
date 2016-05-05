<?php

namespace Http\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Guest
{
    /**
     * Execute the middleware
     * 
     * @param ServerRequestInterface $request  request
     * @param ResponseInterface      $response response
     * @param Callable               $next     callable
     *
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        echo 'Guest Middleware<br/>';

        return $next($request, $response);
    }
}