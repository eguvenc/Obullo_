<?php

namespace Obullo\Mvc;

/**
 * HMVC based Controller.
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Controller
{
    /**
     * Container
     *
     * @var object
     */
    public $container;

    /**
     * Controller instance
     *
     * @var object
     */
    public static $instance = null;
    
    /**
     * Constructor
     *
     * @param object $container container
     */
    public function __construct($container)
    {
        self::$instance = &$this;
        $this->container = $container;
    }

    /**
     * Container proxy
     *
     * @param string $key key
     *
     * @return object Controller
     */
    public function __get($key)
    {
        /**
         * Create new layer for each core classes ( Otherwise HMVC does not work )
         */
        if (in_array($key, ['request', 'router', 'view'])) {
            self::$instance = &$this;
        }
        return $this->container->get($key); // ucfirst($key)
    }

    /**
     * We prevent to set none object variables
     *
     * Forexample in controller this is not allowed $this->user_variable = 'hello'.
     *
     * @param string $key string
     * @param string $val mixed
     *
     * @return void
     */
    public function __set($key, $val)  // Custom variables is not allowed !!!
    {
        if (is_object($val)) {
            $this->{$key} = $val; // WARNING : Store only object types otherwise container params
                                  // variables come in here.
        }
    }

    /**
     * Returns to sub request object
     *
     * @return object
     */
    public function subRequest()
    {
        return $this->container->get('subRequest');
    }

    /**
     * Returns view file output
     *
     * @param mixed $filename filename
     * @param mixed $data     array data
     *
     * @return string
     */
    public function render($filename, $data = array())
    {
        return $this->container->get('view')->render($filename, $data);
    }
}
