<?php

namespace Obullo\Database\Doctrine\DBAL;

use PDO;
use Obullo\Mvc\Controller;
use Obullo\Database\AdapterInterface;
use Obullo\Database\CommonAdapterTrait;

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
     * Statement
     * 
     * @var null
     */
    public $stmt = null; // PDOStatement Object

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
     * Generate db results
     * 
     * @param string $method    name
     * @param array  $arguments method arguments
     * 
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if ($this->stmt == null) {
            return;
        }
        return call_user_func_array(array(new Result($this->stmt), $method), $arguments);
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
     * Establishes the connection with the database.
     *
     * @param string $name possible master slave connection support
     * 
     * @return boolean TRUE if the connection was successfully established, FALSE if
     *                 the connection is already open.
     */
    public function connect($name = null)
    {
        $name = null;
        if ($this->_conn) {    // Lazy loading, If connection is ok not need to again connect.
            return false;
        }
        parent::connect();
        return true;
    }

    /**
     * Get pdo instance
     * 
     * @return object of pdo
     */
    public function getConnection()
    {
        return $this->_conn;
    }

    /**
     * Check active transaction status
     * 
     * @return bool
     */
    public function inTransaction()
    {
        $this->connect();
        return $this->_conn->inTransaction();
    }

    /**
     * Set pdo prepare function
     *
     * @param string $sql     prepared query
     * @param array  $options prepare options
     *
     * @return object adapter
     */
    public function prepare($sql, $options = array())
    {
        $this->stmt = parent::prepare($sql, $options);
        return $this;
    }

    /**
     * Prepared or Direct Pdo Query
     *
     * @return object pdo
     */
    public function query()
    {
        $args = func_get_args();
        $sql = $args[0];
        $this->stmt = parent::query($sql);
        return $this;
    }

    /**
     * Equal to PDO_Statement::bindParam()
     *
     * @param string  $param   parameter name
     * @param mixed   $val     parameter value
     * @param mixed   $type    pdo fetch constant
     * @param integer $length  parameter length
     * @param array   $options parameter option
     *
     * @return object
     */
    public function bindParam($param, $val, $type, $length = null, $options = null)
    {
        $this->stmt->bindParam($param, $val, $type, $length, $options);
        return $this;
    }

    /**
     * Equal to PDO_Statement::bindValue()
     *
     * @param integer $param parameter number 
     * @param mixed   $val   parameter value
     * @param string  $type  pdo fecth constant
     *
     * @return object
     */
    public function bindValue($param, $val, $type)
    {
        $this->stmt->bindValue($param, $val, $type);
        return $this;
    }

    /**
     * Execute prepared query
     *
     * @param array $array bound : default must be null.
     * 
     * @return object of Stmt
     */
    public function execute($array = null)
    {
        $this->stmt->execute($array);
        return $this;
    }

    /**
     * Excecute doctrine sql queries with params
     * 
     * @param string                 $query  statement
     * @param array                  $params parameters
     * @param array                  $types  paremeters types
     * @param QueryCacheProfile|null $qcp    cache option
     * 
     * @return object
     */
    public function executeQuery($query, array $params = array(), $types = array(), QueryCacheProfile $qcp = null)
    {
        $this->stmt = parent::executeQuery($query, $params, $types, $qcp);
        return $this;
    }

    /**
     * Executes a caching query.
     *
     * @param string                                 $query  The SQL query to execute.
     * @param array                                  $params The parameters to bind to the query, if any.
     * @param array                                  $types  The types the previous parameters are in.
     * @param \Doctrine\DBAL\Cache\QueryCacheProfile $qcp    The query cache profile.
     *
     * @return \Doctrine\DBAL\Driver\ResultStatement
     *
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function executeCacheQuery($query, $params, $types, QueryCacheProfile $qcp)
    {
        $this->stmt = parent::executeQuery($query, $params, $types, $qcp);
        return $this;
    }
    
    /**
     * Closes the cursor, freeing the database resources used by this statement.
     *
     * @return boolean TRUE on success, FALSE on failure.
     */
    public function closeCursor()
    {
        $this->stmt->closeCursor();
    }

    /**
     * Get the pdo statement object and use native pdo functions.
     * 
     * Returns Doctrine\DBAL\Statement Object you can get Real PDOStatement using $this->stmt()->getIterator()
     * 
     * @return object
     */
    public function getStmt()
    {
        return $this->stmt;
    }

    /**
     * Exec just CREATE, DELETE, INSERT and UPDATE operations.
     * 
     * Returns to number of affected rows.
     *
     * @param string $sql query sql
     * 
     * @return boolean
     */
    public function exec($sql)
    {
        return parent::exec($sql);
    }

    /**
     * Alias of lastInsertId()
     *
     * @param string $name name null
     * 
     * @return object PDO::Statement
     */
    public function lasInsertId($name = null)
    {
        return $this->lastInsertId($name);
    }
    
    /**
     * Pdo quote function.
     * 
     * @param mixed $str string
     * 
     * @return string
     */
    public function escape($str)
    {
        if (is_array($str)) {
            foreach ($str as $key => $val) {
                if (is_string($val)) {
                    $str[$key] = $this->escape($val);
                }
            }
            return $str;
        }
        return $this->quote($str);
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