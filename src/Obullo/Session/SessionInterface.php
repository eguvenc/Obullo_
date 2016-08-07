<?php

namespace Obullo\Session;

/**
 * Session Interface
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface SessionInterface
{
    /**
     * Set session name
     *
     * @param string $name session name
     *
     * @return void
     */
    public function setName($name = null);

    /**
     * Get name of the session
     *
     * @return string
     */
    public function getName();

    /**
     * Returns to session_id() value
     *
     * @return string
     */
    public function getId();
    
    /**
     * Session start
     *
     * @return void
     */
    public function start();

    /**
     * Destroy the current session
     *
     * @return void
     */
    public function destroy();

    /**
     * Add or change data in the $_SESSION
     *
     * @param mixed  $new    key or array
     * @param string $newval value
     * @param string $prefix prefix
     *
     * @return void
     */
    public function set($new = array(), $newval = '', $prefix = '');

    /**
     * Fetch a specific item from the session array
     *
     * @param string $item   session key
     * @param string $prefix session key prefix
     *
     * @return string
     */
    public function get($item, $prefix = '');

    /**
     * Delete a session variable from the $_SESSION
     *
     * @param mixed  $new    key or array
     * @param string $prefix sesison key prefix
     *
     * @return void
     */
    public function remove($new = array(), $prefix = '');
}
