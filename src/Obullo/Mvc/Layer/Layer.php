<?php

namespace Obullo\Mvc\Layer;

use Obullo\Mvc\Controller;
use Obullo\Mvc\ControllerResolver;

use Psr\Log\LoggerInterface as Logger;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Container\ContainerInterface as Container;

/**
 * Layers is a programming technique that delivers you to "Multitier Architecture"
 * to scale your applications.
 *
 * Derived from Java HMVC pattern, 2009 - 2016.
 *
 * http://www.javaworld.com/article/2076128/design-patterns/-
 * hmvc--the-layered-pattern-for-developing-strong-client-tiers.html
 *
 * @author Ersin Guvenc <eguvenc@gmail.com>
 */

/**
 * Layer
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Layer
{
    const CACHE_KEY = 'Mvc_Layer_';

    protected $router;
    protected $folder;
    protected $request;
    protected $controller;
    protected $error = null;
    protected $done = false;
    protected $params = array();
    protected $container = null;
    protected $hashString = '';
    protected static $path = array();

    /**
     * Constructor
     *
     * @param object $container \Obullo\Container\ContainerInterface
     * @param array  $params    config parameters
     * @param string $folder    folder ( Controller or View/Controller )
     */
    public function __construct(Container $container, array $params, $folder = 'Controller')
    {
        $this->params = $params;
        $this->folder = $folder;
        $this->container = $container;

        register_shutdown_function(array($this, 'close'));  // Close current layer
    }

    /**
     * Create new http request
     *
     * @param ServerRequestInterface $request psr7 request
     * @param string                 $method  request method
     * @param array                  $data    any possible data
     *
     * @return void
     */
    public function newRequest(ServerRequestInterface $request, $method = 'GET', $data = array())
    {
        $this->controller = Controller::$instance;      // We need get backup object of main controller
        $this->request = clone Controller::$instance->request;
        $this->router = clone Controller::$instance->router;  // Create copy of original Router class.

        static::$path[] = $this->request->getUri()->getPath();

        // Only create global objects if the request is first.

        if (static::$path[0] == $this->request->getUri()->getPath()) {
            $this->container->share('request.master', $this->request);
            $this->container->share('router.master', $this->router);
        }
        $this->container->share('request', $request);
        $this->container->get('router')->clear();   // Reset router objects we will reuse it for layer

        $this->hashString = '';           // Reset hash string
        $this->setMethod($method, $data); // Must be at the end otherwise POST GET data does not work

        $this->setHash($request->getUri()->getPath());
        $this->setHash($data);
    }

    /**
     * Set Layer Request Method
     *
     * @param string $method layer method
     * @param array  $data   params
     *
     * @return void
     */
    public function setMethod($method = 'GET', $data = array())
    {
        $request = $this->container->get('request')->withMethod(strtoupper($method));

        if (empty($data)) {
            return;
        }
        if ($method == 'POST') {
            $request = $request->withParsedBody($data);
            $this->container->share('request', $request);
        }
        if ($method == 'GET') {
            $request = $request->withQueryParams($data);
            $this->container->share('request', $request);
        }
    }

    /**
     * Execute layer
     *
     * @param string $path uri
     *
     * @return string
     */
    public function execute($path)
    {
        $uri = $this->container->get('request')->getUri();
        $uri = $uri->withPath($path); //  Create unique uri
        
        $resolver = new ControllerResolver(
            $this->container,
            $this->container->get('request'),
            $this->container->get('response')
        );
        $resolver->setFolder($this->folder);

        $result = $resolver->dispatch($uri->getPath());
        
        if ($result == false) {
            return $this->show404($uri->getPath(), $this->container->get('router')->getMethod());
        }
        return $result;
    }

    /**
     * Show404 output and reset layer variables
     *
     * @param string $path   current uri
     * @param string $method current method
     *
     * @return string 404 message
     */
    protected function show404($path, $method)
    {
        $this->clear();
        $this->setError(
            [
                'code' => '404',
                'error' => 'request not found',
                'uri' => $path .'/'. $method .'Action'
            ]
        );
        return $this->getError();
    }

    /**
     * Reset all variables for multiple layer requests.
     *
     * @return void
     */
    public function clear()
    {
        $this->error = null;    // Clear variables otherwise all responses of layer return to same error.
        $this->done  = false;
    }

    /**
     * Restore original controller objects
     *
     * @return void
     */
    public function restore()
    {
        $this->clear();
        $this->container->share('request', $this->request);
        $this->container->share('router', $this->router);

        Controller::$instance = $this->controller;
        Controller::$instance->router  = $this->router;
        Controller::$instance->request = $this->request;

        $this->done = true;
    }

    /**
     * Create layer connection string next we will convert it to connection id.
     *
     * @param mixed $resource string
     *
     * @return void
     */
    protected function setHash($resource)
    {
        if (is_array($resource)) {
            if (sizeof($resource) > 0) {
                $this->hashString .= str_replace('"', '', json_encode($resource));
            }
            return;
        }
        $this->hashString .= $resource;
    }

    /**
     * Returns to Cache key ( layer id ).
     *
     * @return string
     */
    public function getId()
    {
        $hashString = trim($this->hashString);

        return self::CACHE_KEY. sprintf("%u", crc32((string)$hashString));
    }

    /**
     * Set last response error
     *
     * @param array $error data
     *
     * @return object
     */
    public function setError(array $error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * Get last response error
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Close Layer Connections
     *
     * If we have any possible Layer exceptions
     * reset the router variables and restore all objects
     * to complete Layer process. Otherwise we see uncompleted request errors.
     *
     * @return void
     */
    public function close()
    {
        if ($this->done == false) {  // If "done == true" we understand process completed successfully.
            $this->restore();        // otherwise process is failed and we need to restore variables.
            return;
        }
        $this->done = false;
    }
}
