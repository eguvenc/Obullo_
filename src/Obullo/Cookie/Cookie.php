<?php

namespace Obullo\Cookie;

use InvalidArgumentException;

/**
 * Control cookie set, get, delete and queue operations
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Cookie implements CookieInterface
{
    /**
     * Cookie unique name
     *
     * @var string
     */
    protected $name;

    /**
     * Request cookies
     *
     * @var array
     */
    protected $requestCookies = array();

    /**
     * Response cookies
     *
     * @var array
     */
    protected $responseCookies = array();

    /**
     * Default cookie properties
     *
     * @var array
     */
    protected $defaults = [
        'value' => '',
        'domain' => null,
        'path' => null,
        'secure' => false,
        'httpOnly' => false,
        'expire' => null
    ];

    /**
     * Create new cookies helper
     *
     * @param array $cookies
     */
    public function __construct(array $cookies = [])
    {
        $this->requestCookies = $cookies;
    }

    /**
     * Set default cookie properties
     *
     * @param array $settings
     */
    public function setDefaults(array $settings)
    {
        $this->defaults = array_replace($this->defaults, $settings);
    }

    /**
     * Returns to default settings
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Set cookie name
     *
     * @param string $name cookie name
     *
     * @return object
     */
    public function withName($name)
    {
        $this->name = trim($name);
        return $this;
    }
    
    /**
     * Set cookie value
     *
     * @param string $value value
     *
     * @return object
     */
    public function withValue($value = '')
    {
        $this->validateName();
        $this->responseCookies[$this->name]['value'] = $value;
        return $this;
    }

    /**
     * Set cookie expire in seconds
     *
     * @param integer $expire seconds
     *
     * @return object
     */
    public function withExpire($expire = 0)
    {
        $this->validateName();
        $this->responseCookies[$this->name]['expire'] = (int)$expire;
        return $this;
    }

    /**
     * Set cookie domain name
     *
     * @param string $domain name
     *
     * @return void
     */
    public function withDomain($domain = '')
    {
        $this->validateName();
        $this->responseCookies[$this->name]['domain'] = $domain;
        return $this;
    }

    /**
     * Set cookie path
     *
     * @param string $path name
     *
     * @return object
     */
    public function withPath($path = '/')
    {
        $this->validateName();
        $this->responseCookies[$this->name]['path'] = $path;
        return $this;
    }

    /**
     * Set secure cookie
     *
     * @param boolean $bool true or false
     *
     * @return object
     */
    public function withSecure($bool = false)
    {
        $this->validateName();
        $this->responseCookies[$this->name]['secure'] = $bool;
        return $this;
    }

    /**
     * Make cookie available just for http. ( No javascript )
     *
     * @param boolean $bool true or false
     *
     * @return object
     */
    public function withHttpOnly($bool = false)
    {
        $this->validateName();
        $this->responseCookies[$this->name]['httpOnly'] = $bool;
        return $this;
    }

    /**
     * Set cookie
     *
     * @param array|null|string $name  mixed name or parameters
     * @param mixed             $value value
     *
     * @return void
     */
    public function set($name = null, $value = null)
    {
        if (is_array($name)) {
            $params = $name;
        } elseif (empty($name)) {
            $params = $this->responseCookies[$this->name];
        } elseif (is_string($name)) {
            $this->withName($name);
            if ($value != null) {
                $this->withValue($value);
            }
            $params = $this->responseCookies[$this->name];
        }
        $cookie = $this->buildParams($params);

        setcookie(
            $this->getName(),
            $cookie['value'],
            $cookie['expire'],
            $cookie['path'],
            $cookie['domain'],
            $cookie['secure'],
            $cookie['httpOnly']
        );
        $this->name = null; // Reset name variable & prevent collisions.
    }

    /**
     * Build cookie parameters
     *
     * @param array $params cookie params
     *
     * @return array
     */
    public function buildParams(array $params)
    {
        $cookie = array();
        foreach (array('value','expire','domain','path','secure','httpOnly') as $k) {
            if (array_key_exists($k, $params)) {
                $cookie[$k] = $params[$k];
            } elseif (array_key_exists($k, $this->defaults)) {
                $cookie[$k] = $this->defaults[$k];
            }
        }
        $cookie['expire'] = $this->getExpiration($cookie['expire']);
        return $cookie;
    }

    /**
     * Get expiration of cookie
     *
     * @param int $expire in second
     *
     * @return int
     */
    protected function getExpiration($expire)
    {
        if ($expire == "0" || $expire == 0) {
            return 0;
        }
        if (! is_numeric($expire)) {
            $expire = time() - 86500;
        } else {
            $expire = time() + $expire;
        }
        return $expire;
    }

    /**
     * Convert to `Set-Cookie` headers & reset object variables
     *
     * @return string[]
     */
    public function toHeaders()
    {
        $headers = [];
        foreach ($this->responseCookies as $name => $properties) {
            $headers[] = $this->toHeader($name, $properties);
        }
        return $headers;
    }

    /**
     * Convert to `Set-Cookie` header
     *
     * @param string $name       Cookie name
     * @param array  $properties Cookie properties
     *
     * @return string
     */
    protected function toHeader($name, array $properties)
    {
        $result = urlencode($name) . '=' . urlencode($properties['value']);

        if (isset($properties['domain'])) {
            $result .= '; domain=' . $properties['domain'];
        }

        if (isset($properties['path'])) {
            $result .= '; path=' . $properties['path'];
        }
        $timestamp = $this->getTimestamp($properties);

        if ($timestamp !== 0) {
            $result .= '; expires=' . gmdate('D, d-M-Y H:i:s e', $timestamp);
        }

        if (isset($properties['secure']) && $properties['secure']) {
            $result .= '; secure';
        }

        if (isset($properties['httpOnly']) && $properties['httpOnly']) {
            $result .= '; HttpOnly';
        }

        return $result;
    }

    /**
     * Create timestamp
     *
     * @param array $properties cookie properties
     *
     * @return mixed
     */
    protected function getTimestamp(array $properties)
    {
        $timestamp = 0;
        if (isset($properties['expire'])) {
            if (is_string($properties['expire'])) {
                $timestamp = strtotime($properties['expire']);
            } else {
                $timestamp = (int)$properties['expire'];
            }
        }
        return $timestamp;
    }

    /**
     * Get cookie
     *
     * @param string $key     cookie key
     * @param string $default default value
     *
     * @return string sanizited cookie
     */
    public function get($key, $default = null)
    {
        return isset($this->requestCookies[$key]) ? $this->requestCookies[$key] : $default;
    }

    /**
     * Returns to name of cookie
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
    * Delete a cookie
    *
    * @param string|array $name cookie
    *
    * @return void
    */
    public function delete($name = null)
    {
        if (is_array($name)) {
            $name['expire'] = -1;
            $name['value']  = null;
            $this->set($name);
            return;
        }
        if ($name != null) {
            $this->withName($name);
        }
        $this->withValue(null)->withExpire(-1)->set();
    }

    /**
     * Validate cookie name
     *
     * @return void
     */
    protected function validateName()
    {
        if (empty($this->name)) {
            throw new InvalidArgumentException("You must set a cookie name at first.");
        }
    }
}
