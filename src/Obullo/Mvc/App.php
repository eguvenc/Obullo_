<?php

namespace Obullo\Mvc;

use SplQueue;
use Throwable;
use Exception;

use Obullo\Mvc\ControllerResolver;
use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;

use Obullo\Container\ConfigurationInterface;
use Obullo\Mvc\Bundle\BundleInterface as Bundle;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface as Container;

/**
 * Application
 *
 * @copyright 2009-2017 Obullo
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
     * Request path
     *
     * @var string
     */
    protected $path;

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
     * App bundles
     *
     * @var array
     */
    protected $bundles = array();

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
        $this->path      = $container->get('request')->getUri()->getPath();

        $this->router->setQueue($this->queue);
    }

    /**
     * Set routeable bundles
     *
     * @param object $bundle
     */
    public function addRouteableBundle(Bundle $bundle)
    {
        $this->bundles[] = $bundle;
    }

    /**
     * Start application & create routeable bundles
     *
     * @return void
     */
    public function start()
    {
        if (empty($this->bundles[0])) {
            die("Bundle could not be initialized. Check your '".getenv("APP_ENV")."_app.php' file.");
        }
        foreach ($this->bundles as $bundle) {
            if ($bundle->getMatch()->hasMatch($this->path)) {
                $this->createBundle($bundle);
            }
        }
        if (! defined('APP_PATH')) {
            $this->createBundle($this->bundles[0]);  // Create default bundle
        }
    }

    /**
     * Create routeable bundle
     *
     * @param object Bundle
     *
     * @return void
     */
    protected function createBundle(Bundle $bundle)
    {
        $router = $this->router;
        $name   = $bundle->getName();

        define('APP_PATH', ROOT .'src/'.$name.'/');
        define('APP_NAME', $name);

        include APP_PATH .'Routes.php';

        $bundle->setApplication($this);
        $bundle->addServiceProviders();
        $bundle->addMiddlewares();
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
        $errorMiddleware    = "\\". APP_NAME .'\Middleware\Error';
        $notFoundMiddleware = "\\". APP_NAME .'\Middleware\NotFound';

        try {
            $handler = $this->router->getHandler();

            if ($this->queue->isEmpty()) {  // Execute final handler

                if ($handler instanceof Response) {
                    return $handler;
                }
                $resolver = new ControllerResolver($this->container, $request, $response);
                $result   = $resolver->dispatch($handler);
                
                if (! $result) {
                    $notFound = new $notFoundMiddleware;
                    $notFound->setContainer($this->container);
                    return $notFound($request, $response);
                }
                if ($result instanceof $response) {
                    $response = $result;
                }
                return $response;
            }
            $middleware = $this->queue->dequeue();

            if (! empty($middleware['params'])) {
                $args = array($request, $response, $this);
                array_push($args, $middleware['params']);

                return call_user_func_array($middleware['callable'], $args);
            }
            return $middleware['callable']($request, $response, $this);
        } catch (Throwable $throwable) {  // Throwable is not problem for older php versions.
            $error = new $errorMiddleware;
            $error->setContainer($this->container);
            return $error($throwable, $request, $response);
        } catch (Exception $exception) {
            $error = new $errorMiddleware;
            $error->setContainer($this->container);
            return $error($exception, $request, $response);
        }
    }

    /**
     * Add middleware
     *
     * @return application
     */
    public function add()
    {
        $params = func_get_args();
        $name   = $params[0];
        unset($params[0]);

        $middleware = '\\'. APP_NAME .'\Middleware\\'.$name;

        if (! class_exists($middleware, false)) {
            $this->queue->enqueue(['callable' => new $middleware, 'params' => $params]);
        }
        return $this;
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
     * Returns to bundle objects
     *
     * @return array
     */
    public function getBundles()
    {
        return $this->bundles;
    }

    /**
     * Dummy close function for who want to
     * extend it.
     *
     * @return void
     */
    public function close()
    {
    }
}
