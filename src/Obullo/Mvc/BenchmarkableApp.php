<?php

namespace Obullo\Mvc;

use Interop\Container\ContainerInterface as Container;

/**
 * Benchmarkable Application
 *
 * @copyright 2009-2017 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class BenchmarkableApp extends App
{
    /**
     * Benchmark timer
     * @var int
     */
    protected $start;

    /**
     * Constructor
     *
     * @param container $container container
     */
    public function __construct(Container $container)
    {
        $this->start = microtime(true);

        $container->addServiceProvider('AppBundle\ServiceProvider\Console');
        
        parent::__construct($container);
    }

    /**
     * Close the application
     *
     * @return void
     */
    public function close()
    {
        $container = $this->getContainer();
        $console = $container->get('console');

        $end   = microtime(true) - $this->start;
        $usage = 'memory_get_usage() function not found on your php configuration.';
        
        if (function_exists('memory_get_usage') && ($usage = memory_get_usage()) != '') {
            $usage = round($usage/1024/1024, 2). ' MB';
        }
        $extra['time']   = number_format($end, 4);
        $extra['memory'] = $usage;
        $extra['uri']    = $this->path;

        $console->log('Final output sent to browser', $extra);
        $console->log("-------------------------------------------------------------");

        parent::close();
    }
}
