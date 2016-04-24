<?php

use Http\Middleware\ErrorMiddlewareInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface as Container;

/**
 * Application
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class App
{
    /**
     * Finale handler
     * 
     * @var object
     */
    protected $done;

    /**
     * SplQueue
     * 
     * @var object
     */
    protected $queue;

    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Constructor
     * 
     * @param container $container   container
     * @param array     $middlewares middlewares
     */
    public function __construct(Container $container, array $middlewares)
    {
        $this->queue = new SplQueue;
        $this->container = $container;

        foreach ($middlewares as $middleware) {
            $this->queue->enqueue($middleware);
        }
        $this->done  = new Http\Middleware\FinalHandler;
        $this->done->setContainer($container);
    }

    /**
     * Invoke application
     * 
     * @param Request  $request  request
     * @param Response $response response
     * 
     * @return void
     */
    public function __invoke(Request $request, Response $response)
    {
        $err = null;
        $middleware = null;
        $done = $this->done;

        try {

            // Run route operations in here.
            
            // throw new Exception("testtds");

            if ($this->queue->isEmpty()) {
                return $done($request, $response);
            }
            $middleware = $this->getNext();

            if ($middleware instanceof ErrorMiddlewareInterface) {
                return $middleware($err, $request, $response, $this);
            }
            return $middleware($request, $response, $this);

        }  catch (Exception $exception) {

            if ($middleware == null) {  // If we have router errors middleware variable comes null.
                $middleware = $this->getNext();
            }
            if ($middleware instanceof ErrorMiddlewareInterface) {
                return $middleware($exception, $request, $response, $this);
            }
            return $middleware($request, $response, $this);
        }
    }

    /**
     * Get next middleware
     * 
     * @return object
     */
    protected function getNext()
    {
        $layer = $this->queue->dequeue();
        return $layer;
    }

    /**
     * Returns to container
     * 
     * @return object
     */
    public function getContainer()
    {
        return $this->container;
    }

}