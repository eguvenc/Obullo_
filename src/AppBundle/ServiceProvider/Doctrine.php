<?php

namespace AppBundle\ServiceProvider;

use Obullo\Connectors\DoctrineConnector;
use Obullo\Container\ServiceProvider\AbstractServiceProvider;

class Doctrine extends AbstractServiceProvider
{
    /**
     * The provides array is a way to let the container
     * know that a service is provided by this service
     * provider. Every service that is registered via
     * this service provider must have an alias added
     * to this array or it will be ignored.
     *
     * @var array
     */
    protected $provides = [
        'doctrine:default'
    ];

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to, but remember, every alias registered
     * within this method must be declared in the `$provides` array.
     *
     * @return void
     */
    public function register()
    {
        $container = $this->getContainer();

        $database = $container->get('config')->load('database')->getObject();
        
        $connectionParams = array(
            'dsn'      => $database->connections->default->dsn,
            'username' => $database->connections->default->username,
            'password' => $database->connections->default->password,
            'options'  => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
            ]
        );
        $connector = new DoctrineConnector($connectionParams);
        $connector->setLogger($container->get('logger'));
        $container->share('doctrine:default', $connector->getConnection());
    }
}
