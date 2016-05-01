<?php

namespace Router\Filter;

use Psr\Http\Message\RequestInterface as Request;

/**
 * Middleware conditions
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Condition
{
    protected $path;
    protected $match = false;

    /**
     * Contsructor
     * 
     * @param Request $request request
     */
    public function __construct(Request $request)
    {
        $this->path = $request->getUri()->getPath();
    }

    /**
     * If uri contains path(s)
     * 
     * @param strig|array $path path
     * 
     * @return object
     */
    public function ifContains($path)
    {
        foreach ((array)$path as $value) {
            if (stripos($this->path, "/".trim($value, "/")) !== false) {
                $this->match = true;
                continue;
            }
        }
        return $this;
    }

    /**
     * If uri NOT contains path(s)
     * 
     * @param strig|array $path path
     * 
     * @return object
     */
    public function ifNotContains($path)
    {
        foreach ((array)$path as $value) {
            if (stripos($this->path, "/".trim($value, "/")) === false) {
                $this->match = true;
                continue;
            }
        }
        return $this;
    }

    /**
     * If uri equal to path(s)
     * 
     * @param strig|array $path path
     * 
     * @return object
     */
    public function ifEquals($path)
    {
        foreach ((array)$path as $value) {
            if ($this->path == "/".trim($value, "/")) {
                $this->match = true;
                continue;
            }
        }
        return $this;
    }

    /**
     * If uri NOT equal to path(s)
     * 
     * @param strig|array $path path
     * 
     * @return object
     */
    public function ifNotEquals($path)
    {
        foreach ((array)$path as $value) {
            if ($this->path != "/".trim($value, "/")) {
                $this->match = true;
                continue;
            }
        }
        return $this;
    }

    /**
     * Returns to condition match result
     * 
     * @return boolean
     */
    public function hasMatch()
    {
        return $this->match;
    }

}