<?php

use League\Container\Container;

use Obullo\ServerRequestFactory;

class DoctrineTest extends PHPUnit_Framework_TestCase
{
    protected $container;
    protected $connection;
    protected $adapterClass = '\Obullo\Database\Doctrine\DBAL\Adapter';

    /**
     * Setup variables
     *
     * @return void
     */
    public function setUp()
    {
        define('APP_PATH', ROOT .'src/AppBundle/');
        define('APP_NAME', 'AppBundle');

        $this->container = new Container;
        $this->container->addServiceProvider('AppBundle\ServiceProvider\Logger');
        $this->container->addServiceProvider('AppBundle\ServiceProvider\Config');
        $this->container->addServiceProvider('AppBundle\ServiceProvider\Doctrine');

        ServerRequestFactory::setContainer($this->container);
        $this->container->share('request', ServerRequestFactory::fromGlobals());

        $this->connection = $this->container->get('doctrine:default');
    }

    /**
     * Shared
     *
     * @return void
     */
    public function testShared()
    {
        $connectionShared = $this->container->get('doctrine:default');

        $this->assertInstanceOf('Obullo\Database\Doctrine\DBAL\Adapter', $this->connection, "I expect that the value is instance of $this->adapterClass.");
        $this->assertSame($this->connection, $connectionShared, "I expect that the two variables reference the same object.");
    }
}
