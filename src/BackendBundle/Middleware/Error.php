<?php

namespace BackendBundle\Middleware;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Error implements ErrorMiddlewareInterface
{
    /**
     * Invoke middleware
     * 
     * @param mixed         $err      string error or object of Exception
     * @param Request       $request  Psr\Http\Message\ServerRequestInterface
     * @param Response      $response Psr\Http\Message\ResponseInterface
     * @param callable|null $out      final handler
     * 
     * @return object response
     */
    public function __invoke($err, Request $request, Response $response, callable $out = null)
    {
        return $out($request, $response, $err);
    }
}