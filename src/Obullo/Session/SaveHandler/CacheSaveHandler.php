<?php

namespace Obullo\Session\SaveHandler;

use Psr\Cache\CacheItemPoolInterface as Cache;

/**
 * Cache Save Handler
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class CacheSaveHandler implements SaveHandlerInterface
{
    /**
     * Service parameters
     *
     * @var array
     */
    protected $params;

    /**
     * Key name
     *
     * @var string
     */
    protected $key = 'Session_';

    /**
     * Expiration time of current session
     *
     * @var integer
     */
    protected $lifetime = 7200; // two hours

    /**
     * Session Name
     *
     * @var string
     */
    protected $sessionName;

    /**
     * The cache storage
     *
     * @var CacheStorage
     */
    protected $cacheStorage;

    /**
     * Session Save Path
     *
     * @var string
     */
    protected $savePath;

    /**
     * Constructor
     *
     * @param object $cache  storage
     * @param array  $params service parameters
     */
    public function __construct(Cache $cache, array $params)
    {
        $this->setStorage($cache);

        $this->params   = $params;
        $this->key      = $params['session']['key'];
        $this->lifetime = $params['session']['gc_maxlifetime'];
    }

    /**
    * Php5 session handler : open storage connection
    *
    * @param string $savePath save path
    * @param string $name     session name
    *
    * @return bool
    */
    public function open($savePath, $name)
    {
        // @todo figure out if we want to use these
        $this->savePath    = $savePath;
        $this->sessionName = $name;

        return true;
    }
 
    /**
     * Close the connection. Called by PHP when the script ends.
     *
     * @return void
     */
    public function close()
    {
        return;
    }
 
    /**
     * Read data from the session.
     *
     * @param string $id session id
     *
     * @return mixed
     */
    public function read($id)
    {
        $cacheItem = $this->cacheStorage->getItem($this->key.$id);
        
        $result = null;
        if ($cacheItem->isHit()) {
            $result = $cacheItem->get();
        }
        return $result;
    }
 
    /**
     * Write data to the session.
     *
     * @param string $id   current session id
     * @param mixed  $data mixed data
     *
     * @return bool
     */
    public function write($id, $data)
    {
        if (empty($data)) { // If we have no session data don't write it.
            return false;
        }
        $cacheItem = $this->cacheStorage->getItem($this->key.$id);
        $cacheItem->set($data);
        $cacheItem->expiresAfter($this->getLifetime());

        $result = $this->cacheStorage->save($cacheItem);

        return $result ? true : false;
    }
 
    /**
     * Delete data from the session.
     *
     * @param string $id current session id
     *
     * @return bool
     */
    public function destroy($id)
    {
        if ($this->cacheStorage->hasItem($this->key.$id)) {
            return $this->cacheStorage->deleteItem($this->key.$id);
        }
    }

    /**
     * Run garbage collection
     *
     * @param integer $maxLifetime expration time
     *
     * @return bool
     */
    public function gc($maxLifetime)
    {
        $maxLifetime = null;
        return true;
    }

    /**
     * Set expiration of valid session
     *
     * @param int $ttl lifetime
     *
     * @return void
     */
    public function setLifetime($ttl)
    {
        $this->lifetime = (int)$ttl;
    }

    /**
     * Get expiration of valid session
     *
     * @return int
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * Set storage
     *
     * @param object storage
     */
    public function setStorage($storage)
    {
        $this->cacheStorage = $storage;
    }

    /**
     * Returns to storage
     *
     * @return object
     */
    public function getStorage()
    {
        return $this->cacheStorage;
    }
}
