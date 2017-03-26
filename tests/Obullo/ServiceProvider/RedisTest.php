<?php

use League\Container\Container;

class RedisTest extends PHPUnit_Framework_TestCase
{
    protected $container;
    protected $connection;

    /**
     * Setup variables
     *
     * @return void
     */
    public function setUp()
    {
        $this->container = new Container;
        $this->container->addServiceProvider('AppBundle\ServiceProvider\Config');
        $this->container->addServiceProvider('AppBundle\ServiceProvider\Redis');
        $this->connection = $this->container->get('redis:default');
    }
    
    /**
     * Shared
     *
     * @return void
     */
    public function testShared()
    {
        $connectionShared = $this->container->get('redis:default');

        $this->assertInstanceOf('Redis', $this->connection, "I expect that the value is instance of Redis");
        $this->assertSame($this->connection, $connectionShared, "I expect that the two variables reference the same object.");
    }
}
