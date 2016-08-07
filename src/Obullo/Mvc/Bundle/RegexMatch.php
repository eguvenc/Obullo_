<?php

namespace Obullo\Mvc\Bundle;

/**
 * Bundle regex match
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class RegexMatch
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
        if (preg_match("#".$this->pattern."#", $path)) {
            return true;
        }
        return false;
    }
}
