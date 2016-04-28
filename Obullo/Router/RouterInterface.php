<?php

namespace Router;

use Closure;

/**
 * Router Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface RouterInterface
{
    /**
     * Create grouped routes
     * 
     * @param string $uri     match route
     * @param object $closure which contains $this->attach(); methods
     * 
     * @return object
     */
    public function group($uri, $closure = null);

    /**
     * Creates multiple http route
     * 
     * @param string $methods http methods
     * @param string $match   uri string match regex
     * @param string $rewrite uri rewrite regex value
     * @param string $closure optional closure function
     * 
     * @return object router
     */
    public function map($methods, $match, $rewrite = null, $closure = null);

}