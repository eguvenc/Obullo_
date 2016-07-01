<?php

namespace Obullo\Mvc;

use SplQueue;
use Throwable;
use Exception;

use App\Http\Middleware\FinalHandler;
use App\Http\Middleware\ErrorMiddlewareInterface;
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
        $this->router    = $container->get('router');

        $this->router->setQueue($this->queue);
        $router = $this->router;
 
        include APP . 'routes.php';

        $logger = $container->get('logger');
        $logger->debug("-------------------------------------------------------------");
        $logger->debug(
            'Request Uri', [$container->get('request')->getUri()->getPath()]
        );

        $this->done  = new FinalHandler;
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

            $handler = $this->router->getHandler();

            if ($this->queue->isEmpty()) {
                return $done($request, $response, null, $handler);
            }
            $middleware = $this->queue->dequeue();

            if (! empty($middleware['params'])) {
                
                $args = array($request, $response, $this);
                array_push($args, $middleware['params']);

                return call_user_func_array($middleware['callable'], $args);
            }

            return $middleware['callable']($request, $response, $this);

        } catch (Throwable $throwable) {
            
            $error = new \App\Http\Middleware\Error;

            return $error($throwable, $request, $response, $done);

        } catch (Exception $exception) {

            $error = new \App\Http\Middleware\Error;

            return $error($exception, $request, $response, $done);
        }
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

        $middleware = '\App\Http\Middleware\\'.$name;
        if (! class_exists($middleware, false)) {
            $this->queue->enqueue(['callable' => new $middleware, 'params' => $params]);
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