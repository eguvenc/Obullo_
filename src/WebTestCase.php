<?php

use League\Container\Container;

use Obullo\Router\Router;
use Zend\Diactoros\Response;
use Obullo\ServerRequestFactory;

/**
 * We create a Web test case for PHP Unit
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class WebTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Container
     *
     * @var object
     */
    protected $container;
    

    public function setUp()
    {
        $this->container = new Container;

        /**
         * Create test server
         */
        $server['HTTP_USER_AGENT'] = "Obullo Web Test Case";
        $server['REMOTE_ADDR'] = "127.0.0.1";

        $this->container->share('request', ServerRequestFactory::fromGlobals($server));
        $this->container->share('response', new Response);
        $this->container->share('router', new Router($this->container, ['autoResolver' => true]));

        $container->addServiceProvider('AppBundle\ServiceProvider\Logger');
        $container->addServiceProvider('AppBundle\ServiceProvider\Config');
    }
}
