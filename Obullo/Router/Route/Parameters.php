<?php

namespace Obullo\Router\Route;

use Obullo\Router\RouterInterface as Router;

/**
 * Route parameteres
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Parameters
{
    /**
     * Parse closure parameters
     * 
     * @param string $uri uri
     * @param array  $val route values
     * 
     * @return array
     */ 
    public static function parse($uri, $val)
    {
        $parameters = array();        
        $parametersIndex   = preg_split('#{(.*?)}#', $val['scheme']); // Get parameter indexes
        $parameterUri      = substr($uri, strlen($parametersIndex[0]));
        $parametersReIndex = array_keys(array_slice($parametersIndex, 1));

        $segments = explode('/', $parameterUri);

        foreach ($parametersReIndex as $key) {  // Find parameters we will send it to closure($args)
            $parameters[] = (isset($segments[$key])) ? $segments[$key] : null;
        }
        return $parameters;
    }

}