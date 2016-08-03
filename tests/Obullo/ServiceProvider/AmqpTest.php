<?php

class AmqpTest extends PHPUnit_Framework_TestCase
{
    protected $container;
    protected $AMQPClass;
    protected $AMQPConnection;

    /**
     * Setup variables
     *
     * @return void
     */
    public function setUp()
    {
        global $container;
        $this->container = $container;
        $this->container->addServiceProvider('App\ServiceProvider\Amqp');
        $this->AMQPConnection = $this->container->get('Amqp:Default');
        $this->AMQPClass = (get_class($this->AMQPConnection) == 'AMQPConnection') ? 'AMQPConnection' : 'PhpAmqpLib\Connection\AMQPConnection';
    }

    /**
     * Shared
     *
     * @return void
     */
    public function testShared()
    {
        $AMQPConnectionShared = $this->container->get('Amqp:Default');

        $this->assertInstanceOf($this->AMQPClass, $this->AMQPConnection, "I expect that the value is instance of $this->AMQPClass.");
        $this->assertSame($this->AMQPConnection, $AMQPConnectionShared, "I expect that the two variables reference the same object.");
    }
}
