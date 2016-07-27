<?php

namespace Obullo\Database\Doctrine\DBAL;

use Obullo\Mvc\Controller;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Cache\QueryCacheProfile;

/**
 * Doctrine DBAL Adapter Class
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Adapter extends Connection
{
    /**
     * Available drivers
     *
     * @var array
     */
    public $drivers = [
        'db2',         // Doctrine\DBAL\Driver\IBMDB2\DB2Driver
        'drizzle_pdo_mysql', // Doctrine\DBAL\Driver\DrizzlePDOMySql\Driver
        'ibm_db2',     // Doctrine\DBAL\Driver\IBMDB2\DB2Driver
        'mssql',       // Doctrine\DBAL\Driver\PDOSqlsrv\Driver
        'mysql',       // Doctrine\DBAL\Driver\PDOMySql\Driver
        'mysqli',      // Doctrine\DBAL\Driver\Mysqli\Driver
        'mysql2',      // Doctrine\DBAL\Driver\PDOMySql\Driver // Amazon RDS, for some weird reason
        'oci8',        // Doctrine\DBAL\Driver\OCI8\Driver
        'pdo_mysql',   // Doctrine\DBAL\Driver\PDOMySql\Driver
        'pdo_sqlite',  // Doctrine\DBAL\Driver\PDOSqlite\Driver
        'pdo_pgsql',   // Doctrine\DBAL\Driver\PDOPgSql\Driver
        'pdo_oci',     // Doctrine\DBAL\Driver\PDOOracle\Driver
        'pdo_sqlsrv',  // Doctrine\DBAL\Driver\PDOSqlsrv\Driver
        'postgres',    // Doctrine\DBAL\Driver\PDOPgSql\Driver
        'postgresql',  // Doctrine\DBAL\Driver\PDOPgSql\Driver
        'pgsql',       // Doctrine\DBAL\Driver\PDOPgSql\Driver
        'sqlite',      // Doctrine\DBAL\Driver\PDOSqlite\Driver
        'sqlite3',     // Doctrine\DBAL\Driver\PDOSqlite\Driver
        'sqlanywhere', // Doctrine\DBAL\Driver\SQLAnywhere\Driver
        'sqlsrv',      // Doctrine\DBAL\Driver\SQLSrv\Driver
    ];

    /**
     * Initializes a new instance of the Connection class.
     *
     * @param array                              $params       The connection parameters.
     * @param \Doctrine\DBAL\Driver              $driver       The driver to use.
     * @param \Doctrine\DBAL\Configuration|null  $config       The configuration, optional.
     * @param \Doctrine\Common\EventManager|null $eventManager The event manager, optional.
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct(array $params, Driver $driver, Configuration $config = null, EventManager $eventManager = null)
    {
        $params['user'] = $params['username'];  // Doctrine changes.

        if (isset($params['options'])) {
            $params['driverOptions'] = $params['options'];
        }
        parent::__construct($params, $driver, $config, $eventManager);
    }

    /**
     * Returns the list of supported drivers.
     *
     * @return array
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

    /**
     * Assign all controller objects into db class
     * to available closure $this->object support in beginTransaction() method.
     *
     * @param string $key Controller variable
     *
     * @return void
     */
    public function __get($key)
    {
        if (isset(Controller::$instance->{$key})) {
            return Controller::$instance->{$key};
        }
    }
}
