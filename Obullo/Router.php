<?php

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Router {
    
    protected $path;
    protected $group;
    protected $queue;
    protected $handler;
    protected $request;
    protected $response;
    protected $routes = array();
    
    public function __construct(Request $request, Response $response, SplQueue $queue)
    {
        $this->path     = $request->getUri()->getPath();
        $this->queue    = $queue;
        $this->request  = $request;
        $this->response = $response;
    }

    public function rewrite($method, $pattern, $rewrite)
    {
        if (in_array($this->request->getMethod(), (array)$method)) {
            $this->path = '/'.ltrim(preg_replace('#^'.$pattern.'$#', $rewrite, $this->path), '/');
        }
    }

    public function map($method, $pattern, $handler = null)
    {
        if (! in_array($this->request->getMethod(), (array)$method)) {
            $this->queue->enqueue(['callable' => new Http\Middleware\NotAllowed, 'params' => (array)$method]);
            return $this;
        }
        $this->route($method, $pattern, $handler);
        return $this;
    }

    public function group($pattern, $callable)
    {   
        if (! is_callable($callable)) {
            throw new InvalidArgumentException("Group method second parameter must be callable.");
        }
        $this->group = ($this->group == null) ? new Group : $this->group;
        $this->group->enqueue($pattern, $callable);
        return $this->group;
    }

    protected function route($method, $pattern, $handler)
    {
        if (preg_match('#^'.$pattern.'$#', $this->path, $params)) {

            if (is_string($handler)) {
                if (strpos($handler, '$') !== false && strpos($pattern, '(') !== false) {
                    $handler = preg_replace('#^'.$pattern.'$#', $handler, $this->path);
                }
                $this->handler = $handler;
                var_dump($handler);
                // $dispatcher = new RouteDispatcher($handler);
            }
            if (is_callable($handler)) {
                array_shift($params);
                $this->handler = $handler($this->request, $this->response, array_values($params));
            }
        }
    }

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

    public function getHandler()
    {
        $this->popGroup();

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

    public function add()
    {
        // $params = func_get_args();
        // $name   = $params[0];
        // unset($params[0]);

        // if (! class_exists($middleware, false)) {
        //     $this->queue->enqueue(['callable' => new $middleware, 'params' => $value['params']]);
        // }
    }

}
