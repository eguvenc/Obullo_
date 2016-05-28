<?php

namespace Obullo\Utils;

/**
 * Route helper
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Route
{
    /**
     * Replace underscore to spaces to use ucwords
     * 
     * Before  : widgets\tutorials_a 
     * Ucwords : widgets\Tutorials A
     * Final   : Widgets\Tutorials_A
     * 
     * @param string $string    namespace part
     * @param string $delimiter default underscore "_"
     * 
     * @return void
     */
    public static function ucwords($string, $delimiter = "_")
    {
        $str = str_replace($delimiter, ' ', $string);
        $str = ucwords($str);
        return str_replace(' ', $delimiter, $str);
    }
}