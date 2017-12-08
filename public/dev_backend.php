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
 * Step 1: Composer autoloader
 */
require 'vendor/autoload.php';

/**
 * Step 2: Instantiate the container
 */
$container = new League\Container\Container;
Obullo\ServerRequestFactory::setContainer($container);

$container->share('request', Obullo\ServerRequestFactory::fromGlobals());
$container->share('response', new Zend\Diactoros\Response);
$container->share('router', new Obullo\Router\Router($container));

/**
 * Step 3: Create your mvc application
 */
$application = new Obullo\Mvc\BenchmarkAwareApp($container);
$application->mount(new BackendBundle\IndexBundle);
$application->start();

/**
 * Step 4: Define your server using Zend Diactoros
 */
$server = Zend\Diactoros\Server::createServerfromRequest(
    $application,
    $container->get('request'),
    $container->get('response')
);

/**
 * Step 5: Emit output
 *
 * This method should be called last. This executes the Http Middlewares
 * and returns the HTTP response to the HTTP client.
 */
$server->listen();

/**
 * Step 6: Close application
 */
$application->close();