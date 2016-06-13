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
        $this->container->addServiceProvider('Obullo\Container\ServiceProvider\Memcached');
        $this->connection = $this->container->get('memcached')->shared(['connection' => 'default']);

    }

    /**
     * Test extension
     * 
     * @return void
     */
    public function testExtensionIsLoaded()
    {
        $this->assertInstanceOf('Memcached', $this->connection);
    }

    /**
     * Shared
     * 
     * @return void
     */
    public function testShared()
    {
        $connectionShared = $this->container->get('memcached')->shared(['connection' => 'default']);

        $this->assertInstanceOf('Memcached', $this->connection, "I expect that the value is instance of Memcached");
        $this->assertSame($this->connection, $connectionShared, "I expect that the two variables reference the same object.");
    }

    /**
     * Factory
     * 
     * @return void
     */
    public function testFactory()
    {
        $connectionFactory = $this->container->get('memcached')->factory(
            [
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 1,
                'options' => array(
                    'persistent' => false,
                    'pool' => 'connection_pool',   // http://php.net/manual/en/memcached.construct.php
                    'timeout' => 30,               // Seconds
                    'attempt' => 100,
                    'serializer' => 'php',    // php, json, igbinary
                    'prefix' => null
                )
            ]
        );
        $this->assertNotSame($this->connection, $connectionFactory, "I expect that the shared and factory instances are not the same object.");

        $connectionNewFactory = $this->container->get('memcached')->factory(
            [
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 1,
                'options' => array(
                    'persistent' => false,
                    'pool' => 'connection_pool',
                    'timeout' => 30,
                    'attempt' => 100,
                    'serializer' => 'php',    // php, json, igbinary
                    'prefix' => "test_123"
                )
            ]
        );
        $this->assertNotSame($connectionFactory, $connectionNewFactory, "I expect that the old factory and new factory instances are not the same object.");
    }

}