<?php

namespace Obullo\Router\Route;

use Obullo\Router\RouterInterface as Router;

/**
 * Attach elements to route
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Attach
{
    /**
     * Router
     *
     * @var object
     */
    protected $router;

    /**
     * Domain
     * 
     * @var string
     */
    protected $domain;

    /**
     * Group data
     * 
     * @var array
     */
    protected $group = array();

    /**
     * Attached middleware data
     * 
     * @var array
     */
    protected $attach = array();

    /**
     * Set group object
     * 
     * @param object $group group
     *
     * @return void
     */
    public function setGroup($group)
    {
        $this->group = $group;
        return $this;  
    }

    /**
     * Set domain object
     * 
     * @param domain $domain domain
     *
     * @return void
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }
    
    /**
     * Add middleware to current route
     * 
     * @param array|string $routes current uri
     *
     * @return void
     */
    public function toGroup($routes)
    {
        $routes  = (array)$routes;
        $options = $this->group->getOptions();
        $domain  = $this->domain->getName();


        // If we have not middlewares or no domain matches stop the run.
        // 
        if (empty($options['middleware']) || ! $this->domain->match()) {
            return;
        }
        $host = $this->domain->getBaseHost();   // We have a problem when the host is subdomain 
                                                // but config domain not. This fix the isssue.
        // Attach Regex Support
        // 
        if ($this->domain->isSub($domain)) {
            $host = $this->domain->getHost();
        }
        if ($domain != $host) {
            return;
        }
        foreach ($routes as $route) {
            $this->toAttach($domain, $options['middleware'], $route, $options);
        }
    }

    /**
     * Configure attached middleware
     * 
     * @param string       $domain      name
     * @param string|array $middlewares arguments
     * @param string       $route       route
     * @param array        $options     arguments
     * 
     * @return void
     */
    public function toAttach($domain, $middlewares, $route = 'global', $options = array())
    {
        unset($options['middleware']);
        
        foreach ((array)$middlewares as $middleware) {
            $this->attach[$domain][] = array(
                'name' => $middleware['name'],
                'params' => array_merge($middleware['params'], $options),
                'route' => $route, 
                'attach' => $route
            );
        }
    }

    /**
     * Get middlewares
     * 
     * @return array
     */
    public function getArray ()
    {
        if ($this->domain == null) {
            return array();
        }
        $host = $this->domain->getBaseHost();
        if ($this->domain->isSub($this->domain->getHost())) {
            $host = $this->domain->getHost();
        }
        if (! isset($this->attach[$host])) {  // Check first
            return array();
        }
        return $this->attach[$host];
    }

}