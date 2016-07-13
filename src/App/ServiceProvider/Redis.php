<?php

namespace App\ServiceProvider;

use Obullo\Config\ConfigFile;
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
        'redis'
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

        $file  = new ConfigFile('redis');
        $redis = $file->getObject();

        $container->share('redis', 'Obullo\Container\Connector\Redis')
            ->withArgument($container)
            ->withArgument(
                array(
                    'connections' => 
                    [
                        'default' => [
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
                        ],
                        'second' => [
                            'host' => $redis->connections->second->host,
                            'port' => $redis->connections->second->port,
                            'options' => [
                                'persistent' => false,
                                'auth' => '',
                                'timeout' => 30,
                                'attempt' => 100,
                                'serializer' => 'php',
                                'database' => null,
                                'prefix' => null,
                            ]
                        ],
                    ]
                )
            );
    }
}