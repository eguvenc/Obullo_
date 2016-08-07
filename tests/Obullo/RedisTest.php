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
        $this->container->addServiceProvider('App\ServiceProvider\Redis');
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
}
