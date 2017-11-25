<?php

namespace AppBundle\Middleware;

use Zend\Diactoros\Stream;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NotAllowed
{
    /**
     * Invoke middleware
     *
     * @param ServerRequestInterface $request  request
     * @param ResponseInterface      $response respone
     * @param callable               $next     callable
     *
     * @return object ResponseInterface
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $stream = new Stream(fopen('php://temp', 'r+'));
        $stream->write('Http Method Not Allowed');
            
        return $response
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-Type', 'text/html')
            ->withBody($stream);
    }
}
