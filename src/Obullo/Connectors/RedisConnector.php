<?php

namespace Obullo\Connectors;

use ReflectionClass;
use RuntimeException;

/**
 * Redis Connector
 *
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class RedisConnector implements ConnectorInterface
{
    /**
     * Redis connection
     *
     * @var object
     */
    protected $redis;

    /**
     * Redis config array
     *
     * @var array
     */
    protected $connectionParams = array();

    /**
     * Constructor
     *
     * @param array $container container
     * @param array $params    connection parameters
     *
     * @uses register()
     */
    public function __construct(array $connectionParams)
    {
        $this->connectionParams = $connectionParams;

        if (! extension_loaded('redis')) {
            throw new RuntimeException(
                'The redis extension has not been installed or enabled.'
            );
        }
    }

    /**
     * Creates Redis connections
     *
     * @return object
     */
    public function getConnection()
    {
        $params = $this->connectionParams;

        if (empty($params['host']) || empty($params['port'])) {
            throw new RuntimeException(
                'Check your redis configuration, "host" or "port" key seems empty.'
            );
        }
        $this->redis = new \Redis;
        $timeout = (empty($params['options']['timeout'])) ? 0 : $params['options']['timeout'];

        if (isset($params['options']['persistent']) && $params['options']['persistent']) {
            $this->redis->pconnect($params['host'], $params['port'], $timeout, null, $params['options']['attempt']);
        } else {
            $this->redis->connect($params['host'], $params['port'], $timeout);
        }
        if (! empty($params['options']['auth'])) {  // Do we need reauth for slaves ?

            $auth = $this->redis->auth($params['options']['auth']);

            if (! $auth) {
                throw new RuntimeException("Redis authentication error, wrong password.");
            }
        }
        $this->setOptions($params['options']);

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
     * Close active connection
     */
    public function __destruct()
    {
        // Try to close connection by register_shutdown
        // $this->redis->close();
    }
}
