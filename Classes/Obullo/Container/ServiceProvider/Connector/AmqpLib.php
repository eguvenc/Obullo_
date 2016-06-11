<?php

namespace Obullo\Container\ServiceProvider\Connector;

use RuntimeException;
use UnexpectedValueException;
use PhpAmqpLib\Connection\AMQPConnection;
use Interop\Container\ContainerInterface as Container;
use Obullo\Container\ServiceProvider\AbstractServiceProvider;

/**
 * AMQPLib Service Connection Provider
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AmqpLib extends AbstractServiceProvider
{
    /**
     * Amqp config array
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
        $this->register();
    }

    /**
     * Register all connections as shared ( It should run one time )
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
     * Creates AMQP connections
     * 
     * @param array $val connection parameters
     * 
     * @return void
     */
    protected function createConnection(array $val)
    {
        if (empty($val['host']) || empty($val['password'])) {
            throw new RuntimeException(
                'Check your queue configuration "host" or "password" key seems empty.'
            );
        }
        $val['port']  = empty($val['port']) ? "5672" : $val['port'];
        $val['vhost'] = empty($val['vhost']) ? "/" : $val['vhost'];

        return new AMQPConnection(
            $val['host'],
            $val['port'],
            $val['username'],
            $val['password'],
            $val['vhost']
        );
    }

    /**
     * Retrieve shared AMQP connection instance from connection pool
     *
     * @param array $params provider parameters
     * 
     * @return object AMQP
     */
    public function shared($params = array())
    {
        if (! isset($params['connection'])) {
            $params['connection'] = array_keys($this->params['connections'])[0]; //  Set default connection
        }
        if (! isset($this->params['connections'][$params['connection']])) {
            throw new UnexpectedValueException(
                sprintf(
                    'Connection key %s does not exist in your queue.php config file.',
                    $params['connection']
                )
            );
        }
        $key = $this->getConnectionKey($params['connection']);

        return $this->container->get($key);  // return to shared connection
    }

    /**
     * Create a new AMQPLib connection
     * 
     * If you don't want to add it config file and you want to create new one.
     * 
     * @param array $params connection parameters
     * 
     * @return object AMQPLib client
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
