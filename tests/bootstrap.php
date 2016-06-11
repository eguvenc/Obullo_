<?php

// Define the root
define('ROOT', dirname(__DIR__).'/');

// Set timezone
date_default_timezone_set('Europe/Istanbul');

// Prevent session cookies
ini_set('session.use_cookies', 0);

// Include constants
require ROOT . 'constants.php';

// Enable Composer autoloader
$autoloader = require ROOT . 'vendor/autoload.php';

// Container
$container = new League\Container\Container;

// require dirname(__FILE__) . '/getallheaders.php';

// Register test classes
// $autoloader->addPsr4('Tests\\', __DIR__);