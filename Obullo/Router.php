<?php

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Router {
    
    protected $path;
    protected $request;
    protected $response;
    protected $routes = array();
    protected $rewrites = array();
    
    public function __construct(Request $request, Response $response)
    {
        $this->path     = $request->getUri()->getPath();
        $this->request  = $request;
        $this->response = $response;
    }

    public function rewrite($method, $pattern, $rewrite)
    {
        $this->path = '/'.ltrim(preg_replace('#^'.$pattern.'$#', $rewrite, $this->path), '/');
        $this->routes[$pattern] = [
            'methods' => $method,
            'pattern' => $pattern,
            'handler' => $this->path,
            'rewrite' => true,
            'orig_path' => $this->request->getUri()->getPath(),
        ];
    }

    public function map($method, $pattern, $handler = null)
    {
        $this->routes[$pattern] = [
            'methods' => $method,
            'pattern' => $pattern,
            'rewrite' => false,
            'handler' => $handler,
        ];
    }

    public function group($pattern, $callback)
    {   
        $exp = explode("/", trim($this->path, "/"));
        if (! in_array(trim($pattern, "/"), $exp, true)) {
            return;
        }
        if (! is_callable($callback)) {
            throw new InvalidArgumentException("Group method second parameter must be callable.");
        }
        $callback($this->request, $this->response);   
    }


    public function dispatch()
    {
        foreach ($this->routes as $map) {

            $handler = $map['handler'];

            if (! in_array($this->request->getMethod(), (array)$map['methods']) || $map['rewrite']) {
                continue;
            }
            if (preg_match('#^'.$map['pattern'].'$#', $this->path, $params)) {

                array_shift($params);
                $args = array_values($params);

                if (is_string($handler)) {

                    /**
                     * Rewrite support for routes
                     */
                    if (strpos($handler, '$') !== false && strpos($map['pattern'], '(') !== false) {
                        $handler = preg_replace('#^'.$map['pattern'].'$#', $handler, $this->path);
                    }
                    $dispatcher = new RouteDispatcher($handler);

                    var_dump($dispatcher);
                }
                if (is_callable($handler)) {
                    return $handler($this->request, $this->response, $args);
                }

            }
        }

    }
}
