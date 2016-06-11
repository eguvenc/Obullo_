<?php

namespace Obullo\Container\ServiceProvider\Connector;

use RuntimeException;
use UnexpectedValueException;
use Interop\Container\ContainerInterface as Container;
use Obullo\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Memcached Connector
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Memcached extends AbstractServiceProvider
{
    /**
     * Memcached config array
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
     * Memcached extension
     * 
     * @var object
     */
    protected $memcached;

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

        if (! extension_loaded('memcached')) {
            throw new RuntimeException(
                'The memcached extension has not been installed or enabled.'
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
     * Creates Memcached connections
     * 
     * @param array $val connection array
     * 
     * @return object
     */
    protected function createConnection(array $val)
    {
        if (empty($val['host']) || empty($val['port'])) {
            throw new RuntimeException(
                'Check your memcached configuration, "host" or "port" key seems empty.'
            );
        }
        if ($val['options']['persistent'] && ! empty($val['options']['pool'])) {
            $this->memcached = new \Memcached($val['options']['pool']);
        } else {
            $this->memcached = new \Memcached;
        }
        if (! $this->memcached->addServer($val['host'], $val['port'], $val['weight'])) {
            throw new RuntimeException(
                sprintf(
                    "Memcached connection error could not connect to host: %s.",
                    $val['host']
                )
            );
        }
        $this->setOptions($val['options']);
        $this->memcached->setOption(\Memcached::OPT_CONNECT_TIMEOUT, $val['options']['timeout']);

        return $this->memcached;
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
        $serializer = $this->getValue($options, 'serializer');

        if ($serializer == 'php') {
            $this->memcached->setOption(\Memcached::OPT_SERIALIZER, \Memcached::SERIALIZER_PHP);
        }
        if ($serializer == 'igbinary') {
            $this->enableIgbinary();
        }
        if ($serializer == 'json') {
            $this->enableJson();
        }
        if ($prefix) {
            $this->memcached->setOption(\Memcached::OPT_PREFIX_KEY, $prefix);
        }
    }

    /**
     * Check igbinary is enabled and if yes set serializer
     * 
     * @return void
     */
    protected function enableIgbinary()
    {
        if (! extension_loaded('igbinary')) {
            throw new RuntimeException("Php igbinary extension not enabled on your server.");
        }
        if (! \Memcached::HAVE_IGBINARY) {
            throw new RuntimeException(
                sprintf(
                    "Memcached igbinary support not enabled on your server.<pre>%s</pre>",
                    "Check memcached is configured with --enable-memcached-igbinary option."
                )
            );
        }
        $this->memcached->setOption(\Memcached::OPT_SERIALIZER, \Memcached::SERIALIZER_IGBINARY);
    }

    /**
     * Check json is enabled and if yes set serializer
     * 
     * @return void
     */
    protected function enableJson()
    {
        if (! extension_loaded('json')) {
            throw new RuntimeException("Php json extension not enabled on your server.");
        }
        if (! \Memcached::HAVE_JSON) {
            throw new RuntimeException(
                sprintf(
                    "Memcached json support not enabled on your server.<pre>%s</pre>",
                    "Check memcached is configured with --enable-memcached-json option."
                )
            );
        }
        $this->memcached->setOption(\Memcached::OPT_SERIALIZER, \Memcached::SERIALIZER_JSON);
    }

    /**
     * Get memcached config options
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
     * Retrieve shared Memcached connection instance from connection pool
     *
     * @param array $params provider parameters
     * 
     * @return object Memcached
     */
    public function shared($params = array())
    {
        if (empty($params['connection'])) {
            throw new RuntimeException(
                sprintf(
                    "Memcached provider requires connection parameter. <pre>%s</pre>",
                    "\$container->get('memcached')->shared(['connection' => 'default']);"
                )
            );
        }
        if (! isset($this->params['connections'][$params['connection']])) {
            throw new UnexpectedValueException(
                sprintf(
                    'Connection key %s doest not exist in your memcached configuration.',
                    $params['connection']
                )
            );
        }
        $key = $this->getConnectionKey($params['connection']);

        return $this->container->get($key);  // Return to shared connection
    }

    /**
     * Create a new Memcached connection
     * 
     * If you don't want to add it to config file and you want to create new one.
     * 
     * @param array $params connection parameters
     * 
     * @return object Memcached client
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
                $this->container->get($key)->quit();
            }
        }
    }
}
