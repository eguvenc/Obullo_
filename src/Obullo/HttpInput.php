<?php

namespace Obullo;

use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Http Input
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class HttpInput implements HttpInputInterface
{
    /**
     * Request
     *
     * @var object
     */
    protected $request;

    /**
     * Constructor
     *
     * @param Request $request object
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * GET wrapper
     *
     * @param string $key key
     *
     * @return mixed
     */
    public function get($key)
    {
        $get = $this->request->getQueryParams();

        return isset($get[$key]) ? $get[$key] : false;
    }

    /**
     * POST wrapper
     *
     * @param string $key key
     *
     * @return mixed
     */
    public function post($key)
    {
        $post = $this->request->getParsedBody();

        return isset($post[$key]) ? $post[$key] : false;
    }

    /**
     * REQUEST wrapper
     *
     * @param string|null $key key
     *
     * @return mixed
     */
    public function all($key = null)
    {
        $get  = (array)$this->request->getQueryParams();
        $post = (array)$this->request->getParsedBody();

        $request = array_merge($post, $get);

        if (is_null($key)) {
            return $request;
        }
        return isset($request[$key]) ? $request[$key] : false;
    }

    /**
     * Check ip is valid
     *
     * @param string $ip address
     *
     * @return boolean
     */
    public function isValidIp($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return false;
        }
        return true;
    }

    /**
     * Get ip address
     *
     * @return string
     */
    public function getIpAddress()
    {
        static $ipAddress = '';
        $ipAddress = $this->request->getAttribute('TRUSTED_IP');

        if (empty($ipAddress)) {
            $server = $this->request->getServerParams();
            $ipAddress = isset($server['REMOTE_ADDR']) ? $server['REMOTE_ADDR'] : '0.0.0.0';
        }
        return $ipAddress;
    }
}
