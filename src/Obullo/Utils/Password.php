<?php

namespace Obullo\Utils;

/**
 * Password hash library
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Password
{
    /**
     * Creates a password hash
     * 
     * @param string  $password plain-text
     * @param integer $algo     algorithm
     * @param array   $options  hash options
     * 
     * @return string hashed password
     */
    public function hash($password, $algo, array $options)
    {
        return password_hash($password, $algo, $options);
    }

    /**
     * Checks if the given hash matches the given options
     * 
     * @param string  $hash    hashed password
     * @param integer $algo    algorithm
     * @param array   $options hash options
     * 
     * @return boolean
     */
    public function needsRehash($hash, $algo, array $options)
    {
        return password_needs_rehash($hash, $algo, $options);
    }

    /**
     * Verifies that a password matches a hash
     * 
     * @param string $password plain-text
     * @param string $hash     hashed password
     * 
     * @return boolean
     */
    public function verify($password , $hash)
    {
        return password_verify($password, $hash);
    }
}