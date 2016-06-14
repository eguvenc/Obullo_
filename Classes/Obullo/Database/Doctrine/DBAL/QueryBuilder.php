<?php

namespace Obullo\Database\Doctrine\DBAL;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Query\QueryBuilder as DoctrineQueryBuilder;

/**
 * Wrapper for Doctrine QueryBuilder
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class QueryBuilder extends DoctrineQueryBuilder
{
    /**
     * Run execute methods, set table using from 
     * if tablename not null
     * 
     * @param string $table name
     * 
     * @return void
     */
    public function get($table = null)
    {
        if ($table != null) {
            $this->from($table);
        }
        return parent::execute();
    }

    /**
     * Set limit value
     * 
     * @param int $limit  value
     * @param int $offset value ( optional )
     * 
     * @return object
     */
    public function limit($limit, $offset = null)
    {
        $this->setMaxResults((int)$limit);
        $this->offset($offset);
        return $this;
    }

    /**
     * Set offset value
     * 
     * @param int $offset value
     * 
     * @return object
     */
    public function offset($offset)
    {
        if (is_numeric($offset)) {
            $this->setFirstResult($offset);
        }
        return $this;
    }

    /**
     * Call connection methods
     *
     * This method allows to you reach database connection object methods
     * 
     * Example :
     * 
     * $this->db->query("..");
     * 
     * @param string $method    name
     * @param array  $arguments method arguments
     * 
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $connection = $this->getConnection();
        if ($connection == null) {
            return;
        }
        return call_user_func_array(array($connection, $method), $arguments);
    }
}