<?php

namespace Obullo\Cookie;

/**
 * Cookie Interface
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface CookieInterface
{
    /**
     * Set cookie name
     *
     * @param string $name cookie name
     *
     * @return object
     */
    public function withName($name);
    
    /**
     * Set cookie value
     *
     * @param string $value value
     *
     * @return object
     */
    public function withValue($value = '');

    /**
     * Set cookie expire in seconds
     *
     * @param integer $expire seconds
     *
     * @return object
     */
    public function withExpire($expire = 0);

    /**
     * Set cookie domain name
     *
     * @param string $domain name
     *
     * @return void
     */
    public function withDomain($domain = '');

    /**
     * Set cookie path
     *
     * @param string $path name
     *
     * @return object
     */
    public function withPath($path = '/');

    /**
     * Set secure cookie
     *
     * @param boolean $bool true or false
     *
     * @return object
     */
    public function withSecure($bool = false);

    /**
     * Make cookie available just for http. ( No javascript )
     *
     * @param boolean $bool true or false
     *
     * @return object
     */
    public function withHttpOnly($bool = false);

    /**
     * Set cookie
     *
     * @param array|null $params mixed parameters
     *
     * @return object cookie
     */
    public function set($params = null);
    
    /**
     * Get request cookie
     *
     * @param  string $name    Cookie name
     * @param  mixed  $default Cookie default value
     *
     * @return mixed Cookie value if present, else default
     */
    public function get($name, $default = null);

    /**
    * Delete a cookie
    *
    * @param string $name cookie
    *
    * @return void
    */
    public function delete($name = null);
}
