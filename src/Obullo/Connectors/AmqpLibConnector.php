<?php

namespace Obullo\Connectors;

use RuntimeException;
use PhpAmqpLib\Connection\AMQPConnection;

/**
 * AMQPLib Service Provider
 *
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class AmqpLibConnector implements ConnectorInterface
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

        return $this->connection = new AMQPConnection(
            $params['host'],
            $params['port'],
            $params['username'],
            $params['password'],
            $params['vhost']
        );
    }

    /**
     * Close active connection
     */
    public function __destruct()
    {
        $this->connection->close();
    }
}
