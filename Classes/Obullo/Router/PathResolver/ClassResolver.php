<?php

namespace Obullo\Router\Resolver;

use Obullo\Router\RouterInterface as Router;

/**
 * Resolve class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ClassResolver
{
    /**
     * Router
     *
     * @var object
     */
    protected $router;

    /**
     * Segments
     * 
     * @var array
     */
    protected $segments;

    /**
     * Constructor
     * 
     * @param Router $router router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Resolve
     * 
     * @param array $segments uri segments
     * 
     * @return array resolved segments
     */
    public function resolve(array $segments)
    {
        $this->segments = $segments;
        return $this;
    }

    /**
     * Get segment factor
     * 
     * @return int
     */
    public function getArity()
    {
        return -1;
    }

    /**
     * Uri segments
     * 
     * @return array
     */
    public function getSegments()
    {
        return $this->segments;
    }    

}