<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Define the root
 */
define('ROOT', dirname(__DIR__).'/');

/**
 * Step 1: Require the Obullo using Composer's autoloader
 *
 * If you are not using Composer, you need to load Obullo with your own
 * PSR-4 autoloader.
 */
require ROOT . 'constants.php';
require ROOT . 'vendor/autoload.php';

/**
 * Step 2: Instantiate the Container
 */
$container = new League\Container\Container;

$container->share('request', Obullo\ServerRequestFactory::fromGlobals());
$container->share('response', new Zend\Diactoros\Response);
$container->share('router', new Obullo\Router\Router($container, ['resolveCurrentPath' => true]));

/**
 * Step 3: Add config service & Create configuration variables
 */
$container->addServiceProvider('Obullo\Container\ServiceProvider\Config');

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
$container->addServiceProvider('Obullo\Container\ServiceProvider\Cookie');
$container->addServiceProvider('Obullo\Container\ServiceProvider\Layer');
$container->addServiceProvider('Obullo\Container\ServiceProvider\View');
$container->addServiceProvider('Obullo\Container\ServiceProvider\Logger');
$container->addServiceProvider('Obullo\Container\ServiceProvider\Amqp');
// $container->addServiceProvider('Obullo\Container\ServiceProvider\Redis');
// $container->addServiceProvider('Obullo\Container\ServiceProvider\Memcached');
// $container->addServiceProvider('Obullo\Container\ServiceProvider\Mongo');

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