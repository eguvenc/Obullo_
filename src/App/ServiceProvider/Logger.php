<?php

namespace App\ServiceProvider;

use Obullo\Container\ServiceProvider\AbstractServiceProvider;

use Monolog\Logger as Log;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;

class Logger extends AbstractServiceProvider
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
        'logger',
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

        $logger = $container->share('logger', 'Monolog\Logger')
            ->withArgument('system');

        if (false == $container->get('config')->logger->enabled) {
            $logger->withMethodCall(
                'pushHandler',
                [new NullHandler]
            );
            return;
        }
        $logger->withMethodCall(
            'pushHandler',
            [new StreamHandler(APP . 'Resources/data/log/http.log', Log::DEBUG, true, 0666)]
        );
    }
}