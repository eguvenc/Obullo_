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
     * @param container $container container
     */
    public function __construct(Container $container)
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

        $this->done  = new Http\Middleware\FinalHandler;
        $this->done->setContainer($container);
    }

    /**
     * Add middleware
     *
     * @return object group
     */
    public function add()
    {
        $params = func_get_args();
        $name   = $params[0];
        unset($params[0]);

        $middleware = '\Http\Middleware\\'.$name;
        if (! class_exists($middleware, false)) {
            $this->queue->enqueue(['callable' => new $middleware, 'params' => $params]);
        }
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
            $parameters = $middleware['params'];

            if (count($parameters) > 0) {
                array_push($parameters, array($request, $response, $this));   
                return call_user_func_array($middleware['callable'], $parameters);
            }

            return $middleware['callable']($request, $response, $this);

        }  catch (Exception $exception) {

            $middleware = new Http\Middleware\Error;

            return $middleware['callable']($exception, $request, $response, $done);
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