<?php

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
        global $container;
        $this->container = $container;
        $this->container->addServiceProvider('Obullo\Container\ServiceProvider\Redis');
        $this->connection = $this->container->get('redis')->shared(['connection' => 'default']);

    }
    
    /**
     * Shared
     * 
     * @return void
     */
    public function testShared()
    {
        $connectionShared = $this->container->get('redis')->shared(['connection' => 'default']);

        $this->assertInstanceOf('Redis', $this->connection, "I expect that the value is instance of Redis");
        $this->assertSame($this->connection, $connectionShared, "I expect that the two variables reference the same object.");
    }

    /**
     * Factory
     * 
     * @return void
     */
    public function testFactory()
    {
        $connectionFactory = $this->container->get('redis')->factory(
            [
                'host' => '127.0.0.1',
                'port' => 6379,
                'options' => array(
                    'auth' => '123456',    // Connection password
                    'timeout' => 30,
                    'persistent' => 0,
                    'reconnection.attemps' => 100,
                    'serializer' => 'none',
                    'database' => null,
                    'prefix' => null,
                )
            ]
        );
        $this->assertNotSame($this->connection, $connectionFactory, "I expect that the shared and factory instances are not the same object.");
    }

}