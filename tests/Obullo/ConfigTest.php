<?php

use League\Container\Container;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    protected $container;

    /**
     * Setup variables
     *
     * @return void
     */
    public function setUp()
    {
        $this->container = new Container;
        $this->container->addServiceProvider('AppBundle\ServiceProvider\Config');
    }

    /**
     * Shared
     *
     * @return void
     */
    public function testSetEnv()
    {
        $config = $this->container->get('config');

        $config->setEnv("test");
        
        $this->assertEquals("test", $config->getEnv("test"), "I expect that the value is equal to 'test'.");
    }
}
