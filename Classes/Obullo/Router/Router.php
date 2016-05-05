<?php

namespace Obullo\Router;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use SplQueue;
use Obullo\Router\Group;
use InvalidArgumentException;
use Http\Middleware\NotAllowed;
use Obullo\Router\Filter\FilterTrait;
use Interop\Container\ContainerInterface as Container;

/**
 * Router
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Router
{
    use AddTrait;
    use FilterTrait;

    protected $path;
    protected $group;
    protected $queue;
    protected $handler;
    protected $request;
    protected $response;
    protected $count = 0;
    protected $routes = array();
    protected $dispatched = false;
    
    /**
     * Constructor
     * 
     * @param Container    $container container
     * @param PathResolver $resolver  resolver
     *
     * @return void
     */
    public function __construct(Container $container, $resolver = null)
    {
        $this->path     = $container->get('request')->getUri()->getPath();
        $this->request  = $container->get('request');
        $this->response = $container->get('response');
    }

    /**
     * Set queue for middlewares
     * 
     * @param SplQueue $queue queue
     *
     * @return void
     */
    public function setQueue(SplQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Rewrite all http requests
     * 
     * @param string $method  method
     * @param string $pattern regex pattern
     * @param string $rewrite replacement path
     * 
     * @return void
     */
    public function rewrite($method, $pattern, $rewrite)
    {
        if (in_array($this->request->getMethod(), (array)$method)) {
            $this->path = '/'.ltrim(preg_replace('#^'.$pattern.'$#', $rewrite, $this->path), '/');
        }
    }

    /**
     * Create a route
     * 
     * @param string $method  method
     * @param string $pattern regex pattern
     * @param mixed  $handler mixed
     * 
     * @return void
     */
    public function map($method, $pattern, $handler = null)
    {
        ++$this->count;
        $this->routes[$this->count] = [
            'method' => (array)$method,
            'pattern' => $pattern,
            'handler' => $handler,
            'middlewares' => array()
        ];
        return $this;
    }

    /**
     * Create group
     * 
     * @param string   $pattern  pattern
     * @param callable $callable callable
     * 
     * @return object
     */
    public function group($pattern, $callable)
    {   
        if (! is_callable($callable)) {
            throw new InvalidArgumentException("Group method second parameter must be callable.");
        }
        $this->group = ($this->group == null) ? new Group($this->request) : $this->group;
        $this->group->enqueue($pattern, $callable);
        return $this->group;
    }

    /**
     * Route process
     * 
     * @return void
     */
    protected function dispatch()
    {
        foreach ($this->routes as $r) {

            if (! in_array($this->request->getMethod(), (array)$r['method'])) {
                $this->queue->enqueue(['callable' => new NotAllowed, 'params' => (array)$r['method']]);
                continue;
            }
            $handler = $r['handler'];
            $pattern = $r['pattern'];
            
            if (trim($pattern, "/") == trim($this->path, "/") || preg_match('#^'.$pattern.'$#', $this->path, $params)) {

                $this->queue($r['middlewares']);

                if (is_string($handler)) {
                    if (strpos($handler, '$') !== false && strpos($pattern, '(') !== false) {
                        $handler = preg_replace('#^'.$pattern.'$#', $handler, $this->path);
                    }
                    $this->handler = $handler;
                    // var_dump($handler);
                    // $dispatcher = new RouteDispatcher($handler);
                }
                if (is_callable($handler)) {
                    array_shift($params);
                    $this->handler = $handler($this->request, $this->response, array_values($params));

                    // $this->handler = ['callable' => $handler, 'args' => array_values($params)];
                    // var_dump($handler);
                }
            }
        }
        $this->dispatched = true;
    }

    /**
     * Group process
     * 
     * @return void
     */
    public function popGroup()
    {
        if ($this->group == null) {
            return;
        }
        $exp   = explode("/", trim($this->path, "/"));
        $group = $this->group->dequeue();

        if (in_array(trim($group['pattern'], "/"), $exp, true)) {
            $group['callable']($this->request, $this->response);
            $this->queue($group['middlewares']);
        }
        if (! $this->group->isEmpty()) {
            $this->popGroup();
        }
    }

    /**
     * Get executed handler result
     * 
     * @return object|string
     */
    public function getHandler()
    {
        if (! $this->dispatched) {  // Run one time, this function runs twice
            $this->popGroup();      // in App.php invoke() method.
            $this->dispatch();
        }
        return $this->handler;
    }

    /**
     * Queue middlewares
     * 
     * @param array $middlewares middlewares
     * 
     * @return void
     */
    protected function queue($middlewares)
    {
        if (empty($middlewares)) {
            return;
        }
        foreach ((array)$middlewares as $value) {
            $middleware = '\Http\Middleware\\'.$value['name'];
            if (! class_exists($middleware, false)) {
                $this->queue->enqueue(['callable' => new $middleware, 'params' => $value['params']]);
            }
        }
    }

    /**
     * Add middleware
     * 
     * @param string $name middleware name
     * @param array  $args arguments
     *
     * @return void
     */
    protected function middleware($name, array $args)
    {
        $this->routes[$this->count]['middlewares'][] = array('name' => $name, 'params' => $args);
    }

}