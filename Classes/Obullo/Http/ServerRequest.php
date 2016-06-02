<?php

namespace Obullo\Http;

use Zend\Diactoros\ServerRequest as ZendServerRequest;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Http Request
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ServerRequest extends ZendServerRequest
{
    /**
     * GET wrapper
     * 
     * @param string $key key
     * 
     * @return mixed
     */
    public function get($key)
    {
        $get = $this->getQueryParams();

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
        $post = $this->getParsedBody();

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
        $get  = (array)$this->getQueryParams();
        $post = (array)$this->getParsedBody();

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
        $ipAddress = $this->getAttribute('TRUSTED_IP');

        if (empty($ipAddress)) {
            $server = $this->getServerParams();
            $ipAddress = isset($server['REMOTE_ADDR']) ? $server['REMOTE_ADDR'] : '0.0.0.0';
        }
        return $ipAddress;
    }
    
    /**
     * Detect the request is xmlHttp ( Ajax )
     * 
     * @return boolean
     */
    public function isAjax()
    {
        return $this->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Detect the connection is secure ( Https )
     * 
     * @return boolean
     */
    public function isSecure()
    {
        $server = $this->getServerParams();

        if (! empty($server['HTTPS']) && strtolower($server['HTTPS']) !== 'off') {
            return true;
        } elseif (isset($server['HTTP_X_FORWARDED_PROTO']) && $server['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        } elseif (! empty($server['HTTP_FRONT_END_HTTPS']) && strtolower($server['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }

    /**
     * If http request type equal to POST returns to true otherwise false.
     * 
     * @return boolean
     */
    public function isPost()
    {
        return $this->isMethod('POST');
    }

    /**
     * If http request type equal to GET returns true otherwise false.
     * 
     * @return boolean
     */
    public function isGet()
    {
        return $this->isMethod('GET');
    }

    /**
     * If http request type equal to PUT returns to true otherwise false.
     * 
     * @return boolean
     */
    public function isPut()
    {
        return $this->isMethod('PUT');
    }

    /**
     * If http request type equal to PATCH returns to true otherwise false.
     * 
     * @return boolean
     */
    public function isPatch()
    {
        return $this->isMethod('PATCH');
    }

    /**
     * Check method is head
     * 
     * @return boolean
     */
    public function isHead()
    {
        return $this->isMethod('HEAD');
    }

    /**
     * Check method is options
     * 
     * @return boolean
     */
    public function isOptions()
    {
        return $this->isMethod('OPTIONS');
    }

    /**
     * If http request type equal to DELETE returns to true otherwise false.
     * 
     * @return boolean
     */
    public function isDelete()
    {
        return $this->isMethod('DELETE');
    }

    /**
     * Does this request use a given method?
     *
     * @param string $method HTTP method
     * 
     * @return bool
     */
    public function isMethod($method)
    {
        return $this->getMethod() === $method;
    }

}