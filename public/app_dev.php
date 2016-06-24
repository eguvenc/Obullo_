<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Define the root
 */
define('ROOT', dirname(__DIR__).'/');

/**
  * This makes our life easier when dealing with paths. Everything is relative
  * to the application root now.
  */
chdir(dirname(__DIR__));

/**
 * Step 1: Require the Obullo using Composer's autoloader
 *
 * If you are not using Composer, you need to load Obullo with your own
 * PSR-4 autoloader.
 */
require 'constants.php';
require 'vendor/autoload.php';

/**
 * Step 2: Instantiate the Container
 */
$container = new League\Container\Container;
Obullo\ServerRequestFactory::setContainer($container);

$container->share('request', Obullo\ServerRequestFactory::fromGlobals());
$container->share('response', new Zend\Diactoros\Response);
$container->share('router', new Obullo\Router\Router($container, ['resolveCurrentPath' => true]));

/**
 * Step 3: Add config service & Create configuration variables
 */
$container->addServiceProvider('App\ServiceProvider\Config');

/**
 * Create configuration variables
 */
$container->get('config')
    ->set(
        'app.config',
        array(
            'log' => true,
            'cookie' => [
                'domain' => '',
                'path' => '/',
                'secure' => false,
                'httpOnly' => true,
                'expire' => 604800,
                'prefix' => '',
            ],
        )
    );

/**
 * Step 4: Add your middlewares
 */
// $app->add('Translation');
// $app->add('ParsedBody');

/**
 * Step 5: Add your service providers
 */
$container->addServiceProvider('App\ServiceProvider\Cookie');
$container->addServiceProvider('App\ServiceProvider\Layer');
$container->addServiceProvider('App\ServiceProvider\View');
$container->addServiceProvider('App\ServiceProvider\Logger');
// $container->addServiceProvider('App\ServiceProvider\Amqp');
// $container->addServiceProvider('App\ServiceProvider\Redis');
// $container->addServiceProvider('App\ServiceProvider\Memcached');
// $container->addServiceProvider('App\ServiceProvider\Mongo');

/**
 * Step 6: Define your server using Zend Diactoros & Instantiate the Obullo application
 */
$server = Zend\Diactoros\Server::createServerfromRequest(
    new Obullo\App($container),
    $container->get('request'),
    $container->get('response')
);

/**
 * Step 7: Emit output
 *
 * This method should be called last. This executes the Obullo application
 * and returns the HTTP response to the HTTP client.
 */
$server->listen();