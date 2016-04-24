<?php

namespace Container\Provider\Connector;

use Database\Doctrine\DBAL\QueryBuilder;
use Container\Provider\AbstractServiceProvider;
use Interop\Container\ContainerInterface as Container;

/**
 * Doctrine Query Builder Provider
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class DoctrineQueryBuilder extends AbstractServiceProvider
{
    // /**
    //  * Container
    //  *
    //  * @var object
    //  */
    // public $container;

    // /**
    //  * Constructor
    //  * 
    //  * @param string $container container
    //  */
    // public function __construct(Container $container)
    // {
    //     $this->container = $container;
    // }

    // /**
    //  * Register
    //  * 
    //  * @return void
    //  */
    // public function register()
    // {
    //     return;
    // }

    // /**
    //  * Get connection
    //  *
    //  * @param array $params array
    //  *
    //  * @return object
    //  */
    // public function shared($params = array())
    // {
    //     $connection = $this->container->get('database')->shared($params);

    //     return new QueryBuilder($connection); // Get existing connection
    // }

    // /**
    //  * Create unnamed connection
    //  *
    //  * @param array $params array
    //  *
    //  * @return object
    //  */
    // public function factory($params = array())
    // {
    //     $connection = $this->container->get('database')->factory($params);

    //     return new QueryBuilder($connection);  // Create new undefined connection
    // }
}