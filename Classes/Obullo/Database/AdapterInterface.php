<?php

namespace Obullo\Database;

use Closure;

/**
 * Database Handler Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface AdapterInterface
{
    /**
     * Connect to pdo, open db connections only when we need to them
     * 
     * @return void
     */
    public function connect();
    
    /**
     * Get pdo instance
     * 
     * @return object of pdo
     */
    public function getConnection();

    /**
     * Set pdo prepare function
     *
     * @param string $sql     prepared query
     * @param array  $options prepare options
     *
     * @return object adapter
     */
    public function prepare($sql, $options = array());

    /**
     * Prepared or Direct Pdo Query
     *
     * @return object pdo
     */
    public function query();

    /**
     * Returns the pdo statement object to use native pdo methods.
     * 
     * Example: $this->db->stmt()->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
     * 
     * @return object
     */
    public function getStmt();

    /**
     * Pdo quote with array support
     * 
     * @param array|string $str escape string
     * 
     * @return string
     */
    public function escape($str);

    /**
     * Begin transaction
     * 
     * @return void
     */
    public function beginTransaction();

    /**
     * Begin transactions or run auto transaction with a closure.
     *
     * @param object $func Closure
     * 
     * @return void
     */
    public function transactional(Closure $func);

    /**
     * Commit the transaction
     * 
     * @return object
     */
    public function commit();

    /**
     * Check active transaction status
     * 
     * @return bool
     */
    public function inTransaction();

    /**
     * Rollback transaction
     * 
     * @return object
     */
    public function rollBack();

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
    public function bindParam($param, $val, $type, $length = null, $options = null);

    /**
     * Equal to PDO_Statement::bindValue()
     *
     * @param integer $param parameter number 
     * @param mixed   $val   parameter value
     * @param string  $type  pdo fecth constant
     *
     * @return object
     */
    public function bindValue($param, $val, $type);

    /**
     * Execute prepared query
     *
     * @param array $array bound : default must be null.
     * 
     * @return object of Stmt
     */
    public function execute($array = null);

    /**
     * Execute query and return to number of affected rows.
     *
     * @param string $sql query sql
     * 
     * @return boolean
     */
    public function exec($sql);

    /**
     * Equal to pdo last insert id
     * 
     * @return integer
     */
    public function lastInsertId();

}