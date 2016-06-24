<?php

namespace Obullo\View;

use Interop\Container\ContainerInterface as Container;

/**
 * View Controller
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ViewController
{
	/**
	 * Hmvc Layer
	 * 
	 * @var object
	 */
    protected $layer;

    /**
     * Constructor
     * 
     * @param container $container container
     */
    public function __construct(Container $container)
    {
        $this->layer = $container->get('layer');
    }

    /**
     * Get controller
     * 
     * @param  string $path       class path
     * @param  array  $data       post / get data
     * @param  int    $expiration cache expiration for cache library
     * 
     * @return string
     */
    public function get($path = '/', $data = array(), $expiration = null)
    {
        return $this->layer->get('View/Controller', $path = '/', $data, $expiration);
    }

}