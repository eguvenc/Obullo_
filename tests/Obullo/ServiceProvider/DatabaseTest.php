<?php

class DatabaseTest extends PHPUnit_Framework_TestCase
{
    protected $container;
    protected $connection;
    protected $adapterClass;

    /**
     * Setup variables
     *
     * @return void
     */
    public function setUp()
    {
        global $container;
        $this->container = $container;
        $this->container->addServiceProvider('Obullo\Container\ServiceProvider\Database');
        $this->connection = $this->container->get('database')->shared(['connection' => 'default']);
        
        $this->adapterClass = ($this->connection instanceof \Obullo\Database\Pdo\Adapter) ? 'Obullo\Database\Pdo\Adapter' : 'Obullo\Database\Doctrine\DBAL\Adapter';
    }

    /**
     * Shared
     * 
     * @return void
     */
    public function testShared()
    {
        $connectionShared = $this->container->get('database')->shared(['connection' => 'default']);

        $this->assertInstanceOf($this->adapterClass, $this->connection, "I expect that the value is instance of $this->adapterClass.");
        $this->assertSame($this->connection, $connectionShared, "I expect that the two variables reference the same object.");
    }

    /**
     * Factory
     * 
     * @return void
     */
    public function testFactory()
    {
        $connectionFactory = $this->container->get('database')->factory(
            [
                'dsn'      => 'pdo_mysql:host=localhost;port=;dbname=test',
                'username' => 'root',
                'password' => '123456',
                'options' => [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                    \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                ]
            ]
        );
        $this->assertNotSame($this->connection, $connectionFactory, "I expect that the shared and factory instances are not the same object.");
    }

}