<?php
/*
|--------------------------------------------------------------------------
| Service Providers
|--------------------------------------------------------------------------
| Specifies your service providers.
*/
$container->addProvider('Container\Provider\Cookie');
$container->addProvider('Container\Provider\View');
$container->addProvider('Container\Provider\Logger');
/*
|--------------------------------------------------------------------------
| Connectors
|--------------------------------------------------------------------------
| Specifies your connection providers.
*/
// $container->addProvider('Container\Provider\Amqp');
// $container->addProvider('Container\Provider\Database');
// $container->addProvider('Container\Provider\Redis');
// $container->addProvider('Container\Provider\Memcached');
// $container->addProvider('Container\Provider\Mongo');