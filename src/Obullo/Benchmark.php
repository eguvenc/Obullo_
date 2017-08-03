<?php

namespace Obullo;

use Interop\Container\ContainerInterface as Container;

/**
 * Benchmark helper
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Benchmark
{
    protected static $time;

    /**
     * Start app benchmark
     *
     * @return object
     */
    public static function start()
    {
        self::$time = microtime(true);
    }

    /**
     * Finalize benchmark
     *
     * @param container $container container
     * @param array     $extra     extra
     *
     * @return void
     */
    public static function end(Container $container, $extra = array())
    {
        $logger = $container->get('logger');

        $end = microtime(true) - self::$time;
        $usage = 'memory_get_usage() function not found on your php configuration.';
        
        if (function_exists('memory_get_usage') && ($usage = memory_get_usage()) != '') {
            $usage = round($usage/1024/1024, 2). ' MB';
        }
        $extra['time']   = number_format($end, 4);
        $extra['memory'] = $usage;
        
        $logger->debug('Final output sent to browser', $extra);
    }
}