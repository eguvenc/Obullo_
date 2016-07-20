<?php

namespace Obullo;

use Obullo\Container\ContainerAwareTrait;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequest as ZendServerRequest;

/**
 * Http Request
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ServerRequest extends ZendServerRequest
{
    use ContainerAwareTrait;

    /**
     * After HMVC operation request object will changed.
     *
     * This method returns to first original object of request.
     *
     * @return object
     */
    public function getMaster()
    {
        $container = $this->getContainer();

        if ($container->has('request.master')) {
            return $container->get('request.master');
        }
        return $container->get('request');
    }

    /**
     * Returns to http input
     *
     * @return object
     */
    public function getInput()
    {
        $input = new HttpInput($this);
        return $input;
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
