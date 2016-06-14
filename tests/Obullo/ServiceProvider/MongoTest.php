<?php

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
        global $container;
        $this->container = $container;
        $this->container->addServiceProvider('Obullo\Container\ServiceProvider\Mongo');
        $this->connection = $this->container->get('mongo')->shared(['connection' => 'default']);
        $this->mongoClass = (version_compare(phpversion('mongo'), '1.3.0', '<')) ? '\Mongo' : '\MongoClient';
    }

    /**
     * Shared
     * 
     * @return void
     */
    public function testShared()
    {
        $connectionShared = $this->container->get('mongo')->shared(['connection' => 'default']);

        $this->assertInstanceOf($this->mongoClass, $this->connection, "I expect that the value is instance of MongoClient");
        $this->assertSame($this->connection, $connectionShared, "I expect that the two variables reference the same object.");
    }

    /**
     * Factory
     * 
     * @return void
     */
    public function testFactory()
    {
        $connectionFactory = $this->container->get('mongo')->factory(
            [
                'server' => 'mongodb://localhost:27017',
                'options' => array('connect' => true)
            ]
        );
        $this->assertNotSame($this->connection, $connectionFactory, "I expect that the shared and factory instances are not the same object.");
    }

}