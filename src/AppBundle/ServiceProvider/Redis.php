<?php

namespace AppBundle\ServiceProvider;

use Obullo\Connectors\RedisConnector;
use Obullo\Container\ServiceProvider\AbstractServiceProvider;

class Redis extends AbstractServiceProvider
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
        'Redis:Default'
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

        $redis = $container->get('config')->load('redis')->getObject();

        $connectionParams = [
            'host' => $redis->connections->default->host,
            'port' => $redis->connections->default->port,
            'options' => [
                'persistent' => false,
                'auth' => '',
                'timeout' => 30,
                'attempt' => 100,
                'serializer' => 'none',
                'database' => null,
                'prefix' => null,
            ]
        ];
        $connector = new RedisConnector($connectionParams);
        $container->share('Redis:Default', $connector->getConnection());
    }
}