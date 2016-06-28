<?php

namespace Obullo\Layer;

use Obullo\ServerRequestFactory;
use Psr\Log\LoggerInterface as Logger;
use Interop\Container\ContainerInterface as Container;

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
    protected $folder = 'Controller';
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
    public function get($path = '/', $data = array(), $expiration = '', $folder = 'Controller')
    {
        if (is_numeric($data)) { // Set expiration as second param if data not provided
            $expiration = $data;
            $data = array();
        }
        return $this->newRequest($folder, 'GET', $path, $data, $expiration);
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
    public function post($path = '/', $data = array(), $expiration = '', $folder = 'Controller')
    {
        if (is_numeric($data)) {  // Set expiration as second param if data not provided
            $expiration = $data;
            $data = array();
        }
        return $this->newRequest($folder, 'POST', $path, $data, $expiration);
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
    protected function newRequest($folder, $method, $path = '/', $data = array(), $expiration = '')
    {
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
         * Dispatch route errors
         */
        if ($layer->getError() != '') {
            $error = $layer->getError();
            $layer->restore();
            return Error::getError($error);
        }
        /**
         * Cache support
         */
        if ($this->params['cache'] && $response = $this->container->get('cache')->get($id)) {   
            $layer->restore();
            return base64_decode($response);
        }

        $response = $layer->execute($path); // Execute the process

        /**
         * Cache support
         */
        if (is_numeric($expiration)) {
            $this->container->get('cache')->set($id, base64_encode($response), (int)$expiration); // Write to Cache
        }
        $layer->restore();  // Restore controller objects

        if (is_array($response) && isset($response['error'])) {
            return Error::getError($response);  // Error template support
        }
        $this->log($path, $id, $response);

        return (string)$response;
    }

    /**
     * Flush cache
     * 
     * @param string $path uri string
     * @param array  $data params
     * 
     * @return boolean
     */
    public function flush($path, $data = array())
    {
        $flush = new Flush(
            $this->logger,
            $this->container->get('cache')
        );
        return $flush->uri($uri, $data);
    }

    /**
     * Log response data
     * 
     * @param string $path      uri string
     * @param string $id       layer id
     * @param string $response data
     * 
     * @return void
     */
    protected function log($path, $id, $response)
    {
        $uriString = md5($this->container->get('request')->getMaster()->getUri()->getPath());

        $this->logger->debug(
            'Layer: '.strtolower($path), 
            array(
                'id' => $id, 
                'output' => '<div class="obullo-layer" data-unique="u'.uniqid().'" data-id="'.$id.'" data-uristring="'.$uriString.'">' .$response. '</div>',
            )
        );
    }
    
}