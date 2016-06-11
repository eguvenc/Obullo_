<?php

namespace Obullo\Container\ServiceProvider\Connector;

use RuntimeException;
use UnexpectedValueException;
use Interop\Container\ContainerInterface as Container;
use Obullo\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Memcache Connection Provider
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Memcache extends AbstractServiceProvider
{
    /**
     * Memcache config array
     * 
     * @var array
     */
    protected $params;

    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Memcache extension
     * 
     * @var object
     */
    protected $memcache;

    /**
     * Constructor
     *
     * @param array $container container
     * @param array $params    connection parameters
     *
     * @uses register()
     */
    public function __construct(Container $container, array $params)
    {
        $this->params = $params;
        $this->container = $container;

        if (! extension_loaded('memcache')) {
            throw new RuntimeException(
                'The memcache extension has not been installed or enabled.'
            );
        }
        $this->register();
    }

    /**
     * Register all connections as shared services
     * 
     * @return void
     */
    public function register()
    {
        foreach ($this->params['connections'] as $key => $val) {

            $key = $this->getConnectionKey($key);

            $this->container->share(
                $key,
                function () use ($val) {
                    return $this->createConnection($val);
                }
            );
        }
    }

    /**
     * Creates Memcache connections
     * 
     * @param array $val current connection array
     * 
     * @return object
     */
    protected function createConnection(array $val)
    {
        if (empty($val['host']) || empty($val['port'])) {
            throw new RuntimeException(
                'Check your memcache configuration, "host" or "port" key seems empty.'
            );
        }
        $this->memcache = new \Memcache;

        // http://php.net/manual/tr/memcache.connect.php
        // If you have pool of memcache servers, do not use the connect() function. 
        // If you have only single memcache server then there is no need to use the addServer() function.

        // Check single server connection

        if (empty($this->params['nodes'][0]['host'])) {  // If we haven't got any nodes use connect() method

            $connect = true;
            if ($val['options']['persistent']) {
                $connect = $this->memcache->pconnect($val['host'], $val['port'], $val['options']['timeout']);
            } else {
                $connect = $this->memcache->connect($val['host'], $val['port'], $val['options']['timeout']);
            }
            if (! $connect) {
                throw new RuntimeException(
                    sprintf(
                        "Memcache connection error could not connect to host: %s.",
                        $val['host']
                    )
                );
            }
        }
        return $this->memcache;
    }

    /**
     * Retrieve shared Memcache connection instance from connection pool
     *
     * @param array $params provider parameters
     * 
     * @return object Memcache
     */
    public function shared($params = array())
    {
        if (empty($params['connection'])) {
            throw new RuntimeException(
                sprintf(
                    "Memcache provider requires connection parameter. <pre>%s</pre>",
                    "\$container->get('memcache')->shared(['connection' => 'default']);"
                )
            );
        }
        if (! isset($this->params['connections'][$params['connection']])) {
            throw new UnexpectedValueException(
                sprintf(
                    'Connection key %s does not exist in your memcache configuration.',
                    $params['connection']
                )
            );
        }
        $key = $this->getConnectionKey($params['connection']);

        return $this->container->get($key);  // Return to shared connection
    }

    /**
     * Create a new Memcache connection
     * 
     * If you don't want to add it to config file and you want to create new one.
     * 
     * @param array $params connection parameters
     * 
     * @return object Memcache client
     */
    public function factory($params = array())
    {
        $key = $this->getConnectionId($params);

        if (! $this->container->has($key)) { // Create shared connection if not exists

            $this->container->share(
                $key,
                function () use ($params) {
                    return $this->createConnection($params);
                }
            );
        }
        return $this->container->get($key);
    }

    /**
     * Close all "active" connections
     */
    public function __destruct()
    {
        foreach (array_keys($this->params['connections']) as $key) {

            $key = $this->getConnectionKey($key);

            if ($this->container->hasShared($key, true)) {
                $this->container->get($key)->close();
            }
        }
    }

}
