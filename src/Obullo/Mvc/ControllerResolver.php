<?php

namespace Obullo\Mvc;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Obullo\Router\Resolver\ClassResolver;
use Obullo\Router\Resolver\FolderResolver;
use Obullo\Router\Resolver\AncestorResolver;

/**
 * Resolve controller
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ControllerResolver
{
    protected $router;
    protected $folder = 'Controller';

    /**
     * Constructor
     *
     * @param Container $container container
     * @param Request   $request   request
     * @param Response  $response  response
     */
    public function __construct($container, $request, $response)
    {
        $container->share('response', $response);  // Refresh objects
        $container->share('request', $request);

        $this->router    = $container->get('router');
        $this->container = $container;
    }

    /**
     * Detect class & method names
     *
     * This function takes an array of URI segments as
     * input, and sets the current class/method
     *
     * @param string $handler path
     *
     * @return boolean|null
     */
    public function dispatch($handler)
    {
        $resolver = $this->resolve(explode("/", trim($handler, "/")));
        if ($resolver == null) {
            return;
        }
        $this->router->setClass($resolver->getClass());
        $this->router->setMethod($resolver->getMethod());
        
        return $this->call($handler);
    }

    /**
     * Set dispatch folder
     *
     * @param string $folder folder
     *
     * @return void
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     * Resolve segments
     *
     * @param array $segments uri parts
     *
     * @return array|null
     */
    protected function resolve($segments)
    {
        if (empty($segments[0])) {
            return null;
        }
        if (is_dir(APP_PATH . $this->folder .'/'. ucfirst($segments[0]))) {
            $this->router->setFolder($segments[0]);
            $resolver = new FolderResolver;
            return $resolver->resolve($segments);
        }
        $this->router->setFolder(null);
        $resolver = new ClassResolver;
        return $resolver->resolve($segments);
    }

    /**
     * Returns to called filename
     *
     * @return string
     */
    public function getFilename()
    {
        return APP_PATH . $this->folder .'/'. $this->router->getFolder('/') . $this->router->getClass() . 'Controller.php';
    }

    /**
     * Returns to called filename of namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return '\\'. APP_NAME .'\\' . str_replace('/', '\\', $this->folder) .'\\'. $this->router->getNamespace() . $this->router->getClass() . 'Controller';
    }

    /**
     * Call the controller
     *
     * @return string|Response
     */
    public function call($handler)
    {
        $request   = $this->container->get('request');
        $response  = $this->container->get('response');
        $file      = $this->getFilename();
        $className = $this->getNamespace();

        if (! is_file($file)) {
            $this->router->clear();  // Fix layer errors.
            return false;
        } else {
            $method     = $this->router->getMethod() . 'Action'; // Allow to use reserved php names
            $controller = new $className($this->container);

            if (! method_exists($controller, $method)
                || substr($method, 0, 1) == '_'
            ) {
                $this->router->clear();  // Fix layer errors.
                return false;
            }
        }
        return $controller->$method($request, $response);
    }
}
