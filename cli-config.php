<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

define('ROOT', __DIR__.'/');
define('APP_PATH', __DIR__.'/src/AppBundle');

require_once "vendor/autoload.php";

$container = new League\Container\Container;

/**
 * Step 1: Add service providers
 */
$container->addServiceProvider('AppBundle\ServiceProvider\Config');
$container->addServiceProvider('AppBundle\ServiceProvider\Logger');
$container->addServiceProvider('AppBundle\ServiceProvider\Doctrine');

/**
 * Step 2: Instantiate the container
 */
Obullo\ServerRequestFactory::setContainer($container);
$container->share('request', Obullo\ServerRequestFactory::fromGlobals());


$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode, null, null, false);

// Add alias
$config->addEntityNamespace('AppEntity', 'Entity');
$config->addEntityNamespace('AppRepository', 'Repository');

// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

/*
$conn = array(
    'driver'   => 'pdo_mysql',
    'host'     => '127.0.0.1',
    'dbname'   => 'test',
    'user'     => 'root',
    'password' => '123456'
);
*/
$conn = $container->get('doctrine:default');

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);


return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
