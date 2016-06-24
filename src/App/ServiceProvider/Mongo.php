<?php

namespace App\ServiceProvider;

use Obullo\Container\ServiceProvider\AbstractServiceProvider;

class Mongo extends AbstractServiceProvider
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
        'mongo'
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

        $container->share('mongo', 'Obullo\Container\Connector\Mongo')
            ->withArgument($container)
            ->withArgument(
                array(
                    'connections' =>
                    [
                        'default' => [
                            'server' => 'mongodb://root:123456@localhost:27017',
                            'options'  => ['connect' => true]
                        ],
                        'second' => [
                            'server' => 'mongodb://test:123456@localhost:27017',
                            'options'  => ['connect' => true]
                        ]
                    ]
                )
            );
    }
}