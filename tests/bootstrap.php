<?php

// Define the root
define('ROOT', dirname(__DIR__).'/');

// Prevent session cookies
ini_set('session.use_cookies', 0);

// Include constants
require ROOT . 'constants.php';

// Enable Composer autoloader
$autoloader = require ROOT . 'vendor/autoload.php';

// require dirname(__FILE__) . '/getallheaders.php';

// Register test classes
// $autoloader->addPsr4('Tests\\', __DIR__);