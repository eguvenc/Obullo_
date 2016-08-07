<?php

class DatabaseTest extends PHPUnit_Framework_TestCase
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
        $this->container->addServiceProvider('App\ServiceProvider\Database');

        $this->connection = $this->container->get('database:default');
    }

    /**
     * Shared
     *
     * @return void
     */
    public function testShared()
    {
        $connectionShared = $this->container->get('database:default');

        $this->assertInstanceOf('Obullo\Database\Doctrine\DBAL\Adapter', $this->connection, "I expect that the value is instance of $this->adapterClass.");
        $this->assertSame($this->connection, $connectionShared, "I expect that the two variables reference the same object.");
    }
}
