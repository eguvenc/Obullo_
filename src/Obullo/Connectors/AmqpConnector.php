<?php

namespace Obullo\Connectors;

use AmqpConnection;
use RuntimeException;

/**
 * AMQP Service Provider
 *
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AmqpConnector implements ConnectorInterface
{
    /**
     * Connection
     *
     * @var object
     */
    protected $connection;

    /**
     * Amqp config array
     *
     * @var array
     */
    protected $connectionParams;

    /**
     * Constructor
     *
     * @param array $container        container
     * @param array $connectionParams connection params
     *
     * @return void
     */
    public function __construct(array $connectionParams)
    {
        $this->connectionParams = $connectionParams;

        if (! extension_loaded('AMQP')) {
            throw new RuntimeException(
                'The AMQP extension has not been installed or enabled.'
            );
        }
    }

    /**
     * Creates AMQP connections
     *
     * @param array $params connection parameters
     *
     * @return object
     */
    public function getConnection()
    {
        if (empty($params['host']) || empty($params['password'])) {
            throw new RuntimeException(
                'Check your queue configuration "host" or "password" key seems empty.'
            );
        }
        $params['port']  = empty($params['port']) ? "5672" : $params['port'];
        $params['vhost'] = empty($params['vhost']) ? "/" : $params['vhost'];

        $connection = new AMQPConnection;
        $connection->setHost($params['host']);
        $connection->setPort($params['port']);
        $connection->setLogin($params['username']);
        $connection->setPassword($params['password']);
        $connection->setVHost($params['vhost']);
        $connection->connect();

        return $this->connection = $connection;
    }

    /**
     * Close active connection
     */
    public function __destruct()
    {
        $this->connection->disconnect();
    }
}
