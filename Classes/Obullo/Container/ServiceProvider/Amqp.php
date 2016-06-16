<?php

namespace Obullo\Container\ServiceProvider;

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
        'amqp'
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

        $params =  array(
            'exchange' => [
                'type' => 'direct',  // fanout / header / topic
                'flag' => 'durable', // passive
            ],
            'connections' => 
            [
                'default' => [
                    'host'  => '127.0.0.1',
                    'port'  => 5672,
                    'username'  => 'root',
                    'password'  => '123456',
                    'vhost' => '/',
                ],
            ]
        );

        $container->share('amqp', 'Obullo\Container\ServiceProvider\Connector\Amqp')
            ->withArgument($container)
            ->withArgument($params);

        // AmqpLib Replacement
        // 
        
        // $container->share('amqp', 'Obullo\Container\ServiceProvider\Connector\AmqpLib')
        //     ->withArgument($container)
        //     ->withArgument($params);

    }
}