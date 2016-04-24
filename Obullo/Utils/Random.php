<?php

namespace Utils;

/**
 * Generates Random String
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Random
{
    /**
    * Create a Random String
    *
    * Useful for generating passwords or hashes.
    *
    * @param string  $type type of random string. Options: alnum, alnum.upper, alnum.lower, numeric, nozero, unique
    * @param integer $len  number of characters
    * 
    * @return string
    */
    public static function generate($type = 'alnum', $len = 8)
    {   
        $type = str_replace('_', '.', $type);

        switch($type) {
        case 'basic'    :
            return mt_rand();
          break;
        case 'alnum'    :
        case 'alnum.lower' :
        case 'alnum.upper' :
        case 'numeric'  :
        case 'nozero'   :
        case 'alpha'    :
        case 'alpha.lower' :
        case 'alpha.upper' :
            $pool = self::getPool($type);
            $str = '';
            for ($i=0; $i < $len; $i++) {
                $str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
            }
            return $str;
          break;
        }
    }

    /**
     * Get character pool
     * 
     * @param string $type function
     * 
     * @return string
     */
    protected static function getPool($type)
    {
        switch ($type) {
        case 'alpha'        : $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'alpha.lower'  : $pool = 'abcdefghijklmnopqrstuvwxyz';
            break;
        case 'alpha.upper'  : $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'alnum'        : $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'alnum.lower'  : $pool = '123456789abcdefghijklmnopqrstuvwxyz';
            break;
        case 'alnum.upper'  : $pool = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'numeric'      : $pool = '0123456789';
            break;
        case 'nozero'       : $pool = '123456789';
            break;
        }
        return $pool;
    }

}