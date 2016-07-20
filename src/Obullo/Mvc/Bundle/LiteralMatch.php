<?php

namespace Obullo\Mvc\Bundle;

/**
 * Bundle literal match
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class LiteralMatch
{
    protected $pattern;

    /**
     * Constructor
     *
     * @param string $pattern match pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Returns to true if bundle match with path
     *
     * @param string $path path
     *
     * @return boolean
     */
    public function hasMatch($path)
    {
        $exp    = explode('/', trim($path, '/'));
        $folder = isset($exp[0]) ? "/".$exp[0] : "/";

        if ($folder == $this->pattern) {
            return true;
        }
        return false;
    }
}
