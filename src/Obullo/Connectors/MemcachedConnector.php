<?php

namespace Obullo\Connectors;

use RuntimeException;

/**
 * Memcached Connector
 *
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class MemcachedConnector implements ConnectorInterface
{
    /**
     * Memcached connection
     *
     * @var object
     */
    protected $memcached;

    /**
     * Memcached config array
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

        if (! extension_loaded('memcached')) {
            throw new RuntimeException(
                'The memcached extension has not been installed or enabled.'
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
                'Check your memcached configuration, "host" or "port" key seems empty.'
            );
        }
        if ($params['options']['persistent'] && ! empty($params['options']['pool'])) {
            $this->memcached = new \Memcached($params['options']['pool']);
        } else {
            $this->memcached = new \Memcached;
        }
        if (! $this->memcached->addServer($params['host'], $params['port'], $params['weight'])) {
            throw new RuntimeException(
                sprintf(
                    "Memcached connection error could not connect to host: %s.",
                    $params['host']
                )
            );
        }
        $this->setOptions($params['options']);
        $this->memcached->setOption(\Memcached::OPT_CONNECT_TIMEOUT, $params['options']['timeout']);

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
     * Close active connection
     */
    public function __destruct()
    {
        $this->memcached->quit();
    }
}
