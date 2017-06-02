<?php

namespace AppBundle\ServiceProvider;

use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

use Obullo\Container\ServiceProvider\AbstractServiceProvider;

class Session extends AbstractServiceProvider
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
        'session'
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

        $storage = new NativeSessionStorage(array(), new NativeFileSessionHandler());

        $container->share('session', 'Symfony\Component\HttpFoundation\Session\Session')
            ->withArgument($storage)
            ->withMethodCall('start');
    }
}
