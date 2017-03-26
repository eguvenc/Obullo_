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
 * Start benchmark
 */
Obullo\Benchmark::start();

/**
 * Step 2: Instantiate the container
 */
$container = new League\Container\Container;
Obullo\ServerRequestFactory::setContainer($container);

$container->share('request', Obullo\ServerRequestFactory::fromGlobals());
$container->share('response', new Zend\Diactoros\Response);
$container->share('router', new Obullo\Router\Router($container, ['autoResolver' => true]));

/**
 * Step 3: Create your mvc applications
 */
$application = new Obullo\Mvc\App($container);

$application->addBundle(new AppBundle\IndexBundle('/'));
// $application->addBundle(new BackendBundle\IndexBundle('/backend'));
$application->create();

/**
 * Step 4: Define your server using Zend Diactoros & Instantiate the Obullo application
 */
$server = Zend\Diactoros\Server::createServerfromRequest(
    $application,
    $container->get('request'),
    $container->get('response')
);

/**
 * Step 5: Emit output
 *
 * This method should be called last. This executes the Obullo application
 * and returns the HTTP response to the HTTP client.
 */
$server->listen();

/**
 * End Benchmark
 */
Obullo\Benchmark::end($container);
