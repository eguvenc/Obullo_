<?php

namespace Obullo\Session;

use Obullo\Session\SessionInterface as Session;

/**
 * Session Reminder Class
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Reminder
{
    /**
     * Session Class
     *
     * @var object
     */
    public $session;

    /**
     * Service Parameters
     *
     * @var array
     */
    public $params = array();

    /**
     * Constructor
     *
     * @param object $session session
     * @param array  $params  service parameters
     */
    public function __construct(Session $session, array $params)
    {
        $this->params = $params;
        $this->session = $session;
    }

    /**
     * Set the TTL (in seconds) for the session cookie expiry
     *
     * Can safely be called in the middle of a session.
     *
     * @param null $ttl              expiration   null or integer
     * @param bool $deleteOldSession whether to delete old session data after renenerate
     *
     * @return void
     */
    public function rememberMe($ttl = null, $deleteOldSession = true)
    {
        $this->setSessionCookieLifetime($ttl, $deleteOldSession);
    }

    /**
     * Set a 0s TTL for the session cookie
     *
     * Can safely be called in the middle of a session.
     *
     * @return SessionManager
     */
    public function forgetMe()
    {
        $this->setSessionCookieLifetime(0);
    }

    /**
     * Set the session cookie lifetime
     *
     * If a session already exists, destroys it (without sending an expiration
     * cookie), regenerates the session ID, and restarts the session.
     *
     * @param int  $lifetime         expiration
     * @param bool $deleteOldSession whether to delete old session data after renenerate
     *
     * @return void
     */
    protected function setSessionCookieLifetime($lifetime, $deleteOldSession = true)
    {
        if ($lifetime == null) {
            $lifetime = $this->params['session']['lifetime'];
        }
        session_set_cookie_params(
            $lifetime,
            $this->params['cookie']['path'],
            $this->params['cookie']['domain'],
            $this->params['cookie']['secure'],
            $this->params['cookie']['httpOnly']
        );
        if ($this->session->exists()) {
            // If session exists
            // we need regenerate id & set cookie.

            $this->session->regenerateId($deleteOldSession, $lifetime);
        }
    }
}
