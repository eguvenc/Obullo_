<?php

namespace Container\ServiceProvider;

use Container\ServiceProvider\AbstractServiceProvider;

class View extends AbstractServiceProvider
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
        'view'
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

        $container->share('view', 'View\View')
            ->withArgument($container)
            ->withArgument(
                [
                    'engine' => 'View\Native', // 'View\Plates\Plates',
                ]
            )
            ->withMethodCall(
                'addFolder',
                [
                    'templates',
                    RESOURCES.'templates/'
                ]
            )
            ->withMethodCall(
                'addFolder',
                [
                    'tests',
                    FOLDERS.'tests/views/'
                ]
            )
            ->withMethodCall(
                'addFolder',
                [
                    'examples',
                    FOLDERS.'examples/views/'
                ]
            );
    }
}