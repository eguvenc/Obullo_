<?php

namespace AppBundle\Middleware;

use Exception;
use Zend\Diactoros\Stream;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;

class NotFound implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Invoke middleware
     *
     * @param Request  $request  Psr\Http\Message\ServerRequestInterface
     * @param Response $response Psr\Http\Message\ResponseInterface
     *
     * @return object response
     */
    public function __invoke(Request $request, Response $response)
    {
        $stream = new Stream(fopen('php://temp', 'r+'));
        $stream->write($this->container->get('view')->render('templates::404.phtml'));
                
        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->withBody($stream);
    }
}
