<?php

// Define the root
define('ROOT', dirname(__DIR__).'/');

// Prevent session cookies
ini_set('session.use_cookies', 0);

// Include constants
require ROOT . 'constants.php';

// Enable Composer autoloader
$autoloader = require ROOT . 'vendor/autoload.php';

// Container
$container = new League\Container\Container;

Obullo\ServerRequestFactory::setContainer($container);
$container->share('request', Obullo\ServerRequestFactory::fromGlobals());

$container->addServiceProvider('Obullo\Container\ServiceProvider\Logger');
$container->addServiceProvider('Obullo\Container\ServiceProvider\Config');


// require dirname(__FILE__) . '/getallheaders.php';

// Register test classes
// $autoloader->addPsr4('Tests\\', __DIR__);