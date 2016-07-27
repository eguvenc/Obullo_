<?php

namespace Obullo\Connectors;

use RuntimeException;

/**
 * Mongo Connector
 *
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class MongoConnector implements ConnectorInterface
{
    /**
     * Mongo class type
     *
     * @var string
     */
    protected $mongoClass;

    /**
     * Mongo extension
     *
     * @var object
     */
    protected $connection;

    /**
     * Mongo config array
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
        $this->mongoClass = (version_compare(phpversion('mongo'), '1.3.0', '<')) ? '\Mongo' : '\MongoClient';

        if (! class_exists($this->mongoClass, false)) {
            throw new RuntimeException(
                sprintf(
                    'The %s extension has not been installed or enabled.',
                    trim($this->mongoClass, '\\')
                )
            );
        }
    }

    /**
     * Returns to mongo connection
     *
     * @return object
     */
    public function getConnection()
    {
        $params  = $this->connectionParams;
        $options = isset($params['options']) ? $params['options'] : ['connect' => true];

        $mongoClass = $this->mongoClass;

        return $this->connection = new $mongoClass($params['server'], $options);
    }

    /**
     * Close active connection
     */
    public function __destruct()
    {
        foreach ($this->connection->getConnections() as $con) {
            $this->connection->close($con['hash']);
        }
    }
}
