<?php

namespace Obullo;

use Http\Middleware\ErrorMiddlewareInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface as Container;

use SplQueue;
use Http\Middleware\FinalHandler;

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
 
        include APP. 'routes.php';

        $this->done  = new FinalHandler($this, $router);
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

        }  catch (Exception $exception) {

            $error = new Http\Middleware\Error;

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

        $middleware = '\Http\Middleware\\'.$name;
        if (! class_exists($middleware, false)) {
            $this->queue->enqueue(['callable' => new $middleware, 'params' => $params]);
        }
    }

    /**
     * Add service provider
     * 
     * @param string $class name
     *
     * @return void
     */
    public function addServiceProvider($class)
    {
        $this->container->addServiceProvider($class);
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

    /**
     * Dispatch controller
     * 
     * @param object $request  request
     * @param object $response response
     * 
     * @return object response
     */
    public function dispatch($request, $response)
    {
        $this->container->share('response', $response);  // Refresh objects
        $this->container->share('request', $request);

        return $response->getBody()->write('Hello World !');
    }

    // public function call($request, $response)
    // {
    //     $router = $this->container->get('router');

    //     $file      = FOLDERS .$router->getAncestor('/').$router->getFolder('/').$router->getClass().'.php';
    //     $className = '\\'.$router->getNamespace().$router->getClass();

    //     if (! is_file($file)) {
    //         $router->clear();  // Fix layer errors.
    //         return false;

    //     } else {

    //         include $file;

    //         $controller = new $className($this->container);
    //         $controller->container = $this->container;

    //         if (method_exists($controller, '__invoke')) {  // Assign layout variables
    //             $controller();
    //         }
    //         if (! method_exists($controller, $router->getMethod())
    //             || substr($router->getMethod(), 0, 1) == '_'
    //         ) {
    //             $router->clear();  // Fix layer errors.
    //             return false;
    //         }
    //     }
    //     $this->container->share('response', $response);  // Refresh objects
    //     $this->container->share('request', $request);

    //     $result = call_user_func_array(
    //         array(
    //             $controller,
    //             $router->getMethod()
    //         ),
    //         array_slice($controller->request->getUri()->getRoutedSegments(), $router->getArity())
    //     );
    //     if ($result instanceof Response) {
    //         return $result;
    //     }
    // }

}