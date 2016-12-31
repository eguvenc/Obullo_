<?php

namespace Obullo\Session\SaveHandler;

/**
 * Save Handler Interface
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface SaveHandlerInterface
{
    /**
    * Php5 session handler interface open function
    *
    * @param string $savePath    save path
    * @param string $sessionName session name
    *
    * @return bool
    */
    public function open($savePath, $sessionName);

    /**
     * Close the connection. Called by PHP when the script ends.
     *
     * @return void
     */
    public function close();

    /**
     * Read data from the session.
     *
     * @param string $id session id
     *
     * @return mixed
     */
    public function read($id);

    /**
     * Write data to the session.
     *
     * @param string $id   current session id
     * @param mixed  $data mixed data
     *
     * @return bool
     */
    public function write($id, $data);

    /**
     * Delete data from the session.
     *
     * @param string $id current session id
     *
     * @return bool
     */
    public function destroy($id);

    /**
     * Run garbage collection
     *
     * @param integer $maxLifetime expration time
     *
     * @return bool
     */
    public function gc($maxLifetime);

    /**
     * Set expiration of valid session
     *
     * @param int $ttl lifetime
     *
     * @return void
     */
    public function setLifetime($ttl);

    /**
     * Get expiration of valid session
     *
     * @return int
     */
    public function getLifetime();
}
