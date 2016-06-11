<?php

namespace Obullo\Container\ServiceProvider\Connector;

use ReflectionClass;
use RuntimeException;
use UnexpectedValueException;
use Interop\Container\ContainerInterface as Container;
use Obullo\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Redis Connector
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Redis extends AbstractServiceProvider
{
    /**
     * Redis extension
     * 
     * @var object
     */
    protected $redis;

    /**
     * Redis config array
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
     * Default connection items in redis config
     *  
     * @var array
     */
    protected $defaultConnection = array();

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
        $this->defaultConnection = $this->params['connections'][key($this->params['connections'])];

        if (! extension_loaded('redis')) {
            throw new RuntimeException(
                'The redis extension has not been installed or enabled.'
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
     * Creates Redis connections
     * 
     * @param array $val connection values
     * 
     * @return object
     */
    protected function createConnection(array $val)
    {
        if (empty($val['host']) || empty($val['port'])) {
            throw new RuntimeException(
                'Check your redis configuration, "host" or "port" key seems empty.'
            );
        }
        $this->redis = new \Redis;
        $timeout = (empty($val['options']['timeout'])) ? 0 : $val['options']['timeout'];

        if (isset($val['options']['persistent']) && $val['options']['persistent']) {
            $this->redis->pconnect($val['host'], $val['port'], $timeout, null, $val['options']['attempt']);
        } else {
            $this->redis->connect($val['host'], $val['port'], $timeout);
        }
        if (! empty($this->defaultConnection['options']['auth'])) {         // Do we need reauth for slaves ?

            $auth = $this->redis->auth($this->defaultConnection['options']['auth']);

            if (! $auth) {
                throw new RuntimeException("Redis authentication error, wrong password.");
            }
        }
        $this->setOptions($val['options']);
        return $this->redis;
    }

    /**
     * Set parameters
     * 
     * @param array $options parameters
     * 
     * @return void
     */
    protected function setOptions($options = array())
    {
        $prefix = $this->getValue($options, 'prefix');
        $database = $this->getValue($options, 'database');
        $serializer = $this->getValue($options, 'serializer');

        if ($database) {
            $this->redis->select($database);
        }

        if ($serializer == 'php') {
            $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        }
        if ($serializer == 'igbinary') {
            $class = new ReflectionClass("Redis");
            if (! $class->hasConstant("SERIALIZER_IGBINARY")) {
                throw new RuntimeException("Igbinary is not enabled on your redis installation.");
            }
            $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_IGBINARY);
        }
        if ($prefix) {
            $this->redis->setOption(\Redis::OPT_PREFIX, $prefix);
        }
    }

    /**
     * Get redis config options
     * 
     * @param array  $options parameters
     * @param string $item    key
     * 
     * @return string
     */
    protected function getValue($options, $item)
    {
        if (empty($options[$item])) {
            return null;
        }
        if ($options[$item] == 'none') {
            return null;
        }
        return $options[$item];
    }

    /**
     * Retrieve shared Redis connection instance from connection pool
     *
     * @param array $params provider parameters
     * 
     * @return object Redis
     */
    public function shared($params = array())
    {
        if (empty($params['connection'])) {
            throw new RuntimeException(
                sprintf(
                    "Redis provider requires connection parameter. <pre>%s</pre>",
                    "\$container->get('redis')->shared(['connection' => 'default']);"
                )
            );
        }
        if (! isset($this->params['connections'][$params['connection']])) {
            throw new UnexpectedValueException(
                sprintf(
                    'Connection key %s does not exist in your redis configuration.',
                    $params['connection']
                )
            );
        }
        $key = $this->getConnectionKey($params['connection']);

        return $this->container->get($key);  // Return to shared connection
    }

    /**
     * Create a new Redis connection
     * 
     * If you don't want to add it to config file and you want to create new one.
     * 
     * @param array $params connection parameters
     * 
     * @return object Redis client
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