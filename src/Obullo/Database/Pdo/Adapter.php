<?php

namespace Obullo\Database\Pdo;

use PDO;
use Closure;
use Exception;
use RuntimeException;
use Obullo\Mvc\Controller;
use Obullo\Database\AdapterInterface;
use Obullo\Database\SQLLoggerInterface;

/**
 * Adapter Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Adapter
{
    /**
     * Stores last executed sql query
     * 
     * @var string
     */
    protected $sql;

    /**
     * Pdo connection object
     * 
     * @var object
     */
    protected $conn;

    /**
     * PDOStatement Object
     * 
     * @var null
     */
    protected $stmt;

    /**
     * Timer
     * 
     * @var int
     */
    protected $start;

    /**
     * Connection Params
     * 
     * @var array
     */
    protected $params;

    /**
     * SQLLogger
     * 
     * @var object
     */
    protected $sqlLogger;

    /**
     * Available drivers
     * 
     * @var array
     */
    protected $drivers = [
        'pdo_mysql',
        'pdo_pgsql',
    ];

    /**
     * Stores last executed PDO params
     * 
     * @var array
     */
    protected $parameters = array();

    /**
     * Constructor
     * 
     * @param array $params connection params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
        $this->sqlLogger = $this->getSQLLogger();
    }

    /**
     * Get sql logger object
     * 
     * @return void
     */
    protected function getSQLLogger()
    {
        if (isset($this->params['logger']) && $this->params['logger'] instanceof SQLLoggerInterface) {
            return $this->params['logger'];
        }
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
     * Connect to pdo, open db connections only when we need to them
     * 
     * @return void
     */
    public function connect()
    {   
        if ($this->conn) {    // Lazy loading, If connection is ok not need to again connect.
            return false;
        }
        $this->createConnection();
        return true;
    }

    /**
     * Get pdo instance
     * 
     * @return object of pdo
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Get the pdo statement object and use native pdo functions.
     * 
     * Example: $this->db->stmt()->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
     * 
     * @return object
     */
    public function getStmt()
    {
        return $this->stmt;
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
        $this->connect();       // Open db connections only when we need to them
        $this->sql = $sql;
        $this->stmt = $this->conn->prepare($sql, $options);
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

        $this->connect();
        $this->startQuery($args[0]);
        $this->stmt = $this->conn->query($args[0]);
        $this->stopQuery();
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
        $this->trackParams($param, $val);
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
        $this->trackParams($param, $val);
        return $this;
    }

    /**
     * Execute prepared query
     *
     * @param array $params bound : default must be null.
     * 
     * @return object of Stmt
     */
    public function execute($params = null)
    {
        $this->trackParams($params);     // Store last executed bind values for last_query method.
        $this->startQuery($this->sql);
        $this->stmt->execute($params);
        $this->stopQuery();
        return $this;
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
        $this->connect();
        $this->startQuery($sql);
        $return = $this->conn->exec($sql);
        $this->stopQuery();
        return $return;
    }

    /**
     * Begin transaction
     * 
     * @return void
     */
    public function beginTransaction()
    {
        $this->connect();
        return $this->conn->beginTransaction();
    }

    /**
     * Begin transactions or run auto transaction with a closure.
     *
     * @param object $func Closure
     * 
     * @return void
     */
    public function transactional(Closure $func)
    {
        $this->beginTransaction();
        try
        {
            $return = $func();
            $this->commit();
            return $return ?: true;  // Only fail if we have exceptions
        }
        catch(Exception $e)
        {
            $this->rollBack();
            throw $e;           // throw a PDOException developer will catch it 
        }
    }

    /**
     * Commit the transaction
     * 
     * @return object
     */
    public function commit()
    {
        $this->connect();
        return $this->conn->commit();
    }

    /**
     * Check active transaction status
     * 
     * @return bool
     */
    public function inTransaction()
    {
        $this->connect();
        return $this->conn->inTransaction();
    }

    /**
     * Rollback transaction
     * 
     * @return object
     */
    public function rollBack()
    {
        $this->connect();      
        return $this->conn->rollBack();
    }

    /**
     * Alias of lastInsertId()
     *
     * @param string $name name null
     * 
     * @return object PDO::Statement
     */
    public function lastInsertId($name = null)
    {
        return $this->conn->lastInsertId($name);
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
        $this->connect();
        return $this->conn->quote($str, PDO::PARAM_STR);
    }

    /**
     * Track pdo executed values
     * 
     * @param mixed $key key
     * @param mixed $val value
     * 
     * @return void
     */
    protected function trackParams($key, $val = '')
    {
        if (empty($key)) {
            return;
        }
        if (is_array($key)) {
            $this->parameters = $key;
            return;
        }
        $this->parameters[$key] = $val;
    }

    /**
     * Get prepared parameters
     *
     * @param mixed $failure result value if empty
     * 
     * @return array|null
     */
    protected function getParameters($failure = array())
    {
        return empty($this->parameters) ? $failure : $this->parameters;
    }

    /**
     * Start query timer & add sql log
     * 
     * @param string     $sql    sql
     * @param array|null $params parameters
     * @param array|null $types  types
     * 
     * @return void
     */
    protected function startQuery($sql, $params = null, $types = null)
    {
        $this->sql = $sql;
        if (is_null($params)) {
            $params = $this->getParameters(null);
        }
        if ($this->sqlLogger) {
            $this->sqlLogger->startQuery($sql, $params, $types);
        }
    }

    /**
     * Stop query timer & write sql log
     * 
     * @return void
     */
    protected function stopQuery()
    {
        if ($this->sqlLogger) {
            $this->sqlLogger->stopQuery();
        }
        $this->parameters = array();
    }

    /**
     * Executes an SQL INSERT/UPDATE/DELETE query with the given parameters and returns the number of affected rows.
     *
     * @param string $query  The SQL query.
     * @param array  $params The query parameters.
     * @param array  $types  The parameter types.
     *
     * @return integer The number of affected rows.
     */
    public function executeUpdate($query, array $params = array(), array $types = array())
    {
        $this->connect();
        $this->startQuery($query, $params, $types);

        if ($params) {
            $this->stmt = $this->conn->prepare($query);
            if ($types) {
                $i = -1;
                foreach ($types as $type) {
                    ++$i;
                    $this->stmt->bindValue($i + 1, $params[$i], $type);
                }
                $this->stmt->execute();
            } else {
                $this->stmt->execute($params);
            }
            $result = $this->stmt->rowCount();

        } else {
            $result = $this->exec($query);
        }
        $this->stopQuery();
        return $result;
    }

    /**
     * Executes an SQL UPDATE statement on a table.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $expression The expression of the table to update quoted or unquoted.
     * @param array  $data       An associative array containing column-value pairs.
     * @param array  $identifier The update criteria. An associative array containing column-value pairs.
     * @param array  $types      Types of the merged $data and $identifier arrays in that order.
     *
     * @return integer The number of affected rows.
     */
    public function update($expression, array $data, array $identifier, array $types = array())
    {
        $this->connect();
        $set = array();
        foreach ($data as $columnName => $value) {
            $value = null;
            $set[] = $columnName . ' = ?';
        }
        if (is_string(key($types))) {
            $types = $this->extractTypeValues(array_merge($data, $identifier), $types);
        }
        $params = array_merge(array_values($data), array_values($identifier));

        $sql  = 'UPDATE ' . $expression . ' SET ' . implode(', ', $set)
                . ' WHERE ' . implode(' = ? AND ', array_keys($identifier))
                . ' = ?';
        return $this->executeUpdate($sql, $params, $types);
    }

    /**
     * Inserts a table row with specified data.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $expression The expression of the table to insert data into, quoted or unquoted.
     * @param array  $data       An associative array containing column-value pairs.
     * @param array  $types      Types of the inserted data.
     *
     * @return integer The number of affected rows.
     */
    public function insert($expression, array $data, array $types = array())
    {
        $this->connect();
        if (empty($data)) {
            return $this->executeUpdate('INSERT INTO ' . $expression . ' ()' . ' VALUES ()');
        }
        return $this->executeUpdate(
            'INSERT INTO ' . $expression . ' (' . implode(', ', array_keys($data)) . ')' .
            ' VALUES (' . implode(', ', array_fill(0, count($data), '?')) . ')',
            array_values($data),
            is_string(key($types)) ? $this->extractTypeValues($data, $types) : $types
        );
    }

    /**
     * Extract ordered type list from two associate key lists of data and types.
     *
     * @param array $data  values
     * @param array $types types
     *
     * @return array
     */
    protected function extractTypeValues(array $data, array $types)
    {
        $typeValues = array();
        foreach ($data as $k => $_) {
            $_ = null;
            $typeValues[] = isset($types[$k]) ? $types[$k] : \PDO::PARAM_STR;
        }
        return $typeValues;
    }

    /**
     * Executes an SQL DELETE statement on a table.
     *
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $expression The expression of the table on which to delete.
     * @param array  $identifier The deletion criteria. An associative array containing column-value pairs.
     * @param array  $types      The types of identifiers.
     *
     * @return integer The number of affected rows.
     *
     * @throws InvalidArgumentException
     */
    public function delete($expression, array $identifier, array $types = array())
    {
        if (empty($identifier)) {
            throw new RuntimeException("Delete identifier cannot be empty.");
        }
        $this->connect();
        $criteria = array();

        foreach (array_keys($identifier) as $columnName) {
            $criteria[] = $columnName . ' = ?';
        }
        return $this->executeUpdate(
            'DELETE FROM ' . $expression . ' WHERE ' . implode(' AND ', $criteria),
            array_values($identifier),
            is_string(key($types)) ? $this->extractTypeValues($identifier, $types) : $types
        );
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
     * Quotes a string so that it can be safely used as a table or column name,
     * even if it is a reserved word of the platform. This also detects identifier
     * chains separated by dot and quotes them independently.
     *
     * NOTE: Just because you CAN use quoted identifiers doesn't mean
     * you SHOULD use them. In general, they end up causing way more
     * problems than they solve.
     *
     * @param string $str The identifier name to be quoted.
     *
     * @return string The quoted identifier string.
     */
    public function quoteIdentifier($str)
    {
        if (strpos($str, ".") !== false) {
            $parts = array_map(array($this, "quoteSingleIdentifier"), explode(".", $str));

            return implode(".", $parts);
        }
        return $this->quoteSingleIdentifier($str);
    }

    /**
     * Quotes a single identifier (no dot chain separation).
     *
     * @param string $str The identifier name to be quoted.
     *
     * @return string The quoted identifier string.
     */
    public function quoteSingleIdentifier($str)
    {
        $chr = $this->getIdentifierQuoteCharacter();
        return $chr . str_replace($chr, $chr.$chr, $str) . $chr;
    }

    /**
     * Get identifier char
     * 
     * @return string
     */
    public function getIdentifierQuoteCharacter()
    {
        return $this->escapeIdentifier;
    }

    /**
     * Assign controller objects into db class
     * to available closure $this->db support in transactional() method.
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

    /**
     * Create query builder object
     * 
     * @return void
     */
    public function createQueryBuilder()
    {
        throw new RuntimeException("This operation requires database service configuration with Doctrine/DBAL.");
    }

    /**
     * Close the database connetion.
     */
    public function __destruct()
    {
        $this->conn = null;
    }

}