<?php

namespace Obullo\Router\Route;

use Obullo\Router\Domain;
use InvalidArgumentException;
use Obullo\Router\RouterInterface as Router;

/**
 * Route
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Route
{
    /**
     * Route number
     * 
     * @var integer
     */
    protected $count = 0;

    /**
     * Router
     *
     * @var object
     */
    protected $router;

    /**
     * Domain
     * 
     * @var object
     */
    protected $domain;

    /**
     * Route rules
     * 
     * @var array
     */
    protected $routes = array();

    /**
     * Constructor
     * 
     * @param Router $router router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->domain = $router->getDomain();
    }

    /**
     * Defines http routes
     * 
     * @param string $methods method names
     * @param string $match   uri string match regex
     * @param string $rewrite uri rewrite regex value
     * @param string $closure optional closure function
     * 
     * @return object router
     */
    public function addRoute($methods, $match, $rewrite = null, $closure = null)
    {
        $options = ($this->router->getGroup()) ? $this->router->getGroup()->getOptions() : array();

        ++$this->count;

        $this->routes[$this->domain->getName()][$this->count] = array(
            'sub.domain' => $this->_getSubDomainValue($this->domain->getName(), $options),
            'when' => $methods, 
            'match' => trim($match, "/"),
            'rewrite' => trim($rewrite, "/"),
            'scheme' => $this->_getSchemeValue($match),
            'closure' => $closure,
            'middlewares' => array()
        );
    }

    /**
     * Attach middlewars
     * 
     * @param string $middleware middleware
     * @param mixed  $params     middleware parameters
     *
     * @return void
     */
    public function add($middleware, $params = null)
    {
        if (! is_string($middleware)) {
            throw new InvalidArgumentException("Middleware value must be string.");
        }
        $this->routes[$this->domain->getName()][$this->count]['middlewares'][] = [
            'name' => $middleware,
            'params' => (array)$params
        ];
    }

    /**
     * Returns true if routes array has no data otherwise false
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->routes);
    }

    /**
     * Gets current domain name routes
     * 
     * @return array
     */
    public function getArray()
    {
        $host = $this->domain->getBaseHost();
        if ($this->domain->isSub($this->domain->getHost())) {
            $host = $this->domain->getHost();
        }
        return (empty($this->routes[$host])) ? false : $this->routes[$host];
    }

    /**
     * Returns to all routes array
     * 
     * @return array
     */
    public function getAll()
    {
        return $this->routes;
    }

    /**
     * Replace route scheme
     * 
     * @param array $replace scheme data
     * 
     * @return object
     */
    public function addWhere(array $replace)
    {
        $domain     = $this->domain->getName();
        $immutable  = $this->domain->getImmutable();

        if (! empty($this->routes[$domain][$this->count]['sub.domain'])) {
            $immutable = $this->routes[$domain][$this->count]['sub.domain'];
        }
        if ($domain == $immutable) {

            $replace = $this->addBrackets($replace); // support for 
                                                     // ->where(array('id' => '[0-9]+', 'name' => '[a-z]+', 'any' => '.*'));
                                                     // instead of array('id' => '([0-9]+)'
            $scheme = str_replace(
                array_keys($replace),
                array_values($replace),
                $this->routes[$domain][$this->count]['scheme']
            );
            $scheme = str_replace(
                array('{','}'),
                array('',''),
                $scheme
            );
            $this->routes[$domain][$this->count]['match'] = $scheme;
        }
    }

    /**
     * Add brackets to regex
     * 
     * @param array $replace uri replace string
     *
     * @return array
     */
    protected function addBrackets(array $replace)
    {
        $newArray = array();
        foreach ($replace as $key => $value) {
            if (substr($value, 0, 1) != '(') {      // If have not brackets
                $newArray[$key] = '('.$value.')';   // Add brackets to regex for preg_replace() operation
                                                    // in router.php dispatchRouteMatches() func.
            }
        }
        return $newArray;
    }

    /**
     * Get subdomain value
     *
     * @param string $domain name
     * 
     * @return mixed
     */
    private function _getSubDomainValue($domain)
    {
        if ($this->domain->isSub($domain)) {
            return $domain;
        }
        return null;
    }

    /**
     * Get scheme value
     * 
     * @param string $match param
     * 
     * @return string
     */
    private function _getSchemeValue($match)
    {
        return (strpos($match, '}') !== false) ? trim($match, '/') : null;
    }

}