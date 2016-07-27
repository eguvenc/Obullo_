<?php

namespace Obullo\Mvc\Layer;

use Obullo\ServerRequestFactory;
use Psr\Log\LoggerInterface as Logger;
use Interop\Container\ContainerInterface as Container;
use Obullo\View\Gui\ViewComponentInterface as ViewComponent;

/**
 * Layer Request
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class HmvcRequest implements HmvcRequestInterface
{
    protected $logger;
    protected $params;
    protected $container;

    /**
     * Constructor
     *
     * @param object $container ContainerInterface
     * @param object $logger    LoggerInterface
     * @param array  $params    array parameters
     */
    public function __construct(Container $container, Logger $logger, array $params)
    {
        $this->container = $container;
        $this->params = $params;
        $this->logger = $logger;
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
     * Layer always must create new instance other ways we can't use nested layers.
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
        if ($path instanceof ViewComponent) {
            $component = $path;
            $path      = $component->getPath();
            $folder    = 'View/Controller';
        }
        $path = trim($path, '/');

        $layer = new Layer(
            $this->container,
            $this->params,
            $folder
        );
        $layer->clear();
        $layer->newRequest(
            $this->createRequest($path),
            $method,
            $data
        );
        $id = $layer->getId();

        /**
         * Read Cache
         */
        if ($this->params['cache']) {
            $cacheItem = $this->container->get('cache')->getItem($id);

            if ($cacheItem->isHit()) {
                $layer->restore();
                $this->logger->debug('Mvc Layer (Cached): '.strtolower($path), ['id' => $id]);
                return base64_decode($cacheItem->get());
            }
        }

        $response = $layer->execute($path); // Execute the process

        /**
         * Save Cache
         */
        if (is_numeric($expiration)) {
            $cache = $this->container->get('cache');
            $cacheItem = $cache->getItem($id);
            $cacheItem->set(base64_encode($response));
            $cacheItem->expiresAfter((int)$expiration);
            $cache->save($cacheItem);
        }
        $layer->restore();  // Restore controller objects

        if (is_array($response) && isset($response['error'])) {
            return Error::getError($response);  // Error template support
        }

        $this->logger->debug('Mvc Layer: '.strtolower($path), ['id' => $id]);
        return (string)$response;
    }
    
    /**
     * Returns to flush object
     *
     * @return object
     */
    public function getFlush()
    {
        return new Flush($this->container);
    }
}
