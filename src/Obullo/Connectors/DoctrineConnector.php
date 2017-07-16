<?php

namespace Obullo\Connectors;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

use Obullo\Logger\LoggerAwareTrait;
use Obullo\Database\Doctrine\DBAL\SQLLogger;

/**
 * Doctrine Connector
 *
 * @copyright 2009-2017 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class DoctrineConnector implements ConnectorInterface
{
    use LoggerAwareTrait;

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
     * @return void
     */
    public function __construct(array $connectionParams)
    {
        $this->connectionParams = $connectionParams;
    }

    /**
     * Creates Redis connections
     *
     * @return object
     */
    public function getConnection()
    {
        $params = $this->connectionParams;

        $dsnString = 'driver='.strstr($params['dsn'], ':', true).';'.ltrim(strstr($params['dsn'], ':'), ':');
        parse_str(str_replace(';', '&', $dsnString), $formattedParams);
        $params = array_merge($formattedParams, $params);

        $config = isset($params['config']) ? $params['config'] : new Configuration;
        $eventManager = isset($params['eventManager']) ? $params['eventManager'] : null;

        if ($logger = $this->getLogger()) {
            $config->setSQLLogger(new SQLLogger($logger));
        }
        $params['wrapperClass'] = '\Obullo\Database\Doctrine\DBAL\Adapter';

        return DriverManager::getConnection($params, $config, $eventManager);
    }
}
