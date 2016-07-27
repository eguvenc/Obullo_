<?php

namespace AppBundle\ServiceProvider;

use Obullo\Session\SaveHandler\CacheSaveHandler;
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

        $params = [
            'session' => [
                'key' => 'Session_',
                'gc_maxlifetime' => 3600,
            ],
            'cookie' => [
                'lifetime' => 3600,
                'domain'   => '',
                'path'     => '/',
                'secure'   => false,
                'httpOnly' => false,
                'prefix'   => ''
            ],
        ];
        $saveHandler = new CacheSaveHandler($container->get('cache'), $params);

        $container->share('session', 'Obullo\Session\Session')
            ->withArgument($container->get('request'))
            ->withArgument($container->get('logger'))
            ->withArgument($params)
            ->withMethodCall('setName', ['session_'])
            ->withMethodCall('setSaveHandler', [$saveHandler])
            ->withMethodCall('start');
    }
}
