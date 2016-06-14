<?php

namespace Obullo\Container\ServiceProvider\Connector;

use RuntimeException;
use Obullo\Database\SQLLogger;
use UnexpectedValueException;
use Interop\Container\ContainerInterface as Container;
use Obullo\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Database Service Connection Provider
 * 
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Database extends AbstractServiceProvider
{
    /**
     * Database config array
     * 
     * @var array
     */
    protected $params;

    /**
     * Container
     * 
     * @var object
     */
    protected $container;

    /**
     * Database adapter class
     * 
     * @var string
     */
    protected $adapterClass;

    /**
     * Constructor
     *
     * @param array $container container
     * @param array $params    connection parameters
     * 
     * @uses register()
     */
    public function __construct(Container $container, array $params)
    {
        $this->container = $container;
        $this->params    = $params;
        $this->adapterClass = '\Obullo\Database\Pdo\Adapter';
        $this->register();
    }

    /**
     * Register all connections as shared services ( run once )
     * 
     * @return void
     */
    public function register()
    {
        foreach (array_keys($this->params['connections']) as $key) {

            $this->container->share(
                $this->getConnectionKey($key),
                function () use ($key) {
                    return $this->createConnection($this->params['connections'][$key]);
                }
            );
        }
    }

    /**
     * Creates databse connections
     * 
     * @param array $params database connection params
     * 
     * @return object
     */
    protected function createConnection(array $params)
    {
        $params['dsn'] = str_replace('pdo_', '', $params['dsn']);
        $Class = '\\Obullo\Database\Pdo\Drivers\\'.ucfirst(strstr($params['dsn'], ':', true));

        if ($this->params['sql']['log']) {
            $params['logger'] = new SQLLogger($this->container->get('logger'));
        }
        return new $Class($params);

    }

    /**
     * Retrieve shared database connection instance from connection pool
     *
     * @param array $params provider parameters
     * 
     * @return object PDO
     */
    public function shared($params = array())
    {
        if (! isset($params['connection'])) {
            $params['connection'] = array_keys($this->params['connections'])[0];  //  Set default connection
        }
        if (! isset($this->params['connections'][$params['connection']])) {
            throw new UnexpectedValueException(
                sprintf(
                    'Connection key %s does not exist in your database.php config file.',
                    $params['connection']
                )
            );
        }
        $key = $this->getConnectionKey($params['connection']);

        return $this->container->get($key);  // return to shared connection
    }

    /**
     * Create a new database connection if you don't want to add config file and you want to create new one.
     * 
     * @param array $params connection parameters
     * 
     * @return object database
     */
    public function factory($params = array())
    {   
        $key = $this->getConnectionId($params);

        if (! $this->container->has($key)) { // create shared connection if not exists

            $this->container->share(
                $key,
                function () use ($params) {
                    return $this->createConnection($params);
                }
            );
        }
        return $this->container->get($key);
    }

    /**
     * Close all "active" connections
     */
    public function __destruct()
    {
        foreach (array_keys($this->params['connections']) as $key) {        // Close the connections

            $key = $this->getConnectionKey($key);

            if ($this->container->hasShared($key, true)) {
                $this->container->add($key, '');
            }
        }
    }

}
