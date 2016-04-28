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
require ROOT.'constants.php';
require ROOT.'vendor/autoload.php';

/**
 * Step 2: Instantiate a Container to load your service providers.
 */
$container = new League\Container\Container;

/**
 * Step 3: Instantiate a Obullo application and load your middlewares.
 *
 * This example instantiates a Obullo application using
 * your default middlewares.
 */
$app = new App(
    $container,
    [
        new Http\Middleware\Application,
    ]
);

/**
 * Step 4: Define your Obullo using Zend Diactoros
 */
$server = Zend\Diactoros\Server::createServerfromRequest(
    $app,
    Zend\Diactoros\ServerRequestFactory::fromGlobals()
);

/**
 * Step 5: Run the Obullo application
 *
 * This method should be called last. This executes the Obullo application
 * and returns the HTTP response to the HTTP client.
 */
$server->listen();