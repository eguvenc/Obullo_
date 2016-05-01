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
    public function rewrite($method, $pattern, $rewrite);
    public function group($pattern, $callable);
    public function map($method, $pattern, $handler = null)
}