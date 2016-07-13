<?php

namespace App\ServiceProvider;

use Obullo\Config\ConfigFile;
use Obullo\Container\ServiceProvider\AbstractServiceProvider;

class Database extends AbstractServiceProvider
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
        'database'
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

        $file     = new ConfigFile('database');
        $database = $file->getObject();
        
        $params = array(
            'connections' => 
            [
                'default' => [
                    'dsn'      => $database->connections->default->dsn,
                    'username' => $database->connections->default->username,
                    'password' => $database->connections->default->password,
                    'options'  => [
                        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                        \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                    ]
                ],

            ],
            'log' => [
                'sqlQuery' => true
            ]
        );
        
        // $container->share('database', 'Obullo\Container\Connector\Database')
        //     ->withArgument($container)
        //     ->withArgument($params);

        // DoctrineDBAL Replacement
        // 

        $container->share('database', 'Obullo\Container\Connector\DoctrineDBAL')
            ->withArgument($container)
            ->withArgument($params);

    }
}