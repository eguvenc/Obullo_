<?php

namespace Obullo\Mvc\Request;

use Obullo\ServerRequestFactory;
use Interop\Container\ContainerInterface as Container;

use Obullo\View\Gui\ViewComponent;
use Obullo\View\Gui\ViewComponentInterface;

/**
 * Hmvc
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Hmvc implements HmvcRequestInterface
{
    protected $params;
    protected $container;

    /**
     * Constructor
     *
     * @param object $container ContainerInterface
     * @param array  $params    array parameters
     */
    public function __construct(Container $container, array $params)
    {
        $this->container = $container;
        $this->params = $params;
    }

    /**
     * Create new request
     *
     * @param string $path request uri
     *
     * @return object
     */
    protected function createRequest($path)
    {
        $_SERVER = $_GET = $_POST = array();

        $_SERVER['LAYER_REQUEST'] = true;
        $_SERVER['REQUEST_URI']   = $path;
        $_SERVER['SCRIPT_NAME']   = 'index.php';
        $_SERVER['QUERY_STRING']  = '';

        $request = ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST
        );
        $request->setContainer($this->container);
        return $request;
    }

    /**
     * View Request
     *
     * @param string  $path       uri string
     * @param array   $data       get data
     * @param integer $expiration cache ttl
     * @param string  $folder     folder
     *
     * @return string
     */
    public function view($filename, $data = array(), $expiration = null)
    {
        return $this->get(new ViewComponent($filename), $data, $expiration);
    }

    /**
     * GET Request
     *
     * @param string  $path       uri string
     * @param array   $data       get data
     * @param integer $expiration cache ttl
     * @param string  $folder     folder
     *
     * @return string
     */
    public function get($path = '/', $data = array(), $expiration = null)
    {
        return $this->newRequest('Controller', 'GET', $path, $data, $expiration);
    }

    /**
     * POST Request
     *
     * @param string  $path       uri string
     * @param array   $data       post data
     * @param integer $expiration cache ttl
     * @param string  $folder     folder
     *
     * @return string
     */
    public function post($path = '/', $data = array(), $expiration = null)
    {
        return $this->newRequest('Controller', 'POST', $path, $data, $expiration);
    }

    /**
     * Create new request
     *
     * Layer always must create new instance other ways we can't use nested sub requests.
     *
     * @param string  $folder     folder
     * @param string  $method     request method
     * @param string  $path       uri string
     * @param array   $data       request data
     * @param integer $expiration ttl
     *
     * @return string
     */
    protected function newRequest($folder, $method, $path = '/', $data = array(), $expiration = null)
    {
        if ($path instanceof ViewComponentInterface) {
            $component = $path;
            $path      = $component->getPath();
            $folder    = 'View/Controller';
        }
        $path = trim($path, '/');

        $subRequest = new SubRequest(
            $this->container,
            $this->params,
            $folder
        );
        $subRequest->clear();
        $subRequest->newRequest(
            $this->createRequest($path),
            $method,
            $data
        );
        $id = $subRequest->getId();

        /**
         * Read Cache
         */
        if ($this->params['cache']) {
            $cache = $this->container->get('cache');

            if ($cache->has($id)) {
                $subRequest->restore();

                // Create event listener for cached strtolower($path) & $id data

                return base64_decode($cache->get($id));
            }
        }
        $response = $subRequest->execute($path); // Execute the process

        /**
         * Save Cache
         */
        if (is_numeric($expiration)) {
            $this->container->get('cache')->set($id, base64_encode($response), (int)$expiration);
        }
        $subRequest->restore();  // Restore controller objects

        if (is_array($response) && isset($response['error'])) {
            return Error::getError($response);  // Error template support
        }
        
        // Create event listener for strtolower($path) & $id data

        return (string)$response;
    }
    
    /**
     * Returns to flush object
     *
     * @return object
     */
    public function flush()
    {
        return new Flush($this->container);
    }
}
