<?php

namespace Obullo\Container\ServiceProvider\Connector;

use RuntimeException;
use UnexpectedValueException;
use Interop\Container\ContainerInterface as Container;
use Obullo\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Mongo Connector
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Mongo extends AbstractServiceProvider
{
    /**
     * Mongo config array
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
     * Mongo extension
     * 
     * @var string
     */
    protected $mongoClass;

    /**
     * Constructor
     *
     * Automatically check if the Mongo PECL extension has been installed / enabled.
     *
     * @param string $container container
     * @param array  $params    config parameters
     *
     * @uses register()
     */
    public function __construct(Container $container, array $params)
    {
        $this->container  = $container;
        $this->params     = $params;
        $this->mongoClass = (version_compare(phpversion('mongo'), '1.3.0', '<')) ? '\Mongo' : '\MongoClient';

        if (! class_exists($this->mongoClass, false)) {
            throw new RuntimeException(
                sprintf(
                    'The %s extension has not been installed or enabled.',
                    trim($this->mongoClass, '\\')
                )
            );
        }
        $this->register();
    }

    /**
     * Register all connections as shared services ( Works one time )
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
                    return $this->createConnection($val['server'], $val);
                }
            );
        }
    }

    /**
     * Creates mongo connections
     *
     * @param string $server      dsn
     * @param array  $connections connection parameters
     *
     * @return void
     */
    protected function createConnection($server, array $connections)
    {
        $options = isset($connections['options']) ? $connections['options'] : ['connect' => true];

        return new $this->mongoClass($server, $options);
    }

    /**
     * Retrieve shared mongo connection instance from connection pool
     *
     * @param array $params provider parameters
     *
     * @return object MongoClient
     */
    public function shared($params = array())
    {
        if (! isset($params['connection'])) {
            $params['connection'] = array_keys($this->params['connections'])[0];  //  Set default connection
        }
        if (! isset($this->params['connections'][$params['connection']])) {
            throw new UnexpectedValueException(
                sprintf(
                    'Connection key %s does not exist in your mongo.php config file.',
                    $params['connection']
                )
            );
        }
        $key = $this->getConnectionKey($params['connection']);

        return $this->container->get($key);  // return to shared connection
    }

    /**
     * Create a new mongo connection
     *
     * If you don't want to add it to config file and you want to create new one.
     *
     * @param array $params connection parameters
     *
     * @return object mongo client
     */
    public function factory($params = array())
    {
        if (! isset($params['server'])) {
            throw new UnexpectedValueException("Mongo connection provider requires server parameter.");
        }
        $key = $this->getConnectionId($params);

        if (! $this->container->has($key)) { //  create shared connection if not exists

            $this->container->share(
                $key,
                function () use ($params) {
                    return $this->createConnection($params['server'], $params);
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

                $connection = $this->container->get($key);
                foreach ($connection->getConnections() as $con) {
                    $connection->close($con['hash']);
                }
            }
        }
    }

}
