<?php

namespace Obullo\Session;

use Psr\Log\LoggerInterface as Logger;
use Psr\Http\Message\ServerRequestInterface as Request;
use Obullo\Session\SaveHandler\SaveHandlerInterface as SaveHandler;

/**
 * Session Class
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Session implements SessionInterface
{
    /**
     * Session name
     *
     * @var string
     */
    protected $name;

    /**
     * Config
     *
     * @var object
     */
    protected $config;

    /**
     * Service parameters
     *
     * @var array
     */
    protected $params;

    /**
     * Logger
     *
     * @var object
     */
    protected $logger;

    /**
     * Session handler
     *
     * @var object
     */
    protected $saveHandler;

    /**
     * Constructor
     *
     * @param object $request \Psr\Http\Message\RequestInterface
     * @param object $logger  \Obullo\Log\LoggerInterface
     * @param array  $params  service parameters
     */
    public function __construct(Request $request, Logger $logger, array $params)
    {
        $this->params = $params;
        $this->server = $request->getServerParams();
        $this->cookie = $request->getCookieParams();

        ini_set('session.cookie_domain', $params['cookie']['domain']);
        ini_set('session.gc_maxlifetime', $params['session']['gc_maxlifetime']);

        $this->logger = $logger;
        $this->logger->debug('Session Class Initialized');

        register_shutdown_function(array($this, 'close'));
    }

    /**
     * Set session save handler
     *
     * @param object $handler \Obullo\Session\SaveHandler\SaveHandlerInterface
     *
     * @return void
     */
    public function setSaveHandler(SaveHandler $handler)
    {
        $this->saveHandler = $handler;

        session_set_save_handler(
            array($this->saveHandler, 'open'),
            array($this->saveHandler, 'close'),
            array($this->saveHandler, 'read'),
            array($this->saveHandler, 'write'),
            array($this->saveHandler, 'destroy'),
            array($this->saveHandler, 'gc')
        );
        session_set_cookie_params(
            $this->params['cookie']['lifetime'],
            $this->params['cookie']['path'],
            $this->params['cookie']['domain'],
            $this->params['cookie']['secure'],
            $this->params['cookie']['httpOnly']
        );
    }

    /**
     * Set session name
     *
     * @param string $name session name
     *
     * @return void
     */
    public function setName($name = null)
    {
        $this->name = $name;
        session_name($name);
        return $this;
    }

    /**
     * Get name of the session
     *
     * @return string
     */
    public function getName()
    {
        if (null === $this->name) {
            $this->name = session_name();
        }
        return $this->name;
    }

    /**
     * Session start
     *
     * @return void
     */
    public function start()
    {
        if (! $this->exists()) { // If another session_start() used before ?
            session_start();
        }
    }

    /**
     * Read Cookie and validate Meta Data
     *
     * @return boolean
     */
    public function readSession()
    {
        $name = $this->getName();
        
        $cookie = (isset($this->cookie[$name])) ? $this->cookie[$name] : false;
        if ($cookie === false) {
            return false;
        }
        return true;
    }

    /**
     * Regenerate id
     *
     * Regenerate the session ID, using session save handler's
     * native ID generation Can safely be called in the middle of a session.
     *
     * @param bool $deleteOldSession whether to delete previous session data
     *
     * @return string new session id
     */
    public function regenerateId($deleteOldSession = true)
    {
        session_regenerate_id((bool) $deleteOldSession);

        return $this;
    }

    /**
     * Does a session exist and is it currently active ?
     *
     * @return bool
     */
    public function exists()
    {
        if (session_status() == PHP_SESSION_ACTIVE && session_id()) {  // Session is active & not empty.
            return true;
        }
        if (headers_sent()) {
            return true;
        }
        return false;
    }

    /**
     * Destroy the current session
     *
     * @return void
     */
    public function destroy()
    {
        if (! $this->exists()) {
            return;
        }
        session_destroy();
        if (! headers_sent()) {
            setcookie(
                $this->getName(),                 // session name
                '',                               // value
                $this->server['REQUEST_TIME'] - 42000, // TTL for cookie
                $this->params['cookie']['path'],
                $this->params['cookie']['domain'],
                $this->params['cookie']['secure'],
                $this->params['cookie']['httpOnly']
            );
        }
    }

    /**
     * Fetch a specific item from the session array
     *
     * @param string $item   session key
     * @param string $prefix session key prefix
     *
     * @return string
     */
    public function get($item, $prefix = '')
    {
        return ( ! isset($_SESSION[$prefix . $item])) ? false : $_SESSION[$prefix . $item];
    }

    /**
     * Returns to session_id() value
     *
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * Returns all session data
     *
     * @return array
     */
    public function getAll()
    {
        if (isset($_SESSION)) {
            return $_SESSION;
        }
        return array();
    }

    /**
     * Add or change data in the $_SESSION
     *
     * @param mixed  $new    key or array
     * @param string $newval value
     * @param string $prefix prefix
     *
     * @return void
     */
    public function set($new = array(), $newval = '', $prefix = '')
    {
        if (is_string($new)) {
            $new = array($new => $newval);
        }
        if (sizeof($new) > 0) {
            foreach ($new as $key => $val) {
                $_SESSION[$prefix.$key] = $val;
            }
        }
    }

    /**
     * Delete a session variable from the $_SESSION
     *
     * @param mixed  $new    key or array
     * @param string $prefix sesison key prefix
     *
     * @return void
     */
    public function remove($new = array(), $prefix = '')
    {
        if (is_string($new)) {
            $new = array($new => '');
        }
        if (sizeof($new) > 0) {
            foreach ($new as $key => $val) {
                $val = null;
                unset($_SESSION[$prefix.$key]);
            }
        }
        if (sizeof($_SESSION) == 0) {  // When we want to remove session, we couldn't
                                       // remove the last session id.
            $this->saveHandler->destroy(session_id());  // This solution fix the issue.
        }
    }

    /**
     * Close session writer
     *
     * @return void
     */
    public function close()
    {
        session_write_close();
    }

    /**
     * Remember me
     *
     * @param integer $lifetime         default 3600
     * @param bool    $deleteOldSession whether to delete old session data after renenerate
     *
     * @return void
     */
    public function rememberMe($lifetime = null, $deleteOldSession = true)
    {
        $reminder = new Remminder($this, $this->params);
        $reminder->rememberMe($lifetime, $deleteOldSession);
    }

    /**
     * Forget me
     *
     * @return void
     */
    public function forgetMe()
    {
        $reminder = new Remminder($this, $this->params);
        $reminder->forgetMe();
    }
}
