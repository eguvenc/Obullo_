<?php

use League\Container\Container;

class MemcachedTest extends PHPUnit_Framework_TestCase
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
        $this->container->addServiceProvider('AppBundle\ServiceProvider\Memcached');
        $this->connection = $this->container->get('memcached:default');
    }

    /**
     * Shared
     *
     * @return void
     */
    public function testShared()
    {
        $connectionShared = $this->container->get('memcached:default');

        $this->assertInstanceOf('Memcached', $this->connection, "I expect that the value is instance of Memcached");
        $this->assertSame($this->connection, $connectionShared, "I expect that the two variables reference the same object.");
    }
}
