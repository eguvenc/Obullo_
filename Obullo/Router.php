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
        $this->path = '/'.ltrim(preg_replace('#^'.$pattern.'$#', $rewrite, $this->path), '/');
        $this->route($method, $pattern, $this->path, true);
    }

    public function map($method, $pattern, $handler = null)
    {
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

    protected function route($method, $pattern, $handler, $rewrite = false)
    {
        if (! in_array($this->request->getMethod(), (array)$method) || $rewrite) {
            return;
        }
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

            if (! empty($group['middlewares'])) {
                foreach ($group['middlewares'] as $name) {
                    $middleware = '\Http\Middleware\\'.$name;
                    $this->queue->enqueue(new $middleware);
                }
            }
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

    public function add($middleware)
    {

    }

}
