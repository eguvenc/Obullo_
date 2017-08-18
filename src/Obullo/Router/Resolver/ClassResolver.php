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
     * Segments
     *
     * @var array
     */
    protected $segments;
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
     * Returns to class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->segments[0];
    }

    /**
     * Returns to method name
     *
     * @return string
     */
    public function getMethod()
    {
        if (empty($this->segments[1])) {  // default method
            return 'index';
        }
        return $this->segments[1];
    }
}
