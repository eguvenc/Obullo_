<?php

namespace Obullo;

/**
 * Http Input Interface
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface HttpInputInterface
{
    /**
     * GET wrapper
     *
     * @param string $key key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * POST wrapper
     *
     * @param string $key key
     *
     * @return mixed
     */
    public function post($key);

    /**
     * REQUEST wrapper
     *
     * @param string|null $key key
     *
     * @return mixed
     */
    public function all($key = null);

    /**
     * Check ip is valid
     *
     * @param string $ip address
     *
     * @return boolean
     */
    public function isValidIp($ip);

    /**
     * Get ip address
     *
     * @return string
     */
    public function getIpAddress();
}
