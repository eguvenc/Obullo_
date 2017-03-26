<?php

use League\Container\Container;

class MongoTest extends PHPUnit_Framework_TestCase
{
    protected $container;
    protected $connection;
    protected $mongoClass;

    /**
     * Setup variables
     *
     * @return void
     */
    public function setUp()
    {
        $this->container = new Container;
        $this->container->addServiceProvider('AppBundle\ServiceProvider\Config');
        $this->container->addServiceProvider('AppBundle\ServiceProvider\Mongo');
        $this->connection = $this->container->get('mongo:default');
        $this->mongoClass = (version_compare(phpversion('mongo'), '1.3.0', '<')) ? '\Mongo' : '\MongoClient';
    }

    /**
     * Shared
     *
     * @return void
     */
    public function testShared()
    {
        $connectionShared = $this->container->get('mongo:default');

        $this->assertInstanceOf($this->mongoClass, $this->connection, "I expect that the value is instance of MongoClient");
        $this->assertSame($this->connection, $connectionShared, "I expect that the two variables reference the same object.");
    }
}
