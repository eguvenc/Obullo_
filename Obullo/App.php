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
        $response        = new Zend\Diactoros\Response;
        $request         = Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $router          = new Router($request, $response, $this->queue);
        $this->router    = $router;
 
        include APP. 'routes.php';

        $container->share('response', $response);
        $container->share('request', $request);

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
        $done       = $this->done;

        try {

            if ($this->queue->isEmpty()) {
                return $done($request, $response);
            }
            $handler = $this->router->getHandler();

            if ($handler instanceof $response) {
                return $handler;
            }
            $middleware = $this->queue->dequeue();

            return $middleware($request, $response, $this);

        }  catch (Exception $exception) {

            $middleware = new Http\Middleware\Error;

            return $middleware($exception, $request, $response, $done);
        }
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