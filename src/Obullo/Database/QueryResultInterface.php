<?php

namespace Obullo\Database;

/**
 * Interface SqlLogger
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface QueryResultInterface
{
    /**
     * Returns number of rows.
     *
     * @return integer
     */
    public function getCount();

    /**
     * Get row as object & if fail return false
     *
     * @param boolean $default return value to if operation fail
     * 
     * @return array | object otherwise false
     */
    public function getRow($default = false);

    /**
     * Get row as array & if fail return 
     * 
     * @param boolean $default return value to if operation fail
     * 
     * @return array | object otherwise false
     */
    public function getRowArray($default = false);

    /**
     * Get results as array & if fail return ARRAY
     * 
     * @param boolean $default return value to if operation fail
     * 
     * @return array | object otherwise false
     */
    public function getResult($default = false);

    /**
     * Get results as array & if fail return ARRAY
     * 
     * @param boolean $default return value to if operation fail
     * 
     * @return array | object otherwise false
     */
    public function getResultArray($default = false);
    
}