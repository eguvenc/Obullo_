<?php

namespace AppBundle\ServiceProvider;

use Obullo\Connectors\AmqpConnector;
use Obullo\Container\ServiceProvider\AbstractServiceProvider;

class Amqp extends AbstractServiceProvider
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
        'Amqp:Default'
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

        $connectionParams =  array(
            'exchange' => [
                'type' => 'direct',  // fanout / header / topic
                'flag' => 'durable', // passive
            ],
            'host'  => '127.0.0.1',
            'port'  => 5672,
            'username'  => 'root',
            'password'  => '123456',
            'vhost' => '/',
        );

        $connector = new AmqpConnector($connectionParams);
        $container->share('Amqp:Default', $connector->getConnection());
    }
}
