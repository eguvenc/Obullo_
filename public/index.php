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
 * Step 3: Instantiate a Obullo application
 * 
 * This example instantiates a Obullo application using
 * your default middlewares.
 */
$app = new App($container);

/**
 * Step 4: Add your middlewares
 */
$app->add('Application');

/**
 * Step 5: Define your Obullo using Zend Diactoros
 */
$server = Zend\Diactoros\Server::createServerfromRequest(
    $app,
    $container->get('request'),
    $container->get('response')
);

/**
 * Step 6: Run the Obullo
 *
 * This method should be called last. This executes the Obullo application
 * and returns the HTTP response to the HTTP client.
 */
$server->listen();