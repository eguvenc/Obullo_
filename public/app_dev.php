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
 * Start Benchmark
 */
Obullo\Utils\Benchmark::start();

/**
 * Step 2: Instantiate the Container
 */
$container = new League\Container\Container;
Obullo\ServerRequestFactory::setContainer($container);

$container->share('request', Obullo\ServerRequestFactory::fromGlobals());
$container->share('response', new Zend\Diactoros\Response);
$container->share('router', new Obullo\Router\Router($container, ['resolveCurrentPath' => true]));

/**
 * Step 3: Add your service providers
 */
$container->addServiceProvider('App\ServiceProvider\Config');
$container->addServiceProvider('App\ServiceProvider\Cookie');
$container->addServiceProvider('App\ServiceProvider\Mvc');
$container->addServiceProvider('App\ServiceProvider\View');
$container->addServiceProvider('App\ServiceProvider\Logger');
$container->addServiceProvider('App\ServiceProvider\Database');
$container->addServiceProvider('App\ServiceProvider\Cache');
// $container->addServiceProvider('App\ServiceProvider\Amqp');
$container->addServiceProvider('App\ServiceProvider\Redis');
// $container->addServiceProvider('App\ServiceProvider\Memcached');
// $container->addServiceProvider('App\ServiceProvider\Mongo');

/**
 * Step 4: Add your middlewares
 */
$application = new Obullo\Mvc\App($container);

// $application->add('ParsedBody');

/**
 * Step 5: Define your server using Zend Diactoros & Instantiate the Obullo application
 */
$server = Zend\Diactoros\Server::createServerfromRequest(
    $application,
    $container->get('request'),
    $container->get('response')
);

/**
 * Step 6: Emit output
 *
 * This method should be called last. This executes the Obullo application
 * and returns the HTTP response to the HTTP client.
 */
$server->listen();

/**
 * End Benchmark
 */
Obullo\Utils\Benchmark::end($container);