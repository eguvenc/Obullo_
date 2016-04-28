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
     * Router
     * 
     * @var object
     */
    protected $router;

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
        $this->container = $container;
        $this->queue     = new SplQueue;

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
        $middleware = null;
        $done   = $this->done;
        
        if ($this->router == null) {
            $router = $this->router = new Router($request, $response);
        }
        $container = $this->container;
        $container->share('request', $request);
        $container->share('response', $response);

        try {
            if ($this->queue->isEmpty()) {
                return $done($request, $response);
            }
            
            include APP. 'routes.php';

            $result = $router->dispatch();
            if ($result instanceof $response) {
                return $result;
            }
            $middleware = $this->getNext();

            return $middleware($request, $response, $this);

        }  catch (Exception $exception) {

            $middleware = new Http\Middleware\Error;

            return $middleware($exception, $request, $response, $this);
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