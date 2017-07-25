<?php

namespace Obullo\Mvc\Request;

use Interop\Container\ContainerInterface as Container;

/**
 * Flush cached sub request
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Flush
{
    /**
     * Cache service
     *
     * @var object
     */
    protected $cache;

    /**
     * Constructor
     *
     * @param object $container container
     */
    public function __construct(Container $container)
    {
        $this->cache = $container->get('cache');
    }

    /**
     * Removes layer from cache using layer "path" and "parameters".
     *
     * @param string $path uri string
     * @param array  $data array
     *
     * @return boolean
     */
    public function path($path = '', $data = array())
    {
        $hashString = trim($path, '/');
        if (sizeof($data) > 0) {      // We can't use count() in sub layers sizeof gives better results.
            $hashString .= str_replace('"', '', json_encode($data)); // Remove quotes to fix equality problem
        }
        $id = $this->generateId($hashString);

        if ($this->cache->has($id)) {
            return $this->cache->delete($id);
        }
        return false;
    }

    /**
     * Create unsigned integer id using
     * hash string.
     *
     * @param string $hashString resource
     *
     * @return string id
     */
    public function generateId($hashString)
    {
        $id = trim($hashString);
        
        return SubRequest::CACHE_KEY. (int)sprintf("%u", crc32((string)$id));
    }
}
